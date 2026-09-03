<?php
/**
 * API: Process POS Transaction
 * POST body: { items: [{id, price, qty}], discount_id, customer_name }
 * Returns: { success, transaction_id, total } or { success: false, error }
 */

// Suppress any output-breaking warnings
error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Both files are siblings in the same function/ folder
require_once __DIR__ . '/../conn/database.php';
require_once __DIR__ . '/workingpos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body       = json_decode(file_get_contents('php://input'), true);
$items      = $body['items']       ?? [];
$discountId = $body['discount_id'] ?? null;
$customerName = trim($body['customer_name'] ?? 'Walk-in');
$customerId = (int)($body['customer_id'] ?? 0) ?: null;
$customerType = trim($body['customer_type'] ?? null);
$discountTotal = (float)($body['discount_total'] ?? 0);
$totalVatExemption = (float)($body['total_vat_exemption'] ?? 0);
$discountRule = strtolower(trim((string)($body['discount_rule'] ?? '')));

if (empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit;
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS register_openings (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        business_date DATE NOT NULL,
        opening_cash DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        notes VARCHAR(255) DEFAULT NULL,
        opened_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_register_opening_user_date (user_id, business_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    $openingCheck = $db->prepare("SELECT id FROM register_openings WHERE user_id = ? AND business_date = ?");
    $openingCheck->execute([(int)$_SESSION['user_id'], date('Y-m-d')]);
    if (!$openingCheck->fetchColumn()) {
        echo json_encode(['success' => false, 'error' => 'Opening cash is required before processing a POS sale.']);
        exit;
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Unable to verify the POS register opening.']);
    exit;
}

// Convert discountId to proper int/null
$discountId = (!empty($discountId) && $discountId != '0') ? (int)$discountId : null;

try {
    $product = new Product($db);
    $result  = $product->processTransaction(
        (int)$_SESSION['user_id'],
        $items,
        $discountId,
        $customerName,
        $customerId,
        $discountTotal,
        $totalVatExemption,
        $customerType,
        $discountRule
    );
    echo json_encode($result);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
