<?php
require_once __DIR__ . "/../conn/Database.php";
require_once __DIR__ . "/../function/usermanagement.php";

use Classes\UserManagement;

$usersmanagement = new UserManagement($db);
?>

    <!-- PENDING APPROVAL SECTION -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold"><i class="fas fa-user-check me-2"></i>Pending Account Approvals</div>
        <div class="card-body p-0">
            <?php include "ownerapprovalpage.php"; ?>
        </div>
    </div>