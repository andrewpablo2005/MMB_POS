<?php
/**
 * API: Verify Manager PIN for Override Approval
 * POST body: { username, password }
 * Returns: { success, approver_id, approver_name } or { error }
 */
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$username = trim($body['username'] ?? '');
$password = trim($body['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

try {
    // Look up the user
    $stmt = $db->prepare("SELECT id, username, password, position FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    // Must be Owner or Admin to approve overrides
    $role = strtolower($user['position']);
    if ($role !== 'owner' && $role !== 'admin') {
        echo json_encode(['error' => 'Only Owner or Admin can approve overrides']);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    echo json_encode([
        'success'       => true,
        'approver_id'   => $user['id'],
        'approver_name' => $user['username']
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
