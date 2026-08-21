<?php

namespace Classes;

// PDO DB
require_once __DIR__ . "/../conn/database.php"; //yours is Database.php

global $db; // Make $db accessible


class Project
{
    public int $username;
    public string $password;
    private $con;
    private string $response;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function login(string $username, string $password)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        // 🔐 CHECK IP LOCK
        $stmt = $this->con->prepare("SELECT * FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        $attemptData = $stmt->fetch();

        if ($attemptData && $attemptData['attempts'] >= 5) {
            $lastAttempt = strtotime($attemptData['last_attempt']);

            if ((time() - $lastAttempt) < 300) {
                return "Too many attempts. Try again later.";
            }
        }

        // 🔍 GET USER
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // =========================
        // ✅ USER EXISTS
        // =========================
        if ($user) {

            // 🔐 CHECK USER LOCK
            if ($user['failed_attempts'] >= 5) {
                $lastAttempt = strtotime($user['last_attempt']);

                if ((time() - $lastAttempt) < 300) {
                    return "Account locked. Try again after 5 minutes.";
                }
            }

            // 🔐 VERIFY PASSWORD
            if (password_verify($password, $user['password'])) {

                // 🔄 RESET USER ATTEMPTS
                $resetUser = $this->con->prepare("UPDATE users SET failed_attempts = 0 WHERE id = ?");
                $resetUser->execute([$user['id']]);

                // 🔄 RESET IP ATTEMPTS
                $resetIP = $this->con->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $resetIP->execute([$ip]);

                // 🔐 SESSION
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['position'] = $user['position'];

                // 🔁 REDIRECT BASED ON ROLE (case-insensitive)
                $position = strtolower($user['position']);
                if ($position === 'owner') {
                    header('Location: /MMBPOS/ownerpage/dashboard.php');
                    exit;
                } elseif ($position === 'admin') {
                    header('Location: /MMBPOS/adminpage/dashboard.php');
                    exit;
                } elseif ($position === 'staff') {
                    header('Location: /MMBPOS/staffpos/dashboard.php');
                    exit;
                } else {
                    // Fallback if position doesn't match any role
                    return "Invalid user position: " . htmlspecialchars($user['position']);
                }
            }
            // =========================
            // ❌ WRONG PASSWORD
            // =========================
            else {

                // 🔺 INCREMENT USER ATTEMPTS
                $newAttempts = $user['failed_attempts'] + 1;

                $updateUser = $this->con->prepare("UPDATE users SET failed_attempts = ?, last_attempt = NOW() WHERE id = ?");
                $updateUser->execute([$newAttempts, $user['id']]);

                // 🔺 INCREMENT IP ATTEMPTS
                if ($attemptData) {
                    $ipAttempts = $attemptData['attempts'] + 1;

                    $updateIP = $this->con->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = NOW() WHERE ip_address = ?");
                    $updateIP->execute([$ipAttempts, $ip]);

                } else {
                    $insertIP = $this->con->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())");
                    $insertIP->execute([$ip]);
                }

                return "Invalid username or password";
            }
        }

        // =========================
        // ❌ USER NOT FOUND
        // =========================
        else {

            // 🔺 INCREMENT IP ATTEMPTS ONLY
            if ($attemptData) {
                $ipAttempts = $attemptData['attempts'] + 1;

                $updateIP = $this->con->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = NOW() WHERE ip_address = ?");
                $updateIP->execute([$ipAttempts, $ip]);

            } else {
                $insertIP = $this->con->prepare("INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())");
                $insertIP->execute([$ip]);
            }

            return "Invalid username or password";
        }
    }
    private function responseSQL($stmt)
    {
        if ($stmt) {
            $this->response = "Success";
        } else {
            $this->response = "Failed";
        }
    }
}
?>