<?php
// Quick database connection test and schema verification
require_once __DIR__ . '/conn/Database.php';

try {
    require_once __DIR__ . '/conn/database.php';
    $connection = Database::getConnection();
    
    if (!$connection) {
        die("❌ Database connection failed\n");
    }
    
    echo "✅ Database connection successful\n\n";
    
    // Check if products table exists and has correct columns
    $stmt = $connection->prepare("DESCRIBE products");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Products table columns:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    // Verify key columns exist
    $expected_columns = [
        'id', 'generic_name', 'branded_name', 'strength', 
        'measurement_id', 'barcode', 'category_id', 
        'units_per_package', 'package_type', 'dosage_form',
        'strength_per_quantity', 'strength_per_quantity_unit',
        'imageproduct', 'is_basic_necessities'
    ];
    
    $missing = array_diff($expected_columns, $columns);
    if (!empty($missing)) {
        echo "\n⚠️  Missing columns in products table:\n";
        foreach ($missing as $col) {
            echo "  - $col\n";
        }
    } else {
        echo "\n✅ All expected columns present in products table\n";
    }
    
    // Check inventory table
    echo "\n---\nInventory table columns:\n";
    $stmt = $connection->prepare("DESCRIBE inventory");
    $stmt->execute();
    $inv_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($inv_columns as $col) {
        echo "  - $col\n";
    }
    
    $expected_inv = [
        'id', 'product_id', 'supplier_id', 'batch_number',
        'date_received', 'expiry_date', 'purchase_cost', 'markup',
        'sale_price', 'received_quantity', 'current_quantity'
    ];
    
    $missing_inv = array_diff($expected_inv, $inv_columns);
    if (!empty($missing_inv)) {
        echo "\n⚠️  Missing columns in inventory table:\n";
        foreach ($missing_inv as $col) {
            echo "  - $col\n";
        }
    } else {
        echo "\n✅ All expected columns present in inventory table\n";
    }
    
    // Check data counts
    echo "\n---\nData verification:\n";
    $stmt = $connection->prepare("SELECT COUNT(*) as cnt FROM products");
    $stmt->execute();
    $product_count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    echo "Products: $product_count\n";
    
    $stmt = $connection->prepare("SELECT COUNT(*) as cnt FROM inventory");
    $stmt->execute();
    $inventory_count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    echo "Inventory batches: $inventory_count\n";
    
    // Test a sample product query
    echo "\n---\nSample product query:\n";
    $stmt = $connection->prepare("
        SELECT 
            p.id,
            p.generic_name,
            p.units_per_package,
            COALESCE(SUM(i.current_quantity), 0) as current_quantity
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        GROUP BY p.id
        LIMIT 3
    ");
    $stmt->execute();
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($samples)) {
        foreach ($samples as $sample) {
            echo sprintf("  Product #%d: %s (Units: %d, Stock: %d)\n",
                $sample['id'],
                $sample['generic_name'],
                $sample['units_per_package'],
                $sample['current_quantity']
            );
        }
    } else {
        echo "  No products found\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
