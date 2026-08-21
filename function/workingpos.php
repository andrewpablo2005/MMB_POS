<?php

class Product {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ GET PRODUCTS with category name, stock, barcode and category-level discount flags
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

    // ✅ GET ALL CATEGORIES for filter buttons
    public function getCategories() {
        $sql = "SELECT * FROM product_categories ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ GET DISCOUNTS
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

    // ✅ PROCESS TRANSACTION
    public function processTransaction($userId, $cartItems, $discountId, $customerName, $customerId, $discountTotal = 0, $totalVatExemption = 0, $customerType = null, $discountRule = 'regular') {
        try {
            $this->conn->beginTransaction();

            // Calculate totals
            $grossTransactionAmount = 0;
            foreach ($cartItems as $item) {
                $grossTransactionAmount += (float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0); // qty here is 'packs'
            }

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
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// ✅ API ENDPOINT: Get products for inventory refresh
if (isset($_GET['action']) && $_GET['action'] === 'getProducts') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    require_once __DIR__ . '/../conn/database.php';
    
    header('Content-Type: application/json');
    
    try {
        $product = new Product($db);
        $products = $product->getProducts();
        echo json_encode($products);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}