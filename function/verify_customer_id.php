<?php
/**
 * API: Verify Customer ID for Senior/PWD Discount
 * POST body: { type: 'senior'|'pwd', name: string, id_number: string }
 * Returns:
 * { exists: true } -> customer already on record, just apply discount
 * { exists: false } -> new customer — cashier must complete the in-app
 * physical-ID inspection checklist, then save via
 * save_customer_id.php
 * { error: string } -> validation error
 *
 * SECURITY: requires an authenticated session (cashier must be logged in).
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

// Only a logged-in cashier may verify customers
if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body       = json_decode(file_get_contents('php://input'), true);
$type       = strtolower(trim((string)($body['type'] ?? '')));
$name       = trim((string)($body['name'] ?? ''));
$id_number  = trim((string)($body['id_number'] ?? ''));

// Validate
if (!in_array($type, ['senior', 'pwd'], true)) {
    echo json_encode(['error' => 'Invalid type. Must be senior or pwd.']);
    exit;
}
if ($name === '' || mb_strlen($name) > 100) {
    echo json_encode(['error' => 'Customer name is required (max 100 characters).']);
    exit;
}
if ($id_number === '' || mb_strlen($id_number) > 50) {
    echo json_encode(['error' => 'ID number is required (max 50 characters).']);
    exit;
}

try {
    // Check if already exists in the verified registry (literal table names —
    // never interpolate identifiers)
    $sql = $type === 'senior'
        ? 'SELECT id FROM senior_customers WHERE id_number = ?'
        : 'SELECT id FROM pwd_customers WHERE id_number = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_number]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Already verified before — no external lookup needed
        echo json_encode(['exists' => true, 'customer_id' => (int)$existing['id']]);
        exit;
    }

    // New customer — cashier performs the IN-APP physical ID inspection
    // (the official NCSC/DOH online checkers remain available as an optional
    // helper, but the required step is inspecting the physical ID card)
    echo json_encode(['exists' => false]);

} catch (PDOException $e) {
    error_log('verify_customer_id error: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
