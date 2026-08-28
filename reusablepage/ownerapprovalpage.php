<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner']);
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

<div class="table-responsive">

    <table class="table table-striped table-hover align-middle w-100 myTable pendingtable">

        <thead class="table-dark">
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Age</th>
                <th>Address</th>
                <th>Email</th>
                <th>Position</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>

        <!-- ONLY ONE TBODY -->
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>

                <td><?= htmlspecialchars((string)($u['username']), ENT_QUOTES, 'UTF-8') ?></td>

                <td>
                    <?= ($u['firstname']) . ' ' . ($u['lastname'])?>
                </td>

                <td><?= htmlspecialchars((string)($u['age']), ENT_QUOTES, 'UTF-8') ?></td>

                <td>
                    <?= ($u['city']) . ', ' . ($u['province'])?>
                </td>

                <td><?= htmlspecialchars((string)($u['email']), ENT_QUOTES, 'UTF-8') ?></td>

                <td><?= htmlspecialchars((string)($u['position']), ENT_QUOTES, 'UTF-8') ?></td>
                
                <td class="text-center">

                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                        <form method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                            <button name="action" value="approve"
                                class="btn btn-success btn-sm">
                                Approve
                            </button>
                        </form>

                        <form method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)($u['id']), ENT_QUOTES, 'UTF-8') ?>">
                            <button name="action" value="reject"
                                class="btn btn-danger btn-sm">
                                Reject
                            </button>
                        </form>

                    </div>

                </td>

            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>

<script src="../js/usersmanagement.js"></script>