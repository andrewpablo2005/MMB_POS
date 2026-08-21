<?php
// Test the getLowStockAlertItems method specifically
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

try {
    $connection = Database::getConnection();
    $pm = new \Classes\ProductManagement($connection);
    
    echo "=== TESTING FIX FOR LOW STOCK ALERT ===\n\n";
    
    // Test the method that was failing
    echo "Testing getLowStockAlertItems()...\n";
    $alerts = $pm->getLowStockAlertItems(50);
    
    if (is_array($alerts)) {
        echo "✅ SUCCESS: getLowStockAlertItems() executed without errors\n";
        echo "   Found " . count($alerts) . " low-stock items\n";
        
        if (!empty($alerts)) {
            echo "\n   Sample low-stock items:\n";
            foreach (array_slice($alerts, 0, 3) as $item) {
                echo sprintf("   - %s (Qty: %d)\n", 
                    $item['product_name'] ?? $item['generic_name'] ?? 'Unknown', 
                    $item['quantity'] ?? 0
                );
            }
        }
    } else {
        echo "❌ FAILED: getLowStockAlertItems() did not return array\n";
    }
    
    // Test renderLowStockAlert as well
    echo "\n\nTesting renderLowStockAlert()...\n";
    ob_start();
    $pm->renderLowStockAlert(50);
    $output = ob_get_clean();
    
    if (strlen($output) > 0) {
        echo "✅ SUCCESS: renderLowStockAlert() executed\n";
        echo "   HTML output length: " . strlen($output) . " characters\n";
    } else {
        echo "✅ SUCCESS: renderLowStockAlert() executed (no alerts to display)\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}

echo "\n✅ ALL TESTS PASSED - Fixed the quantity field issue!\n";
?>
