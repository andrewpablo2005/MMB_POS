<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /MMBPOS/login.php");
    exit;
}

if (strtolower($_SESSION['position']) !== 'admin') {
    echo "Access denied";
    exit;
}

$activeTab = $_GET['tab'] ?? 'dashboard';

require_once __DIR__ . "/../conn/Database.php";
require_once __DIR__ . "/../conn/connection_links.php";
require_once __DIR__ . "/../function/userregistration.php";

use Classes\UserRegistration;

$user = new UserRegistration($db);

// ✅ SAFE POST HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($user->pre_addUser()) {
        header("Location: dashboard.php?tab=users&added=1");
        exit;
    }
}

// ✅ ALERT AFTER REDIRECT
if (isset($_GET['added'])) {
    echo "<script>alert('User added successfully!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admiin Dashboard</title>
    <?php require_once __DIR__ . "/../conn/connection_links.php"; ?>
    <style>
        @media (min-width: 992px) {
            #sidebar.offcanvas-lg.offcanvas-start {
                position: fixed !important;
                top: 56px;
                left: 0;
                height: calc(100vh - 56px);
                width: min(320px, var(--bs-offcanvas-width, 280px));
                overflow-y: auto;
                z-index: 1030;
            }

            #sidebar .offcanvas-body {
                padding-bottom: 1rem;
            }

            .dashboard-main-content {
                margin-left: min(320px, var(--bs-offcanvas-width, 280px));
            }
        }
    </style>
</head>

<body class="d-flex flex-column m-0 p-0" style="min-height: 100vh;">
   <?php include __DIR__ . "/../reusablepage/header.php"; ?>


    <div class="d-flex dashboard-main-content">

        <!-- SIDEBAR -->
        <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" style="--bs-offcanvas-width: 50%;">
            <div class="offcanvas-header d-lg-none">
                <h5>Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">
                <div class="nav flex-column nav-pills me-3" role="tablist">

                    <a class="nav-link <?= $activeTab === 'dashboard' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-dashboard"><i class="fas fa-chart-line me-2"></i>Dashboard</a>

                    <a class="nav-link <?= $activeTab === 'product' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-product"><i class="fas fa-box me-2"></i>Product Management</a>

                    <a class="nav-link <?= $activeTab === 'inventory' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-inventory"><i class="fas fa-warehouse me-2"></i>Inventory</a>

                    <a class="nav-link <?= $activeTab === 'sales' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-sales"><i class="fas fa-shopping-cart me-2"></i>Sales (POS)</a>

                    <a class="nav-link <?= $activeTab === 'reports' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-reports"><i class="fas fa-file-alt me-2"></i>Reports</a>

                    <a class="nav-link <?= $activeTab === 'pendingaccount' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-pendingaccount"><i class="fas fa-hourglass-half me-2"></i>Pending Account</a>

                    <a class="nav-link <?= $activeTab === 'users' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-users"><i class="fas fa-users me-2"></i>User Management</a>

                    <a class="nav-link <?= $activeTab === 'system' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-system"><i class="fas fa-cog me-2"></i>System Settings</a>
                </div>
            </div>
        </div>

        <div class="tab-content flex-grow-1 w-100" id="v-pills-tabContent" style="background-color: #f8fafc; min-height: 100vh; overflow: hidden;">

            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'dashboard' ? 'show active' : '' ?>" id="v-pills-dashboard">
                <?php include __DIR__ . "/../reusablepage/dashboard.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'product' ? 'show active' : '' ?>" id="v-pills-product">
                <?php include __DIR__ . "/../reusablepage/productmanagement.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'inventory' ? 'show active' : '' ?>" id="v-pills-inventory">
                <?php include __DIR__ . "/../reusablepage/inventorymanagement.php"; ?>
            </div>
            <div class="tab-pane fade <?= $activeTab === 'sales' ? 'show active' : '' ?>" id="v-pills-sales" style="padding: 0; height: 100%; overflow: hidden;">
                <?php include __DIR__ . "/../reusablepage/salespos.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'reports' ? 'show active' : '' ?>" id="v-pills-reports">
                <?php include __DIR__ . "/../reusablepage/reports.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'pendingaccount' ? 'show active' : '' ?>"
                id="v-pills-pendingaccount">
                <?php include __DIR__ . "/../reusablepage/pendingaccountadmin.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'users' ? 'show active' : '' ?>" id="v-pills-users">
                <?php include __DIR__ . "/../reusablepage/adminaddaccount.php"; ?>
            </div>
            <div class="tab-pane fade px-3 py-3 <?= $activeTab === 'system' ? 'show active' : '' ?>" id="v-pills-system">
                <?php include __DIR__ . "/../reusablepage/systemsettings.php"; ?>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>