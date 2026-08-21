<?php
// Detailed debugging test
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

try {
    $connection = Database::getConnection();
    $pm = new \Classes\ProductManagement($connection);
    
    echo "=== DETAILED DEBUG TEST ===\n\n";
    
    // Test getAllProducts
    echo "--- getAllProducts() ---\n";
    $products = $pm->getAllProducts();
    if (!empty($products)) {
        $first = $products[0];
        echo "Product ID: " . $first['id'] . "\n";
        echo "Generic Name: " . ($first['generic_name'] ?? 'NOT SET') . "\n";
        echo "Strength Per Quantity Unit: " . ($first['strength_per_quantity_unit'] ?? 'NOT SET') . "\n";
        echo "\nAll available fields in first product:\n";
        foreach ($first as $key => $value) {
            echo "  - $key: $value\n";
        }
    }
    
    echo "\n--- getAllInventoryBatches() ---\n";
    $batches = $pm->getAllInventoryBatches();
    if (!empty($batches)) {
        $first = $batches[0];
        echo "Batch ID: " . $first['id'] . "\n";
        echo "Supplier ID: " . ($first['supplier_id'] ?? 'NOT SET') . "\n";
        echo "Purchase Cost: " . ($first['purchase_cost'] ?? 'NOT SET') . "\n";
        echo "Sale Price: " . ($first['sale_price'] ?? 'NOT SET') . "\n";
        echo "\nAll available fields in first batch:\n";
        foreach ($first as $key => $value) {
            echo "  - $key: $value\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
