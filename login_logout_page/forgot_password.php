<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../conn/database.php';
require_once __DIR__ . '/../conn/basepath.php';
require_once __DIR__ . '/../conn/password_reset.php';

global $db;
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Enter a valid email address.';
        $messageType = 'danger';
    } else {
        try {
            mmb_password_reset_ensure_table($db);
            $stmt = $db->prepare("SELECT u.id, COALESCE(NULLIF(CONCAT_WS(' ', ui.firstname, ui.lastname), ''), u.username) AS display_name
                                  FROM users u
                                  INNER JOIN users_info ui ON ui.user_id = u.id
                                  WHERE LOWER(ui.email) = ? AND u.status = 'active'
                                  LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $expiresAt = date('Y-m-d H:i:s', time() + 1800);
                $db->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL')
                    ->execute([(int)$user['id']]);
                $db->prepare('INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
                    ->execute([(int)$user['id'], $tokenHash, $expiresAt]);

                $resetUrl = mmbpos_base_path() . '/login_logout_page/reset_password.php?token=' . rawurlencode($rawToken);
                $absoluteUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $resetUrl;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                    $absoluteUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $resetUrl;
                }
                mmb_password_reset_send_email($email, (string)$user['display_name'], $absoluteUrl);
            }

            $message = 'If an active account uses that email, a password reset link has been sent.';
        } catch (Throwable $e) {
            error_log('Password reset request failed: ' . $e->getMessage());
            $message = 'Unable to process the request right now. Please try again later.';
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MMB Drugstore</title>
    <?php require_once __DIR__ . '/../conn/connection_links.php'; ?>
    <link rel="stylesheet" href="<?= mmbpos_base_path() ?>/css/login.css?v=4">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <main class="login-card p-4" style="width:100%;max-width:420px;">
        <div class="login-header">
            <h2>Forgot Password</h2>
            <p>Enter your account email to receive a reset link.</p>
        </div>
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control form-control-lg mb-3" id="email" name="email" required autocomplete="email">
            <button type="submit" class="btn btn-primary btn-login w-100">Send Reset Link</button>
        </form>
        <div class="text-center mt-3">
            <a href="<?= mmbpos_base_path() ?>/login_logout_page/login.php">Back to login</a>
        </div>
    </main>
</body>
</html>
