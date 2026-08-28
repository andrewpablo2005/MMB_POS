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
        alert(<?= json_encode($result['message'] ?? '') ?>);

        <?php if (!empty($result['success'])): ?>
            window.location.href = 'dashboard.php?tab=system';
        <?php endif; ?>
    </script>
<?php endif; ?>

<div class="container-fluid py-4 px-5">

    <div class="page-head">
        <div>
            <h4>System Settings</h4>
            <p class="page-sub">Update your account details, contact information, and security settings.</p>
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
</div>
