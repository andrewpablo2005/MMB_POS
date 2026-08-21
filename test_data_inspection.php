<?php
require_once __DIR__ . '/conn/database.php';

try {
    $connection = Database::getConnection();
    
    echo "=== DATABASE DATA INSPECTION ===\n\n";
    
    // Check products table for strength_per_quantity_unit
    echo "--- Products Table (strength_per_quantity_unit) ---\n";
    $stmt = $connection->prepare("
        SELECT id, generic_name, strength_per_quantity_unit 
        FROM products 
        LIMIT 5
    ");
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $unit = $row['strength_per_quantity_unit'] === NULL ? 'NULL' : (strlen($row['strength_per_quantity_unit']) === 0 ? 'EMPTY' : $row['strength_per_quantity_unit']);
        echo sprintf("  #%d %s: '%s'\n", $row['id'], $row['generic_name'], $unit);
    }
    
    // Check inventory table for supplier_id and pricing
    echo "\n--- Inventory Table (supplier_id, pricing) ---\n";
    $stmt = $connection->prepare("
        SELECT 
            id, 
            product_id, 
            supplier_id, 
            purchase_cost, 
            markup, 
            sale_price,
            current_quantity
        FROM inventory 
        LIMIT 5
    ");
    $stmt->execute();
    $count = 0;
    while ($row = $stmt->fetch() && $count < 5) {
        echo sprintf("  Batch #%d (Product #%d):\n", $row['id'], $row['product_id']);
        echo sprintf("    Supplier ID: %s\n", $row['supplier_id'] === NULL ? 'NULL' : (strlen($row['supplier_id']) === 0 ? 'EMPTY' : $row['supplier_id']));
        echo sprintf("    Purchase Cost: %s\n", $row['purchase_cost'] === NULL ? 'NULL' : (strlen($row['purchase_cost']) === 0 ? 'EMPTY' : $row['purchase_cost']));
        echo sprintf("    Sale Price: %s\n", $row['sale_price'] === NULL ? 'NULL' : (strlen($row['sale_price']) === 0 ? 'EMPTY' : $row['sale_price']));
        $count++;
    }
    
    // Check if there are ANY non-null supplier_ids
    echo "\n--- Inventory Stats ---\n";
    $stmt = $connection->prepare("
        SELECT 
            COUNT(*) as total_batches,
            COUNT(NULLIF(supplier_id, '')) as batches_with_supplier,
            COUNT(NULLIF(purchase_cost, '')) as batches_with_purchase_cost,
            COUNT(NULLIF(sale_price, '')) as batches_with_sale_price
        FROM inventory
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    foreach ($stats as $key => $value) {
        echo "  $key: $value\n";
    }
    
    // Check suppliers table
    echo "\n--- Suppliers Table ---\n";
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM suppliers");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "  Total suppliers: $count\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
