<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner']);
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../function/usermanagement.php";

use Classes\UserManagement;

$usersmanagement = new UserManagement($db);
?>

<!-- PENDING APPROVAL SECTION -->
<div class="card mb-4">
    <div class="card-header fw-bold"><i class="fas fa-user-check me-2 text-primary"></i>Pending Account Approvals</div>
    <div class="card-body p-0">
        <?php include "ownerapprovalpage.php"; ?>
    </div>
</div>