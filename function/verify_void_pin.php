<?php
/**
 * API: Verify Void PIN for cart item removal authorization
 * POST body: { void_pin: string }
 * Returns: { success: true, approver_name } or { error: string }
 *
 * SECURITY:
 *  - Requires an authenticated session (cashier must be logged in).
 *  - Rate limited: max 5 failed attempts per session, then a 5-minute lockout.
 *  - PINs are stored hashed (bcrypt). Legacy plaintext PINs are still
 *    verified during migration, but never accepted for writing.
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

// 🔐 Must be a logged-in cashier/staff member to even attempt this
if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

// 🔐 Simple per-session throttle: 5 failures → 5 minute lockout
$now = time();
if (isset($_SESSION['void_attempts'], $_SESSION['void_last_attempt'])) {
    if ($_SESSION['void_attempts'] >= 5 && ($now - (int)$_SESSION['void_last_attempt']) < 300) {
        echo json_encode(['error' => 'Too many failed attempts. Try again in a few minutes.']);
        exit;
    }
    if (($now - (int)$_SESSION['void_last_attempt']) >= 300) {
        $_SESSION['void_attempts'] = 0;
    }
}

$body    = json_decode(file_get_contents('php://input'), true);
$pin     = trim((string)($body['void_pin'] ?? ''));

if (!$pin) {
    echo json_encode(['error' => 'Void PIN is required']);
    exit;
}

if (!preg_match('/^\d{7}$/', $pin)) {
    echo json_encode(['error' => 'Void PIN must be 7 digits']);
    exit;
}

function recordVoidFailure(): void
{
    $_SESSION['void_attempts'] = ($_SESSION['void_attempts'] ?? 0) + 1;
    $_SESSION['void_last_attempt'] = time();
}

try {
    // All Owner/Admin accounts are candidates (PIN is per-account now,
    // but any manager PIN authorizes a void, matching POS conventions).
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.position, u.void_password, ui.firstname, ui.lastname
        FROM users u
        LEFT JOIN users_info ui ON u.id = ui.user_id
        WHERE u.position IN ('Owner', 'Admin')
          AND u.status = 'active'
    ");
    $stmt->execute();
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $matched = null;
    foreach ($managers as $manager) {
        $stored = (string)($manager['void_password'] ?? '');
        if ($stored === '') {
            continue;
        }
        // Hashed PINs (modern) — bcrypt/argon2 hashes start with $2y$/$2a$/$argon
        if (preg_match('/^\$2[ay]?\$|^\$argon2/i', $stored)) {
            if (password_verify($pin, $stored)) {
                $matched = $manager;
                break;
            }
            continue;
        }
        // Legacy plaintext fallback (only for old rows not yet re-hashed)
        if (hash_equals($stored, $pin)) {
            $matched = $manager;
            break;
        }
    }

    if (!$matched) {
        recordVoidFailure();
        echo json_encode(['error' => 'Invalid Void PIN. Access denied.']);
        exit;
    }

    // Success — reset throttle
    unset($_SESSION['void_attempts'], $_SESSION['void_last_attempt']);

    $fullName = trim(($matched['firstname'] ?? '') . ' ' . ($matched['lastname'] ?? '')) ?: $matched['username'];

    echo json_encode([
        'success'       => true,
        'approver_id'   => $matched['id'],
        'approver_name' => $fullName,
        'position'      => $matched['position'],
    ]);

} catch (PDOException $e) {
    error_log('verify_void_pin error: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
