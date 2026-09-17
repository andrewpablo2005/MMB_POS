<?php
require_once __DIR__ . '/../reusablepage/guard.php';
guard_require_roles(['owner', 'admin']);
require_once __DIR__ . '/addprodfunct.php';

use Classes\ProductManagement;

$inventoryManager = new ProductManagement($db);
$dashboardPath = strtolower(trim((string) ($_SESSION['position'] ?? ''))) === 'owner'
    ? '../ownerpage/dashboard.php'
    : '../adminpage/dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $inventoryManager->moveNoStockBatch()) {
    header('Location: ' . $dashboardPath . '?tab=inventory&inventory_tab=no-stock&success=no-stock');
    exit;
}

$errorMessage = $inventoryManager->getResponse() ?: 'Unable to move empty batch to no-stock history.';
header('Location: ' . $dashboardPath . '?tab=inventory&inventory_error=' . rawurlencode($errorMessage));
exit;
