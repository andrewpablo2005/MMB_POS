<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner']);
require_once __DIR__ . "/../conn/basepath.php";
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/usermanagement.php";

use Classes\UserManagement;

$usersmanagement = new UserManagement($db);

$result = null;

// CONTROLLER
if (isset($_POST['addUser'])) {
    $result = $usersmanagement->addUser($_POST);
}

if (isset($_POST['updateUser'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $result = $usersmanagement->updateUser($id, $_POST);
}

if (isset($_POST['deleteUser'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $result = $usersmanagement->deleteUser($id);
}

if (isset($_POST['toggleUserStatus'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = ($_POST['status'] ?? '') === 'active' ? 'active' : 'disabled';
    $result = $usersmanagement->setUserStatus($id, $status);
}

// FETCH
$users = $usersmanagement->getAllUsers();
?>

<!-- ALERT + REDIRECT -->
<?php if ($result): ?>
    <script>
        alert("<?= htmlspecialchars((string)($result['message']), ENT_QUOTES, 'UTF-8') ?>");

        <?php if ($result['success']): ?>
            window.location.href = 'dashboard.php?tab=users';
        <?php endif; ?>
    </script>
<?php endif; ?>

<!-- CUSTOM TABLE SPACING -->
<link rel="stylesheet" href="<?= mmbpos_base_path() ?>/css/table.css">
<div>

    <div class="card">
        <div class="card-body p-4">

            <!-- PAGE HEADER -->
            <div class="page-head">
                <div>
                    <h4>User Management</h4>
                    <p class="page-sub">Accounts, roles and access status for the whole team.</p>
                </div>

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adduser">
                    <i class="fas fa-plus me-1"></i>Add User
                </button>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle w-100 myTable userstable">

                    <thead class="table-dark">
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-name">Name</th>
                            <th class="col-position">Position</th>
                            <th>Status</th>
                            <th class="col-action text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="col-id"><?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?></td>

                                <td class="col-name"><?= ($u['firstname']), " ", ($u['lastname']) ?></td>

                                <td class="col-position">
                                    <?php
                                    $pos = $u['position'];

                                    if ($pos === 'Admin') {
                                        $badgeClass = 'badge-soft-danger';
                                    } elseif ($pos === 'Owner') {
                                        $badgeClass = 'badge-soft-dark';
                                    } elseif ($pos === 'Staff') {
                                        $badgeClass = 'badge-soft-info';
                                    } else {
                                        $badgeClass = 'badge-soft-warning';
                                    }
                                    ?>

                                    <span class="badge <?= $badgeClass ?> badge-uniform">
                                        <?= htmlspecialchars($pos) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php $isActive = ($u['status'] ?? 'active') === 'active'; ?>
                                    <span class="user-status <?= $isActive ? 'is-active' : 'is-disabled' ?>">
                                        <span class="status-dot"></span><?= $isActive ? 'Active' : 'Disabled' ?>
                                    </span>
                                </td>

                                <td class="col-action text-center ">


                                    <!-- VIEW -->
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#view<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        View
                                    </button>

                                    <!-- EDIT -->
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#edit<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        Edit
                                    </button>

                                    <!-- DELETE -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="status" value="<?= ($u['status'] ?? 'active') === 'active' ? 'disabled' : 'active' ?>">
                                        <button type="submit" name="toggleUserStatus" class="btn <?= ($u['status'] ?? 'active') === 'active' ? 'btn-outline-secondary' : 'btn-primary' ?> btn-sm"
                                            onclick="return confirm('<?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?> this account?')">
                                            <?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>

                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" name="deleteUser" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this user?')">
                                            Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
<?php include 'addaccount.php'; ?>
<?php include 'viewaccount.php'; ?>
<?php include 'editaccount.php'; ?>