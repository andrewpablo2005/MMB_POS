<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = (int)($body['product_id'] ?? 0);
$approverId = (int)($body['approver_id'] ?? 0);
$reason = trim((string)($body['reason'] ?? ''));
$percent = max(0, min(100, (float)($body['discount_percent'] ?? 0)));
if ($productId <= 0 || $approverId <= 0 || $reason === '' || $percent <= 0) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $approver = $db->prepare("SELECT id FROM users WHERE id = ? AND status = 'active' AND LOWER(position) IN ('owner', 'admin')");
    $approver->execute([$approverId]);
    if (!$approver->fetchColumn()) {
        echo json_encode(['error' => 'Invalid approver']);
        exit;
    }
    $stmt = $db->prepare("INSERT INTO override_log
        (transaction_id, product_id, product_name, cashier_id, cashier_name, approver_id, approver_name,
         original_price, discounted_price, discount_amount, discount_percent, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $original = (float)($body['original_price'] ?? 0);
    $discounted = (float)($body['discounted_price'] ?? 0);
    $stmt->execute([
        $body['transaction_id'] ?? null, $productId, trim((string)($body['generic_name'] ?? '')),
        (int)$_SESSION['user_id'], (string)($_SESSION['username'] ?? ''), $approverId,
        trim((string)($body['approver_name'] ?? '')), $original, $discounted,
        (float)($body['discount_amount'] ?? ($original - $discounted)), $percent, $reason
    ]);
    echo json_encode(['success' => true, 'log_id' => $db->lastInsertId()]);
} catch (Throwable $e) {
    error_log('log_override error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to log override']);
}
