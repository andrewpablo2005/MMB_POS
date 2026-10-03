<?php
require_once __DIR__ . '/guard.php';
guard_require_roles(['admin']);
require_once __DIR__ . '/../conn/database.php';

$pendingApprovalCount = (int) $db->query('SELECT COUNT(*) FROM pre_approved_users')->fetchColumn();
$requestedUserTab = ($_GET['tab'] ?? '') === 'pendingaccount'
    ? 'pending'
    : (($_GET['user_tab'] ?? '') === 'pending' ? 'pending' : 'add');
?>

<div class="admin-user-management-page">
    <div class="page-head">
        <div>
            <h4>User Management</h4>
            <p class="page-sub">Add staff accounts and review pending approvals.</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="adminUserManagementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $requestedUserTab === 'add' ? 'active' : '' ?>" id="admin-add-user-tab"
                data-bs-toggle="tab" data-bs-target="#admin-add-user-pane" type="button" role="tab"
                aria-controls="admin-add-user-pane" aria-selected="<?= $requestedUserTab === 'add' ? 'true' : 'false' ?>">
                Add User
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $requestedUserTab === 'pending' ? 'active' : '' ?>" id="admin-pending-approvals-tab"
                data-bs-toggle="tab" data-bs-target="#admin-pending-approvals-pane" type="button" role="tab"
                aria-controls="admin-pending-approvals-pane" aria-selected="<?= $requestedUserTab === 'pending' ? 'true' : 'false' ?>">
                For Approvals
                <?php if ($pendingApprovalCount > 0): ?>
                    <span class="badge rounded-pill text-bg-danger ms-2" aria-label="<?= $pendingApprovalCount ?> pending approvals">
                        <?= $pendingApprovalCount > 99 ? '99+' : $pendingApprovalCount ?>
                    </span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminUserManagementTabContent">
        <div class="tab-pane fade <?= $requestedUserTab === 'add' ? 'show active' : '' ?>" id="admin-add-user-pane"
            role="tabpanel" aria-labelledby="admin-add-user-tab" tabindex="0">
            <?php include __DIR__ . '/adminaddaccount.php'; ?>
        </div>
        <div class="tab-pane fade <?= $requestedUserTab === 'pending' ? 'show active' : '' ?>" id="admin-pending-approvals-pane"
            role="tabpanel" aria-labelledby="admin-pending-approvals-tab" tabindex="0">
            <?php include __DIR__ . '/pendingaccountadmin.php'; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.getElementById('adminUserManagementTabs');
        if (!tabs) return;

        tabs.addEventListener('shown.bs.tab', function (event) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', 'users');
            url.searchParams.set('user_tab', event.target.id === 'admin-pending-approvals-tab' ? 'pending' : 'add');
            window.history.replaceState(null, '', url.pathname + url.search + url.hash);
        });
    });
</script>