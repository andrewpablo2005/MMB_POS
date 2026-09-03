<?php
error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to close the register.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?: [];
$userId = (int)$_SESSION['user_id'];
$today = date('Y-m-d');
$businessDate = trim((string)($body['business_date'] ?? $today));
$action = $body['action'] ?? 'preview';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $businessDate)) {
    echo json_encode(['success' => false, 'error' => 'Invalid business date.']);
    exit;
}
if ($businessDate !== $today) {
    echo json_encode(['success' => false, 'error' => 'Register closing is only allowed for the current date.']);
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
        UNIQUE KEY uq_register_opening_user_date (user_id, business_date),
        CONSTRAINT fk_register_opening_user FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

    $openingStmt = $db->prepare("SELECT opening_cash, notes, opened_at FROM register_openings WHERE user_id = ? AND business_date = ?");
    $openingStmt->execute([$userId, $businessDate]);
    $opening = $openingStmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if ($action === 'open') {
        if ($opening) {
            echo json_encode(['success' => false, 'error' => 'The register is already opened for this date.']);
            exit;
        }
        $openingCash = round((float)($body['opening_cash'] ?? -1), 2);
        if ($openingCash <= 0) {
            echo json_encode(['success' => false, 'error' => 'Opening cash must be greater than zero.']);
            exit;
        }
        $insertOpening = $db->prepare("INSERT INTO register_openings (user_id, business_date, opening_cash, notes) VALUES (?, ?, ?, ?)");
        $insertOpening->execute([$userId, $businessDate, $openingCash, trim((string)($body['notes'] ?? '')) ?: null]);
        echo json_encode(['success' => true, 'opened' => true, 'opening_cash' => $openingCash]);
        exit;
    }

    $salesStmt = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) AS sales_total,
                                      COUNT(*) AS transaction_count
                               FROM transactions
                               WHERE user_id = ? AND DATE(created_at) = ?");
    $salesStmt->execute([$userId, $businessDate]);
    $sales = $salesStmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $refundStmt = $db->prepare("SELECT COALESCE(SUM(rt.refund_amount), 0) AS refund_total
                                FROM return_transactions rt
                                JOIN transactions t ON t.id = rt.original_transaction_id
                                WHERE t.user_id = ? AND DATE(rt.created_at) = ?
                                  AND LOWER(rt.refund_method) = 'cash'");
    $refundStmt->execute([$userId, $businessDate]);
    $refundTotal = (float)($refundStmt->fetchColumn() ?: 0);
    $openingCash = (float)($opening['opening_cash'] ?? 0);
    $systemCash = round($openingCash + (float)($sales['sales_total'] ?? 0) - $refundTotal, 2);

    if ($action !== 'preview' && $systemCash <= 0) {
        echo json_encode(['success' => false, 'error' => 'Register cannot be closed because there is no system cash for today.']);
        exit;
    }

    $existingStmt = $db->prepare("SELECT id, system_cash, counted_cash, variance, notes, closed_at
                                  FROM register_closings WHERE user_id = ? AND business_date = ?");
    $existingStmt->execute([$userId, $businessDate]);
    $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

    if ($action === 'preview') {
        echo json_encode([
            'success' => true,
            'business_date' => $businessDate,
            'sales_total' => (float)($sales['sales_total'] ?? 0),
            'refund_total' => $refundTotal,
            'opening_cash' => $openingCash,
            'opening_exists' => (bool)$opening,
            'system_cash' => $systemCash,
            'transaction_count' => (int)($sales['transaction_count'] ?? 0),
            'already_closed' => (bool)$existing,
            'closing' => $existing ?: null,
        ]);
        exit;
    }

    $countedCash = round((float)($body['counted_cash'] ?? -1), 2);
    if ($countedCash < 0) {
        echo json_encode(['success' => false, 'error' => 'Enter the counted cash amount.']);
        exit;
    }
    if ($existing) {
        echo json_encode(['success' => false, 'error' => 'This register is already closed for that date.']);
        exit;
    }

    $variance = round($countedCash - $systemCash, 2);
    $insert = $db->prepare("INSERT INTO register_closings
        (user_id, business_date, system_cash, counted_cash, variance, notes)
        VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$userId, $businessDate, $systemCash, $countedCash, $variance, trim((string)($body['notes'] ?? '')) ?: null]);

    echo json_encode([
        'success' => true,
        'closed' => true,
        'system_cash' => $systemCash,
        'counted_cash' => $countedCash,
        'variance' => $variance,
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Unable to process register closing: ' . $e->getMessage()]);
}