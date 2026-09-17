<?php
/**
 * Local environment configuration template.
 *
 * Copy this file to conn/config.local.php and fill in real values.
 * config.local.php is gitignored and must NEVER be committed — it holds
 * production database and SMTP credentials.
 *
 * On the live server this file lives at htdocs/conn/config.local.php.
 */
return [
    // ── Database ────────────────────────────────────────────────────────
    'host'    => 'sqlXXX.infinityfree.com', // live: your InfinityFree SQL host
    'db'      => 'if0_42761744_mmbpos',     // live: your database name
    'user'    => 'if0_42761744',            // live: your database username
    'pass'    => 'DATABASE_PASSWORD_HERE',
    'port'    => 3306,
    'charset' => 'utf8mb4',

    // ── SMTP (password-reset emails, PHPMailer) ────────────────────────
    // Gmail: enable 2-Step Verification, then create an App Password at
    // https://myaccount.google.com/apppasswords and paste it below.
    // If a password was ever pushed to a public repo, Google auto-revokes
    // it — always generate a fresh one and keep it only in this file.
    'mail_host'       => 'smtp.gmail.com',
    'mail_username'   => 'your.address@gmail.com',
    'mail_password'   => 'GMAIL_APP_PASSWORD_HERE',
    'mail_port'       => 587,
    'mail_encryption' => 'tls',              // tls (587) or ssl (465)
    'mail_from'       => 'your.address@gmail.com',
    'mail_from_name'  => "MMB's Drugstore",
];
