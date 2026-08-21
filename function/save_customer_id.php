<?php
/**
 * API: Save Customer ID for Senior/PWD Discount
 * POST body: { type: 'senior'|'pwd', name: string, id_number: string, cashier_id: int }
 * Returns: { success: true } or { error: string }
 */

error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$body       = json_decode(file_get_contents('php://input'), true);
$type       = strtolower(trim($body['type'] ?? ''));
$name       = trim($body['name'] ?? '');
$id_number  = trim($body['id_number'] ?? '');
$cashier_id = (int)($_SESSION['user_id'] ?? 0);

if (!in_array($type, ['senior', 'pwd'])) {
    echo json_encode(['error' => 'Invalid type.']);
    exit;
}

$table = $type === 'senior' ? 'senior_customers' : 'pwd_customers';

try {
    $stmt = $db->prepare("INSERT INTO `$table` (customer_name, id_number, cashier_id) VALUES (?, ?, ?)");
    $stmt->execute([$name, $id_number, $cashier_id]);
    $customerId = $db->lastInsertId();

    echo json_encode(['success' => true, 'customer_id' => $customerId]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
