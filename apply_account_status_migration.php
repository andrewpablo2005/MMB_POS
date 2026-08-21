<?php
require_once __DIR__ . '/conn/database.php';

$columnStmt = $db->prepare("SHOW COLUMNS FROM users LIKE 'status'");
$columnStmt->execute();

if (!$columnStmt->fetch()) {
    $db->exec("ALTER TABLE users ADD COLUMN status ENUM('active', 'disabled') NOT NULL DEFAULT 'active'");
    echo "Account status column added." . PHP_EOL;
} else {
    echo "Account status column already exists." . PHP_EOL;
}
