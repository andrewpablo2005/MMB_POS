<?php 
session_start();

// 🔐 CHECK IF LOGGED IN
if (!isset($_SESSION['user_id'])) {
    header("Location: /MMBPOS/login.php");
    exit;
}

// 🔐 OPTIONAL: CHECK ROLE (case-insensitive)
if (strtolower($_SESSION['position']) !== 'staff') {
    echo "Access denied";
    exit;
}

require_once __DIR__ . "/../conn/Database.php";
require_once __DIR__ . "/../conn/connection_links.php";
require_once __DIR__ . "/../function/userregistration.php";

use Classes\UserRegistration;

$user = new UserRegistration($db);
$user->pre_addUser();
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

