<?php
/**
 * API: Save Override Log Entry
 * POST body: { transaction_id, product_id, generic_name, original_price, discounted_price, discount_percent, reason, approver_id, approver_name }
 */
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);

$cashierId   = $_SESSION['user_id'];
$cashierName = $_SESSION['username'];
$data        = [
    'transaction_id'   => $body['transaction_id'] ?? null,
    'product_id'       => (int)($body['product_id'] ?? 0),
    'generic_name'     => $body['generic_name'] ?? '',
    'cashier_id'       => $cashierId,
    'cashier_name'     => $cashierName,
    'approver_id'      => (int)($body['approver_id'] ?? 0),
    'approver_name'    => $body['approver_name'] ?? '',
    'original_price'   => (float)($body['original_price'] ?? 0),
    'discounted_price' => (float)($body['discounted_price'] ?? 0),
    'discount_amount'  => (float)($body['discount_amount'] ?? 0),
    'discount_percent' => (float)($body['discount_percent'] ?? 0),
    'reason'           => $body['reason'] ?? '',
];

if (!$data['product_id'] || !$data['approver_id'] || !$data['reason']) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO override_log 
        (transaction_id, product_id, generic_name, cashier_id, cashier_name, approver_id, approver_name, 
         original_price, discounted_price, discount_amount, discount_percent, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['transaction_id'],
        $data['product_id'],
        $data['generic_name'],
        $data['cashier_id'],
        $data['cashier_name'],
        $data['approver_id'],
        $data['approver_name'],
        $data['original_price'],
        $data['discounted_price'],
        $data['discount_amount'],
        $data['discount_percent'],
        $data['reason'],
    ]);

    echo json_encode(['success' => true, 'log_id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to log override: ' . $e->getMessage()]);
}
