<?php
require_once __DIR__ . '/basepath.php';

if (!function_exists('mmb_password_reset_ensure_table')) {
    function mmb_password_reset_ensure_table(PDO $db): void
    {
        $db->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            token_hash CHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            used_at DATETIME DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_password_reset_token_hash (token_hash),
            KEY idx_password_reset_user (user_id),
            KEY idx_password_reset_expires (expires_at),
            CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }
}

if (!function_exists('mmb_password_reset_send_email')) {
    function mmb_password_reset_send_email(string $recipient, string $recipientName, string $resetUrl): bool
    {
        $mailerRoot = __DIR__ . '/../phpmailer/src/';
        require_once $mailerRoot . 'Exception.php';
        require_once $mailerRoot . 'PHPMailer.php';
        require_once $mailerRoot . 'SMTP.php';

        $config = [];
        $configFile = __DIR__ . '/config.local.php';
        if (is_readable($configFile)) {
            $loaded = include $configFile;
            if (is_array($loaded)) {
                $config = $loaded;
            }
        }

        $host = trim((string)($config['mail_host'] ?? getenv('MMB_MAIL_HOST') ?: 'smtp.gmail.com'));
        $username = trim((string)($config['mail_username'] ?? getenv('MMB_MAIL_USERNAME') ?: getenv('MMBPOS_SMTP_USER') ?: ''));
        $password = preg_replace('/\s+/', '', (string)($config['mail_password'] ?? getenv('MMB_MAIL_PASSWORD') ?: getenv('MMBPOS_SMTP_PASS') ?: ''));
        $port = (int)($config['mail_port'] ?? getenv('MMB_MAIL_PORT') ?: 587);
        $encryption = strtolower(trim((string)($config['mail_encryption'] ?? getenv('MMB_MAIL_ENCRYPTION') ?: 'tls')));
        $from = trim((string)($config['mail_from'] ?? getenv('MMBPOS_SMTP_FROM') ?: $username));
        $fromName = trim((string)($config['mail_from_name'] ?? "MMB's Drugstore"));

        if ($host === '' || $username === '' || $password === '' || $from === '') {
            error_log('Password reset email is not configured. Add mail_* values to conn/config.local.php.');
            return false;
        }

        try {
            $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = $host;
            $mailer->SMTPAuth = true;
            $mailer->Username = $username;
            $mailer->Password = $password;
            $mailer->Port = $port;
            if ($encryption === 'ssl' || $encryption === 'smtps') {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mailer->CharSet = 'UTF-8';
            $mailer->setFrom($from, $fromName);
            $mailer->addAddress($recipient, $recipientName !== '' ? $recipientName : $recipient);
            $mailer->isHTML(true);
            $mailer->Subject = "MMB's Drugstore password reset";
            $safeName = htmlspecialchars($recipientName !== '' ? $recipientName : 'there', ENT_QUOTES, 'UTF-8');
            $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
            $mailer->Body = '<p>Hello ' . $safeName . ',</p>'
                . '<p>We received a request to reset your MMB\'s Drugstore password.</p>'
                . '<p><a href="' . $safeUrl . '">Reset your password</a></p>'
                . '<p>This link expires in 30 minutes and can be used only once. If you did not request this, you can ignore this email.</p>';
            $mailer->AltBody = "Reset your MMB's Drugstore password: " . $resetUrl . "\n\nThis link expires in 30 minutes and can be used only once.";
            return $mailer->send();
        } catch (Throwable $e) {
            error_log('Password reset email failed: ' . $e->getMessage());
            return false;
        }
    }
}
