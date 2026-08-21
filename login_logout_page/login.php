<?php
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ ."/../conn/connection_links.php";
require_once __DIR__ . "/../function/loginfunction.php";

use Classes\Project;

global $db;

$error_message = '';

// PROCESS LOGIN BEFORE ANY HTML OUTPUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($db) {
        $project = new Project($db);
        $error_message = $project->login($username, $password);
    } else {
        $error_message = "Database connection error";
    }
}

// NOW OUTPUT HTML AFTER LOGIN PROCESSING
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System - Login</title>
    <?php require_once __DIR__ . "/../conn/connection_links.php"; ?>
    <link rel="stylesheet" href="/MMBPOS/css/login.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container-fluid">
        <div class="row g-0 align-items-center" style="min-height: 100vh;">
            <!-- Left Column - Login Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div style="width: 100%; max-width: 420px;">
                    <div class="login-header">
                        <h2>Welcome Back</h2>
                        <p>Login to your Pharmacy Management System</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-3 mt-1"></i>
                            <div>
                                <strong>Login Error</strong>
                                <p class="mb-0"><?php echo $error_message; ?></p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-user text-muted" style="width: 16px;"></i>
                                </span>
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="Enter your username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-muted" style="width: 16px;"></i>
                                </span>
                                <input type="password" class="form-control" 
                                    id="password" name="password"
                                    placeholder="Enter your password" required>
                                <span class="input-group-text cursor-pointer" id="togglePassword" style="border-left: none;">
                                    <i class="fa fa-eye text-muted" style="width: 16px; cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary btn-login w-100 fw-bold">
                            Sign In
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <div class="security-badge d-inline-flex">
                            <i class="fas fa-shield-alt"></i> 
                            <span>Secure Login - Authorized Only</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Illustration Background -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%); position: relative; overflow: hidden; min-height: 100vh;">
                <!-- Animated Decorative Elements -->
                <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
                <div style="position: absolute; bottom: -150px; left: -150px; width: 500px; height: 500px; background: rgba(255, 255, 255, 0.05); border-radius: 50%; animation: float 8s ease-in-out infinite 2s;"></div>

                <!-- Illustration Content -->
                <div class="text-white text-center position-relative" style="z-index: 2; padding: 60px 40px;">
                    <div class="mb-5">
                        <div style="font-size: 80px; margin-bottom: 30px; opacity: 0.95;">
                            <i class="fas fa-prescription-bottle-medical"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-3" style="font-size: 28px;">Pharmacy Management</h3>
                    <p class="mb-5" style="font-size: 16px; line-height: 1.6; max-width: 300px; opacity: 0.95;">
                        Streamline your pharmacy operations with our comprehensive management system designed for modern healthcare
                    </p>
                    
                    <div class="d-flex justify-content-center gap-5 mb-5">
                        <div class="text-center">
                            <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.85;">
                                <i class="fas fa-pills"></i>
                            </div>
                            <small style="font-size: 14px;">Inventory</small>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.85;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <small style="font-size: 14px;">Analytics</small>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 40px; margin-bottom: 12px; opacity: 0.85;">
                                <i class="fas fa-users"></i>
                            </div>
                            <small style="font-size: 14px;">Management</small>
                        </div>
                    </div>

                    <div style="padding: 20px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; backdrop-filter: blur(10px);">
                        <small style="font-size: 13px; opacity: 0.9;">✓ HIPAA Compliant • ✓ Secure • ✓ Real-time Analytics</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/MMBPOS/js/hideunhidepassword.js"></script>
</body>

</html>