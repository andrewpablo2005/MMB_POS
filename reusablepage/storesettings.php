<?php
require_once __DIR__ . '/guard.php';
guard_require_roles(['owner', 'admin']);
require_once __DIR__ . '/../conn/database.php';
require_once __DIR__ . '/../conn/activity_log.php'; // audit trail (Task 42)
require_once __DIR__ . '/../function/addprodfunct.php';

$categoryManager = new \Classes\ProductManagement($db);
$categories = $categoryManager->getCategories();

$storeSettingsMessage = null;
$receiptPaper = '80';
$vatRate = '0.00';
$seniorDiscountRate = '20.00';
$pwdDiscountRate = '20.00';
$statutoryDiscountCap = '125.00';
$lowStockThreshold = '15';
$nearExpiryDays = '60';

try {
    $db->exec("CREATE TABLE IF NOT EXISTS store_settings (
        setting_key VARCHAR(50) NOT NULL PRIMARY KEY,
        setting_value VARCHAR(255) NULL,
        updated_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $settingsStmt = $db->query("SELECT setting_key, setting_value FROM store_settings WHERE setting_key IN ('receipt_paper', 'vat_rate', 'senior_discount_rate', 'pwd_discount_rate', 'statutory_discount_cap', 'low_stock_threshold', 'near_expiry_days')");
    foreach ($settingsStmt->fetchAll(PDO::FETCH_ASSOC) as $setting) {
        if ($setting['setting_key'] === 'receipt_paper' && in_array($setting['setting_value'], ['58', '80'], true)) {
            $receiptPaper = $setting['setting_value'];
        }
        if ($setting['setting_key'] === 'vat_rate'
            && is_numeric($setting['setting_value']) && (float)$setting['setting_value'] >= 0 && (float)$setting['setting_value'] <= 100) {
            $vatRate = number_format((float)$setting['setting_value'], 2, '.', '');
        }
        if (in_array($setting['setting_key'], ['senior_discount_rate', 'pwd_discount_rate'], true)
            && is_numeric($setting['setting_value']) && (float)$setting['setting_value'] >= 0 && (float)$setting['setting_value'] <= 100) {
            if ($setting['setting_key'] === 'senior_discount_rate') {
                $seniorDiscountRate = number_format((float)$setting['setting_value'], 2, '.', '');
            } else {
                $pwdDiscountRate = number_format((float)$setting['setting_value'], 2, '.', '');
            }
        }
        if ($setting['setting_key'] === 'statutory_discount_cap'
            && is_numeric($setting['setting_value']) && (float)$setting['setting_value'] >= 0) {
            $statutoryDiscountCap = number_format((float)$setting['setting_value'], 2, '.', '');
        }
        if ($setting['setting_key'] === 'low_stock_threshold'
            && ctype_digit((string) $setting['setting_value']) && (int) $setting['setting_value'] >= 1 && (int) $setting['setting_value'] <= 100000) {
            $lowStockThreshold = (string) (int) $setting['setting_value'];
        }
        if ($setting['setting_key'] === 'near_expiry_days'
            && ctype_digit((string) $setting['setting_value']) && (int) $setting['setting_value'] >= 1 && (int) $setting['setting_value'] <= 3650) {
            $nearExpiryDays = (string) (int) $setting['setting_value'];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveStoreSettings'])) {
        $csrfToken = (string)($_POST['csrf_token'] ?? '');
        $postedPaper = (string)($_POST['receipt_paper'] ?? '');
        $postedVatRate = (float)($_POST['vat_rate'] ?? -1);
        $postedSeniorRate = (float)($_POST['senior_discount_rate'] ?? -1);
        $postedPwdRate = (float)($_POST['pwd_discount_rate'] ?? -1);
        $postedCap = (float)($_POST['statutory_discount_cap'] ?? -1);
        $postedLowStock = filter_var($_POST['low_stock_threshold'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]);
        $postedNearExpiry = filter_var($_POST['near_expiry_days'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3650]]);

        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Invalid or expired security token.'];
        } elseif (!in_array($postedPaper, ['58', '80'], true)) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Choose a valid receipt paper size.'];
        } elseif ($postedVatRate < 0 || $postedVatRate > 100) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter a VAT rate from 0 to 100%.'];
        } elseif ($postedSeniorRate < 0 || $postedSeniorRate > 100 || $postedPwdRate < 0 || $postedPwdRate > 100) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter Senior and PWD discount rates from 0 to 100%.'];
        } elseif ($postedCap < 0 || $postedCap > 100000) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter a weekly discount limit from 0 to 100,000.'];
        } elseif ($postedLowStock === false) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter a low-stock threshold from 1 to 100,000 units.'];
        } elseif ($postedNearExpiry === false) {
            $storeSettingsMessage = ['type' => 'danger', 'text' => 'Enter a near-expiry warning period from 1 to 3,650 days.'];
        } else {
            $saveStmt = $db->prepare(
                'INSERT INTO store_settings (setting_key, setting_value, updated_at)
                 VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()'
            );
            $saveStmt->execute(['receipt_paper', $postedPaper]);
            $saveStmt->execute(['vat_rate', number_format($postedVatRate, 2, '.', '')]);
            $saveStmt->execute(['senior_discount_rate', number_format($postedSeniorRate, 2, '.', '')]);
            $saveStmt->execute(['pwd_discount_rate', number_format($postedPwdRate, 2, '.', '')]);
            $saveStmt->execute(['statutory_discount_cap', number_format($postedCap, 2, '.', '')]);
            $saveStmt->execute(['low_stock_threshold', (string) $postedLowStock]);
            $saveStmt->execute(['near_expiry_days', (string) $postedNearExpiry]);
            $receiptPaper = $postedPaper;
            $vatRate = number_format($postedVatRate, 2, '.', '');
            $seniorDiscountRate = number_format($postedSeniorRate, 2, '.', '');
            $pwdDiscountRate = number_format($postedPwdRate, 2, '.', '');
            $statutoryDiscountCap = number_format($postedCap, 2, '.', '');
            $lowStockThreshold = (string) $postedLowStock;
            $nearExpiryDays = (string) $postedNearExpiry;

            // AUDIT (Task 42)
            mmb_log_activity($db, 'settings', 'settings_update',
                "Updated store settings — VAT: {$vatRate}%, Senior discount: {$seniorDiscountRate}%, PWD discount: {$pwdDiscountRate}%, receipt paper: {$postedPaper}mm, weekly discount limit: " . number_format($postedCap, 2) . " PHP, low stock: {$postedLowStock}, near expiry: {$postedNearExpiry} days");

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
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#categorySettingsModal">
            Category Settings
        </button>
    </div>

    <?php if ($storeSettingsMessage): ?>
        <div class="alert alert-<?= htmlspecialchars($storeSettingsMessage['type'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
            <?= htmlspecialchars($storeSettingsMessage['text'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php if ($storeSettingsMessage['type'] === 'success'): ?>
            <script>
                window.location.replace(window.location.href);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="settings-section">
                    <h6>Statutory Discount</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="seniorDiscountRate">Senior Discount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="seniorDiscountRate" name="senior_discount_rate"
                                    min="0" max="100" step="0.01" value="<?= htmlspecialchars($seniorDiscountRate, ENT_QUOTES, 'UTF-8') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pwdDiscountRate">PWD Discount</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="pwdDiscountRate" name="pwd_discount_rate"
                                    min="0" max="100" step="0.01" value="<?= htmlspecialchars($pwdDiscountRate, ENT_QUOTES, 'UTF-8') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
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
                    <h6>VAT</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="vatRate">VAT Rate</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="vatRate" name="vat_rate"
                                    min="0" max="100" step="0.01" value="<?= htmlspecialchars($vatRate, ENT_QUOTES, 'UTF-8') ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Applied to products in VAT-enabled categories. Product prices are VAT-inclusive, so this shows the VAT portion already included in the price.</small>
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

                <div class="settings-section">
                    <h6>Inventory Alerts</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="lowStockThreshold">Minimum Stock Alert</label>
                            <input type="number" class="form-control" id="lowStockThreshold" name="low_stock_threshold"
                                min="1" max="100000" step="1" value="<?= htmlspecialchars($lowStockThreshold, ENT_QUOTES, 'UTF-8') ?>" required>
                            <small class="text-muted">Show a low-stock notification when total active inventory is at or below this quantity.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nearExpiryDays">Near-Expiry Warning Period</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="nearExpiryDays" name="near_expiry_days"
                                    min="1" max="3650" step="1" value="<?= htmlspecialchars($nearExpiryDays, ENT_QUOTES, 'UTF-8') ?>" required>
                                <span class="input-group-text">days</span>
                            </div>
                            <small class="text-muted">Warn for active inventory expiring within this many days.</small>
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

<?php include __DIR__ . '/categorysettings.php'; ?>
