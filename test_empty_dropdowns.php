<?php
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

// Simulate form submission with EMPTY unit_measurement and category_id (user didn't select)
$_POST = [
    'addProduct' => '1',
    'generic_name' => 'Test Product',
    'branded_name' => 'Test Brand',
    'strength' => '500',
    'unit_measurement' => '',  // ❌ USER DIDN'T SELECT - this causes FK error
    'barcode' => 'TEST-' . time(),
    'category_id' => '',  // ❌ USER DIDN'T SELECT - this causes FK error
    'units_per_package' => '10',
    'package_type' => 'Box',
    'dosage_form' => 'Tablet',
    'strength_per_quantity' => '500',
    'strength_per_quantity_unit' => 'mg',
    'is_basic_necessities' => '0',
    
    // Batch/Inventory fields
    'expiry_date' => '2027-12-31',
    'batch_number' => 'TEST-BATCH-' . time(),
    'supplier_id' => '',  // Empty - should be NULL
    'purchase_cost' => '10.00',
    'markup' => '20',
    'sale_price' => '15.00',
    'received_quantity' => '5'
];

$_FILES = [];

try {
    $db = Database::getConnection();
    $pm = new \Classes\ProductManagement($db);
    
    echo "=== TESTING WITH EMPTY DROPDOWNS (User didn't select) ===\n";
    echo "Category ID: '" . $_POST['category_id'] . "' (empty)\n";
    echo "Unit Measurement: '" . $_POST['unit_measurement'] . "' (empty)\n";
    echo "Supplier ID: '" . $_POST['supplier_id'] . "' (empty/NULL)\n\n";
    
    $result = $pm->addProduct();
    
    if ($result) {
        echo "✅ SUCCESS: " . $pm->getResponse() . "\n";
    } else {
        echo "❌ FAILED: " . $pm->getResponse() . "\n";
        echo "\n=== DIAGNOSIS ===\n";
        echo "This is the EXACT error you're getting when submitting the form!\n";
        echo "The form is sending empty category_id and/or unit_measurement.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
