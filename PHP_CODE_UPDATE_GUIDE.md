# MMBPOS - PHP Code Update Guide
## Database Schema Changes - Implementation Reference

**Date:** August 15, 2026  
**Scope:** Product, Batch/Inventory, and Supplier management  
**Status:** Ready to implement

---

## CRITICAL CHANGES SUMMARY

### 1. Column Renaming - MUST UPDATE IN PHP CODE
```
OLD NAME                  → NEW NAME
──────────────────────────────────────────────
inventory.quantity        → inventory.current_quantity
products.pcs              → products.units_per_package
products.strength_per_unit → products.strength_per_quantity_unit
```

### 2. Removed Columns - MUST REMOVE FROM PHP CODE
These columns NO LONGER EXIST in products table:
- products.supplier_name ❌
- products.supplier_id ❌
- products.supplier_contact ❌
- products.supplier_address ❌
- products.supplier_email ❌
- products.unit_measurement ❌
- products.category ❌
- products.net_price ❌
- products.total_price ❌

### 3. New Columns - CAN ADD TO PHP CODE
- products.strength_per_quantity_unit (NEW)

### 4. New Table - OPTIONAL
- inventory_transactions (optional audit table)

---

## QUICK REFERENCE: WHERE TO UPDATE PHP FILES

### Search & Replace Patterns

```bash
# Find all files that need updates:
grep -r "->quantity" *.php function/*.php reusablepage/*.php --include="*.php"
grep -r "\['quantity'\]" *.php function/*.php reusablepage/*.php --include="*.php"
grep -r "pcs" *.php function/*.php reusablepage/*.php --include="*.php"
grep -r "supplier_name" *.php function/*.php reusablepage/*.php --include="*.php"
grep -r "supplier_id" *.php function/*.php reusablepage/*.php --include="*.php"
```

### Files Likely to Need Updates (Based on Workspace)
- `function/workingpos.php` - POS sales (inventory deductions)
- `function/process_transaction.php` - Transaction processing
- `function/addprodfunct.php` - Product addition
- `reusablepage/salespos.php` - Sales interface
- `reusablepage/inventorymanagement.php` - Inventory management
- `reusablepage/updateproductmodal.php` - Product updates
- `reusablepage/productmanagement.php` - Product management

---

## CODE UPDATE EXAMPLES

### ✓ Example 1: Fetching Current Quantity (WRONG → RIGHT)

**BEFORE (WRONG):**
```php
$query = "SELECT id, branded_name, quantity FROM products WHERE id = ?";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$availableQty = $row['quantity']; // ❌ WRONG - doesn't exist anymore
```

**AFTER (CORRECT):**
```php
// Query inventory (batch) table, not products table
$query = "SELECT i.id, p.branded_name, i.current_quantity, i.batch_number, i.expiry_date 
          FROM inventory i 
          JOIN products p ON i.product_id = p.id 
          WHERE i.product_id = ? 
          ORDER BY i.expiry_date ASC"; // ← FEFO: earliest expiry first
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$availableQty = $row['current_quantity']; // ✓ CORRECT

// If you need to loop through all batches for a product:
$batches = [];
while ($batch = mysqli_fetch_assoc($result)) {
    $batches[] = $batch;
}
// Use this for FEFO: select the batch with earliest expiry
```

---

### ✓ Example 2: Getting Supplier Info (WRONG → RIGHT)

**BEFORE (WRONG):**
```php
// Supplier info was IN products table
$query = "SELECT id, branded_name, supplier_name, supplier_id FROM products WHERE id = ?";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$supplierName = $row['supplier_name']; // ❌ WRONG - doesn't exist anymore
```

**AFTER (CORRECT):**
```php
// Supplier info is now linked via inventory (batch) table
$query = "SELECT p.id, p.branded_name, i.batch_number, s.supplier_name, s.contact_number, s.email
          FROM products p
          JOIN inventory i ON p.id = i.product_id
          JOIN suppliers s ON i.supplier_id = s.id
          WHERE p.id = ? AND i.current_quantity > 0
          ORDER BY i.expiry_date ASC";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$supplierName = $row['supplier_name']; // ✓ CORRECT
```

---

### ✓ Example 3: Deducting Inventory After Sale (CRITICAL)

**BEFORE (WRONG):**
```php
// This would update products table (no longer has quantity)
$query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
mysqli_query($conn, $query, $quantity, $product_id);
```

**AFTER (CORRECT):**
```php
// Must update inventory (batch) table, not products table
// 1. Find the batch with earliest expiry (FEFO)
$batch_query = "SELECT id FROM inventory 
                WHERE product_id = ? AND current_quantity >= ? 
                ORDER BY expiry_date ASC LIMIT 1";
$batch_result = mysqli_query($conn, $batch_query, $product_id, $quantity);
$batch = mysqli_fetch_assoc($batch_result);
$inventory_id = $batch['id'];

// 2. Deduct from that batch
$update_query = "UPDATE inventory 
                 SET current_quantity = current_quantity - ? 
                 WHERE id = ?";
mysqli_query($conn, $update_query, $quantity, $inventory_id);

// 3. OPTIONAL: Log this transaction (for audit trail)
// INSERT INTO inventory_transactions (inventory_id, transaction_type, quantity_change, ...)
```

---

### ✓ Example 4: Adding New Product (WRONG → RIGHT)

**BEFORE (WRONG):**
```php
// Storing supplier info in products table
$query = "INSERT INTO products 
          (branded_name, generic_name, strength, measurement_id, pcs, 
           supplier_name, supplier_id, supplier_contact, net_price, total_price) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
mysqli_query($conn, $query, $brand, $generic, $strength, $unit_id, $pcs,
             $supplier_name, $supplier_id, $supplier_contact, $price, $total);
```

**AFTER (CORRECT):**
```php
// 1. INSERT product (without supplier/pricing)
$product_query = "INSERT INTO products 
                  (branded_name, generic_name, dosage_form, strength, measurement_id,
                   strength_per_quantity, strength_per_quantity_unit,
                   barcode, package_type, units_per_package, category_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
mysqli_query($conn, $product_query, $brand, $generic, $dosage_form, $strength, 
             $unit_id, $strength_per_qty, $strength_per_qty_unit, $barcode, 
             $package_type, $units_per_pkg, $category_id);
$product_id = mysqli_insert_id($conn);

// 2. THEN INSERT batch (with supplier/pricing)
$batch_query = "INSERT INTO inventory 
                (product_id, supplier_id, batch_number, date_received,
                 manufacture_date, expiry_date, received_quantity, current_quantity,
                 purchase_cost, markup, sale_price) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
mysqli_query($conn, $batch_query, $product_id, $supplier_id, $batch_number,
             $date_received, $manufacture_date, $expiry_date, $received_qty,
             $current_qty, $purchase_cost, $markup, $sale_price);
```

---

### ✓ Example 5: Display Product with Pricing (WRONG → RIGHT)

**BEFORE (WRONG):**
```php
// Pricing was in products table
$query = "SELECT id, branded_name, generic_name, strength, category, 
                 net_price, total_price 
          FROM products WHERE id = ?";
$result = mysqli_query($conn, $query, $product_id);
$product = mysqli_fetch_assoc($result);
echo "Price: " . $product['net_price']; // ❌ WRONG - doesn't exist
```

**AFTER (CORRECT):**
```php
// Pricing is now in inventory (batch) table
$query = "SELECT p.id, p.branded_name, p.generic_name, p.strength,
                 pc.category_name, i.sale_price, i.batch_number, i.expiry_date,
                 i.current_quantity
          FROM products p
          JOIN product_categories pc ON p.category_id = pc.id
          JOIN inventory i ON p.id = i.product_id
          WHERE p.id = ? AND i.current_quantity > 0
          ORDER BY i.expiry_date ASC";
$result = mysqli_query($conn, $query, $product_id);
$product = mysqli_fetch_assoc($result);
echo "Price: " . $product['sale_price']; // ✓ CORRECT
echo "Available: " . $product['current_quantity'];
echo "Expires: " . $product['expiry_date'];
```

---

### ✓ Example 6: FEFO (First Expire, First Out) Query

**HOW TO DISPLAY INVENTORY FOR POS (FEFO Order):**
```php
// When displaying available products for sale, show batches by expiry date
$query = "SELECT i.id, i.batch_number, i.current_quantity, i.sale_price, 
                 i.expiry_date, i.manufacture_date,
                 p.branded_name, p.generic_name, p.strength, um.different_measurement
          FROM inventory i
          JOIN products p ON i.product_id = p.id
          JOIN unit_measurement um ON p.measurement_id = um.unit_id
          WHERE p.id = ? AND i.current_quantity > 0
          ORDER BY i.expiry_date ASC"; // ← Earliest expiry first
          
$result = mysqli_query($conn, $query, $product_id);

// Display in POS with warning for soon-to-expire
while ($batch = mysqli_fetch_assoc($result)) {
    $expiry = strtotime($batch['expiry_date']);
    $today = strtotime(date('Y-m-d'));
    $daysUntilExpiry = ($expiry - $today) / 86400;
    
    echo "Batch: " . $batch['batch_number'] . 
         " | Qty: " . $batch['current_quantity'] . 
         " | Price: ₱" . $batch['sale_price'] . 
         " | Expires in " . $daysUntilExpiry . " days";
         
    if ($daysUntilExpiry < 30) {
        echo " ⚠️ EXPIRING SOON"; // Red flag for expiry alert
    }
}
```

---

### ✓ Example 7: Updating Existing Product Batch

**BEFORE (WRONG - Tried to update products):**
```php
$query = "UPDATE products SET quantity = ?, price = ? WHERE id = ?";
mysqli_query($conn, $query, $new_qty, $new_price, $product_id);
```

**AFTER (CORRECT - Update inventory batch):**
```php
// Update a specific batch
$query = "UPDATE inventory 
          SET current_quantity = ?, sale_price = ?, updated_at = NOW() 
          WHERE id = ?"; // inventory.id, not product.id
mysqli_query($conn, $query, $new_qty, $new_price, $inventory_id);

// OR adjust quantity if quantity was sold:
$query = "UPDATE inventory 
          SET current_quantity = current_quantity - ?
          WHERE id = ?";
mysqli_query($conn, $query, $qty_sold, $inventory_id);
```

---

### ✓ Example 8: Product Search (Handle New Structure)

**BEFORE:**
```php
$search = "%$_GET['search']%";
$query = "SELECT * FROM products WHERE branded_name LIKE ? OR generic_name LIKE ? OR supplier_name LIKE ?";
```

**AFTER:**
```php
$search = "%$_GET['search']%";
// Search in products table (supplier joined only if needed)
$query = "SELECT DISTINCT p.* FROM products p
          WHERE p.branded_name LIKE ? OR p.generic_name LIKE ?
          ORDER BY p.branded_name";
mysqli_query($conn, $query, $search, $search);

// If you need supplier info for search results:
$query = "SELECT DISTINCT p.id, p.branded_name, p.generic_name, 
                 s.supplier_name, i.current_quantity, i.sale_price
          FROM products p
          LEFT JOIN inventory i ON p.id = i.product_id
          LEFT JOIN suppliers s ON i.supplier_id = s.id
          WHERE p.branded_name LIKE ? OR p.generic_name LIKE ? OR s.supplier_name LIKE ?
          ORDER BY p.branded_name";
```

---

## TESTING CHECKLIST

After making PHP updates, test these workflows:

- [ ] **Add Product**: Create new product and batch - verify both records created
- [ ] **Display Inventory**: Show products in stock - verify correct quantities and prices from inventory table
- [ ] **Sale Transaction**: Sell item - verify current_quantity decreases, not products.quantity
- [ ] **FEFO Display**: Check that soonest-expiry batch is shown first
- [ ] **Multiple Batches**: Same product from different suppliers - verify each batch is independent
- [ ] **Return Item**: Return a product - verify current_quantity increases correctly
- [ ] **Supplier Lookup**: Get supplier info - verify it comes from suppliers table via inventory
- [ ] **Product Edit**: Update product info - verify changes don't affect historical batches/pricing
- [ ] **Inventory Adjustment**: Adjust quantity - verify only current_quantity changes, not received_quantity
- [ ] **Pricing**: Verify batch-level pricing works and different batches can have different prices

---

## COMMON MISTAKES TO AVOID

❌ **DON'T:**
```php
$qty = $row['quantity'];                    // Wrong column name
$supplier = $row['supplier_name'];          // Column doesn't exist
UPDATE products SET quantity = ...;         // Update wrong table
SELECT net_price FROM products ...;         // Column doesn't exist
$pcs = $row['pcs'];                        // Column renamed
```

✓ **DO:**
```php
$qty = $row['current_quantity'];            // Correct column
$supplier = $row['supplier_name'];          // From suppliers table via JOIN
UPDATE inventory SET current_quantity = ...; // Update correct table
SELECT sale_price FROM inventory ...;       // Price is in inventory
$pcs = $row['units_per_package'];          // New name
```

---

## DATABASE QUERY TEMPLATE LIBRARY

### Get Product Details with Batch/Pricing Info
```php
$query = "SELECT 
            p.id, p.branded_name, p.generic_name, p.dosage_form,
            p.strength, um.different_measurement, p.strength_per_quantity,
            p.strength_per_quantity_unit, p.barcode, p.package_type,
            p.units_per_package, pc.category_name,
            i.id as batch_id, i.batch_number, i.current_quantity,
            i.received_quantity, i.purchase_cost, i.markup, i.sale_price,
            i.expiry_date, i.date_received,
            s.supplier_name, s.contact_person, s.contact_number
          FROM products p
          JOIN unit_measurement um ON p.measurement_id = um.unit_id
          JOIN product_categories pc ON p.category_id = pc.id
          LEFT JOIN inventory i ON p.id = i.product_id
          LEFT JOIN suppliers s ON i.supplier_id = s.id
          WHERE p.id = ?
          ORDER BY i.expiry_date ASC";
```

### Get All Products with Current Stock (FEFO Order)
```php
$query = "SELECT 
            p.id, p.branded_name, p.generic_name, p.strength,
            i.batch_number, i.current_quantity, i.sale_price, i.expiry_date,
            s.supplier_name
          FROM inventory i
          JOIN products p ON i.product_id = p.id
          LEFT JOIN suppliers s ON i.supplier_id = s.id
          WHERE i.current_quantity > 0
          ORDER BY i.expiry_date ASC, p.branded_name ASC";
```

### Get Low Stock Alert (For Admin)
```php
$query = "SELECT 
            p.branded_name, SUM(i.current_quantity) as total_qty,
            MIN(i.expiry_date) as earliest_expiry
          FROM inventory i
          JOIN products p ON i.product_id = p.id
          GROUP BY i.product_id
          HAVING total_qty < 20 OR earliest_expiry < DATE_ADD(NOW(), INTERVAL 30 DAY)
          ORDER BY earliest_expiry ASC";
```

---

## FINAL VALIDATION

Run this SQL to confirm structure is correct:
```sql
-- Verify products table (no supplier fields)
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'products' AND COLUMN_NAME LIKE '%supplier%';
-- Result should be EMPTY

-- Verify inventory has current_quantity
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'inventory' AND COLUMN_NAME = 'current_quantity';
-- Result should show: current_quantity INT(11)

-- Verify no pricing in products
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'products' AND COLUMN_NAME IN ('net_price', 'total_price');
-- Result should be EMPTY

-- Check foreign keys are active
SELECT CONSTRAINT_NAME, TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME IN ('products', 'inventory') AND REFERENCED_TABLE_NAME IS NOT NULL;
-- Should show: fk_products_category, fk_products_measurement, 
--              fk_inventory_product, fk_inventory_supplier
```

---

**Questions? Refer to DATABASE_UPDATE_PLAN.md for complete details.**

**Last Updated:** August 15, 2026
