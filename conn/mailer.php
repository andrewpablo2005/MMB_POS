<?php
/**
 * MMB POS shared mailer — Resend HTTPS API (Task 45).
 *
 * Why: InfinityFree blocks outbound SMTP (fsockopen to ports 25/465/587),
 * so PHPMailer/SMTP can never work on the live host. Resend exposes a
 * plain HTTPS REST endpoint which IS allowed.
 *
 * Config lives in conn/config.local.php (gitignored — server-side only):
 *   'resend_api_key' => 're_...',
 *   'mail_from'      => 'noreply@mmbpos.neilpaolocabrera.site',
 *   'mail_from_name' => 'MMB POS'
 *
 * Every email in the system must go through mmb_send_email() so the
 * transport stays swappable in one place.
 */

if (!function_exists('mmb_mail_config')) {
    function mmb_mail_config(): array
    {
        static $config = null;
        if ($config === null) {
            $config = [];
            $file = __DIR__ . '/config.local.php';
            if (is_readable($file)) {
                $loaded = include $file;
                if (is_array($loaded)) {
                    $config = $loaded;
                }
            }
        }
        return $config;
    }
}

if (!function_exists('mmb_send_email')) {
    /**
     * Send an email through the Resend API (HTTPS — works on InfinityFree).
     *
     * @param string $to      recipient address
     * @param string $subject subject line
     * @param string $html    HTML body
     * @param string $text    optional plain-text fallback body
     * @return bool  true when Resend accepts the message (HTTP 2xx)
     */
    function mmb_send_email(string $to, string $subject, string $html, string $text = ''): bool
    {
        $config = mmb_mail_config();
        $apiKey = trim((string)($config['resend_api_key'] ?? getenv('MMB_RESEND_API_KEY') ?: ''));
        $fromEmail = trim((string)($config['mail_from'] ?? getenv('MMB_MAIL_FROM') ?: ''));
        $fromName = trim((string)($config['mail_from_name'] ?? 'MMB POS'));

        if ($apiKey === '' || $fromEmail === '') {
            error_log('Resend mailer not configured: set resend_api_key + mail_from in conn/config.local.php');
            return false;
        }
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("Resend mailer: invalid recipient '{$to}'");
            return false;
        }

        $from = $fromName !== '' ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail;
        $payload = [
            'from' => $from,
            'to' => [$to],
            'subject' => $subject,
            'html' => $html,
        ];
        if ($text !== '') {
            $payload['text'] = $text;
        }
        $body = json_encode($payload);
        if ($body === false) {
            error_log('Resend mailer: JSON encode failed');
            return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $apiKey,
        ];

        // cURL is enabled on InfinityFree; fall back to streams if missing.
        if (function_exists('curl_init')) {
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $response = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                error_log('Resend mailer: cURL error (' . trim($curlErr) . ')');
                return false;
            }
        } else {
            $ctx = stream_context_create(['http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'timeout' => 15,
                'ignore_errors' => true,
            ]]);
            $response = @file_get_contents('https://api.resend.com/emails', false, $ctx);
            $status = 0;
            foreach ($http_response_header ?? [] as $h) {
                if (preg_match('#HTTP/\S+\s+(\d{3})#', $h, $m)) {
                    $status = (int)$m[1];
                }
            }
            if ($response === false) {
                error_log('Resend mailer: stream request failed');
                return false;
            }
        }

        // 200/201/202 = accepted by Resend
        if ($status < 200 || $status >= 300) {
            error_log("Resend mailer: HTTP {$status} — " . substr((string)$response, 0, 500));
            return false;
        }
        return true;
    }
}

if (!function_exists('mmb_email_template')) {
    /**
     * Branded HTML wrapper so every system email looks consistent
     * (red header band + footer, matching the POS theme).
     */
    function mmb_email_template(string $heading, string $bodyHtml): string
    {
        return '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f4f5f7;">'
            . '<div style="max-width:560px;margin:24px auto;background:#ffffff;border-radius:14px;'
            . 'overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#1f2937;border:1px solid #e5e7eb;">'
            . '<div style="background:#dc2626;padding:20px 28px;">'
            . '<span style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:0.5px;">MMB POS</span>'
            . '<span style="color:rgba(255,255,255,0.85);font-size:12px;margin-left:10px;">MMB&#39;s Drugstore</span>'
            . '</div>'
            . '<div style="padding:28px;">'
            . '<h2 style="margin:0 0 14px;font-size:18px;color:#111827;">'
            . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</h2>'
            . '<div style="font-size:14px;line-height:1.7;">' . $bodyHtml . '</div>'
            . '</div>'
            . '<div style="padding:16px 28px;background:#f9fafb;font-size:11px;color:#6b7280;'
            . 'border-top:1px solid #e5e7eb;">'
            . 'This is an automated message from the MMB POS system. '
            . 'If you were not expecting it, you can safely ignore this email.'
            . '</div></div></body></html>';
    }
}
