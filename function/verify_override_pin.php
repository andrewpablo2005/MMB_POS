<?php
/**
 * API: Verify manager authentication for a per-item POS discount override.
 * POST body: {
 *   username: string,
 *   password: string,
 *   product_id: int,
 *   product_name: string,
 *   original_price: number,
 *   discount_percent: number,
 *   reason: string
 * }
 * Returns: { success: true, approver_name } or { success: false, error }
 */

error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated. Please log in again.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$voidPin = trim((string)($body['void_pin'] ?? ''));
$username = trim((string)($body['username'] ?? ''));
$password = (string)($body['password'] ?? '');
$productId = (int)($body['product_id'] ?? 0);
$productName = trim((string)($body['product_name'] ?? ''));
$discountPercent = (float)($body['discount_percent'] ?? 0);
$reason = trim((string)($body['reason'] ?? ''));
$originalPrice = (float)($body['original_price'] ?? 0);

if ($voidPin === '' && ($username === '' || $password === '')) {
    echo json_encode(['success' => false, 'error' => 'Manager Void PIN is required.']);
    exit;
}

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product.']);
    exit;
}

if (!is_numeric($discountPercent) || $discountPercent <= 0 || $discountPercent >= 100) {
    echo json_encode(['success' => false, 'error' => 'Override percentage must be between 0 and 100.']);
    exit;
}

if ($reason === '') {
    echo json_encode(['success' => false, 'error' => 'A reason is required for the override.']);
    exit;
}

try {
    $manager = null;
    if ($voidPin !== '') {
        $stmt = $db->prepare(
            "SELECT u.id, u.username, u.position, u.void_password, ui.firstname, ui.lastname
             FROM users u
             LEFT JOIN users_info ui ON u.id = ui.user_id
             WHERE u.position IN ('Owner', 'Admin') AND u.status = 'active'"
        );
        $stmt->execute();
        $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($managers as $candidate) {
            $stored = (string)($candidate['void_password'] ?? '');
            if ($stored === '') {
                continue;
            }
            if (preg_match('/^\$2[ay]?\$|^\$argon2/i', $stored)) {
                if (password_verify($voidPin, $stored)) {
                    $manager = $candidate;
                    break;
                }
                continue;
            }
            if (hash_equals($stored, $voidPin)) {
                $manager = $candidate;
                break;
            }
        }
    } else {
        $stmt = $db->prepare(
            "SELECT u.id, u.username, u.position, u.password, ui.firstname, ui.lastname
             FROM users u
             LEFT JOIN users_info ui ON u.id = ui.user_id
             WHERE u.username = ? AND u.status = 'active' AND u.position IN ('Owner', 'Admin')"
        );
        $stmt->execute([$username]);
        $manager = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$manager || !password_verify($password, (string)($manager['password'] ?? ''))) {
            echo json_encode(['success' => false, 'error' => 'Invalid manager username or password.']);
            exit;
        }
    }

    if (!$manager) {
        echo json_encode(['success' => false, 'error' => 'Invalid Manager Void PIN. Access denied.']);
        exit;
    }

    $discountedPrice = $originalPrice * (1 - ($discountPercent / 100));
    $approverName = trim((($manager['firstname'] ?? '') . ' ' . ($manager['lastname'] ?? '')));
    if ($approverName === '') {
        $approverName = (string)($manager['username'] ?? 'Manager');
    }

    $insert = $db->prepare(
        "INSERT INTO override_log
         (transaction_id, product_id, product_name, cashier_id, cashier_name, approver_id, approver_name,
          original_price, discounted_price, discount_amount, discount_percent, reason, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $cashierId = (int)($_SESSION['user_id'] ?? 0);
    $cashierName = trim((string)($_SESSION['username'] ?? ''));
    $cashierName = $cashierName !== '' ? $cashierName : 'Cashier';
    $discountAmount = max(0.0, $originalPrice - $discountedPrice);

    $insert->execute([
        null,
        $productId,
        $productName !== '' ? $productName : 'Unknown product',
        $cashierId,
        $cashierName,
        (int)($manager['id'] ?? 0),
        $approverName,
        round($originalPrice, 2),
        round($discountedPrice, 2),
        round($discountAmount, 2),
        round($discountPercent, 2),
        $reason,
    ]);

    echo json_encode([
        'success' => true,
        'approver_name' => $approverName,
        'discount_percent' => round($discountPercent, 2),
        'discounted_price' => round($discountedPrice, 2),
    ]);
} catch (Throwable $e) {
    error_log('verify_override_pin error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error while validating override authorization.']);
}
