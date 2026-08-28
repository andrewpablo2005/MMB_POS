<?php
require_once __DIR__ . "/../function/loginfunction.php"; //yours is loginfunction
session_start();

// 🔐 CHECK IF LOGGED IN
if (!isset($_SESSION['user_id'])) {
    header("Location: " . mmbpos_base_path() . "/login_logout_page/login.php");
    exit;
}

// 🔐 CHECK ROLE — Owner dashboard must only be accessible to the Owner
if (strtolower(trim((string)($_SESSION['position'] ?? ''))) !== 'owner') {
    http_response_code(403);
    exit('Access denied: Owner accounts only.');
}

$activeTab = $_GET['tab'] ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <!-- Include CSS/JS libraries here -->
    <?php require_once __DIR__ . "/../conn/connection_links.php"; ?>
    <style>
        @media (min-width: 992px) {
            #sidebar.offcanvas-lg.offcanvas-start {
                position: fixed !important;
                top: 64px;
                left: 0;
                height: calc(100vh - 64px);
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

<body class="d-flex flex-column m-0 p-0" style="min-height: 100vh; overflow-x: hidden;">
    <?php include __DIR__ . "/../reusablepage/header.php"; ?>

    <div class="d-flex flex-grow-1 dashboard-main-content">

        <!-- SIDEBAR -->
        <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" style="--bs-offcanvas-width: 50%;">
            <div class="offcanvas-header d-lg-none">
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-mark brand-mark-sm"><i class="fas fa-prescription-bottle-medical"></i></span>
                    <span class="fw-bold" style="font-size: .95rem;">MMB's Drugstore</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <div class="sidebar-label">Menu</div>
                <div class="nav flex-column nav-pills me-3" role="tablist">

                    <a class="nav-link <?= $activeTab === 'dashboard' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-dashboard"><i class="fas fa-chart-line"></i>Dashboard</a>

                    <a class="nav-link <?= $activeTab === 'product' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-product"><i class="fas fa-box"></i>Product Management</a>

                    <a class="nav-link <?= $activeTab === 'inventory' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-inventory"><i class="fas fa-warehouse"></i>Inventory</a>

                    <a class="nav-link <?= $activeTab === 'sales' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-sales"><i class="fas fa-shopping-cart"></i>Sales (POS)</a>

                    <a class="nav-link <?= $activeTab === 'reports' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-reports"><i class="fas fa-file-alt"></i>Reports</a>

                    <a class="nav-link <?= $activeTab === 'security' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-security"><i class="fas fa-shield-alt"></i>Security</a>

                    <a class="nav-link <?= $activeTab === 'users' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-users"><i class="fas fa-users"></i>User Management</a>

                    <a class="nav-link <?= $activeTab === 'system' ? 'active' : '' ?>" data-bs-toggle="pill"
                        href="#v-pills-system"><i class="fas fa-cog"></i>System Settings</a>
                </div>
            </div>
        </div>

        <div class="tab-content flex-grow-1 w-100" id="v-pills-tabContent" style="min-height: 100vh; overflow: hidden;">
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'dashboard' ? 'show active' : '' ?>" id="v-pills-dashboard">
                <?php include __DIR__ . "/../reusablepage/dashboard.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'product' ? 'show active' : '' ?>" id="v-pills-product">
                <?php include __DIR__ . "/../reusablepage/productmanagement.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'inventory' ? 'show active' : '' ?>" id="v-pills-inventory">
                <?php include __DIR__ . "/../reusablepage/inventorymanagement.php"; ?>
            </div>
            <div class="tab-pane fade <?= $activeTab === 'sales' ? 'show active' : '' ?>" id="v-pills-sales" style="padding: 0; height: 100%; overflow: hidden;">
                <?php include __DIR__ . "/../reusablepage/salespos.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'reports' ? 'show active' : '' ?>" id="v-pills-reports">
                <?php include __DIR__ . "/../reusablepage/reports.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'security' ? 'show active' : '' ?>" id="v-pills-security">
                <?php include __DIR__ . "/../reusablepage/userauthentication.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'users' ? 'show active' : '' ?>" id="v-pills-users">
                <?php include __DIR__ . "/../reusablepage/usermanagement.php"; ?>
            </div>
            <div class="tab-pane fade px-3 px-lg-4 py-4 <?= $activeTab === 'system' ? 'show active' : '' ?>" id="v-pills-system">
                <?php include __DIR__ . "/../reusablepage/systemsettings.php"; ?>
            </div>
        </div>
    </div>
   
</body>

</html>