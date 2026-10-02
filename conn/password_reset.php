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
        // Resend HTTPS API (Task 45) — InfinityFree blocks outbound SMTP,
        // so the old PHPMailer path could never deliver on the live host.
        require_once __DIR__ . '/mailer.php';

        $safeName = htmlspecialchars($recipientName !== '' ? $recipientName : 'there', ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');

        $html = mmb_email_template(
            'Password reset',
            '<p>Hello ' . $safeName . ',</p>'
            . '<p>We received a request to reset your MMB&#39;s Drugstore POS password.</p>'
            . '<p style="margin:26px 0;">'
            . '<a href="' . $safeUrl . '" style="background:#dc2626;color:#ffffff;text-decoration:none;'
            . 'padding:12px 22px;border-radius:8px;font-weight:bold;display:inline-block;">Reset your password</a>'
            . '</p>'
            . '<p style="font-size:13px;color:#6b7280;">This link expires in 30 minutes and can be used only once. '
            . 'If you did not request this, you can ignore this email.</p>'
            . '<p style="font-size:12px;color:#9ca3af;word-break:break-all;">' . $safeUrl . '</p>'
        );
        $text = "Reset your MMB's Drugstore POS password: " . $resetUrl
            . "\n\nThis link expires in 30 minutes and can be used only once.";

        return mmb_send_email($recipient, 'MMB POS password reset', $html, $text);
    }
}
