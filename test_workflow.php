<?php
// Comprehensive workflow test
// Change to function directory so relative includes work
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

$tests_passed = 0;
$tests_failed = 0;

function test_result($test_name, $passed, $message = '') {
    global $tests_passed, $tests_failed;
    if ($passed) {
        echo "✅ PASS: $test_name\n";
        if ($message) echo "   $message\n";
        $tests_passed++;
    } else {
        echo "❌ FAIL: $test_name\n";
        if ($message) echo "   Error: $message\n";
        $tests_failed++;
    }
}

echo "=== TESTING PHP CODE MIGRATION ===\n\n";

try {
    $connection = Database::getConnection();
    test_result("Database connection", true);
    
    // Test 1: ProductManagement class instantiation
    echo "\n--- TEST 1: ProductManagement Class ---\n";
    $pm = new \Classes\ProductManagement($connection);
    test_result("ProductManagement class instantiation", true);
    
    // Test 2: Get all products
    echo "\n--- TEST 2: Get All Products ---\n";
    try {
        $products = $pm->getAllProducts();
        test_result("getAllProducts() works", true, "Retrieved " . count($products) . " products");
        
        if (!empty($products)) {
            $first_product = $products[0];
            
            // Verify key fields exist
            $required_fields = ['id', 'generic_name', 'units_per_package', 'current_quantity', 'strength_per_quantity_unit'];
            $missing = [];
            foreach ($required_fields as $field) {
                if (!isset($first_product[$field])) {
                    $missing[] = $field;
                }
            }
            
            if (empty($missing)) {
                test_result("Product array has all required fields", true, 
                    "Fields: id, generic_name, units_per_package, current_quantity, strength_per_quantity_unit");
            } else {
                test_result("Product array has all required fields", false, "Missing: " . implode(', ', $missing));
            }
            
            // Verify NO pricing fields in products
            $bad_fields = ['supplier_name', 'net_price', 'total_price', 'markup_percent'];
            $found = [];
            foreach ($bad_fields as $field) {
                if (isset($first_product[$field])) {
                    $found[] = $field;
                }
            }
            
            if (empty($found)) {
                test_result("Products do NOT contain removed fields", true, "No supplier/pricing in product data");
            } else {
                test_result("Products do NOT contain removed fields", false, "Found: " . implode(', ', $found));
            }
        }
    } catch (\Exception $e) {
        test_result("getAllProducts() works", false, $e->getMessage());
    }
    
    // Test 3: Get inventory batches
    echo "\n--- TEST 3: Get Inventory Batches ---\n";
    try {
        $batches = $pm->getAllInventoryBatches();
        test_result("getAllInventoryBatches() works", true, "Retrieved " . count($batches) . " batches");
        
        if (!empty($batches)) {
            $first_batch = $batches[0];
            
            // Verify batch fields
            $required_batch_fields = ['id', 'product_id', 'supplier_id', 'supplier_name', 'batch_number', 
                                     'current_quantity', 'received_quantity', 'purchase_cost', 'markup', 'sale_price', 'expiry_date'];
            $missing_batch = [];
            foreach ($required_batch_fields as $field) {
                if (!isset($first_batch[$field])) {
                    $missing_batch[] = $field;
                }
            }
            
            if (empty($missing_batch)) {
                test_result("Batch array has all required fields", true, 
                    "Includes: product_id, supplier_id, current_quantity, pricing fields");
            } else {
                test_result("Batch array has all required fields", false, "Missing: " . implode(', ', $missing_batch));
            }
        }
    } catch (\Exception $e) {
        test_result("getAllInventoryBatches() works", false, $e->getMessage());
    }
    
    // Test 4: POS getProducts query
    echo "\n--- TEST 4: POS Sales Compatibility ---\n";
    try {
        // Simulate the POS getProducts query
        $query = "SELECT 
                    p.id,
                    p.generic_name,
                    p.barcode,
                    p.units_per_package,
                    p.strength_per_quantity,
                    p.strength_per_quantity_unit,
                    SUM(i.current_quantity) as total_quantity,
                    MIN(i.expiry_date) as nearest_expiry
                FROM products p
                LEFT JOIN inventory i ON p.id = i.product_id
                GROUP BY p.id
                LIMIT 3";
        
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $pos_products = $stmt->fetchAll();
        
        test_result("POS getProducts query works", true, "Sample retrieved: " . count($pos_products) . " items");
        
        if (!empty($pos_products)) {
            foreach ($pos_products as $product) {
                echo "   - " . $product['generic_name'] . ": {$product['total_quantity']} units\n";
            }
        }
    } catch (\Exception $e) {
        test_result("POS getProducts query works", false, $e->getMessage());
    }
    
    // Test 5: Inventory quantity deduction (simulated)
    echo "\n--- TEST 5: Inventory Deduction Logic ---\n";
    try {
        // Find a product with stock
        $stmt = $connection->prepare("
            SELECT id, current_quantity FROM inventory 
            WHERE current_quantity > 0 
            LIMIT 1
        ");
        $stmt->execute();
        $inventory_item = $stmt->fetch();
        
        if ($inventory_item) {
            $initial_qty = $inventory_item['current_quantity'];
            $batch_id = $inventory_item['id'];
            
            // Test the UPDATE query that POS uses
            $test_query = "UPDATE inventory 
                          SET current_quantity = current_quantity - 5 
                          WHERE id = ? AND current_quantity >= 5";
            
            $stmt = $connection->prepare($test_query);
            $result = $stmt->execute([$batch_id]);
            
            // Verify update worked
            $stmt = $connection->prepare("SELECT current_quantity FROM inventory WHERE id = ?");
            $stmt->execute([$batch_id]);
            $new_qty = $stmt->fetchColumn();
            
            if ($new_qty == $initial_qty - 5) {
                test_result("Inventory quantity deduction works", true, 
                    "Qty: $initial_qty → $new_qty (correctly reduced by 5)");
                
                // Restore for next tests
                $restore = $connection->prepare("UPDATE inventory SET current_quantity = ? WHERE id = ?");
                $restore->execute([$initial_qty, $batch_id]);
            } else {
                test_result("Inventory quantity deduction works", false, 
                    "Qty: $initial_qty → $new_qty (expected $initial_qty - 5)");
            }
        } else {
            test_result("Inventory quantity deduction works", false, "No inventory items with stock found");
        }
    } catch (\Exception $e) {
        test_result("Inventory quantity deduction works", false, $e->getMessage());
    }
    
    // Test 6: Field name consistency
    echo "\n--- TEST 6: Field Name Consistency ---\n";
    try {
        $stmt = $connection->prepare("SELECT * FROM products LIMIT 1");
        $stmt->execute();
        $prod = $stmt->fetch();
        
        // Check for OLD field names that should have been removed/renamed
        $old_field_check = [
            'pcs' => 'Should be units_per_package',
            'quantity' => 'Should be current_quantity (in inventory table)',
            'net_price' => 'Should be in inventory.purchase_cost',
            'total_price' => 'Not applicable',
            'strength_per_unit' => 'Should be strength_per_quantity_unit'
        ];
        
        $old_fields_found = [];
        foreach (array_keys($prod) as $col) {
            if (in_array($col, array_keys($old_field_check))) {
                $old_fields_found[$col] = $old_field_check[$col];
            }
        }
        
        if (empty($old_fields_found)) {
            test_result("No old deprecated field names", true, "Schema uses new naming convention");
        } else {
            test_result("No old deprecated field names", false, 
                "Found old fields: " . implode(', ', array_keys($old_fields_found)));
        }
    } catch (\Exception $e) {
        test_result("Field name consistency check", false, $e->getMessage());
    }
    
    // Summary
    echo "\n========================================\n";
    echo "TEST SUMMARY\n";
    echo "========================================\n";
    echo "✅ Passed: $tests_passed\n";
    echo "❌ Failed: $tests_failed\n";
    echo "Total: " . ($tests_passed + $tests_failed) . "\n";
    
    if ($tests_failed == 0) {
        echo "\n🎉 ALL TESTS PASSED! Database migration is working correctly.\n";
    } else {
        echo "\n⚠️  Some tests failed. Please review above.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
