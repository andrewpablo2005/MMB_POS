<?php
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

// Simulate POST data for adding a batch
$_POST = [
    'addInventoryBatch' => '1',
    'product_id' => '1',
    'quantity' => '10',
    'expiry_date' => '2027-12-31',
    'batch_number' => 'TEST-BATCH-001',
    'supplier_id' => '',  // Empty - should become NULL
    'purchase_cost' => '25.50',
    'markup' => '30',
    'sale_price' => '45.00'
];

try {
    $db = Database::getConnection();
    $pm = new \Classes\ProductManagement($db);
    
    echo "=== TESTING BATCH INSERTION ===\n";
    echo "Attempting to add batch with data:\n";
    echo "  Product ID: 1\n";
    echo "  Quantity: 10\n";
    echo "  Batch Number: TEST-BATCH-001\n";
    echo "  Supplier ID: (empty/NULL)\n";
    echo "  Purchase Cost: 25.50\n";
    echo "  Markup: 30%\n";
    echo "  Sale Price: 45.00\n\n";
    
    $result = $pm->addInventoryBatch();
    
    if ($result) {
        echo "✅ SUCCESS: " . $pm->getResponse() . "\n";
        
        // Verify the batch was created
        $stmt = $db->prepare("SELECT * FROM inventory WHERE batch_number = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute(['TEST-BATCH-001']);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "\nBatch created successfully:\n";
        foreach ($batch as $key => $value) {
            echo sprintf("  %s: %s\n", $key, $value ?? 'NULL');
        }
        
        // Clean up
        $db->prepare("DELETE FROM inventory WHERE batch_number = ?")->execute(['TEST-BATCH-001']);
    } else {
        echo "❌ FAILED: " . $pm->getResponse() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . " (Line " . $e->getLine() . ")\n";
}
?>
