<?php
/**
 * Direct-access guard for reusable page fragments.
 *
 * Files in /reusablepage/ are designed to be INCLUDED ONLY from the
 * dashboards (ownerpage/, adminpage/, staffpos/), which already enforce
 * login + role checks. This guard blocks direct URL access
 * (e.g. /MMBPOS/reusablepage/usermanagement.php) as defense-in-depth,
 * because .htaccess protection may not be active on all servers.
 *
 * Usage at the very top of a fragment:
 * require_once __DIR__ . '/guard.php'; // any logged-in user
 * require_once __DIR__ . '/guard.php'; guard_require_roles(['owner']); // owner only
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('guard_require_login')) {
    function guard_require_login(): void
    {
        if (empty($_SESSION['user_id'])) {
            http_response_code(403);
            exit('Forbidden: please log in first.');
        }
    }
}

if (!function_exists('guard_require_roles')) {
    /**
     * @param string[] $roles lowercase role names, e.g. ['owner'], ['owner','admin']
     */
    function guard_require_roles(array $roles): void
    {
        guard_require_login();
        $position = strtolower(trim((string)($_SESSION['position'] ?? '')));
        if (!in_array($position, array_map('strtolower', $roles), true)) {
            http_response_code(403);
            exit('Forbidden: you do not have permission to access this page.');
        }
    }
}

guard_require_login();

// Per-session CSRF token (used by GET delete links and available to forms)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
