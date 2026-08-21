<?php
/**
 * API: Verify Customer ID for Senior/PWD Discount
 * POST body: { type: 'senior'|'pwd', name: string, id_number: string, cashier_id: int }
 * Returns:
 *   { exists: true }  → customer already on record, just apply discount
 *   { exists: false } → new customer, saved to DB, open verification site on client
 *   { error: string } → validation error
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

// Validate
if (!in_array($type, ['senior', 'pwd'])) {
    echo json_encode(['error' => 'Invalid type. Must be senior or pwd.']);
    exit;
}
if (!$name) {
    echo json_encode(['error' => 'Customer name is required.']);
    exit;
}
if (!$id_number) {
    echo json_encode(['error' => 'ID number is required.']);
    exit;
}

$table = $type === 'senior' ? 'senior_customers' : 'pwd_customers';

try {
    // Check if already exists
    $stmt = $db->prepare("SELECT id FROM `$table` WHERE id_number = ?");
    $stmt->execute([$id_number]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Already on record — no need to redirect
        echo json_encode(['exists' => true, 'customer_id' => $existing['id']]);
        exit;
    }

    // New customer — signal client to open verification site and show manual buttons
    echo json_encode(['exists' => false]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
