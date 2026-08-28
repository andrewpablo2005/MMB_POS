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

<!-- ✅ ALERT + REDIRECT -->
<?php if ($result): ?>
    <script>
        alert("<?= htmlspecialchars((string)($result['message']), ENT_QUOTES, 'UTF-8') ?>");

        <?php if ($result['success']): ?>
            window.location.href = 'dashboard.php?tab=users';
        <?php endif; ?>
    </script>
<?php endif; ?>

<!-- ✅ CUSTOM TABLE SPACING -->
<link rel="stylesheet" href="<?= mmbpos_base_path() ?>/css/table.css">
<link rel="stylesheet" href="../css/button.css">
<div class="container-fluid px-4 mt-3">

    <div class="card shadow-sm">
        <div class="card-body">

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">User Management</h4>

                <button class="button" data-bs-toggle="modal" data-bs-target="#adduser" style="color:white;"> 
                    Add User
                      <span class="button__icon"><svg xmlns="http://www.w3.org/2000" width="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor"
                            height="24" fill="none" class="svg">
                            <line y2="19" y1="5" x2="12" x1="12"></line>
                            <line y2="12" y1="12" x2="19" x1="5"></line>
                        </svg></span>
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
                                        $badgeClass = 'bg-danger';
                                    } elseif ($pos === 'Owner') {
                                        $badgeClass = 'bg-dark';
                                    } elseif ($pos === 'Staff') {
                                        $badgeClass = 'bg-success';
                                    } else {
                                        $badgeClass = 'bg-secondary';
                                    }
                                    ?>

                                    <span class="badge <?= $badgeClass ?> badge-uniform">
                                        <?= htmlspecialchars($pos) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= ($u['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ($u['status'] ?? 'active') === 'active' ? 'Active' : 'Disabled' ?>
                                    </span>
                                </td>

                                <td class="col-action text-center ">


                                    <!-- VIEW -->
                                    <button class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#view<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        View
                                    </button>

                                    <!-- EDIT -->
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#edit<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        Edit
                                    </button>

                                    <!-- DELETE -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="status" value="<?= ($u['status'] ?? 'active') === 'active' ? 'disabled' : 'active' ?>">
                                        <button type="submit" name="toggleUserStatus" class="btn <?= ($u['status'] ?? 'active') === 'active' ? 'btn-warning' : 'btn-success' ?> btn-sm"
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