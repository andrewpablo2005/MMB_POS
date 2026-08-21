<?php
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';

$db = Database::getConnection();

echo "=== CHECKING SCHEMA CONSTRAINTS ===\n\n";

// Check inventory table structure
echo "--- Inventory Table ---\n";
$stmt = $db->query("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='inventory' AND TABLE_SCHEMA='mmbpos'");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    if (in_array($col['COLUMN_NAME'], ['supplier_id', 'product_id'])) {
        echo sprintf("  %s: %s, Nullable=%s, Key=%s\n", 
            $col['COLUMN_NAME'], $col['DATA_TYPE'], $col['IS_NULLABLE'], $col['COLUMN_KEY']);
    }
}

// Check foreign key constraints
echo "\n--- Foreign Key Constraints ---\n";
$stmt = $db->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='inventory' AND TABLE_SCHEMA='mmbpos'");
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($fks as $fk) {
    if ($fk['REFERENCED_TABLE_NAME']) {
        echo sprintf("  %s.%s -> %s.%s\n", 
            'inventory', $fk['COLUMN_NAME'], $fk['REFERENCED_TABLE_NAME'], $fk['REFERENCED_COLUMN_NAME']);
    }
}

// Check suppliers table
echo "\n--- Suppliers Table ---\n";
$stmt = $db->query("SELECT COUNT(*) as count FROM suppliers");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total suppliers: " . $result['count'] . "\n";

echo "\n--- Test: Insert with NULL supplier_id ---\n";
try {
    $testStmt = $db->prepare("INSERT INTO inventory (product_id, supplier_id, batch_number, expiry_date, purchase_cost, markup, sale_price, received_quantity, current_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $testStmt->execute([1, null, 'TEST-BATCH', null, 10.00, 20, 15.00, 5, 5]);
    echo "✅ NULL supplier_id works\n";
    
    // Clean up
    $db->prepare("DELETE FROM inventory WHERE batch_number = ?")->execute(['TEST-BATCH']);
} catch (Exception $e) {
    echo "❌ NULL supplier_id failed: " . $e->getMessage() . "\n";
}

?>
