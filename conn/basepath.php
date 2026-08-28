<?php
/**
 * URL base path of the app — auto-detected so the app works when deployed at
 * the domain root (e.g. https://mmbpos.wuaze.com/) OR inside a subfolder
 * (e.g. XAMPP's http://localhost/MMBPOS/).
 *
 * Usage: href="<?= mmbpos_base_path() ?>/css/login.css"
 * header('Location: ' . mmbpos_base_path() . '/ownerpage/dashboard.php');
 */
if (!function_exists('mmbpos_base_path')) {
    function mmbpos_base_path(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        $sn = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $dir = rtrim(str_replace('\\', '/', dirname($sn)), '/');
        // Scripts live one level below the app root (ownerpage/, staffpos/, ...).
        // Strip that last segment to get the app's URL base.
        if (preg_match('#^(.*)/(ownerpage|adminpage|staffpos|login_logout_page|reusablepage|function|conn)$#', $dir, $m)) {
            $base = rtrim(str_replace('\\', '/', $m[1]), '/');
        } else {
            // Script sits at the app root itself (index.php / entry point).
            $base = $dir;
        }
        return $base;
    }
}
