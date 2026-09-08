<?php
require_once __DIR__ . '/guard.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/usermanagement.php";

use Classes\UserManagement;

$usersmanagement = new UserManagement($db);

$userId = $_SESSION['user_id'] ?? 0;
$result = null;
$clearDataResult = null;

$clearDataTables = [
    'inventory',
    'inventory_backup',
    'inventory_disposals',
    'inventory_transactions',
    'login_attempts',
    'override_log',
    'pre_approved_users',
    'pre_approved_users_info',
    'products',
    'products_backup',
    'pwd_customers',
    'register_closings',
    'register_openings',
    'return_items',
    'return_transactions',
    'senior_customers',
    'suppliers',
    'transactions',
    'transaction_batch_allocations',
    'transaction_items',
    'transaction_item_batches'
];

if (isset($_POST['clear_database_data'])) {
    $csrfToken = (string)($_POST['csrf_token'] ?? '');
    $position = strtolower(trim((string)($_SESSION['position'] ?? '')));

    if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrfToken)) {
        $clearDataResult = ['success' => false, 'message' => 'Invalid or expired security token.'];
    } elseif (!in_array($position, ['owner', 'admin'], true)) {
        $clearDataResult = ['success' => false, 'message' => 'Only the owner or an admin can clear database data.'];
    } else {
        $foreignKeysDisabled = false;
        try {
            $db->exec('SET FOREIGN_KEY_CHECKS = 0');
            $foreignKeysDisabled = true;
            $clearedRows = 0;

            foreach ($clearDataTables as $table) {
                $statement = $db->exec('DELETE FROM `' . $table . '`');
                $clearedRows += $statement === false ? 0 : $statement;
                // Task 35: resetting AUTO_INCREMENT is cosmetic — tables without
                // an auto column (or InnoDB edge cases) must not abort the loop
                // halfway through and leave the database half-cleared.
                try {
                    $db->exec('ALTER TABLE `' . $table . '` AUTO_INCREMENT = 1');
                } catch (PDOException $alterError) {
                    // intentionally ignored — the DELETE above already succeeded
                }
            }

            $clearDataResult = [
                'success' => true,
                'message' => 'Database data cleared successfully (' . $clearedRows . ' row(s) removed).'
            ];
        } catch (PDOException $exception) {
            $clearDataResult = ['success' => false, 'message' => 'Could not clear database data: ' . $exception->getMessage()];
        } finally {
            if ($foreignKeysDisabled) {
                $db->exec('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
    }
}

// ── Receipt paper setting (store-wide, shared by every POS terminal) ──
// Reads from / persists to the `store_settings` key-value table (created
// on first use — same guarded-migration pattern as save_customer_id.php).
$receiptPaper = '80';
try {
    $db->exec("CREATE TABLE IF NOT EXISTS store_settings (
        setting_key   VARCHAR(50)  NOT NULL PRIMARY KEY,
        setting_value VARCHAR(255) NULL,
        updated_at    DATETIME     NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmtPaper = $db->prepare("SELECT setting_value FROM store_settings WHERE setting_key = 'receipt_paper'");
    $stmtPaper->execute();
    $paperRow = $stmtPaper->fetch(PDO::FETCH_ASSOC);
    if ($paperRow && in_array($paperRow['setting_value'], ['58', '80'], true)) {
        $receiptPaper = $paperRow['setting_value'];
    }
} catch (PDOException $ignore) {
    // Table/read unavailable — keep the 80mm default
}

// HANDLE UPDATE — SECURITY FIX: always operate on the LOGGED-IN user's
// own account. The posted user_id is ignored (it previously allowed anyone
// to rewrite any account, including the owner's credentials).
if (isset($_POST['updateUserSystem'])) {
    $result = $usersmanagement->updateUserSystem(
        (int)$_SESSION['user_id'],
        $_POST
    );

    // Persist the receipt paper choice in the same save action (owner/admin
    // only — this page is not reachable by staff, but check anyway).
    $postedPaper = (string)($_POST['receipt_paper'] ?? '');
    if (in_array($postedPaper, ['58', '80'], true)) {
        $position = strtolower(trim((string)($_SESSION['position'] ?? '')));
        if (in_array($position, ['owner', 'admin'], true)) {
            try {
                $stmtPaper = $db->prepare(
                    'INSERT INTO store_settings (setting_key, setting_value, updated_at)
                     VALUES (?,?, NOW())
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()'
                );
                $stmtPaper->execute(['receipt_paper', $postedPaper]);
                $receiptPaper = $postedPaper;
            } catch (PDOException $ignore) {
                // Paper setting not saved — the account update result still stands
            }
        }
    }
}

$currentUser = $usersmanagement->getUserById($userId);
?>
<?php if ($result): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            mmbNotify({
                type: <?= !empty($result['success']) ? "'success'" : "'danger'" ?>,
                title: <?= json_encode((string)($result['message'] ?? '')) ?>,
                duration: 5000
            });
            <?php if (!empty($result['success'])): ?>
                setTimeout(function () { window.location.href = 'dashboard.php?tab=system'; }, 1800);
            <?php endif; ?>
        });
    </script>
<?php endif; ?>
<?php if ($clearDataResult): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            mmbNotify({
                type: <?= !empty($clearDataResult['success']) ? "'success'" : "'danger'" ?>,
                title: <?= json_encode((string)($clearDataResult['message'] ?? '')) ?>,
                duration: 7000
            });
        });
    </script>
<?php endif; ?>

<div class="container-fluid py-4 px-5">

    <div class="page-head">
        <div>
            <h4>Account Settings</h4>
            <p class="page-sub">Update your own account details, contact information, and security settings.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">

            <form method="POST">
                <!-- user_id is intentionally NOT taken from the form; the
                     server always uses the session user id -->

                <!-- ACCOUNT -->
                <div class="settings-section">
                    <h6><i class="fas fa-user me-2"></i>Account</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username"
                                value="<?= htmlspecialchars($currentUser['username'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <!-- PERSONAL -->
                <div class="settings-section">
                    <h6><i class="fas fa-id-card me-2"></i>Personal Information</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname"
                                value="<?= htmlspecialchars($currentUser['firstname'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middlename"
                                value="<?= htmlspecialchars($currentUser['middlename'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname"
                                value="<?= htmlspecialchars($currentUser['lastname'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <!-- CONTACT -->
                <div class="settings-section">
                    <h6><i class="fas fa-phone me-2"></i>Contact</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" minlength="11" maxlength="11" name="contactnumber"
                                value="<?= htmlspecialchars($currentUser['contactnumber'] ?? '') ?>" placeholder="09XXXXXXXXX">
                        </div>
                    </div>
                </div>

                <!-- RECEIPT PRINTING (store-wide) -->
                <div class="settings-section">
                    <h6><i class="fas fa-receipt me-2"></i>Receipt Printing</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Printer Paper Size</label>
                            <select class="form-select" name="receipt_paper" id="receiptPaperSelect">
                                <option value="80" <?= $receiptPaper === '80' ? 'selected' : '' ?>>80mm thermal roll (standard)</option>
                                <option value="58" <?= $receiptPaper === '58' ? 'selected' : '' ?>>58mm thermal roll (compact)</option>
                            </select>
                            <small class="text-muted">Applies to every POS terminal. Saved together with your account changes.</small>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light h-100 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fas fa-print text-muted"></i>
                                    <strong id="receiptPaperLabel"><?= $receiptPaper === '58' ? '58mm — compact roll' : '80mm — standard roll' ?></strong>
                                </div>
                                <small class="text-muted mb-2">Printed receipt width follows this choice.</small>
                                <div class="d-flex gap-2 align-items-end">
                                    <div id="receiptPaperRoll" style="width:<?= $receiptPaper === '58' ? '34' : '48' ?>px; height:64px; border:1px dashed #94a3b8; border-radius:2px; background:#fff;"></div>
                                    <div id="receiptPaperPrinter" style="width:<?= $receiptPaper === '58' ? '46' : '66' ?>px; height:78px; border:1px solid #64748b; border-radius:2px 2px 4px 4px; background:#f8fafc; position:relative;">
                                        <div style="position:absolute; top:6px; left:50%; transform:translateX(-50%); width:60%; height:3px; background:#cbd5e1;"></div>
                                        <div style="position:absolute; top:14px; left:12%; right:12%; height:2px; background:#e2e8f0;"></div>
                                        <div style="position:absolute; top:20px; left:12%; right:12%; height:2px; background:#e2e8f0;"></div>
                                        <div style="position:absolute; top:26px; left:20%; right:20%; height:2px; background:#e2e8f0;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECURITY -->
                <div class="settings-section">
                    <h6><i class="fas fa-shield-halved me-2"></i>Security</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Void PIN</label>
                            <input type="password" inputmode="numeric" pattern="[0-9]{7}" minlength="7" maxlength="7" class="form-control" name="void_password"
                                placeholder="7-digit PIN (leave blank to keep)"
                                autocomplete="new-password">
                            <small class="text-muted">Stored hashed. Leave blank to keep your current PIN.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" minlength="8" maxlength="16" name="password"
                                placeholder="New password (leave blank to keep)"
                                autocomplete="new-password">
                            <small class="text-muted">Leave blank to keep your current password.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="updateUserSystem" class="btn btn-primary px-4">
                        <i class="fas fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div class="card border-danger mt-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="text-danger fs-4"><i class="fas fa-database"></i></div>
                <div class="flex-grow-1">
                    <h5 class="mb-1">Clear database data</h5>
                    <p class="text-muted mb-3">Removes operational, transaction, inventory, customer, supplier, and product data. This cannot be undone.</p>
                    <p class="small mb-3"><strong>Preserved:</strong> discounts, dosage forms, product categories, serving units, store settings, users, and user information.</p>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" name="clear_database_data" class="btn btn-danger"
                                data-mmb-confirm="This permanently deletes operational database data. Preserved reference and account tables will remain. Continue?"
                                data-mmb-ok="Yes, clear database data">
                            <i class="fas fa-eraser me-1"></i> Clear Data Tables
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live preview: the paper illustration follows the dropdown immediately,
// even before the form is saved.
(function () {
    const select  = document.getElementById('receiptPaperSelect');
    const label   = document.getElementById('receiptPaperLabel');
    const roll    = document.getElementById('receiptPaperRoll');
    const printer = document.getElementById('receiptPaperPrinter');
    if (!select || !label) return;
    select.addEventListener('change', function () {
        const mm = select.value;
        label.textContent = mm === '58' ? '58mm — compact roll' : '80mm — standard roll';
        if (roll)    roll.style.width    = (mm === '58' ? 34 : 48) + 'px';
        if (printer) printer.style.width = (mm === '58' ? 46 : 66) + 'px';
    });
})();
</script>
