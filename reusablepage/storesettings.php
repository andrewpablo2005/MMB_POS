<?php
require_once __DIR__ . '/guard.php';
guard_require_roles(['owner', 'admin']);
require_once __DIR__ . '/../conn/database.php';

$storeSettingsMessage = null;
$receiptPaper = '80';
$statutoryDiscountCap = '125.00';

try {
    $db->exec("CREATE TABLE IF NOT EXISTS store_settings (
        setting_key VARCHAR(50) NOT NULL PRIMARY KEY,
        setting_value VARCHAR(255) NULL,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $settingsStmt = $db->query("SELECT setting_key, setting_value FROM store_settings WHERE setting_key IN ('receipt_paper', 'statutory_discount_cap')");
    foreach ($settingsStmt->fetchAll(PDO::FETCH_ASSOC) as $setting) {
        if ($setting['setting_key'] === 'receipt_paper' && in_array($setting['setting_value'], ['58', '80'], true)) {
            $receiptPaper = $setting['setting_value'];
        }
        if ($setting['setting_key'] === 'statutory_discount_cap'
            && is_numeric($setting['setting_value']) && (float)$setting['setting_value'] >= 0) {
            $statutoryDiscountCap = number_format((float)$setting['setting_value'], 2, '.', '');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveStoreSettings'])) {
        $csrfToken = (string)($_POST['csrf_token'] ?? '');
        $postedPaper = (string)($_POST['receipt_paper'] ?? '');
        $postedCap = (float)($_POST['statutory_discount_cap'] ?? -1);

        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Invalid or expired security token.'];
        } elseif (!in_array($postedPaper, ['58', '80'], true)) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Choose a valid receipt paper size.'];
        } elseif ($postedCap < 0 || $postedCap > 100000) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter a weekly discount limit from 0 to 100,000.'];
        } else {
            $saveStmt = $db->prepare(
                'INSERT INTO store_settings (setting_key, setting_value, updated_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()'
            );
            $saveStmt->execute(['receipt_paper', $postedPaper]);
            $saveStmt->execute(['statutory_discount_cap', number_format($postedCap, 2, '.', '')]);
            $receiptPaper = $postedPaper;
            $statutoryDiscountCap = number_format($postedCap, 2, '.', '');
            $storeSettingsMessage = ['type' => 'success', 'text' => 'Store settings saved successfully.'];
        }
    }
} catch (PDOException $exception) {
    $storeSettingsMessage = ['type' => 'danger', 'text' => 'Unable to load or save store settings.'];
}
?>

<div class="container-fluid py-4 px-5">
    <div class="page-head">
        <div>
            <h4>Store Settings</h4>
            <p class="page-sub">Manage settings shared by every POS terminal.</p>
        </div>
    </div>

    <?php if ($storeSettingsMessage): ?>
        <div class="alert alert-<?= htmlspecialchars($storeSettingsMessage['type'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
            <?= htmlspecialchars($storeSettingsMessage['text'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="settings-section">
                    <h6>Statutory Discount</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="statutoryDiscountCap">Weekly Discount Limit</label>
                            <div class="input-group">
                                <span class="input-group-text">PHP</span>
                                <input type="number" class="form-control" id="statutoryDiscountCap" name="statutory_discount_cap"
                                    min="0" max="100000" step="0.01" value="<?= htmlspecialchars($statutoryDiscountCap, ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <small class="text-muted">Maximum Senior/PWD discount amount per customer per week.</small>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h6>Receipt Printing</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="receiptPaperSelect">Printer Paper Size</label>
                            <select class="form-select" name="receipt_paper" id="receiptPaperSelect">
                                <option value="80" <?= $receiptPaper === '80' ? 'selected' : '' ?>>80mm thermal roll (standard)</option>
                                <option value="58" <?= $receiptPaper === '58' ? 'selected' : '' ?>>58mm thermal roll (compact)</option>
                            </select>
                            <small class="text-muted">Applies to every POS terminal.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="saveStoreSettings" class="btn btn-primary px-4">Save Store Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
