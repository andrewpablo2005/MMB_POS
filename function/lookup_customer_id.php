<?php
/**
 * API: Lookup Customer by ID Number (Senior/PWD registry)
 * POST body: { type: 'senior'|'pwd', id_number: string }
 * Returns:
 * { found: true,  name: 'JUAN DELA CRUZ', customer_id: 5 } -> on record
 * { found: false }                                        -> not on file (new customer)
 * { error: string }                                       -> validation error
 *
 * Used by the POS verify modal to AUTO-FILL the customer name as soon as
 * the cashier finishes typing/scanning the ID number.
 *
 * SECURITY: requires an authenticated session (cashier must be logged in).
 * Names are returned escaped-free (plain text) — the client escapes them
 * before rendering (XSS guard in pos_wepos.js).
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

// Only a logged-in cashier may look up customers
if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body      = json_decode(file_get_contents('php://input'), true);
$type      = strtolower(trim((string)($body['type'] ?? '')));
$id_number = trim((string)($body['id_number'] ?? ''));

if (!in_array($type, ['senior', 'pwd'], true)) {
    echo json_encode(['error' => 'Invalid type. Must be senior or pwd.']);
    exit;
}
// Minimum length guards the auto-complete from firing on every keystroke;
// maximum length matches the column size in senior_customers / pwd_customers.
if ($id_number === '' || mb_strlen($id_number) < 3 || mb_strlen($id_number) > 50) {
    echo json_encode(['found' => false]);
    exit;
}

try {
    // Literal table names — never interpolate identifiers
    $sql = $type === 'senior'
        ? 'SELECT id, customer_name FROM senior_customers WHERE id_number = ?'
        : 'SELECT id, customer_name FROM pwd_customers WHERE id_number = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$id_number]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode([
            'found'       => true,
            'name'        => (string)$existing['customer_name'],
            'customer_id' => (int)$existing['id'],
        ]);
        exit;
    }

    echo json_encode(['found' => false]);

} catch (PDOException $e) {
    error_log('lookup_customer_id error: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
