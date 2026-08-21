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

$stmt = $db->prepare("SELECT u.id FROM users u WHERE u.void_password = ? AND u.position IN ('Owner', 'Admin') LIMIT 1");
$stmt->execute([$voidPin]);
$approver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$approver) {
    echo json_encode(['success' => false, 'error' => 'Invalid Void PIN. Access denied.']);
    exit;
}

try {
    $db->beginTransaction();

    $stmtTx = $db->prepare("SELECT id FROM transactions WHERE id = ?");
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

        if ($price !== (float)$original['price']) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Return price mismatch for one or more items.']);
            exit;
        }

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

    $stmtInsertReturn = $db->prepare("INSERT INTO return_transactions (original_transaction_id, user_id, refund_amount, reason, refund_method, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())");
    $stmtInsertReturn->execute([
        $transactionId,
        $currentUserId,
        $refundTotal,
        $reason,
        $refundMethod,
    ]);

    $returnId = (int)$db->lastInsertId();

    $stmtInsertItem = $db->prepare("INSERT INTO return_items (return_transaction_id, product_id, quantity, price, subtotal, item_type, restocked)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtRestock = $db->prepare("UPDATE inventory i
        JOIN (SELECT id FROM inventory WHERE product_id = ? ORDER BY id ASC LIMIT 1) sel ON i.id = sel.id
        SET i.current_quantity = i.current_quantity + ?");

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
            $stmtRestock->execute([$item['product_id'], $restoreQty]);
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
