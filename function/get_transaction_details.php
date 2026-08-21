<?php
/**
 * API: Get Transaction Details for Processing Return
 * GET query: ?id=12
 * Returns: { success: true, transaction: {...}, items: [...] } or { success: false, error }
 */

error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

$txId = (int)($_GET['id'] ?? 0);

if ($txId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid Transaction Ref # / ID.']);
    exit;
}

try {
    // 1. Fetch transaction header
    $stmtTx = $db->prepare("
        SELECT t.id, t.customer_name, t.total_amount, t.created_at, u.username AS cashier_name
        FROM transactions t
        LEFT JOIN users u ON t.user_id = u.id
        WHERE t.id = ?
    ");
    $stmtTx->execute([$txId]);
    $transaction = $stmtTx->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        echo json_encode(['success' => false, 'error' => 'Transaction #' . $txId . ' not found.']);
        exit;
    }

    // 2. Fetch transaction items with product names
    $stmtItems = $db->prepare("
        SELECT 
            ti.product_id,
            ti.quantity,
            ti.price,
            ti.subtotal,
            COALESCE(p.branded_name, p.generic_name, 'Product #' || p.id) AS raw_name,
            p.generic_name,
            p.branded_name
        FROM transaction_items ti
        LEFT JOIN products p ON ti.product_id = p.id
        WHERE ti.transaction_id = ?
    ");
    $stmtItems->execute([$txId]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch already returned quantities for this transaction
    $stmtReturns = $db->prepare("
        SELECT ri.product_id, COALESCE(SUM(ri.quantity), 0) AS returned_qty
        FROM return_transactions rt
        JOIN return_items ri ON rt.id = ri.return_transaction_id
        WHERE rt.original_transaction_id = ?
        GROUP BY ri.product_id
    ");
    $stmtReturns->execute([$txId]);
    $alreadyReturned = [];
    while ($r = $stmtReturns->fetch(PDO::FETCH_ASSOC)) {
        $alreadyReturned[(int)$r['product_id']] = (int)$r['returned_qty'];
    }

    foreach ($items as &$item) {
        $pid = (int)$item['product_id'];
        $branded = trim($item['branded_name'] ?? '');
        $generic = trim($item['generic_name'] ?? '');
        
        if ($branded !== '' && $generic !== '') {
            $item['product_name'] = $branded . ' (' . $generic . ')';
        } else if ($generic !== '') {
            $item['product_name'] = $generic;
        } else if ($branded !== '') {
            $item['product_name'] = $branded;
        } else {
            $item['product_name'] = 'Product #' . $pid;
        }

        $lineSubtotal = (float)($item['subtotal'] ?? 0);
        if ($lineSubtotal > 0) {
            $item['price'] = round($lineSubtotal / max(1, (int)$item['quantity']), 2);
        }

        $item['already_returned'] = $alreadyReturned[$pid] ?? 0;
        $item['available_for_return'] = max(0, (int)$item['quantity'] - $item['already_returned']);
    }

    echo json_encode([
        'success'     => true,
        'transaction' => $transaction,
        'items'       => $items
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
