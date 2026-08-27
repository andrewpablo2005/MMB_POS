<?php
/**
 * API: Verify Manager credentials for Override Approval
 * POST body: { username, password }
 * Returns: { success, approver_id, approver_name } or { error }
 *
 * SECURITY:
 *  - Requires an authenticated session (the cashier must be logged in).
 *  - Password is verified BEFORE the role check so that error messages
 *    cannot be used to enumerate which usernames are Owner/Admin.
 *  - All failures return the SAME generic message.
 *  - Per-session throttling: 5 failures → 5 minute lockout.
 */
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// 🔐 Must be a logged-in user to even attempt this
if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

// 🔐 Simple per-session throttle
$now = time();
if (isset($_SESSION['override_attempts'], $_SESSION['override_last_attempt'])) {
    if ($_SESSION['override_attempts'] >= 5 && ($now - (int)$_SESSION['override_last_attempt']) < 300) {
        echo json_encode(['error' => 'Too many failed attempts. Try again in a few minutes.']);
        exit;
    }
    if (($now - (int)$_SESSION['override_last_attempt']) >= 300) {
        $_SESSION['override_attempts'] = 0;
    }
}

$body = json_decode(file_get_contents('php://input'), true);
$username = trim((string)($body['username'] ?? ''));
$password = (string)($body['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

function recordOverrideFailure(): void
{
    $_SESSION['override_attempts'] = ($_SESSION['override_attempts'] ?? 0) + 1;
    $_SESSION['override_last_attempt'] = time();
}

try {
    // Look up the user
    $stmt = $db->prepare("SELECT id, username, password, position, ui.firstname, ui.lastname FROM users u LEFT JOIN users_info ui ON ui.user_id = u.id WHERE u.username = ? AND u.status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $valid = false;
    if ($user && password_verify($password, $user['password'])) {
        $role = strtolower((string)$user['position']);
        $valid = ($role === 'owner' || $role === 'admin');
    }

    if (!$valid) {
        recordOverrideFailure();
        // Generic message for every failure mode (no user/role enumeration)
        echo json_encode(['error' => 'Invalid credentials or not authorized to approve overrides']);
        exit;
    }

    // Success — reset throttle
    unset($_SESSION['override_attempts'], $_SESSION['override_last_attempt']);

    $fullName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?: $user['username'];

    echo json_encode([
        'success'       => true,
        'approver_id'   => $user['id'],
        'approver_name' => $fullName
    ]);

} catch (PDOException $e) {
    error_log('verify_override_pin error: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
