<?php
/**
 * Central Activity Logger — audit trail for the whole system.
 *
 * Every meaningful action (login, product add/edit, sales, returns,
 * user management, settings changes ...) is recorded with WHO did it,
 * WHEN, from WHERE, and WHAT exactly happened. The records are shown
 * in the "Activity Logs" report (Reports tab, owner/admin only).
 *
 * DESIGN RULES:
 *  1. FAIL-OPEN — logging must NEVER block or break the business action.
 *     Every insert is wrapped in try/catch; failures are logged to
 *     error_log and silently ignored.
 *  2. SELF-HEALING — the `activity_logs` table is created automatically
 *     (CREATE TABLE IF NOT EXISTS) on first use, exactly like
 *     password_reset_tokens and store_settings. No manual DB migration
 *     is required on any environment (localhost XAMPP or InfinityFree).
 *  3. SNAPSHOT DATA — username and role are copied into the row, so the
 *     log stays readable even after a user account is deleted.
 *     No foreign key on purpose.
 *
 * Usage:
 *   mmb_log_activity($db, 'products', 'product_add',
 *       "Added product 'Biogesic 500mg'", 'product', $productId);
 *
 *   // Login events happen before the session is populated, so pass an
 *   // explicit actor override:
 *   mmb_log_activity($db, 'auth', 'login', 'Signed in', '', null,
 *       ['user_id' => $user['id'], 'username' => $user['username'],
 *        'role' => $position]);
 */

if (!function_exists('mmb_activity_log_ensure_table')) {
    /**
     * Create the activity_logs table if it does not exist yet (idempotent).
     * Runs at most once per request (static guard).
     */
    function mmb_activity_log_ensure_table(PDO $db): void
    {
        static $ensured = false;
        if ($ensured || $db === null) {
            return;
        }
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS activity_logs (
                id INT NOT NULL AUTO_INCREMENT,
                user_id INT NULL,
                username VARCHAR(155) NULL,
                role VARCHAR(20) NULL,
                module VARCHAR(30) NOT NULL DEFAULT 'system',
                action VARCHAR(40) NOT NULL DEFAULT 'unknown',
                entity_type VARCHAR(30) NULL,
                entity_id INT NULL,
                description VARCHAR(500) NULL,
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_activity_created (created_at),
                KEY idx_activity_user (user_id),
                KEY idx_activity_action (action),
                KEY idx_activity_module (module)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
            $ensured = true;
        } catch (Throwable $e) {
            // Table creation failed — logging stays disabled for this request,
            // the calling code is NOT affected (fail-open).
            error_log('[activity_log] table create failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('mmb_log_activity')) {
    /**
     * Record one activity entry. Never throws, never returns a value —
     * the business action always proceeds regardless of the outcome.
     *
     * @param PDO|null $db         Active database connection (null = no-op)
     * @param string $module      Auth / Products / Inventory / Sales / Users / Settings / System
     * @param string $action      Machine name, e.g. product_add, login_failed, sale_completed
     * @param string $description Human-readable one-liner shown in the report
     * @param string $entityType  Optional affected entity type (product, user, transaction ...)
     * @param int|null $entityId  Optional affected entity id
     * @param array|null $actor   Optional actor override for events that fire before
     *                            the session is populated (login) or for anonymous
     *                            events (public registration):
     *                            ['user_id'=>?, 'username'=>?, 'role'=>?]
     */
    function mmb_log_activity(
        ?PDO $db,
        string $module,
        string $action,
        string $description = '',
        string $entityType = '',
        ?int $entityId = null,
        ?array $actor = null
    ): void {
        if ($db === null) {
            return; // fail-open: no DB connection, no logging
        }
        try {
            mmb_activity_log_ensure_table($db);

            // Resolve actor: explicit override wins over the session.
            $userId = null;
            $username = null;
            $role = null;
            if (is_array($actor)) {
                $userId    = isset($actor['user_id']) ? (int) $actor['user_id'] : null;
                $username  = isset($actor['username']) ? (string) $actor['username'] : null;
                $role      = isset($actor['role']) ? (string) $actor['role'] : null;
            } elseif (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['user_id'])) {
                $userId    = (int) $_SESSION['user_id'];
                $username  = (string) ($_SESSION['username'] ?? '');
                $role      = strtolower(trim((string) ($_SESSION['position'] ?? '')));
            }

            $ip   = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
            $ua   = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');

            $stmt = $db->prepare("INSERT INTO activity_logs
                (user_id, username, role, module, action, entity_type, entity_id, description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $username !== '' ? mb_substr($username, 0, 155) : null,
                $role !== '' ? mb_substr($role, 0, 20) : null,
                mb_substr($module, 0, 30),
                mb_substr($action, 0, 40),
                $entityType !== '' ? mb_substr($entityType, 0, 30) : null,
                $entityId,
                $description !== '' ? mb_substr($description, 0, 500) : null,
                $ip !== '' ? mb_substr($ip, 0, 45) : null,
                $ua !== '' ? mb_substr($ua, 0, 255) : null,
            ]);
        } catch (Throwable $e) {
            // Fail-open by design — never break the action being logged.
            error_log('[activity_log] insert failed: ' . $e->getMessage());
        }
    }
}
