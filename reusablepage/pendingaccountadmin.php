<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['admin']);
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/userregistration.php";

use Classes\UserRegistration;

$userAction = new UserRegistration($db);

// HANDLE ACTION
if (isset($_POST['action'])) {
    $_GET['id'] = $_POST['id']; // reuse your function

    if ($_POST['action'] == 'approve')
        $userAction->approve();
    if ($_POST['action'] == 'reject')
        $userAction->reject();
}

// USE FUNCTION HERE
$users = $userAction->getAllPreUsers();
?>
<div class="page-head">
    <div>
        <h4>Pending Accounts</h4>
        <p class="page-sub">Review and approve new staff account requests.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <div class="table-responsive">

            <table class="table table-striped table-hover align-middle w-100 myTable pendingtable">
                <thead class="table-dark">
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Email</th>
                    </tr>
                </thead>

                <!-- ONLY ONE TBODY -->
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>

                            <td><?= htmlspecialchars((string)($u['username']), ENT_QUOTES, 'UTF-8') ?></td>

                            <td>
                                <?= ($u['firstname']) . ' ' . ($u['lastname']) ?>
                            </td>

                            <td><?= htmlspecialchars((string)($u['age']), ENT_QUOTES, 'UTF-8') ?></td>

                            <td>
                                <?= ($u['city']) . ', ' . ($u['province']) ?>
                            </td>

                            <td><?= htmlspecialchars((string)($u['email']), ENT_QUOTES, 'UTF-8') ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>


<!-- usersmanagement.js loads globally via conn/connection_links.php.
     This duplicate tag double-loaded the script with a stale ?v=2 cache
     entry whose old function declarations (initDataTable without
     prepareDataTableEmptyState/language) overrode the current code at
     runtime — the root cause of the issue #5 DataTables alert and of the
     custom empty-table messages never rendering. -->