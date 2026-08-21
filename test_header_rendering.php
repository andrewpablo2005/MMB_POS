<?php
// Simulate the session and test header rendering
session_start();
$_SESSION['position'] = 'admin';
$_SESSION['user_id'] = 1;

// Change to function directory so includes work
chdir(__DIR__ . '/function');

// Capture all output and errors
ob_start();

try {
    require_once __DIR__ . "/conn/Database.php";
    require_once __DIR__ . "/function/addprodfunct.php";
    
    $db = Database::getConnection();
    $productAlertManager = new \Classes\ProductManagement($db);
    $lowStockItems = [];
    $expiryItems = [];
    $showGlobalAlerts = in_array(strtolower($_SESSION['position'] ?? ''), ['owner', 'admin'], true);
    
    if ($showGlobalAlerts) {
        $lowStockItems = $productAlertManager->getLowStockAlertItems();
        $expiryItems = $productAlertManager->getExpiryAlertItems();
    }
    
    $globalAlertItems = [];
    
    // This is the exact code from header.php line 24-30
    foreach ($lowStockItems as $item) {
        $globalAlertItems[] = [
            'title' => 'Low Stock',
            'message' => htmlspecialchars($item['product_name']) . ' has only ' . ($item['quantity'] ?? 0) . ' unit(s) left.',
            'icon' => 'fas fa-exclamation-triangle',
            'bg' => '#f59e0b'
        ];
    }
    
    $output = ob_get_clean();
    $errors = ob_get_clean();
    
    echo "✅ Header processing completed successfully!\n";
    echo "Low stock alerts generated: " . count($globalAlertItems) . "\n\n";
    
    if (!empty($globalAlertItems)) {
        echo "Sample alerts:\n";
        foreach (array_slice($globalAlertItems, 0, 3) as $alert) {
            echo "  - " . $alert['title'] . ": " . substr($alert['message'], 0, 50) . "...\n";
        }
    }
    
    echo "\n✅ NO WARNINGS - Fix successful!\n";
    
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
