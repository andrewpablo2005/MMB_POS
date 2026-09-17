<?php
/**
 * Runtime bootstrap — guarded schema ensures + crash-safe fragment includes.
 * Task 35: "pages must never die".
 *
 * WHY THIS EXISTS
 * The FTP deploy pushes FILES only; schema changes committed to mmbpos.sql
 * never reach the live database (they must be applied by hand in phpMyAdmin).
 * When a new table is added to the dump but not to the live DB, every page
 * whose queries touch it fatals mid-render and the dashboard tab comes up
 * blank (that is exactly how the serving_size feature took down POS,
 * Inventory and Reports). The ensure calls below self-heal the core
 * reference tables on first hit, mirroring the guarded-migration pattern
 * already used by store_settings.php and save_customer_id.php.
 */

if (!function_exists('db_ensure_core_schema')) {
    /**
     * Create any missing core reference table and seed it with defaults.
     * Runs at most once per request, and at most once per session when the
     * session layer is active. Never throws — a failed ensure must not take
     * the page down; mmb_include_fragment() surfaces readable errors instead.
     */
    function db_ensure_core_schema(PDO $db): void
    {
        static $ranThisRequest = false;
        if ($ranThisRequest) {
            return;
        }
        $ranThisRequest = true;

        $sessionActive = (session_status() === PHP_SESSION_ACTIVE);
        if ($sessionActive && !empty($_SESSION['mmb_schema_ok'])) {
            return;
        }

        try {
            // ── serving_unit (product "Serving Unit" dropdown) ──
            // Queried by POS, Inventory, Reports and the Add/Edit Product forms.
            $db->exec("CREATE TABLE IF NOT EXISTS serving_unit (
                id INT(11) NOT NULL AUTO_INCREMENT,
                serving_unit_name VARCHAR(20) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_serving_unit_name (serving_unit_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            if ((int) $db->query("SELECT COUNT(*) FROM serving_unit")->fetchColumn() === 0) {
                $seed = $db->prepare('INSERT IGNORE INTO serving_unit (serving_unit_name) VALUES (?)');
                foreach (['mg', 'mcg', 'g', 'mL', 'IU', 'Units'] as $unit) {
                    $seed->execute([$unit]);
                }
            }

            // ── dosage_forms (product form dropdown) ──
            $db->exec("CREATE TABLE IF NOT EXISTS dosage_forms (
                id INT(11) NOT NULL AUTO_INCREMENT,
                form_name VARCHAR(100) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            if ((int) $db->query("SELECT COUNT(*) FROM dosage_forms")->fetchColumn() === 0) {
                $seed = $db->prepare('INSERT INTO dosage_forms (form_name) VALUES (?)');
                foreach (['Tablet', 'Capsule', 'Syrup', 'Suspension', 'Cream', 'Ointment'] as $form) {
                    $seed->execute([$form]);
                }
            }

            if ($sessionActive) {
                $_SESSION['mmb_schema_ok'] = 1;
            }
        } catch (Throwable $e) {
            error_log('db_bootstrap: ' . $e->getMessage());
        }
    }
}

if (!function_exists('mmb_include_fragment')) {
    /**
     * Buffered, crash-safe include for dashboard tab fragments.
     *
     * If a fragment throws (missing table, bad query, …) the dashboard still
     * finishes rendering and the tab shows a readable error card instead of
     * a blank pane. Partial output produced before the failure is discarded
     * so no half-rendered markup leaks into the page.
     */
    function mmb_include_fragment(string $__fragmentFile): void
    {
        // Fragments were written to run at global scope (they read $db,
        // $activeTab, …). Import the global scope into this function so the
        // behavior is identical to a plain include in the dashboard.
        extract($GLOBALS, EXTR_SKIP);

        ob_start();
        try {
            include $__fragmentFile;
        } catch (Throwable $e) {
            ob_end_clean();
            error_log('fragment ' . basename($__fragmentFile) . ': ' . $e->getMessage());

            $detail = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            echo '<div class="alert alert-danger" style="max-width:760px;margin:2rem auto;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;">'
                . '<div style="font-weight:700;margin-bottom:4px;">This page could not load.</div>'
                . '<div style="font-size:13px;color:#7f1d1d;">Something in the data layer failed. '
                . 'Try refreshing first; if it keeps failing, the database table below needs to be created '
                . '(see mmbpos.sql or ask the owner to run the fix in phpMyAdmin).</div>'
                . '<code style="display:block;margin-top:8px;font-size:12px;word-break:break-all;background:#fff;border:1px solid #fecaca;border-radius:4px;padding:8px;color:#991b1b;">'
                . $detail . '</code></div>';
            return;
        }
        echo ob_get_clean();
    }
}
