<?php
require_once __DIR__ . '/conn/database.php';
$db = Database::getConnection();

echo "=== DATABASE SCHEMA CHECK ===\n\n";

// Check product categories
$result = $db->query('SELECT * FROM product_categories LIMIT 1');
$cols = $result->fetch(PDO::FETCH_ASSOC);
echo "Product Categories columns:\n";
foreach (array_keys($cols) as $col) {
    echo "  - " . $col . "\n";
}

echo "\n";

// Check unit_measurement table
$result = $db->query('DESCRIBE unit_measurement');
$cols = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Unit Measurement columns:\n";
foreach ($cols as $col) {
    echo "  - " . $col['Field'] . "\n";
}

echo "\n=== ISSUE ===\n";
echo "The unit_measurement table column name might be different.\n";
echo "Common issues:\n";
echo "1. Form is sending invalid unit_measurement ID\n";
echo "2. Form is sending invalid category_id\n";
echo "3. Supplier FK constraint is failing\n";
?>
