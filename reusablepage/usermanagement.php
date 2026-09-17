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
        document.addEventListener('DOMContentLoaded', function () {
            mmbNotify({
                type: <?= !empty($result['success']) ? "'success'" : "'danger'" ?>,
                title: <?= json_encode((string)($result['message'] ?? '')) ?>,
                duration: 5000
            });
            <?php if ($result['success']): ?>
                setTimeout(function () { window.location.href = 'dashboard.php?tab=users'; }, 1800);
            <?php endif; ?>
        });
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
                    Add User
                </button>
            </div>

            <ul class="nav nav-tabs mb-4" id="userManagementTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="user-accounts-tab" data-bs-toggle="tab" data-bs-target="#user-accounts-pane" type="button" role="tab" aria-controls="user-accounts-pane" aria-selected="true">
                        User Accounts
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="user-security-tab" data-bs-toggle="tab" data-bs-target="#user-security-pane" type="button" role="tab" aria-controls="user-security-pane" aria-selected="false">
                        Pending Approvals
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="userManagementTabContent">
                <div class="tab-pane fade show active" id="user-accounts-pane" role="tabpanel" aria-labelledby="user-accounts-tab" tabindex="0">
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
                                    <button type="button" class="btn btn-info btn-sm"
                                        onclick="mmbOpenUserModal(this)"
                                        data-bs-target="#view<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        View
                                    </button>

                                    <!-- EDIT -->
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="mmbOpenUserModal(this)"
                                        data-bs-target="#edit<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        Edit
                                    </button>

                                    <!-- DELETE -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="status" value="<?= ($u['status'] ?? 'active') === 'active' ? 'disabled' : 'active' ?>">
                                        <button type="submit" name="toggleUserStatus" class="btn <?= ($u['status'] ?? 'active') === 'active' ? 'btn-outline-secondary' : 'btn-primary' ?> btn-sm"
                                            data-mmb-confirm="<?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?> this account?">
                                            <?= ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>

                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" name="deleteUser" class="btn btn-danger btn-sm"
                                            data-mmb-confirm="Delete this user? This cannot be undone." data-mmb-ok="Yes, delete">
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

                <div class="tab-pane fade" id="user-security-pane" role="tabpanel" aria-labelledby="user-security-tab" tabindex="0">
                    <?php include 'userauthentication.php'; ?>
                </div>
            </div>

        </div>
    </div>

</div>
<?php include __DIR__ . '/addaccount.php'; ?>
<?php include __DIR__ . '/viewaccount.php'; ?>
<?php include __DIR__ . '/editaccount.php'; ?>

<script>
    window.mmbOpenUserModal = function (trigger) {
        const selector = trigger?.getAttribute('data-bs-target');
        const modal = selector ? document.querySelector(selector) : null;
        if (!modal) {
            if (typeof mmbNotify === 'function') {
                mmbNotify({ type: 'danger', title: 'Unable to open user details', message: 'The selected user modal was not found.' });
            }
            return;
        }

        if (window.bootstrap?.Modal) {
            bootstrap.Modal.getOrCreateInstance(modal).show();
            return;
        }

        modal.style.display = 'block';
        modal.classList.add('show');
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show mmb-user-modal-backdrop';
        backdrop.addEventListener('click', function () {
            window.mmbCloseUserModal(modal);
        });
        document.body.appendChild(backdrop);
    };

    window.mmbCloseUserModal = function (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.mmb-user-modal-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });
    };

    document.addEventListener('click', function (event) {
        const closeButton = event.target.closest('[data-bs-dismiss="modal"]');
        if (closeButton && !window.bootstrap?.Modal) {
            const modal = closeButton.closest('.modal');
            if (modal) window.mmbCloseUserModal(modal);
        }
    });
</script>
