<?php
// Change to the function directory so relative includes work
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

// Simulate the add product form POST data
$_POST = [
    'addProduct' => '1',
    'generic_name' => 'Test Product',
    'branded_name' => 'Test Brand',
    'strength' => '500',
    'unit_measurement' => '1',
    'barcode' => 'TEST-' . time(),
    'category_id' => '26',
    'units_per_package' => '10',
    'package_type' => 'Box',
    'dosage_form' => 'Tablet',
    'strength_per_quantity' => '500',
    'strength_per_quantity_unit' => 'mg',
    'is_basic_necessities' => '0',
    
    // Batch/Inventory fields
    'expiry_date' => '2027-12-31',
    'batch_number' => 'TEST-BATCH-' . time(),
    'supplier_id' => '',  // Empty - should become NULL
    'purchase_cost' => '10.00',
    'markup' => '20',
    'sale_price' => '15.00',
    'received_quantity' => '5'
];

// No file upload for this test
$_FILES = [];

try {
    $db = Database::getConnection();
    $pm = new \Classes\ProductManagement($db);
    
    echo "=== TESTING PRODUCT INSERTION WITH EMPTY SUPPLIER_ID ===\n";
    echo "POST data supplier_id: '" . $_POST['supplier_id'] . "' (empty string)\n\n";
    
    $result = $pm->addProduct();
    
    if ($result) {
        echo "✅ SUCCESS: " . $pm->getResponse() . "\n";
    } else {
        echo "❌ FAILED: " . $pm->getResponse() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . " (Line " . $e->getLine() . ")\n";
}
?>
