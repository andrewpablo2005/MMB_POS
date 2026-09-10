<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../conn/database.php';
require_once __DIR__ . '/../conn/basepath.php';
require_once __DIR__ . '/../conn/password_reset.php';

global $db;
$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$error = '';
$success = '';
$tokenRow = null;

try {
    mmb_password_reset_ensure_table($db);
    if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {
        $stmt = $db->prepare("SELECT prt.id, prt.user_id
                              FROM password_reset_tokens prt
                              INNER JOIN users u ON u.id = prt.user_id AND u.status = 'active'
                              WHERE prt.token_hash = ? AND prt.used_at IS NULL AND prt.expires_at > NOW()
                              LIMIT 1");
        $stmt->execute([hash('sha256', $token)]);
        $tokenRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = (string)($_POST['password'] ?? '');
        $confirmation = (string)($_POST['password_confirmation'] ?? '');
        if (!$tokenRow) {
            $error = 'This reset link is invalid or has expired.';
        } elseif (strlen($password) < 8 || strlen($password) > 72) {
            $error = 'Password must be between 8 and 72 characters.';
        } elseif ($password !== $confirmation) {
            $error = 'Passwords do not match.';
        } else {
            $db->beginTransaction();
            $db->prepare('UPDATE users SET password = ?, failed_attempts = 0, last_attempt = NULL WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), (int)$tokenRow['user_id']]);
            $db->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ? AND used_at IS NULL')
                ->execute([(int)$tokenRow['id']]);
            $db->commit();
            $tokenRow = null;
            $success = 'Your password has been changed. You can now sign in.';
        }
    }
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Password reset failed: ' . $e->getMessage());
    $error = 'Unable to reset the password right now. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MMB Drugstore</title>
    <?php require_once __DIR__ . '/../conn/connection_links.php'; ?>
    <link rel="stylesheet" href="<?= mmbpos_base_path() ?>/css/login.css?v=4">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <main class="login-card p-4" style="width:100%;max-width:420px;">
        <div class="login-header">
            <h2>Reset Password</h2>
            <p>Choose a new password for your account.</p>
        </div>
        <?php if ($error !== ''): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert alert-success" role="alert"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
            <a class="btn btn-primary btn-login w-100" href="<?= mmbpos_base_path() ?>/login_logout_page/login.php">Back to Login</a>
        <?php elseif ($tokenRow): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                <label for="password" class="form-label">New password</label>
                <div class="input-group input-group-lg mb-3">
                    <input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="72" required autocomplete="new-password">
                    <span class="input-group-text cursor-pointer password-toggle" data-password-target="password" role="button" tabindex="0" aria-label="Show new password">
                        <i class="fa fa-eye text-muted" style="width: 16px;"></i>
                    </span>
                </div>
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <div class="input-group input-group-lg mb-3">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="8" maxlength="72" required autocomplete="new-password">
                    <span class="input-group-text cursor-pointer password-toggle" data-password-target="password_confirmation" role="button" tabindex="0" aria-label="Show password confirmation">
                        <i class="fa fa-eye text-muted" style="width: 16px;"></i>
                    </span>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">Save New Password</button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">This reset link is invalid or has expired.</div>
            <a class="btn btn-primary btn-login w-100" href="<?= mmbpos_base_path() ?>/login_logout_page/forgot_password.php">Request a New Link</a>
        <?php endif; ?>
    </main>
    <script>
        document.querySelectorAll('.password-toggle').forEach(function (toggle) {
            function switchVisibility() {
                const password = document.getElementById(toggle.dataset.passwordTarget);
                if (!password) return;
                const visible = password.type === 'password';
                password.type = visible ? 'text' : 'password';
                toggle.querySelector('i').classList.toggle('fa-eye', !visible);
                toggle.querySelector('i').classList.toggle('fa-eye-slash', visible);
                toggle.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            }
            toggle.addEventListener('click', switchVisibility);
            toggle.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    switchVisibility();
                }
            });
        });
    </script>
</body>
</html>
