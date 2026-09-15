<?php
// Proper logout: destroy session data AND the session cookie
require_once __DIR__ . "/../conn/basepath.php";
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../conn/activity_log.php"; // audit trail (Task 42)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// AUDIT: record the sign-out BEFORE the session is destroyed (Task 42)
if (!empty($_SESSION['user_id'])) {
    mmb_log_activity($db, 'auth', 'logout', 'Signed out');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();

header("Location: " . mmbpos_base_path() . "/");
exit;
