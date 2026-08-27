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

// 🔥 USE FUNCTION HERE
$users = $userAction->getAllPreUsers();
?>
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">

            <table class="table table-striped table-hover align-middle w-100 myTable pendingtable">
                <h1>Pending Account</h1>
                <thead class="table-dark">
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Email</th>
                    </tr>
                </thead>

                <!-- ✅ ONLY ONE TBODY -->
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


<script src="../js/usersmanagement.js"></script>