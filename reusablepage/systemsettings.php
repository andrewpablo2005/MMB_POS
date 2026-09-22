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
    'inventory_no_stock',
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
                $tableExistsStmt = $db->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
                $tableExistsStmt->execute([$table]);
                if (!$tableExistsStmt->fetchColumn()) {
                    continue;
                }

                try {
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
                } catch (PDOException $deleteError) {
                    // Some legacy or optional tables may not exist in a given schema.
                    // Keep clearing the rest of the database instead of aborting the reset.
                    continue;
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

// HANDLE UPDATE — SECURITY FIX: always operate on the LOGGED-IN user's
// own account. The posted user_id is ignored (it previously allowed anyone
// to rewrite any account, including the owner's credentials).
if (isset($_POST['updateUserSystem'])) {
    $result = $usersmanagement->updateUserSystem(
        (int)$_SESSION['user_id'],
        $_POST
    );

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
                    <h6>Account</h6>
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
                    <h6>Personal Information</h6>
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
                    <h6>Contact</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" minlength="11" maxlength="11" name="contactnumber"
                                value="<?= htmlspecialchars($currentUser['contactnumber'] ?? '') ?>" placeholder="09XXXXXXXXX">
                        </div>
                    </div>
                </div>

                <!-- SECURITY -->
                <div class="settings-section">
                    <h6>Security</h6>
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
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>

    <?php if (in_array(strtolower(trim((string) ($_SESSION['position'] ?? ''))), ['owner', 'admin'], true)): ?>
        <div class="card mt-4 border-danger">
            <div class="card-body p-4">
                <div class="settings-section mb-0">
                    <h6 class="text-danger">Reset Database Data</h6>
                    <p class="text-muted mb-3">
                        This removes products, inventory, suppliers, sales, customers, and related operational records.
                        User accounts and account details will remain.
                    </p>
                    <form method="POST" onsubmit="return confirm('Reset all operational database data? User accounts will remain, but this action cannot be undone.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" name="clear_database_data" value="1" class="btn btn-outline-danger">
                            <i class="fas fa-database me-1" aria-hidden="true"></i> Reset Database Data
                        </button>
                    </form>
                </div>
            </div>
        </div> 
    <?php endif; ?> 

    
</div>

