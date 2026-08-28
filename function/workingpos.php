<?php

class Product {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET PRODUCTS with category name, stock, barcode and category-level discount flags
    public function getProducts() {
        $sql = "SELECT p.*, 
                    pc.category_name,
                    pc.has_vat, pc.senior_discount, pc.pwd_discount,
                    um.different_measurement AS measurement_name,
                    COALESCE(SUM(CASE
                        WHEN i.current_quantity > 0
                             AND (i.expiry_date IS NULL OR i.expiry_date >= CURDATE())
                        THEN i.current_quantity
                        ELSE 0
                    END), 0) AS stock,
                    MIN(CASE
                        WHEN i.current_quantity > 0
                             AND i.expiry_date IS NOT NULL
                             AND i.expiry_date >= CURDATE()
                        THEN i.expiry_date
                        ELSE NULL
                    END) AS earliest_expiry_date,
                    COALESCE((SELECT newest.sale_price
                              FROM inventory newest
                              WHERE newest.product_id = p.id
                              ORDER BY newest.date_received DESC, newest.id DESC
                              LIMIT 1), 0) AS total_price,
                    COALESCE((SELECT newest.purchase_cost
                              FROM inventory newest
                              WHERE newest.product_id = p.id
                              ORDER BY newest.date_received DESC, newest.id DESC
                              LIMIT 1), 0) AS net_price,
                    COALESCE((SELECT newest.sale_price
                              FROM inventory newest
                              WHERE newest.product_id = p.id
                              ORDER BY newest.date_received DESC, newest.id DESC
                              LIMIT 1), 0) AS sale_price
                FROM products p
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                LEFT JOIN unit_measurement um ON p.measurement_id = um.unit_id
                LEFT JOIN inventory i ON p.id = i.product_id
                GROUP BY p.id
                ORDER BY p.generic_name ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET ALL CATEGORIES for filter buttons
    public function getCategories() {
        $sql = "SELECT * FROM product_categories ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET DISCOUNTS
    public function getDiscounts() {
        $sql = "SELECT * FROM discounts ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function calculateSpecialDiscount(array $cartItems, ?string $customerType, string $discountRule = 'regular', ?string $customerId = null, float $weekDiscountTotal = 0.0, float $weekEligibleSubtotal = 0.0): array {
        $customerType = strtolower(trim((string)($customerType ?? '')));
        if (!in_array($customerType, ['senior', 'pwd'], true)) {
            return [
                'discount_total' => 0.0,
                'eligible_subtotal' => 0.0,
                'rate' => 0.0,
                'remaining_discount_cap' => 125.0,
                'remaining_purchase_cap' => 2500.0,
                'eligible_item_count' => 0,
            ];
        }

        $eligibleSubtotal = 0.0;
        $eligibleItemCount = 0;

        foreach ($cartItems as $item) {
            $isEligible = !empty($item['eligible_for_discount']) || !empty($item['senior']) || !empty($item['pwd']);
            if ($isEligible) {
                $qty = max(0, (int)($item['qty'] ?? 0));
                $price = (float)($item['price'] ?? 0);
                if ($qty > 0 && $price > 0) {
                    $eligibleSubtotal += $price * $qty;
                    $eligibleItemCount++;
                }
            }
        }

        $rate = ($discountRule === 'statutory') ? 0.20 : 0.05;
        $remainingDiscountCap = max(0.0, 125.0 - (float)$weekDiscountTotal);
        $remainingPurchaseCap = max(0.0, 2500.0 - (float)$weekEligibleSubtotal);
        $discountableSubtotal = min($eligibleSubtotal, $remainingPurchaseCap);
        $discountTotal = round(min($discountableSubtotal * $rate, $remainingDiscountCap), 2);

        return [
            'discount_total' => $discountTotal,
            'eligible_subtotal' => $discountableSubtotal,
            'rate' => $rate,
            'remaining_discount_cap' => $remainingDiscountCap,
            'remaining_purchase_cap' => $remainingPurchaseCap,
            'eligible_item_count' => $eligibleItemCount,
        ];
    }

    // PROCESS TRANSACTION
    public function processTransaction($userId, $cartItems, $discountId, $customerName, $customerId, $discountTotal = 0, $totalVatExemption = 0, $customerType = null, $discountRule = 'regular') {
        try {
            $this->conn->beginTransaction();

            // =========================================================
            // SECURITY: SERVER-SIDE VALIDATION
            // The client (POS JS) computes prices/discounts for display,
            // but the server re-derives them from the database so that a
            // tampered request cannot buy stock at ₱0.01, use negative
            // quantities, or apply inflated discounts.
            // =========================================================

            // 1) Fetch authoritative data for every product in the cart
            $productData = [];   // id => ['base_price' => float, 'units_per_package' => int, 'senior' => bool, 'pwd' => bool]
            foreach ($cartItems as $item) {
                $pid = (int)($item['id'] ?? 0);
                if ($pid <= 0 || isset($productData[$pid])) {
                    continue;
                }
                $prodStmt = $this->conn->prepare("
                    SELECT p.id,
                           COALESCE(p.units_per_package, 1) AS units_per_package,
                           COALESCE(pc.senior_discount, 0) AS senior_eligible,
                           COALESCE(pc.pwd_discount, 0) AS pwd_eligible,
                           COALESCE((SELECT newest.sale_price
                                     FROM inventory newest
                                     WHERE newest.product_id = p.id
                                     ORDER BY newest.date_received DESC, newest.id DESC
                                     LIMIT 1), 0) AS base_price
                    FROM products p
                    LEFT JOIN product_categories pc ON p.category_id = pc.id
                    WHERE p.id = ?
                ");
                $prodStmt->execute([$pid]);
                $row = $prodStmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $productData[$pid] = [
                        'base_price'         => (float)$row['base_price'],
                        'units_per_package'  => max(1, (int)$row['units_per_package']),
                        'senior_eligible'    => (bool)$row['senior_eligible'],
                        'pwd_eligible'       => (bool)$row['pwd_eligible'],
                    ];
                }
            }

            // 2) Fetch manager price overrides logged by THIS cashier recently
            // (product_id => list of allowed discounted rates)
            $overrideRates = [];
            $cartIds = array_unique(array_filter(array_map(fn($i) => (int)($i['id'] ?? 0), $cartItems)));
            if ($cartIds) {
                $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
                $ovStmt = $this->conn->prepare("
                    SELECT product_id, discount_percent
                    FROM override_log
                    WHERE cashier_id = ?
                      AND product_id IN ($placeholders)
                      AND created_at >= (NOW() - INTERVAL 2 HOUR)
                    ORDER BY created_at DESC
                ");
                $ovStmt->execute(array_merge([(int)$userId], $cartIds));
                foreach ($ovStmt->fetchAll(PDO::FETCH_ASSOC) as $ov) {
                    $pid = (int)$ov['product_id'];
                    $rate = round(max(0.0, min(100.0, (float)$ov['discount_percent'])) / 100.0, 4);
                    if ($rate > 0 && !in_array($rate, $overrideRates[$pid] ?? [], true)) {
                        $overrideRates[$pid][] = $rate;
                    }
                }
            }

            // 3) Validate & normalize cart items using server-side values
            $serverCart = [];
            foreach ($cartItems as $item) {
                if (!is_array($item)) {
                    throw new Exception('Invalid cart item');
                }
                $pid = (int)($item['id'] ?? 0);
                $qty = (int)($item['qty'] ?? 0);

                // Reject zero/negative/fractional quantities (previously a
                // negative qty zeroed the total while stock was still handed over)
                if ($pid <= 0 || $qty <= 0) {
                    throw new Exception('Invalid quantity for product ID ' . $pid);
                }

                if (!isset($productData[$pid])) {
                    throw new Exception('Unknown product ID ' . $pid);
                }

                $data = $productData[$pid];
                $clientPrice = (float)($item['price'] ?? 0);

                // Price binding: accept the client price ONLY when it matches
                // the authoritative shelf price (or an authorized manager
                // override price). Otherwise the server price wins.
                $unitPrice = $data['base_price'];
                if (abs($clientPrice - $data['base_price']) < 0.011) {
                    $unitPrice = $data['base_price'];
                } else {
                    foreach ($overrideRates[$pid] ?? [] as $rate) {
                        $allowedOverride = round($data['base_price'] * (1 - $rate), 2);
                        if (abs($clientPrice - $allowedOverride) < 0.011) {
                            $unitPrice = $allowedOverride;
                            break;
                        }
                    }
                }

                // pcs/units_per_package comes from the DB, never the client
                $serverCart[] = [
                    'id'    => $pid,
                    'qty'   => $qty,
                    'price' => $unitPrice,
                    'pcs'   => $data['units_per_package'],
                    'senior' => $data['senior_eligible'],
                    'pwd'    => $data['pwd_eligible'],
                    'eligible_for_discount' => $data['senior_eligible'] || $data['pwd_eligible'],
                ];
            }
            $cartItems = $serverCart;

            // Calculate totals (authoritative prices)
            $grossTransactionAmount = 0;
            foreach ($cartItems as $item) {
                $grossTransactionAmount += (float)$item['price'] * (int)$item['qty']; // qty here is 'packs'
            }

            // =========================================================
            // DISCOUNT VALIDATION — the server caps whatever the client
            // claims at what it computes itself as the maximum allowed.
            // =========================================================
            $appliedDiscount = (float)$discountTotal;
            if ($appliedDiscount <= 0 && in_array(strtolower(trim((string)$customerType)), ['senior', 'pwd'], true)) {
                $weekDiscountStmt = $this->conn->prepare(
                    "SELECT COALESCE(SUM(discount_total), 0) AS week_discount_total
                     FROM transactions
                     WHERE customer_type = ? AND customer_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"
                );
                $weekDiscountStmt->execute([strtolower(trim((string)$customerType)), $customerId]);
                $weekDiscountRow = $weekDiscountStmt->fetch(PDO::FETCH_ASSOC);
                $weekDiscountTotal = (float)($weekDiscountRow['week_discount_total'] ?? 0);

                $weekEligibleStmt = $this->conn->prepare(
                    "SELECT COALESCE(SUM(total_amount), 0) AS week_eligible_subtotal
                     FROM transactions
                     WHERE customer_type = ? AND customer_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"
                );
                $weekEligibleStmt->execute([strtolower(trim((string)$customerType)), $customerId]);
                $weekEligibleRow = $weekEligibleStmt->fetch(PDO::FETCH_ASSOC);
                $weekEligibleSubtotal = (float)($weekEligibleRow['week_eligible_subtotal'] ?? 0);

                $discountDetails = self::calculateSpecialDiscount($cartItems, $customerType, $discountRule, $customerId, $weekDiscountTotal, $weekEligibleSubtotal);
                $appliedDiscount = (float)$discountDetails['discount_total'];
            }

            // Cap the client-claimed discount at the maximum the server allows:
            // (a) statutory senior/PWD discount (with weekly caps), and/or
            // (b) the selected store discount rate from the discounts table, and/or
            // (c) manager price overrides logged for this cashier.
            $allowedDiscount = 0.0;

            // (a) statutory portion
            $customerTypeNorm = strtolower(trim((string)$customerType));
            if (in_array($customerTypeNorm, ['senior', 'pwd'], true)) {
                $weekCapStmt = $this->conn->prepare(
                    "SELECT COALESCE(SUM(discount_total), 0) AS week_discount_total
                     FROM transactions
                     WHERE customer_type = ? AND customer_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"
                );
                $weekCapStmt->execute([$customerTypeNorm, $customerId]);
                $weekCapRow = $weekCapStmt->fetch(PDO::FETCH_ASSOC);
                $statutory = self::calculateSpecialDiscount($cartItems, $customerType, 'statutory', $customerId, (float)($weekCapRow['week_discount_total'] ?? 0), 0.0);
                $allowedDiscount = max($allowedDiscount, (float)$statutory['discount_total']);
            }

            // (b) store discount portion
            if ($discountId) {
                $discStmt = $this->conn->prepare("SELECT discount_rate, discount_name FROM discounts WHERE id = ?");
                $discStmt->execute([(int)$discountId]);
                $discRow = $discStmt->fetch(PDO::FETCH_ASSOC);
                if ($discRow) {
                    $rate = (float)$discRow['discount_rate'];
                    $rate = $rate > 1 ? $rate / 100.0 : $rate;
                    $isStatutoryRow = stripos((string)$discRow['discount_name'], 'senior') !== false
                                   || stripos((string)$discRow['discount_name'], 'pwd') !== false;
                    if (!$isStatutoryRow && $rate > 0) {
                        $regularDiscount = 0.0;
                        foreach ($cartItems as $item) {
                            $regularDiscount += (float)$item['price'] * (int)$item['qty'] * $rate;
                        }
                        $allowedDiscount = max($allowedDiscount, round($regularDiscount, 2));
                    }
                }
            }

            // (c) manager override portion (VAT-exclusive base, mirroring the POS UI)
            $overrideAllowed = 0.0;
            foreach ($cartItems as $item) {
                foreach ($overrideRates[$item['id']] ?? [] as $rate) {
                    $gross = (float)$item['price'] * (int)$item['qty'];
                    $net = $gross / 1.12;
                    $overrideAllowed += $net * $rate;
                }
            }
            $allowedDiscount = round(max($allowedDiscount, $overrideAllowed), 2);

            // Clamp the claimed discount to the allowed maximum
            $appliedDiscount = round(min(max(0.0, $appliedDiscount), $allowedDiscount, $grossTransactionAmount), 2);

            // Cap the VAT exemption claim: only statutory-eligible items and
            // manager-overridden items qualify (12% VAT-inclusive pricing).
            $allowedVatExemption = 0.0;
            foreach ($cartItems as $item) {
                $isStatutoryEligible = in_array($customerTypeNorm, ['senior', 'pwd'], true)
                    && ($item['senior'] || $item['pwd']);
                $hasOverride = !empty($overrideRates[$item['id']]);
                if ($isStatutoryEligible || $hasOverride) {
                    $gross = (float)$item['price'] * (int)$item['qty'];
                    $allowedVatExemption += $gross - ($gross / 1.12);
                }
            }
            $allowedVatExemption = round($allowedVatExemption, 2);
            $totalVatExemption = round(min(max(0.0, (float)$totalVatExemption), $allowedVatExemption), 2);

            $totalAmount = round(max(0.0, $grossTransactionAmount - $appliedDiscount - (float)$totalVatExemption), 2);

            // Insert transaction with discount_total, total_vat_exemption, and customer_type
            $stmt = $this->conn->prepare("INSERT INTO transactions (user_id, discount_id, customer_name, customer_id, total_amount, discount_total, total_vat_exemption, customer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $discountId ?: null, $customerName ?: null, $customerId ?: null, $totalAmount, $appliedDiscount, $totalVatExemption, $customerType]);
            $transactionId = $this->conn->lastInsertId();

            // Insert transaction items and deduct inventory
            foreach ($cartItems as $item) {
                $grossLineSubtotal = (float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0);
                $subtotal = $grossLineSubtotal;

                $discountShare = 0.0;
                if ($appliedDiscount > 0 && $grossTransactionAmount > 0) {
                    $discountShare = round(($grossLineSubtotal / $grossTransactionAmount) * $appliedDiscount, 2);
                    $subtotal = round(max(0.0, $grossLineSubtotal - $discountShare), 2);
                }

                $vatShare = 0.0;
                if ((float)$totalVatExemption > 0 && $grossTransactionAmount > 0) {
                    $vatShare = round(($grossLineSubtotal / $grossTransactionAmount) * (float)$totalVatExemption, 2);
                    $subtotal = round(max(0.0, $subtotal - $vatShare), 2);
                }

                $actualQtyToDeduct = $item['qty'] * ($item['pcs'] ?? 1); // Use pcs for actual deduction

                $batchAllocations = [];
                if ($actualQtyToDeduct > 0) {
                    $remainingQty = $actualQtyToDeduct;
                    $batchStmt = $this->conn->prepare(
                        "SELECT id, current_quantity, expiry_date, purchase_cost
                         FROM inventory
                                                 WHERE product_id = ?
                                                     AND current_quantity > 0
                                                     AND (expiry_date IS NULL OR expiry_date >= CURDATE())
                         ORDER BY expiry_date ASC, id ASC FOR UPDATE"
                    );
                    $batchStmt->execute([$item['id']]);
                    $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

                    $availableQty = 0;
                    foreach ($batches as $batch) {
                        $availableQty += (int)($batch['current_quantity'] ?? 0);
                    }

                    if ($availableQty < $remainingQty) {
                        throw new Exception('Insufficient stock for product ID ' . $item['id']);
                    }

                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) {
                            break;
                        }

                        $batchAvailable = (int)($batch['current_quantity'] ?? 0);
                        if ($batchAvailable <= 0) {
                            continue;
                        }

                        $deductQty = min($remainingQty, $batchAvailable);
                        $updateStmt = $this->conn->prepare(
                            "UPDATE inventory SET current_quantity = current_quantity - ? WHERE id = ? AND current_quantity >= ?"
                        );
                        $updateStmt->execute([$deductQty, (int)$batch['id'], $deductQty]);

                        if ($updateStmt->rowCount() < 1) {
                            throw new Exception('Failed to deduct stock from inventory batch ' . $batch['id']);
                        }

                        $batchAllocations[] = [
                            'inventory_id' => (int)$batch['id'],
                            'quantity' => $deductQty,
                            'purchase_cost' => (float)($batch['purchase_cost'] ?? 0),
                        ];
                        $remainingQty -= $deductQty;
                    }

                    if ($remainingQty > 0) {
                        throw new Exception('Stock deduction did not complete for product ID ' . $item['id']);
                    }
                }

                $batchId = count($batchAllocations) === 1 ? $batchAllocations[0]['inventory_id'] : null;
                $stmt = $this->conn->prepare("INSERT INTO transaction_items (transaction_id, product_id, batch_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$transactionId, $item['id'], $batchId, $item['qty'], $item['price'], $subtotal]);
                $transactionItemId = (int)$this->conn->lastInsertId();

                if ($batchAllocations) {
                    $allocationStmt = $this->conn->prepare(
                        "INSERT INTO transaction_item_batches (transaction_item_id, inventory_id, quantity, purchase_cost) VALUES (?, ?, ?, ?)"
                    );
                    foreach ($batchAllocations as $allocation) {
                        $allocationStmt->execute([
                            $transactionItemId,
                            $allocation['inventory_id'],
                            $allocation['quantity'],
                            $allocation['purchase_cost'],
                        ]);
                    }
                }
            }

            $this->conn->commit();
            return ['success' => true, 'transaction_id' => $transactionId, 'total' => $totalAmount];
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log('processTransaction failed: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()];
        }
    }
}

// API ENDPOINT: Get products for inventory refresh
if (isset($_GET['action']) && $_GET['action'] === 'getProducts') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../conn/database.php';

    header('Content-Type: application/json');

    // SECURITY: product catalog (prices, stock, cost) requires a login
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    try {
        $product = new Product($db);
        $products = $product->getProducts();
        // Strip purchase cost (net_price) — internal margin data must not
        // be exposed to the browser; the POS UI does not use it.
        foreach ($products as &$p) {
            unset($p['net_price']);
        }
        unset($p);
        echo json_encode($products);
    } catch (Exception $e) {
        error_log('getProducts endpoint error: ' . $e->getMessage());
        echo json_encode(['error' => 'Failed to load products']);
    }
    exit;
}