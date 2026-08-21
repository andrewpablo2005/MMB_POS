-- ============================================================================
-- MMBPOS DATABASE UPDATE - EXECUTABLE SQL SCRIPT
-- Drugstore/POS System - Product, Batch & Supplier Management
-- Date: August 15, 2026
-- ============================================================================
-- IMPORTANT: Run backups first (see comments below)
-- ============================================================================

-- ============================================================================
-- STEP 1: CREATE BACKUPS (RUN FIRST!)
-- ============================================================================
-- Uncomment these lines to create backups before making changes

/*
CREATE TABLE products_backup AS SELECT * FROM products;
CREATE TABLE inventory_backup AS SELECT * FROM inventory;
*/


-- ============================================================================
-- STEP 2: CREATE DOSAGE FORMS LOOKUP TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS dosage_forms (
  id INT(11) NOT NULL AUTO_INCREMENT,
  form_name VARCHAR(100) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_dosage_form_name (form_name),
  KEY idx_dosage_form_name (form_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO dosage_forms (form_name, is_active)
SELECT * FROM (
  SELECT 'Tablet' AS form_name, 1 AS is_active UNION ALL
  SELECT 'Capsule', 1 UNION ALL
  SELECT 'Syrup', 1 UNION ALL
  SELECT 'Suspension', 1 UNION ALL
  SELECT 'Cream', 1 UNION ALL
  SELECT 'Ointment', 1 UNION ALL
  SELECT 'Drops', 1 UNION ALL
  SELECT 'Injection', 1 UNION ALL
  SELECT 'Powder', 1 UNION ALL
  SELECT 'Granules', 1 UNION ALL
  SELECT 'Solution', 1 UNION ALL
  SELECT 'Gel', 1 UNION ALL
  SELECT 'Lotion', 1 UNION ALL
  SELECT 'Spray', 1 UNION ALL
  SELECT 'Patch', 1 UNION ALL
  SELECT 'Inhaler', 1 UNION ALL
  SELECT 'Lozenge', 1 UNION ALL
  SELECT 'Suppository', 1 UNION ALL
  SELECT 'Oral Liquid', 1 UNION ALL
  SELECT 'Chewable Tablet', 1
) AS new_forms
WHERE NOT EXISTS (
  SELECT 1 FROM dosage_forms df WHERE df.form_name = new_forms.form_name
);

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS dosage_form_id INT(11) NULL AFTER dosage_form,
  ADD CONSTRAINT fk_products_dosage_form
    FOREIGN KEY (dosage_form_id) REFERENCES dosage_forms(id)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================================
-- STEP 3: ALTER PRODUCTS TABLE
-- ============================================================================

-- 2.1: Remove redundant/incorrect columns from products
ALTER TABLE products
  DROP COLUMN IF EXISTS supplier_name,
  DROP COLUMN IF EXISTS supplier_id,
  DROP COLUMN IF EXISTS supplier_contact,
  DROP COLUMN IF EXISTS supplier_address,
  DROP COLUMN IF EXISTS supplier_email,
  DROP COLUMN IF EXISTS unit_measurement,
  DROP COLUMN IF EXISTS category,
  DROP COLUMN IF EXISTS net_price,
  DROP COLUMN IF EXISTS total_price;

-- 2.2: Add missing column for strength_per_quantity_unit
ALTER TABLE products
  ADD COLUMN strength_per_quantity_unit VARCHAR(50) DEFAULT NULL COMMENT 'Unit for strength_per_quantity (e.g., mL, g)' AFTER strength_per_quantity;

-- 2.3: Rename pcs to units_per_package
ALTER TABLE products
  CHANGE COLUMN pcs units_per_package INT(11) DEFAULT NULL COMMENT 'Units per package/blister/bottle';

-- 2.4: Improve existing column definitions for clarity
ALTER TABLE products
  MODIFY COLUMN branded_name VARCHAR(255) NOT NULL COMMENT 'Brand name (e.g., Amoxil)',
  MODIFY COLUMN generic_name VARCHAR(255) NOT NULL COMMENT 'Generic/active ingredient (e.g., Amoxicillin)',
  MODIFY COLUMN strength DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Main strength value (e.g., 500mg)',
  MODIFY COLUMN measurement_id INT(11) NOT NULL COMMENT 'FK to unit_measurement table',
  MODIFY COLUMN dosage_form VARCHAR(100) DEFAULT NULL COMMENT 'Tablet, Capsule, Syrup, Suspension, Cream, Drops',
  MODIFY COLUMN package_type VARCHAR(100) DEFAULT NULL COMMENT 'Blister, Bottle, Strip, Box, Jar, etc.',
  MODIFY COLUMN strength_per_quantity DECIMAL(10,2) DEFAULT NULL COMMENT 'Qty for strength (e.g., 5 for 5mL in syrup)',
  MODIFY COLUMN barcode VARCHAR(100) UNIQUE DEFAULT NULL COMMENT 'Barcode for POS scanning';

-- 2.5: Add indexes for performance
ALTER TABLE products
  ADD INDEX IF NOT EXISTS idx_barcode (barcode),
  ADD INDEX IF NOT EXISTS idx_category_id (category_id),
  ADD INDEX IF NOT EXISTS idx_measurement_id (measurement_id),
  ADD INDEX IF NOT EXISTS idx_branded_name (branded_name);

-- 2.6: Add foreign key constraints
ALTER TABLE products
  ADD CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_products_measurement FOREIGN KEY (measurement_id) REFERENCES unit_measurement(unit_id) ON DELETE RESTRICT ON UPDATE CASCADE;


-- ============================================================================
-- STEP 3: ALTER INVENTORY TABLE (Batch Management)
-- ============================================================================

-- 3.1: Rename quantity column to current_quantity for clarity
ALTER TABLE inventory
  CHANGE COLUMN quantity current_quantity INT(11) DEFAULT 0 COMMENT 'Current available quantity after sales/adjustments';

-- 3.2: Improve column definitions
ALTER TABLE inventory
  MODIFY COLUMN batch_number VARCHAR(255) UNIQUE DEFAULT NULL COMMENT 'Unique batch identifier',
  MODIFY COLUMN date_received DATE DEFAULT NULL COMMENT 'Date batch was received',
  MODIFY COLUMN manufacture_date DATE DEFAULT NULL COMMENT 'Manufacturing date',
  MODIFY COLUMN expiry_date DATE DEFAULT NULL COMMENT 'Expiry date (critical for FEFO)',
  MODIFY COLUMN purchase_cost DECIMAL(10,2) DEFAULT NULL COMMENT 'Cost per unit',
  MODIFY COLUMN markup DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Markup percentage',
  MODIFY COLUMN sale_price DECIMAL(10,2) DEFAULT NULL COMMENT 'Selling price per unit',
  MODIFY COLUMN received_quantity INT(11) DEFAULT 0 COMMENT 'Original quantity received in batch';

-- 3.3: Add indexes for batch operations and FEFO
ALTER TABLE inventory
  ADD INDEX IF NOT EXISTS idx_batch_number (batch_number),
  ADD INDEX IF NOT EXISTS idx_product_id (product_id),
  ADD INDEX IF NOT EXISTS idx_supplier_id (supplier_id),
  ADD INDEX IF NOT EXISTS idx_expiry_date (expiry_date) COMMENT 'For FEFO queries',
  ADD INDEX IF NOT EXISTS idx_date_received (date_received);

-- 3.4: Ensure foreign keys are correct
ALTER TABLE inventory
  ADD CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_inventory_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE;


-- ============================================================================
-- STEP 4: FIX INVENTORY DATA
-- ============================================================================

-- 4.1: Populate received_quantity from current_quantity
-- This assumes all current inventory came from original batch received
UPDATE inventory
  SET received_quantity = current_quantity
  WHERE received_quantity = 0 AND current_quantity > 0;

-- 4.2: Verify data integrity
-- SELECT id, product_id, batch_number, received_quantity, current_quantity
-- FROM inventory
-- WHERE current_quantity > received_quantity
-- LIMIT 10;


-- ============================================================================
-- STEP 5: VERIFY SUPPLIERS TABLE
-- ============================================================================

-- Suppliers table should already exist. Verify structure:
-- SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'suppliers' ORDER BY ORDINAL_POSITION;

-- Expected columns: id, supplier_name, contact_person, contact_number, email, address, supplier_type, is_active, created_at, updated_at


-- ============================================================================
-- STEP 6: CREATE INVENTORY TRANSACTIONS TABLE (OPTIONAL)
-- ============================================================================

-- This table tracks all inventory movements for audit trail
-- Use for: sales deductions, damage reports, returns, adjustments

CREATE TABLE IF NOT EXISTS inventory_transactions (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  inventory_id INT(11) NOT NULL COMMENT 'Which batch',
  transaction_type ENUM('received', 'sold', 'adjusted', 'damaged', 'returned', 'expired') NOT NULL,
  quantity_change INT(11) NOT NULL COMMENT 'Positive or negative',
  quantity_before INT(11) NOT NULL COMMENT 'Quantity before this transaction',
  quantity_after INT(11) NOT NULL COMMENT 'Quantity after this transaction',
  reason VARCHAR(255) DEFAULT NULL COMMENT 'Why: sales receipt, damage report, etc.',
  reference_id INT(11) DEFAULT NULL COMMENT 'Transaction_id or return_transaction_id',
  created_by INT(11) DEFAULT NULL COMMENT 'User who recorded this',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  KEY idx_inventory_id (inventory_id),
  KEY idx_transaction_type (transaction_type),
  KEY idx_created_at (created_at),
  KEY idx_reference_id (reference_id),
  
  CONSTRAINT fk_inv_trans_inventory FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_inv_trans_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Audit trail for all inventory quantity changes';


-- ============================================================================
-- VERIFICATION QUERIES (Run these to verify structure)
-- ============================================================================

/*
-- Check products table structure
DESCRIBE products;

-- Check inventory table structure
DESCRIBE inventory;

-- Check suppliers table structure
DESCRIBE suppliers;

-- Verify foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME IN ('products', 'inventory', 'suppliers') AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- Check indexes
SHOW INDEXES FROM products;
SHOW INDEXES FROM inventory;
SHOW INDEXES FROM suppliers;

-- Verify data integrity
SELECT COUNT(*) as total_products FROM products;
SELECT COUNT(*) as total_batches FROM inventory;
SELECT COUNT(*) as total_suppliers FROM suppliers;

-- Check for batches with quantity issues
SELECT id, product_id, batch_number, received_quantity, current_quantity
FROM inventory
WHERE current_quantity > received_quantity OR received_quantity = 0
LIMIT 20;
*/


-- ============================================================================
-- SAMPLE DATA (Optional: Test with new structure)
-- ============================================================================

/*
-- 1. INSERT SUPPLIER
INSERT INTO suppliers (supplier_name, contact_person, contact_number, email, address, supplier_type, is_active)
VALUES (
  'ABC Pharma Supply',
  'Juan Dela Cruz',
  '0917-123-4567',
  'sales@abcpharmasupply.com',
  '123 Commerce Street, Davao City, Philippines',
  'Pharmaceutical Distributor',
  1
);
-- Supplier ID: Assume 1 (adjust based on actual inserted ID)

-- 2. INSERT PRODUCT (Tablet Example)
INSERT INTO products (
  branded_name,
  generic_name,
  dosage_form,
  strength,
  measurement_id,
  strength_per_quantity,
  strength_per_quantity_unit,
  barcode,
  package_type,
  units_per_package,
  category_id,
  is_basic_necessities,
  imageproduct
) VALUES (
  'Tylenol',
  'Paracetamol',
  'Tablet',
  500,
  2,           -- mg
  NULL,
  NULL,
  '4801234567890',
  'Blister',
  10,
  18,          -- OTC
  0,
  NULL
);
-- Product ID: Assume 20

-- 3. INSERT BATCH
INSERT INTO inventory (
  product_id,
  supplier_id,
  batch_number,
  date_received,
  manufacture_date,
  expiry_date,
  received_quantity,
  current_quantity,
  purchase_cost,
  markup,
  sale_price
) VALUES (
  20,                    -- Tylenol product
  1,                     -- ABC Pharma Supply
  'BATCH-20260815-001',
  '2026-08-15',
  '2026-01-15',
  '2028-01-15',
  100,
  100,
  2.50,
  5.00,
  3.00
);

-- 4. SYRUP EXAMPLE (with strength_per_quantity)
INSERT INTO products (
  branded_name,
  generic_name,
  dosage_form,
  strength,
  measurement_id,
  strength_per_quantity,
  strength_per_quantity_unit,
  barcode,
  package_type,
  units_per_package,
  category_id
) VALUES (
  'CoughAway Syrup',
  'Dextromethorphan HBr',
  'Syrup',
  250,
  2,                     -- mg
  5,                     -- 5 mL per dose
  'mL',
  '4802000000001',
  'Bottle',
  120,                   -- 120mL bottle
  18
);
-- Product ID: Assume 21

INSERT INTO inventory (
  product_id,
  supplier_id,
  batch_number,
  date_received,
  manufacture_date,
  expiry_date,
  received_quantity,
  current_quantity,
  purchase_cost,
  markup,
  sale_price
) VALUES (
  21,
  1,
  'BATCH-21-26-0815',
  '2026-08-15',
  '2026-06-15',
  '2028-06-15',
  50,
  48,
  8.00,
  12.50,
  9.00
);
*/


-- ============================================================================
-- ROLLBACK PLAN (If something goes wrong)
-- ============================================================================

/*
-- Restore from backup
TRUNCATE products;
TRUNCATE inventory;
INSERT INTO products SELECT * FROM products_backup;
INSERT INTO inventory SELECT * FROM inventory_backup;
*/

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
-- EXECUTION ORDER:
-- 1. Uncomment and run STEP 1 (backups) FIRST
-- 2. Run STEPS 2-3 (ALTER TABLE statements)
-- 3. Run STEP 4 (fix data)
-- 4. Run STEP 5 (verify suppliers)
-- 5. Run STEP 6 (optional: create audit table)
-- 6. Run verification queries to confirm success
-- ============================================================================
