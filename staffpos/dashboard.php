<?php 
session_start();
require_once __DIR__ . "/../conn/basepath.php";

// 🔐 CHECK IF LOGGED IN
if (!isset($_SESSION['user_id'])) {
    header("Location: " . mmbpos_base_path() . "/login_logout_page/login.php");
    exit;
}

// 🔐 CHECK ROLE (case-insensitive)
if (strtolower($_SESSION['position']) !== 'staff') {
    http_response_code(403);
    echo "Access denied";
    exit;
}

require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../conn/connection_links.php";
require_once __DIR__ . "/../function/userregistration.php";

use Classes\UserRegistration;

// NOTE: Staff accounts must NOT be able to pre-register users (privilege
// escalation vector — position was client-supplied). Account creation is
// handled by Admin (adminaddaccount) and Owner (addaccount) dashboards.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <?php require_once __DIR__ . "/../conn/connection_links.php"; ?>
</head>

<body>

    <?php include __DIR__ . "/../reusablepage/header.php"; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-fill">
        <?php include __DIR__ . "/../reusablepage/salespos.php"; ?>
    </main>

    <!-- FOOTER -->
    

</body>

</html>

