<?php
/**
 * API: Verify Void PIN for cart item removal authorization
 * POST body: { void_pin: string }
 * Returns: { success: true, approver_name } or { error: string }
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

$body    = json_decode(file_get_contents('php://input'), true);
$pin     = trim($body['void_pin'] ?? '');

if (!$pin) {
    echo json_encode(['error' => 'Void PIN is required']);
    exit;
}

if (!preg_match('/^\d{7}$/', $pin)) {
    echo json_encode(['error' => 'Void PIN must be 7 digits']);
    exit;
}

try {
    // Find an Owner or Admin whose void_password matches this PIN
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.position, ui.firstname, ui.lastname
        FROM users u
        LEFT JOIN users_info ui ON u.id = ui.user_id
        WHERE u.void_password = ?
          AND u.position IN ('Owner', 'Admin')
        LIMIT 1
    ");
    $stmt->execute([$pin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Invalid Void PIN. Access denied.']);
        exit;
    }

    $fullName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?: $user['username'];

    echo json_encode([
        'success'       => true,
        'approver_id'   => $user['id'],
        'approver_name' => $fullName,
        'position'      => $user['position'],
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
