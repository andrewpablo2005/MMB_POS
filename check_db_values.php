<?php
require_once __DIR__ . '/conn/database.php';
$db = Database::getConnection();

echo "=== DATABASE SCHEMA CHECK ===\n\n";

// Check product categories
$result = $db->query('SELECT id, category_name FROM product_categories LIMIT 10');
$categories = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Product Categories (" . count($categories) . " records):\n";
foreach ($categories as $cat) {
    echo "  ID: " . $cat['id'] . " - " . $cat['category_name'] . "\n";
}

echo "\n";

// Check unit_measurement table
$result = $db->query('SELECT id, unit_name FROM unit_measurement LIMIT 10');
$units = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Unit Measurements (" . count($units) . " records):\n";
foreach ($units as $unit) {
    echo "  ID: " . $unit['id'] . " - " . $unit['unit_name'] . "\n";
}

echo "\n";

// Check suppliers
$result = $db->query('SELECT id FROM suppliers');
$suppliers = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Suppliers: " . count($suppliers) . " records\n";

echo "\n=== KEY FINDINGS ===\n";
echo "✅ Valid Category ID to use: " . $categories[0]['id'] . "\n";
echo "✅ Valid Unit ID to use: " . $units[0]['id'] . "\n";
echo "⚠️  Suppliers: " . count($suppliers) . " (use empty/NULL for supplier_id in forms)\n";
?>
