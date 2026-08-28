<?php
/**
 * API: Process Product Return / Refund
 * POST body: { original_transaction_id, void_pin, refund_method, reason, items: [{ product_id, qty, price, is_restockable }] }
 * Returns: { success: true, return_id, refund_total } or { success: false, error }
 */

error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

$transactionId = (int)($body['original_transaction_id'] ?? 0);
$voidPin = trim((string)($body['void_pin'] ?? ''));
$refundMethod = trim((string)($body['refund_method'] ?? ''));
$reason = trim((string)($body['reason'] ?? ''));
$items = $body['items'] ?? [];

if ($transactionId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Please provide a valid original transaction ID.']);
    exit;
}

if (!$refundMethod) {
    echo json_encode(['success' => false, 'error' => 'Please select a refund method.']);
    exit;
}

if (!$reason) {
    echo json_encode(['success' => false, 'error' => 'Please select a return reason.']);
    exit;
}

if (!is_array($items) || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Please select at least one item to return.']);
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$currentPosition = strtolower(trim((string)($_SESSION['position'] ?? '')));

if (!$voidPin) {
    echo json_encode(['success' => false, 'error' => 'Please enter Manager or Owner Void PIN.']);
    exit;
}

if (!preg_match('/^[0-9]{7}$/', $voidPin)) {
    echo json_encode(['success' => false, 'error' => 'Void PIN must be 7 digits.']);
    exit;
}

// Verify the manager Void PIN against hashed (and legacy plaintext) PINs
$managerStmt = $db->prepare("
    SELECT u.id, u.void_password
    FROM users u
    WHERE u.position IN ('Owner', 'Admin') AND u.status = 'active'
");
$managerStmt->execute();
$approver = null;
foreach ($managerStmt->fetchAll(PDO::FETCH_ASSOC) as $manager) {
    $stored = (string)($manager['void_password'] ?? '');
    if ($stored === '') {
        continue;
    }
    if (preg_match('/^\$2[ay]?\$|^\$argon2/i', $stored)) {
        if (password_verify($voidPin, $stored)) {
            $approver = $manager;
            break;
        }
    } elseif (hash_equals($stored, $voidPin)) {
        $approver = $manager;
        break;
    }
}

if (!$approver) {
    echo json_encode(['success' => false, 'error' => 'Invalid Void PIN. Access denied.']);
    exit;
}

try {
    $db->beginTransaction();

    // Lock the original transaction row so two concurrent return requests
    // for the same transaction cannot both pass the already-returned check
    // (prevents double refunds / double restock).
    $stmtTx = $db->prepare("SELECT id FROM transactions WHERE id = ? FOR UPDATE");
    $stmtTx->execute([$transactionId]);
    $transaction = $stmtTx->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => 'Original transaction not found.']);
        exit;
    }

    $stmtItems = $db->prepare("SELECT ti.product_id, ti.quantity, ti.price, ti.subtotal, COALESCE(p.units_per_package, 1) AS pcs
        FROM transaction_items ti
        LEFT JOIN products p ON ti.product_id = p.id
        WHERE ti.transaction_id = ?");
    $stmtItems->execute([$transactionId]);
    $originalItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    if (!$originalItems) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => 'No items found for the original transaction.']);
        exit;
    }

    $originalItemMap = [];
    foreach ($originalItems as $item) {
        $lineSubtotal = (float)($item['subtotal'] ?? 0);
        $unitPrice = $lineSubtotal > 0
            ? round($lineSubtotal / max(1, (int)$item['quantity']), 2)
            : (float)$item['price'];

        $originalItemMap[(int)$item['product_id']] = [
            'quantity' => (int)$item['quantity'],
            'price' => $unitPrice,
            'pcs' => max(1, (int)$item['pcs']),
        ];
    }

    $stmtReturned = $db->prepare("SELECT ri.product_id, COALESCE(SUM(ri.quantity), 0) AS returned_qty
        FROM return_transactions rt
        JOIN return_items ri ON rt.id = ri.return_transaction_id
        WHERE rt.original_transaction_id = ?
        GROUP BY ri.product_id");
    $stmtReturned->execute([$transactionId]);

    $alreadyReturned = [];
    while ($row = $stmtReturned->fetch(PDO::FETCH_ASSOC)) {
        $alreadyReturned[(int)$row['product_id']] = (int)$row['returned_qty'];
    }

    $selectedItems = [];
    $refundTotal = 0.0;

    foreach ($items as $item) {
        $productId = (int)($item['product_id'] ?? 0);
        $qty = (int)($item['qty'] ?? 0);
        $price = (float)($item['price'] ?? 0);
        $isRestockable = !empty($item['is_restockable']) ? 1 : 0;

        if ($productId <= 0 || $qty <= 0) {
            continue;
        }

        $original = $originalItemMap[$productId] ?? null;
        if (!$original) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Selected item does not belong to the original transaction.']);
            exit;
        }

        if (abs($price - (float)$original['price']) >= 0.011) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Return price mismatch for one or more items.']);
            exit;
        }
        $price = (float)$original['price']; // server price is authoritative

        $returnedQty = $alreadyReturned[$productId] ?? 0;
        $availableQty = max(0, $original['quantity'] - $returnedQty);

        if ($qty > $availableQty) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Return quantity exceeds available quantity for product ID ' . $productId . '.']);
            exit;
        }

        $selectedItems[] = [
            'product_id' => $productId,
            'qty' => $qty,
            'price' => $price,
            'subtotal' => round($price * $qty, 2),
            'is_restockable' => $isRestockable,
            'pcs' => $original['pcs'],
        ];

        $refundTotal += $price * $qty;
    }

    if (empty($selectedItems)) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => 'Please select at least one valid item to return.']);
        exit;
    }

    $refundTotal = round($refundTotal, 2);

    // Attribute the refund to the manager who authorized it (audit trail)
    try {
        $db->exec("ALTER TABLE return_transactions ADD COLUMN approver_id INT NULL DEFAULT NULL");
    } catch (PDOException $ignore) {
        // column already exists
    }

    $stmtInsertReturn = $db->prepare("INSERT INTO return_transactions (original_transaction_id, user_id, refund_amount, reason, refund_method, approver_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmtInsertReturn->execute([
        $transactionId,
        $currentUserId,
        $refundTotal,
        $reason,
        $refundMethod,
        (int)$approver['id'],
    ]);

    $returnId = (int)$db->lastInsertId();

    $stmtInsertItem = $db->prepare("INSERT INTO return_items (return_transaction_id, product_id, quantity, price, subtotal, item_type, restocked)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Restock into the batch the goods were originally deducted from
    // (recorded in transaction_item_batches), so FEFO/expiry integrity is
    // preserved. Falls back to the earliest unexpired batch; if the product
    // has no batch at all, the restock fails loudly instead of vanishing.
    $stmtRestockOriginal = $db->prepare("
        UPDATE inventory i
        JOIN (
            SELECT tib.inventory_id AS id
            FROM transaction_items ti
            JOIN transaction_item_batches tib ON tib.transaction_item_id = ti.id
            WHERE ti.transaction_id = ? AND ti.product_id = ?
            ORDER BY tib.id ASC
            LIMIT 1
        ) sel ON i.id = sel.id
        SET i.current_quantity = i.current_quantity + ?
    ");
    $stmtRestockFallback = $db->prepare("
        UPDATE inventory i
        JOIN (
            SELECT id FROM inventory
            WHERE product_id = ? AND (expiry_date IS NULL OR expiry_date >= CURDATE())
            ORDER BY expiry_date ASC, id ASC
            LIMIT 1
        ) sel ON i.id = sel.id
        SET i.current_quantity = i.current_quantity + ?
    ");
    $stmtHasBatch = $db->prepare("SELECT COUNT(*) AS c FROM inventory WHERE product_id = ?");

    foreach ($selectedItems as $item) {
        $stmtInsertItem->execute([
            $returnId,
            $item['product_id'],
            $item['qty'],
            $item['price'],
            $item['subtotal'],
            'returned',
            $item['is_restockable'],
        ]);

        if ($item['is_restockable']) {
            $restoreQty = $item['qty'] * $item['pcs'];

            // Try the original batch first
            $stmtRestockOriginal->execute([$transactionId, $item['product_id'], $restoreQty]);
            if ($stmtRestockOriginal->rowCount() < 1) {
                // Fallback: earliest unexpired batch
                $stmtRestockFallback->execute([$item['product_id'], $restoreQty]);
                if ($stmtRestockFallback->rowCount() < 1) {
                    // No batch exists at all — fail loudly (stock must not vanish)
                    $stmtHasBatch->execute([$item['product_id']]);
                    $hasAny = ((int)($stmtHasBatch->fetch(PDO::FETCH_ASSOC)['c'] ?? 0)) > 0;
                    if (!$hasAny) {
                        throw new RuntimeException(
                            'Cannot restock product ID ' . $item['product_id'] .
                            ': no inventory batch exists for this product. Create a batch first.'
                        );
                    }
                }
            }
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'return_id' => $returnId,
        'refund_total' => $refundTotal,
    ]);
    exit;
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $message = $e->getMessage();
    if (stripos($message, 'return_transactions') !== false || stripos($message, 'return_items') !== false) {
        $message = 'Return system is not configured. Please ensure return_transactions and return_items tables exist.';
    }

    echo json_encode(['success' => false, 'error' => 'Database error: ' . $message]);
    exit;
}
