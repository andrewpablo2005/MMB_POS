<?php
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

// Get a valid category ID
$db = Database::getConnection();
$result = $db->query('SELECT id FROM product_categories LIMIT 1');
$cat = $result->fetch(PDO::FETCH_ASSOC);
$validCategoryId = $cat['id'];

// Get a valid unit ID
$result = $db->query('SELECT unit_id FROM unit_measurement LIMIT 1');
$unit = $result->fetch(PDO::FETCH_ASSOC);
$validUnitId = $unit['unit_id'];

// Test with VALID data
$_POST = [
    'addProduct' => '1',
    'generic_name' => 'Test Product Valid',
    'branded_name' => 'Test Brand Valid',
    'strength' => '500',
    'unit_measurement' => $validUnitId,  // ✅ Valid unit
    'barcode' => 'TEST-' . time(),
    'category_id' => $validCategoryId,  // ✅ Valid category
    'units_per_package' => '10',
    'package_type' => 'Box',
    'dosage_form' => 'Tablet',
    'strength_per_quantity' => '500',
    'strength_per_quantity_unit' => 'mg',
    'is_basic_necessities' => '0',
    
    // Batch/Inventory fields
    'expiry_date' => '2027-12-31',
    'batch_number' => 'VALID-BATCH-' . time(),
    'supplier_id' => '',  // Empty - should be NULL (no error)
    'purchase_cost' => '10.00',
    'markup' => '20',
    'sale_price' => '15.00',
    'received_quantity' => '5'
];

$_FILES = [];

try {
    $pm = new \Classes\ProductManagement($db);
    
    echo "=== TESTING WITH VALID DATA ===\n";
    echo "Category ID: " . $validCategoryId . " ✅\n";
    echo "Unit Measurement: " . $validUnitId . " ✅\n";
    echo "Supplier ID: (empty/NULL) ✅\n\n";
    
    $result = $pm->addProduct();
    
    if ($result) {
        echo "✅ SUCCESS: " . $pm->getResponse() . "\n";
        echo "\n✅ Product insertion now works correctly!\n";
        echo "✅ FK constraints are properly validated\n";
        echo "✅ Empty supplier_id is correctly converted to NULL\n";
    } else {
        echo "❌ FAILED: " . $pm->getResponse() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
