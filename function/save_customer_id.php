<?php
/**
 * API: Save Customer ID for Senior/PWD Discount (first-time verification)
 * POST body: { type: 'senior'|'pwd', name: string, id_number: string }
 *
 * The cashier has completed the in-app physical-ID inspection checklist.
 * The record stores WHO verified the customer and WHEN, so every discount
 * is attributable to a cashier (audit trail).
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

// Only a logged-in cashier may register a verified customer
if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body       = json_decode(file_get_contents('php://input'), true);
$type       = strtolower(trim((string)($body['type'] ?? '')));
$name       = trim((string)($body['name'] ?? ''));
$id_number  = trim((string)($body['id_number'] ?? ''));
$cashier_id = (int)($_SESSION['user_id'] ?? 0);

if (!in_array($type, ['senior', 'pwd'], true)) {
    echo json_encode(['error' => 'Invalid type.']);
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

// ── ISSUE #9: strict format validation (mirrors pos_wepos.js) ──
// Name: letters only (A-Z, Ñ), spaces, apostrophes, hyphens, periods —
// at least two words (First + Last). Middle name NOT required.
$name = mb_strtoupper($name);
if (preg_match('/[^A-Za-zÑñ .\'-]/u', $name) || count(preg_split('/\s+/', trim($name))) < 2) {
    echo json_encode(['error' => 'Name must be letters only, as printed on the ID (at least first and last name). Numbers are not allowed.']);
    exit;
}

// ID: Senior = 10-12 digits (hyphens/spaces allowed); PWD = optional
// "PWD-" prefix + 7-12 digits (DOH PWD-YYYY-NNNNNNN or LGU numeric).
$id_number = strtoupper(preg_replace('/\s+/', ' ', $id_number));
if ($type === 'pwd') {
    if (!preg_match('/^(?:PWD[- ]?)?\d{7,12}$/', $id_number)) {
        echo json_encode(['error' => 'PWD ID format: PWD-YYYY-NNNNNNN or 7-12 digits. Letters are not accepted.']);
        exit;
    }
} else {
    $digitsOnly = preg_replace('/[\s-]/', '', $id_number);
    if (!preg_match('/^\d{10,12}$/', $digitsOnly)) {
        echo json_encode(['error' => 'Senior ID: 10-12 digits (PhilSys / OSCA / UMID). Letters are not accepted.']);
        exit;
    }
}

try {
    // Idempotent: if this ID number is already verified, return the existing row
    $selectSql = $type === 'senior'
        ? 'SELECT id FROM senior_customers WHERE id_number = ?'
        : 'SELECT id FROM pwd_customers WHERE id_number = ?';
    $stmt = $db->prepare($selectSql);
    $stmt->execute([$id_number]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        echo json_encode(['success' => true, 'customer_id' => (int)$existing['id'], 'already_exists' => true]);
        exit;
    }

    // Audit columns (guarded one-time migration for databases created
    // before these columns existed)
    foreach (['senior_customers', 'pwd_customers'] as $table) {
        try {
            $db->exec("ALTER TABLE `$table` ADD COLUMN verified_by INT NULL DEFAULT NULL, ADD COLUMN verified_at DATETIME NULL DEFAULT NULL");
        } catch (PDOException $ignore) {
            // columns already exist
        }
    }

    $insertSql = $type === 'senior'
        ? 'INSERT INTO senior_customers (customer_name, id_number, cashier_id, verified_by, verified_at) VALUES (?, ?, ?, ?, NOW())'
        : 'INSERT INTO pwd_customers (customer_name, id_number, cashier_id, verified_by, verified_at) VALUES (?, ?, ?, ?, NOW())';
    $stmt = $db->prepare($insertSql);
    $stmt->execute([$name, $id_number, $cashier_id, $cashier_id]);
    $customerId = (int)$db->lastInsertId();

    echo json_encode(['success' => true, 'customer_id' => $customerId]);
} catch (PDOException $e) {
    error_log('save_customer_id error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to save customer. Please try again.']);
}
