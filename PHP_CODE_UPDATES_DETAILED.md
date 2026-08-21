# PHP CODE UPDATE IMPLEMENTATION - DETAILED GUIDE
## Database Schema Changes - Line-by-Line Fixes

**Created:** August 15, 2026  
**Status:** Ready for implementation  
**Complexity:** Medium (requires targeted updates in 15+ files)

---

## QUICK REFERENCE - COLUMN NAME CHANGES

| Old Column | New Column | Table | Impact |
|-----------|-----------|-------|--------|
| `quantity` | `current_quantity` | inventory | CRITICAL - Used everywhere |
| `pcs` | `units_per_package` | products | HIGH - Product form |
| `strength_per_unit` | `strength_per_quantity_unit` | products | MEDIUM - Display only |
| ~~`supplier_name`~~ | JOIN suppliers | products→suppliers | HIGH - Removed from products |
| ~~`supplier_id`~~ | supplier_id in inventory | products→inventory | HIGH - Moved to inventory |
| ~~`net_price`~~ | purchase_cost/markup/sale_price in inventory | products→inventory | CRITICAL - Moved to inventory |
| ~~`total_price`~~ | sale_price in inventory | products→inventory | CRITICAL - Moved to inventory |

---

## FILES TO UPDATE (15 total)

### PRIORITY 1: CRITICAL (Will cause errors immediately)

1. ✅ [function/addprodfunct.php](#1-function-addprodfunctphp) - **PARTIALLY DONE**
2. ❌ [function/workingpos.php](#2-function-workingposphp) - POS sales logic
3. ❌ [function/process_transaction.php](#3-function-process_transactionphp) - Transaction processing
4. ❌ [reusablepage/inventorymanagement.php](#4-reusablepage-inventorymanagementphp) - Inventory display

### PRIORITY 2: HIGH (Form input/output issues)

5. ❌ [reusablepage/productmanagement.php](#5-reusablepage-productmanagementphp) - Product display
6. ❌ [reusablepage/updateproductmodal.php](#6-reusablepage-updateproductmodalphp) - Edit form
7. ❌ [reusablepage/addproductmodal.php](#7-reusablepage-addproductmodalphp) - Add form
8. ❌ [reusablepage/dashboard.php](#8-reusablepage-dashboardphp) - Dashboard display
9. ❌ [reusablepage/header.php](#9-reusablepage-headerphp) - Low stock alerts

### PRIORITY 3: MEDIUM (Display/Reports)

10. ❌ [reusablepage/reports.php](#10-reusablepage-reportsphp) - Report queries
11. ❌ [function/process_return.php](#11-function-process_returnphp) - Return handling
12. ❌ [function/get_transaction_details.php](#12-function-get_transaction_detailsphp) - Transaction details

### PRIORITY 4: LOW (JavaScript/Misc)

13. ❌ [js/updateprod.js](#13-js-updateprodjs) - Product quantity display
14. ❌ [js/pos_wepos.js](#14-js-pos_wepposjs) - POS quantity handling
15. ❌ [temp_check_transactions.php](#15-temp_check_transactionsphp) - Test file (can skip)

---

## DETAILED UPDATE INSTRUCTIONS

### 1. function/addprodfunct.php

**Status:** PARTIALLY DONE - Need to complete the rest of the file

**Remaining Changes:**

#### A. Update `getAllInventoryBatches()` method
Find line with `COALESCE(SUM(i.quantity), 0)` and change to:
```php
// OLD
COALESCE(SUM(i.quantity), 0) AS quantity

// NEW  
COALESCE(SUM(i.current_quantity), 0) AS current_quantity
```

#### B. Update SELECT references in `getAllInventoryBatches()`
Find: `i.quantity`
Replace with: `i.current_quantity`

Also update the table reference in the SELECT:
```php
// OLD
SELECT 
    ...
    i.quantity,

// NEW
SELECT
    ...
    i.current_quantity,
```

#### C. Update `getAllProducts()` method
This method currently has dynamic column checks for removed columns. Update it to remove references to:
- `supplier_name`, `supplier_id`, `supplier_contact`, `supplier_address`, `supplier_email`
- `net_price`, `total_price`
- `pcs` (use `units_per_package` instead)
- `strength_per_unit` (use `strength_per_quantity_unit` instead)

Remove these lines from `getAllProducts()`:
```php
// DELETE THESE:
$hasNetPrice    = $this->hasColumn('products', 'net_price');
$hasTotalPrice  = $this->hasColumn('products', 'total_price');
$hasPcs         = $this->hasColumn('products', 'pcs');
$hasSupplierName = $this->hasColumn('products', 'supplier_name');
$hasSupplierContact = $this->hasColumn('products', 'supplier_contact');
$hasSupplierAddress = $this->hasColumn('products', 'supplier_address');
$hasSupplierEmail = $this->hasColumn('products', 'supplier_email');
```

And replace:
```php
// OLD
$pcsField         = $hasPcs         ? "p.pcs AS pcs," : "0 AS pcs,";
$strengthPerUnitField = $hasStrengthPerUnit ? "p.strength_per_unit," : "'' AS strength_per_unit,";

// NEW
$pcsField         = "p.units_per_package AS units_per_package,";
$strengthPerUnitField = "p.strength_per_quantity_unit,";
```

Remove supplier field concatenation:
```php
// DELETE THIS BLOCK:
$supplierFields = '';
if ($hasSupplierName) { $supplierFields .= 'p.supplier_name,'; }
if ($hasSupplierContact) { $supplierFields .= 'p.supplier_contact,'; }
if ($hasSupplierAddress) { $supplierFields .= 'p.supplier_address,'; }
if ($hasSupplierEmail) { $supplierFields .= 'p.supplier_email,'; }
```

And in the final SQL, change:
```php
// OLD
{$netPriceField}
{$totalPriceField}
...
{$supplierFields}

// NEW  
-- Remove these fields entirely, they're in inventory now
```

Also update the aggregation to use `current_quantity`:
```php
// OLD
COALESCE(SUM(i.quantity), 0) AS quantity

// NEW
COALESCE(SUM(i.current_quantity), 0) AS current_quantity
```

#### D. Update `updateProduct()` method  
Similar changes as addProduct - remove supplier/pricing fields from products update

---

### 2. function/workingpos.php

**Critical for POS sales logic**

Find all occurrences of `['quantity']` or `->quantity` when referencing inventory and change to `['current_quantity']`

Example:
```php
// OLD - Checking available stock
if ($item['quantity'] < $qty) {
    return error...
}

// NEW
if ($item['current_quantity'] < $qty) {
    return error...
}
```

Find where inventory is deducted after sale:
```php
// OLD
UPDATE products SET quantity = quantity - ?

// NEW
UPDATE inventory SET current_quantity = current_quantity - ? WHERE id = ?
```

---

### 3. function/process_transaction.php

Similar to workingpos.php:

Find and replace all:
- `$row['quantity']` → `$row['current_quantity']`
- `$item['quantity']` → `$item['current_quantity']`
- UPDATE statements referencing products.quantity → UPDATE inventory current_quantity

---

### 4. reusablepage/inventorymanagement.php

Find line ~72 in the foreach loop:
```php
// OLD
<td><?= htmlspecialchars(($batch['quantity'] ?? 0)) ?></td>

// NEW
<td><?= htmlspecialchars(($batch['current_quantity'] ?? 0)) ?></td>
```

Also search for `$batch['quantity']` and replace all with `$batch['current_quantity']`

---

### 5. reusablepage/productmanagement.php

**Remove pricing columns from display:**

Find the table header row and remove:
```php
<th>Net Price</th>
<th>Total Price</th>
```

Find corresponding table data cells and remove:
```php
<td>₱ <?= number_format($prod['net_price'], 2) ?></td>
<td>₱ <?= number_format($prod['total_price'], 2) ?></td>
```

Replace quantity display:
```php
// OLD
<td><?= $prod['quantity'] ?></td>

// NEW
<td><?= $prod['current_quantity'] ?? 0 ?></td>
```

Update column headers:
```php
// OLD
<th>Package</th>

// NEW  
<th>Units/Package</th>
```

---

### 6. reusablepage/updateproductmodal.php

**Remove Supplier Section Entirely**

Delete the entire "Supplier Information" section:
```php
<!-- DELETE FROM HERE -->
<hr class="my-4">

<h6 class="mb-3 text-secondary fw-bold">Supplier Information</h6>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="supplier_name_<?= $prod['id'] ?>" class="form-label">Supplier Name</label>
        ...
    </div>
    ...
</div>
<!-- TO HERE -->
```

**Remove Pricing Section Entirely**

Delete the "Pricing, Stock and Expiration" section:
```php
<!-- DELETE FROM HERE -->
<h6 class="mb-3 text-secondary fw-bold">Pricing, Stock and Expiration</h6>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="net_price_<?= $prod['id'] ?>">Net Price...</label>
        ...
    </div>
    ...
</div>
<!-- TO HERE -->
```

**Keep only:**
- Batch Number field
- Expiry Date field
- Basic Necessities checkbox

**Important:** The form should now only edit product master data, NOT batch data. Batch/supplier/pricing should be edited in a separate inventory batch form.

---

### 7. reusablepage/addproductmodal.php

**Same as updateproductmodal.php:**
- Delete "Supplier Information" section
- Delete "Pricing" fields (net_price, total_price, markup_percent)
- Keep only product master fields

---

### 8. reusablepage/dashboard.php

Find line ~287:
```php
// OLD
<td><span class="badge-warn"><?php echo $item['stock_quantity']; ?></span></td>

// NEW
<td><span class="badge-warn"><?php echo $item['current_quantity']; ?></span></td>
```

Search for any `['quantity']` and replace with `['current_quantity']`

---

### 9. reusablepage/header.php

Find line ~26:
```php
// OLD  
'message' => htmlspecialchars($item['product_name']) . ' has only ' . $item['quantity'] . ' unit(s) left.',

// NEW
'message' => htmlspecialchars($item['product_name']) . ' has only ' . $item['current_quantity'] . ' unit(s) left.',
```

---

### 10. reusablepage/reports.php

Find all SQL queries that reference `i.quantity` and change to `i.current_quantity`

Also find any references to `p.net_price`, `p.total_price` and if they exist, remove them or use inventory.sale_price instead

---

### 11. function/process_return.php

Find quantity deduction logic:
```php
// OLD
UPDATE products SET quantity = quantity + ?

// NEW
UPDATE inventory SET current_quantity = current_quantity + ? WHERE id = ?
```

---

### 12. function/get_transaction_details.php

Find SELECT statements and replace:
```php
// OLD
SELECT ... i.quantity ...

// NEW
SELECT ... i.current_quantity ...
```

---

### 13. js/updateprod.js

Find line ~8:
```javascript
// OLD
document.getElementById("stock_quantity").value = prod.quantity;

// NEW
document.getElementById("stock_quantity").value = prod.current_quantity;
```

---

### 14. js/pos_wepos.js

Find line ~151:
```javascript
// OLD
const pcs = parseInt(cardEl.dataset.pcs) || 1;

// NEW  
const unitsPerPackage = parseInt(cardEl.dataset.units_per_package) || 1;
```

Also update data attributes in HTML that reference `pcs` to use `units_per_package`

---

### 15. temp_check_transactions.php

This is a test file - can skip or update for consistency:
```php
// OLD
ti.quantity,

// NEW
ti.quantity,  // Keep this - it's from transaction_items, not inventory
```

---

## IMPLEMENTATION ORDER

### Phase 1: Core Updates (Do First)
1. ✅ database_update.sql (DONE)
2. ❌ addprodfunct.php (COMPLETE remaining changes)
3. ❌ workingpos.php
4. ❌ process_transaction.php

### Phase 2: Display Updates (Do Second)
5. ❌ inventorymanagement.php
6. ❌ productmanagement.php
7. ❌ dashboard.php
8. ❌ reports.php
9. ❌ header.php

### Phase 3: Forms & Modals (Do Third)
10. ❌ updateproductmodal.php
11. ❌ addproductmodal.php
12. ❌ process_return.php
13. ❌ get_transaction_details.php

### Phase 4: JavaScript & Misc (Do Last)
14. ❌ js/updateprod.js
15. ❌ js/pos_wepos.js
16. ❌ temp_check_transactions.php

---

## TESTING AFTER EACH PHASE

### After Phase 1
- [ ] Can add product without errors
- [ ] Can add inventory batch
- [ ] Can see inventory in inventory management page

### After Phase 2
- [ ] Products display without pricing errors
- [ ] Inventory quantities show correctly
- [ ] Reports display without errors
- [ ] Dashboard shows current quantities

### After Phase 3
- [ ] Can edit product without errors
- [ ] Can add product without pricing fields
- [ ] Can process returns without quantity errors
- [ ] Transaction details display correctly

### After Phase 4
- [ ] POS product quantities display
- [ ] Product update JavaScript works
- [ ] No console errors in browser

---

## VALIDATION CHECKLIST

After updating all files, verify:

- [ ] No PHP errors in error_log
- [ ] No "undefined index" notices for removed columns
- [ ] Products can be added/edited
- [ ] Inventory batches can be added/edited
- [ ] Sales process correctly deducts `current_quantity`
- [ ] Returns correctly increase `current_quantity`
- [ ] No pricing shown in product master forms
- [ ] Supplier info not shown in product forms
- [ ] All quantity references use `current_quantity` from inventory
- [ ] Units per package displays correctly

---

## ROLLBACK OPTION

If critical issues arise after updates:
```sql
-- Restore products from backup
INSERT INTO products SELECT * FROM products_backup WHERE id NOT IN (SELECT id FROM products);

-- Restore inventory from backup
INSERT INTO inventory SELECT * FROM inventory_backup WHERE id NOT IN (SELECT id FROM inventory);
```

---

**Next Step:** Begin Phase 1 updates starting with completing addprodfunct.php

**Estimated Time:** 3-5 hours total for all phases  
**Difficulty:** Medium - mostly straightforward replacements with some logic updates
