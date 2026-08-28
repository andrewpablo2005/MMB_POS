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

// ✅ HANDLE UPDATE — SECURITY FIX: always operate on the LOGGED-IN user's
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

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="row g-0">

            <!-- LEFT PANEL -->
            <div class="col-lg-4 bg-primary text-white p-5 d-flex flex-column justify-content-center">
                <h3 class="fw-bold mb-3">
                    <i class="bi bi-gear-fill me-2"></i>Settings
                </h3>
                <p class="opacity-75">
                    Update your account details, contact information, and system security settings.
                </p>

                <hr class="border-light">

                <div class="mt-3">
                    <p class="mb-2"><i class="bi bi-person me-2"></i> Account Info</p>
                    <p class="mb-2"><i class="bi bi-card-text me-2"></i> Personal Info</p>
                    <p class="mb-2"><i class="bi bi-phone me-2"></i> Contact</p>
                    <p class="mb-0"><i class="bi bi-shield-lock me-2"></i> Security</p>
                </div>
            </div>

            <!-- RIGHT PANEL (FORM) -->
            <div class="col-lg-8 bg-white p-5">

                <h4 class="fw-bold mb-4">System Settings</h4>

                <form method="POST">
                    <!-- user_id is intentionally NOT taken from the form; the
                         server always uses the session user id -->

                    <!-- ROW 1 -->
                    <div class="row g-3 mb-3">
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

                    <!-- ROW 2 -->
                    <div class="row g-3 mb-3">
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

                    <!-- ROW 3 -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" minlength="11" maxlength="11" name="contactnumber"
                                value="<?= htmlspecialchars($currentUser['contactnumber'] ?? '') ?>" placeholder="Contact Number ex 09XXXXXXXXX">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Void PIN</label>
                            <input type="password" inputmode="numeric" pattern="[0-9]{7}" minlength="7" maxlength="7" class="form-control" name="void_password"
                                placeholder="Enter 7-digit new Void PIN (leave blank to keep)" 
                                autocomplete="new-password">
                            <small class="text-muted">Stored hashed. Leave blank to keep your current PIN.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" minlength="8" maxlength="16" name="password"
                                placeholder="Enter 8 alphanumeric password ex Qwerty123!"
                                autocomplete="new-password">
                            <small class="text-muted">Leave blank to keep your current password.</small>
                        </div>

                    </div>

                    <!-- BUTTON -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" name="updateUserSystem"
                            class="btn btn-primary px-4 py-2 rounded-3">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>
