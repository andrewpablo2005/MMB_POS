<?php
/**
 * API: Store-wide Settings (key/value)
 *
 * GET  ?key=receipt_paper            -> { success:true, key:'receipt_paper', value:'80' }
 * POST { action:'set', key, value }  -> { success:true }
 *      POST { action:'get', key }    -> { success:true, key, value }
 *
 * Currently managed settings (whitelist):
 *   receipt_paper : '58' | '80'  (thermal roll width in mm — used by the
 *                                  POS receipt printer at print time)
 *
 * Storage: `store_settings` table, created on first use (CREATE TABLE IF NOT
 * EXISTS), mirroring the guarded-migration pattern in save_customer_id.php.
 *
 * SECURITY:
 *   - Any authenticated user may READ (the staff POS needs the paper size
 *     when printing receipts).
 *   - Only owner/admin may WRITE (store-wide configuration).
 */

error_reporting(0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../conn/database.php';

if (empty($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

// Whitelist: key => ['default', [allowed values]]
$storeSettings = [
    'receipt_paper' => ['default' => '80', 'allowed' => ['58', '80']],
];

// Ensure storage exists (guarded one-time migration)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS store_settings (
        setting_key   VARCHAR(50)  NOT NULL PRIMARY KEY,
        setting_value VARCHAR(255) NULL,
        updated_at    DATETIME     NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    error_log('store_settings create error: ' . $e->getMessage());
    echo json_encode(['error' => 'Settings storage is unavailable.']);
    exit;
}

/**
 * Read one setting with its default fallback. Returns null on unknown key.
 */
function storeSettingsRead(PDO $db, array $meta, string $key): ?string {
    try {
        $stmt = $db->prepare('SELECT setting_value FROM store_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $value = $row['setting_value'] ?? null;
        if (in_array($value, $meta['allowed'], true)) {
            return $value;
        }
        return $meta['default'];
    } catch (PDOException $e) {
        error_log('store_settings read error: ' . $e->getMessage());
        return $meta['default'];
    }
}

/**
 * Validate + persist one setting. Owner/admin only.
 */
function storeSettingsWrite(PDO $db, array $storeSettings, string $key, string $value): array {
    // Only owner/admin may change store-wide configuration
    $position = strtolower(trim((string)($_SESSION['position'] ?? '')));
    if (!in_array($position, ['owner', 'admin'], true)) {
        return ['error' => 'Only the owner or an admin can change store settings.'];
    }
    if (!isset($storeSettings[$key])) {
        return ['error' => 'Unknown setting.'];
    }
    if (!in_array($value, $storeSettings[$key]['allowed'], true)) {
        return ['error' => 'Invalid value for this setting.'];
    }
    try {
        $stmt = $db->prepare(
            'INSERT INTO store_settings (setting_key, setting_value, updated_at)
             VALUES (?, ?, NOW())
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()'
        );
        $stmt->execute([$key, $value]);
        return ['success' => true];
    } catch (PDOException $e) {
        error_log('store_settings write error: ' . $e->getMessage());
        return ['error' => 'Failed to save the setting. Please try again.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $key = (string)($_GET['key'] ?? '');
    if (!isset($storeSettings[$key])) {
        echo json_encode(['error' => 'Unknown setting.']);
        exit;
    }
    echo json_encode([
        'success' => true,
        'key'     => $key,
        'value'   => storeSettingsRead($db, $storeSettings[$key], $key),
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true);
    $action = strtolower(trim((string)($body['action'] ?? '')));
    $key    = trim((string)($body['key'] ?? ''));

    if (!isset($storeSettings[$key])) {
        echo json_encode(['error' => 'Unknown setting.']);
        exit;
    }

    if ($action === 'get') {
        echo json_encode([
            'success' => true,
            'key'     => $key,
            'value'   => storeSettingsRead($db, $storeSettings[$key], $key),
        ]);
        exit;
    }

    if ($action === 'set') {
        $value = trim((string)($body['value'] ?? ''));
        $result = storeSettingsWrite($db, $storeSettings, $key, $value);
        echo json_encode($result);
        exit;
    }

    echo json_encode(['error' => 'Invalid action.']);
    exit;
}

echo json_encode(['error' => 'Invalid request method']);
