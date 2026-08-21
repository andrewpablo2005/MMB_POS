# MMBPOS DATABASE UPDATE PLAN
## Drugstore/POS System - Product, Batch & Supplier Management

**Date:** August 15, 2026  
**Status:** Ready for Implementation

---

## PART A: DATABASE CHANGES REQUIRED

### Summary of Actions

| Table | Action | Reason |
|-------|--------|--------|
| `products` | MODIFY | Remove supplier fields, fix redundant columns, add strength_per_quantity_unit |
| `unit_measurement` | UNCHANGED | Already exists with 15 units (sufficient) |
| `product_categories` | UNCHANGED | Already properly structured |
| `suppliers` | UNCHANGED | Structure is correct (currently empty) |
| `inventory` | RENAME CONCEPTUALLY + MODIFY | Rename to `inventory_batches`, clarify columns, fix data |
| `inventory_transactions` | CREATE | Track inventory movements (optional but recommended) |

### Detailed Changes

#### 1. **PRODUCTS TABLE** - MODIFY
**Issues:**
- Contains supplier info that duplicates `suppliers` table
- Redundant: both `unit_measurement` AND `measurement_id`
- Redundant: both `category` (text) AND `category_id` (FK)
- Pricing should be in batch/inventory, not product master
- Missing: `strength_per_quantity_unit` column
- Unclear fields: `pcs`, `strength_per_unit`

**Changes:**
- REMOVE: `supplier_name`, `supplier_id`, `supplier_contact`, `supplier_address`, `supplier_email`
- REMOVE: `unit_measurement` (keep only `measurement_id` with FK to `unit_measurement`)
- REMOVE: `category` (keep only `category_id` with FK to `product_categories`)
- REMOVE: `net_price`, `total_price` (these belong in batches, not product master)
- RENAME: `pcs` → `units_per_package`
- RENAME: `strength_per_unit` → `strength_per_quantity_unit`
- ADD: `strength_per_quantity_unit` COLUMN if missing (VARCHAR 50)
- CLARIFY: All data types and constraints

**Data Migration Strategy:**
- Supplier data in products will be orphaned (no FK). This is acceptable since suppliers should be linked at batch level, not product level.
- Net/total price data cannot be preserved in products - will be managed at batch level going forward.
- All other product data preserved.

---

#### 2. **INVENTORY TABLE** - MODIFY & CONCEPTUALLY RENAME TO `inventory_batches`
**Note:** The table will remain named `inventory` in database for backward compatibility with existing PHP code. Conceptually treat it as batches.

**Issues:**
- All `received_quantity` values are 0 (incorrect)
- Column naming could be clearer
- Need to distinguish received vs. current quantity better
- Missing historical tracking

**Changes:**
- RENAME COLUMN: `quantity` → `current_quantity` (for clarity)
- CLARIFY: `received_quantity` is the original batch quantity received
- ENSURE: `current_quantity` ≤ `received_quantity`
- ADD: Check constraint if database supports it
- Keep: All other columns are good (batch_number, dates, supplier_fk, costs, markup, price, expiry_date)
- ADD INDEX: On `expiry_date` for FEFO queries (already exists as inventory_disposals references it)

**Data Migration:**
- Current `quantity` values will be preserved as `current_quantity` (via column rename or copy)
- Fix `received_quantity` values based on `current_quantity` (assume received = current for now)

---

#### 3. **SUPPLIERS TABLE** - UNCHANGED
Structure is correct. No changes needed. Already has all required fields.

---

#### 4. **UNIT_MEASUREMENT TABLE** - UNCHANGED
Has 15 units. Structure is good for products and strength representation.

---

#### 5. **PRODUCT_CATEGORIES TABLE** - UNCHANGED
Structure is correct with VAT, senior, and PWD discount flags.

---

#### 6. **INVENTORY_TRANSACTIONS TABLE** - CREATE (Optional but Recommended)
**Purpose:** Track inventory movements (adjustments, returns, damage) for audit trail.

**Structure:**
```
inventory_transactions:
  - id (PK)
  - inventory_id (FK to inventory)
  - transaction_type (ENUM: 'received', 'sold', 'adjusted', 'damaged', 'returned')
  - quantity_change (INT) - can be negative
  - quantity_before (INT)
  - quantity_after (INT)
  - reason (VARCHAR 255)
  - reference_id (INT) - FK to transaction_id or return_transaction_id if applicable
  - created_at (DATETIME)
  - created_by (INT) - FK to users.id
```

---

## PART B: EXACT SQL STATEMENTS

### STEP 1: Backup (RECOMMENDED - Run First)
```sql
-- Create backup of current products and inventory data
CREATE TABLE products_backup AS SELECT * FROM products;
CREATE TABLE inventory_backup AS SELECT * FROM inventory;
```

---

### STEP 2: ALTER PRODUCTS TABLE

```sql
-- Remove redundant columns
ALTER TABLE products
  DROP COLUMN supplier_name,
  DROP COLUMN supplier_id,
  DROP COLUMN supplier_contact,
  DROP COLUMN supplier_address,
  DROP COLUMN supplier_email,
  DROP COLUMN unit_measurement,
  DROP COLUMN category,
  DROP COLUMN net_price,
  DROP COLUMN total_price;

-- Rename and add clarity columns
ALTER TABLE products
  CHANGE COLUMN pcs units_per_package INT(11) DEFAULT NULL COMMENT 'Number of units per package/blister/bottle',
  ADD COLUMN strength_per_quantity_unit VARCHAR(50) DEFAULT NULL COMMENT 'Unit of measurement for strength_per_quantity (e.g., mL, g)' AFTER strength_per_quantity;

-- Ensure proper data types and constraints
ALTER TABLE products
  MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  MODIFY COLUMN branded_name VARCHAR(255) NOT NULL,
  MODIFY COLUMN generic_name VARCHAR(255) NOT NULL,
  MODIFY COLUMN strength DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Main strength value (e.g., 500 for 500mg tablet)',
  MODIFY COLUMN measurement_id INT(11) NOT NULL COMMENT 'FK to unit_measurement for strength unit',
  MODIFY COLUMN strength_per_quantity DECIMAL(10,2) DEFAULT NULL COMMENT 'Quantity value for strength (e.g., 5 for 5mL in syrup)',
  MODIFY COLUMN barcode VARCHAR(100) UNIQUE COMMENT 'Unique barcode for POS scanning',
  MODIFY COLUMN category_id INT(11) DEFAULT NULL,
  MODIFY COLUMN dosage_form VARCHAR(100) DEFAULT NULL COMMENT 'Form: Tablet, Capsule, Syrup, Suspension, Cream, Drops, etc.',
  MODIFY COLUMN package_type VARCHAR(100) DEFAULT NULL COMMENT 'Package type: Blister, Bottle, Strip, Box, Jar, etc.';

-- Add indexes for common queries
ALTER TABLE products
  ADD INDEX idx_barcode (barcode),
  ADD INDEX idx_category_id (category_id),
  ADD INDEX idx_measurement_id (measurement_id);

-- Add foreign key constraints
ALTER TABLE products
  ADD CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_products_measurement FOREIGN KEY (measurement_id) REFERENCES unit_measurement(unit_id) ON DELETE RESTRICT ON UPDATE CASCADE;
```

---

### STEP 3: ALTER INVENTORY TABLE (Batch Management)

```sql
-- Rename quantity to current_quantity for clarity
ALTER TABLE inventory
  CHANGE COLUMN quantity current_quantity INT(11) DEFAULT 0 COMMENT 'Current available quantity after sales/adjustments';

-- Add missing columns if needed
ALTER TABLE inventory
  ADD COLUMN IF NOT EXISTS received_quantity INT(11) DEFAULT 0 COMMENT 'Original quantity received in batch' AFTER current_quantity;

-- Ensure proper data types and constraints
ALTER TABLE inventory
  MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  MODIFY COLUMN product_id INT(11) NOT NULL,
  MODIFY COLUMN supplier_id INT(11) DEFAULT NULL,
  MODIFY COLUMN batch_number VARCHAR(255) UNIQUE COMMENT 'Unique batch identifier',
  MODIFY COLUMN date_received DATE DEFAULT NULL COMMENT 'Date batch was received',
  MODIFY COLUMN manufacture_date DATE DEFAULT NULL COMMENT 'Manufacturing date',
  MODIFY COLUMN expiry_date DATE DEFAULT NULL COMMENT 'Expiry date for FEFO tracking',
  MODIFY COLUMN purchase_cost DECIMAL(10,2) DEFAULT NULL COMMENT 'Cost per unit at purchase',
  MODIFY COLUMN markup DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Markup percentage',
  MODIFY COLUMN sale_price DECIMAL(10,2) DEFAULT NULL COMMENT 'Selling price per unit';

-- Add indexes for batch operations
ALTER TABLE inventory
  ADD INDEX idx_batch_number (batch_number),
  ADD INDEX idx_product_id (product_id),
  ADD INDEX idx_supplier_id (supplier_id),
  ADD INDEX idx_expiry_date (expiry_date) COMMENT 'For FEFO queries',
  ADD INDEX idx_date_received (date_received);

-- Ensure foreign keys are correct
ALTER TABLE inventory
  ADD CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT fk_inventory_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add check constraint (if MariaDB 10.4.32 supports it)
-- ALTER TABLE inventory ADD CONSTRAINT chk_quantity_logic CHECK (current_quantity <= received_quantity);
-- Note: This may need to be enforced at application level if constraint not supported
```

---

### STEP 4: Fix Inventory Data (Correct received_quantity)

```sql
-- Set received_quantity = current_quantity for existing records
-- This assumes all current quantity is from the original received batch
UPDATE inventory
  SET received_quantity = current_quantity
  WHERE received_quantity = 0;
```

---

### STEP 5: Ensure SUPPLIERS Table is Correct

```sql
-- Verify suppliers table structure (no changes needed, but verify)
-- This table should already exist from your SQL

-- Populate sample if needed (see Part E)
```

---

### STEP 6: CREATE INVENTORY TRANSACTIONS TABLE (Optional but Recommended)

```sql
CREATE TABLE IF NOT EXISTS inventory_transactions (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  inventory_id INT(11) NOT NULL,
  transaction_type ENUM('received', 'sold', 'adjusted', 'damaged', 'returned') NOT NULL,
  quantity_change INT(11) NOT NULL COMMENT 'Can be negative',
  quantity_before INT(11) NOT NULL,
  quantity_after INT(11) NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  reference_id INT(11) DEFAULT NULL COMMENT 'FK to transactions or return_transactions',
  created_by INT(11) DEFAULT NULL COMMENT 'FK to users.id',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  KEY idx_inventory_id (inventory_id),
  KEY idx_transaction_type (transaction_type),
  KEY idx_created_at (created_at),
  KEY idx_reference_id (reference_id),
  
  CONSTRAINT fk_inv_trans_inventory FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_inv_trans_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## PART C: FINAL RECOMMENDED SCHEMA

### 1. PRODUCTS (Product Master)
```sql
CREATE TABLE products (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  branded_name VARCHAR(255) NOT NULL COMMENT 'Brand name (e.g., Amoxil)',
  generic_name VARCHAR(255) NOT NULL COMMENT 'Generic/active ingredient (e.g., Amoxicillin)',
  dosage_form VARCHAR(100) DEFAULT NULL COMMENT 'Tablet, Capsule, Syrup, Suspension, Cream, Drops, etc.',
  strength DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Main strength value (e.g., 500 for 500mg)',
  measurement_id INT(11) NOT NULL COMMENT 'FK to unit_measurement table',
  strength_per_quantity DECIMAL(10,2) DEFAULT NULL COMMENT 'Conditional: Qty value (e.g., 5 for 5mL in syrups)',
  strength_per_quantity_unit VARCHAR(50) DEFAULT NULL COMMENT 'Unit for strength_per_quantity (e.g., mL, g)',
  barcode VARCHAR(100) UNIQUE COMMENT 'EAN/barcode for POS scanning',
  package_type VARCHAR(100) DEFAULT NULL COMMENT 'Blister, Bottle, Strip, Box, Jar, etc.',
  units_per_package INT(11) DEFAULT NULL COMMENT 'Number of units per package',
  category_id INT(11) DEFAULT NULL COMMENT 'FK to product_categories',
  classification_id INT(11) DEFAULT NULL COMMENT 'Additional classification if needed',
  imageproduct VARCHAR(500) DEFAULT NULL,
  is_basic_necessities TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'VAT classification flag',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  UNIQUE KEY unique_barcode (barcode),
  KEY idx_category_id (category_id),
  KEY idx_measurement_id (measurement_id),
  KEY idx_generic_name (generic_name),
  KEY idx_branded_name (branded_name),
  
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_products_measurement FOREIGN KEY (measurement_id) REFERENCES unit_measurement(unit_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Product master - defines all medicines and items sold';
```

### 2. INVENTORY (Batch Management) - Rename Conceptually
```sql
CREATE TABLE inventory (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  product_id INT(11) NOT NULL COMMENT 'FK to products',
  supplier_id INT(11) DEFAULT NULL COMMENT 'FK to suppliers - which supplier provided this batch',
  batch_number VARCHAR(255) UNIQUE COMMENT 'Unique batch identifier',
  date_received DATE DEFAULT NULL COMMENT 'Date batch arrived',
  manufacture_date DATE DEFAULT NULL COMMENT 'Manufacturing date',
  expiry_date DATE DEFAULT NULL COMMENT 'Expiry date (critical for FEFO)',
  purchase_cost DECIMAL(10,2) DEFAULT NULL COMMENT 'Cost per unit',
  markup DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Markup percentage',
  sale_price DECIMAL(10,2) DEFAULT NULL COMMENT 'Selling price per unit',
  received_quantity INT(11) DEFAULT 0 COMMENT 'Original quantity in batch',
  current_quantity INT(11) DEFAULT 0 COMMENT 'Available qty after sales/adjustments',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  UNIQUE KEY unique_batch (batch_number),
  KEY idx_product_id (product_id),
  KEY idx_supplier_id (supplier_id),
  KEY idx_expiry_date (expiry_date) COMMENT 'FEFO priority',
  KEY idx_date_received (date_received),
  
  CONSTRAINT fk_inventory_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_inventory_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Batch/Inventory tracking - one row per batch per product';
```

### 3. SUPPLIERS
```sql
CREATE TABLE suppliers (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  supplier_name VARCHAR(255) NOT NULL UNIQUE COMMENT 'Pharmaceutical distributor, wholesaler, etc.',
  contact_person VARCHAR(255) DEFAULT NULL,
  contact_number VARCHAR(20) DEFAULT NULL,
  email VARCHAR(255) DEFAULT NULL,
  address TEXT DEFAULT NULL COMMENT 'Full address',
  supplier_type VARCHAR(100) DEFAULT NULL COMMENT 'Pharmaceutical Distributor, Wholesaler, Importer, etc.',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  UNIQUE KEY unique_supplier_name (supplier_name),
  KEY idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Supplier/Distributor master data';
```

### 4. UNIT_MEASUREMENT (Unchanged)
```
Contains: mcg, mg, g, kg, µL, mL, L, mm, cm, m, %, IU, mEq, mmol, Units
```

### 5. PRODUCT_CATEGORIES (Unchanged)
```
Contains: Prescription Medicines, OTC, Medical Supplies, Vitamins, etc.
15 categories with VAT and discount flags
```

### 6. INVENTORY_TRANSACTIONS (New - Optional)
```sql
CREATE TABLE inventory_transactions (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  inventory_id INT(11) NOT NULL,
  transaction_type ENUM('received', 'sold', 'adjusted', 'damaged', 'returned') NOT NULL,
  quantity_change INT(11) NOT NULL,
  quantity_before INT(11) NOT NULL,
  quantity_after INT(11) NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  reference_id INT(11) DEFAULT NULL,
  created_by INT(11) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY idx_inventory_id (inventory_id),
  KEY idx_created_at (created_at),
  
  CONSTRAINT fk_inv_trans_inventory FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Audit trail for inventory changes';
```

---

## PART D: RELATIONSHIPS

### Product Master to Batch to Supplier Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                       PRODUCTS                                   │
│  (Unique product definitions - Tylenol, Biogesic, etc.)         │
│                                                                   │
│  ├─ product_id (PK)                                             │
│  ├─ branded_name                                                 │
│  ├─ generic_name                                                 │
│  ├─ dosage_form                                                  │
│  ├─ strength + measurement_id (FK) ──┐                          │
│  ├─ strength_per_quantity[_unit]      │ strength representation │
│  ├─ category_id (FK) ──────┐          │                        │
│  └─ barcode (UNIQUE)        │         │                        │
└──────────────────┬──────────┼─────────┘                         │
                   │1         │                                    │
                   │          ├─────────────────────────────────┐ │
                   │          │                                 │ │
                   ▼M   1▼K   ▼1K                               │ │
        ┌──────────────────────────┐                             │ │
        │  PRODUCT_CATEGORIES      │                             │ │
        │  - id (PK)               │                             │ │
        │  - category_name         │                             │ │
        │  - has_vat, discounts    │                             │ │
        └──────────────────────────┘                             │ │
                                      ┌────────────────────┐     │ │
                                      │UNIT_MEASUREMENT    │◄────┘ │
                                      │- unit_id (PK)      │       │
                                      │- different_measurement │   │
                                      └────────────────────┘       │
                                                                   │
                                    ┌─────────────────────────────┘
                                    │
        ┌───────────────────────────┼──────────────┐
        │                           │              │
        │1                          │              │
        ▼M                          │              │
┌─────────────────────────┐         │              │
│     INVENTORY           │◄────────┼──────────────┤
│   (Batches/Lots)        │         │              │
│                         │         │              │
│  ├─ id (PK)             │    product_id          │
│  ├─ product_id (FK) ────┤         │              │
│  ├─ supplier_id (FK) ───┼─────────┼──────────┐   │
│  ├─ batch_number (UNQ)  │         │          │   │
│  ├─ date_received       │         │          │   │
│  ├─ manufacture_date    │         │          │   │
│  ├─ expiry_date ◄──────┼─────FEFO priority  │   │
│  ├─ received_quantity   │         │          │   │
│  ├─ current_quantity    │         │          │   │
│  ├─ purchase_cost       │         │          │   │
│  ├─ markup              │         │          │   │
│  └─ sale_price          │         │          │   │
└────────┬────────────────┘         │          │   │
         │1                         │          │   │
         ▼M                         │          │   │
    ┌────────────────────┐         │          │   │
    │SUPPLIERS           │◄────────┘          │   │
    │                    │                    │   │
    ├─ id (PK)          │                    │   │
    ├─ supplier_name (UNQ)                   │   │
    ├─ contact_person   │                    │   │
    ├─ contact_number   │                    │   │
    ├─ email            │                    │   │
    ├─ address          │                    │   │
    ├─ supplier_type    │                    │   │
    └─ is_active        │◄───────────────────┘   │
         ▲1                                      │
         │                                       │
         └─────── Relationship preserved ────────┘

KEY CONCEPT:
- Multiple SUPPLIERS can provide the SAME PRODUCT
- Each BATCH is ONE relationship: PRODUCT + SUPPLIER + unique lot
- Inventory is tracked PER BATCH (not per product)
- Pricing varies PER BATCH (different suppliers, different costs)
- Expiry dates are tracked PER BATCH (critical for medicines)
```

### Database Relationships Summary

| Relationship | Type | Purpose |
|--------------|------|---------|
| products.category_id → product_categories.id | N:1 | Categorize products |
| products.measurement_id → unit_measurement.unit_id | N:1 | Define strength units |
| inventory.product_id → products.id | N:1 | Each batch links to a product |
| inventory.supplier_id → suppliers.id | N:1 | Each batch from a supplier |
| transactions.discount_id → discounts.id | N:1 | Apply discounts |
| transaction_items.product_id → products.id | N:1 | Track sold products |
| return_items.product_id → products.id | N:1 | Track returns |

---

## PART E: SAMPLE DATA

### Step 1: Create Supplier

```sql
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
-- Assume supplier ID = 1
```

### Step 2: Create Product

```sql
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
  2,           -- measurement_id for 'mg'
  NULL,        -- No strength_per_quantity for tablets
  NULL,        -- No strength_per_quantity_unit for tablets
  '4801234567890',
  'Blister',
  10,          -- 10 tablets per blister
  18,          -- category_id for 'Over-the-Counter (OTC)'
  0,           -- Not a basic necessity
  NULL         -- Image path if available
);
-- Assume product ID = 20
```

### Step 3: Create Batch/Inventory Record

```sql
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
  20,              -- product_id for Tylenol from step 2
  1,               -- supplier_id for ABC Pharma Supply from step 1
  'BATCH-20260815-001',
  '2026-08-15',
  '2026-01-15',
  '2028-01-15',
  100,             -- Received 100 tablets
  100,             -- Currently have 100 available
  2.50,            -- Purchase cost per tablet: ₱2.50
  5.00,            -- Markup: 5%
  3.00             -- Sale price per tablet: ₱3.00 (rounded from 2.625)
);
-- Assume inventory ID = 20
```

### Example: Syrup (with strength_per_quantity)

```sql
-- First, insert supplier (assume already exists)

-- Product: Cough Syrup 250mg/5mL
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
  250,                -- Main strength: 250 mg per dose
  2,                  -- measurement_id for 'mg'
  5,                  -- Strength per quantity: 5
  'mL',               -- Strength per quantity unit: mL (i.e., 250mg per 5mL)
  '4802000000001',
  'Bottle',
  120,                -- 120 mL bottle (assuming 10 mL doses = 12 doses)
  18
);
-- product_id = 21

-- Batch record for syrup
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
  21,                 -- CoughAway Syrup product
  1,                  -- ABC Pharma Supply
  'BATCH-21-26-0815',
  '2026-08-15',
  '2026-06-15',
  '2028-06-15',
  50,                 -- Received 50 bottles
  48,                 -- Currently 48 bottles (2 sold)
  8.00,               -- Cost per bottle: ₱8.00
  12.50,              -- Markup: 12.5%
  9.00                -- Sale price per bottle: ₱9.00
);
-- inventory_id = 21
```

### Example: Cream (strength per gram)

```sql
-- Product: Hydrocortisone Cream 1%
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
  'SkinCare Cream',
  'Hydrocortisone',
  'Cream',
  1,                  -- 1% (can use percentage or 10 mg/g)
  11,                 -- measurement_id for '%'
  NULL,               -- No secondary quantity needed
  NULL,
  '4803000000001',
  'Tube',
  1,                  -- 1 tube per unit (30g or 50g typically)
  21                  -- First Aid category
);

-- Batch record
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
  22,                 -- SkinCare Cream
  1,                  -- ABC Pharma Supply
  'BATCH-22-26-0815',
  '2026-08-15',
  '2025-08-15',
  '2027-08-15',
  75,                 -- 75 tubes
  75,
  25.00,              -- ₱25 per tube
  8.00,               -- 8% markup
  27.00               -- Sale price ₱27 per tube
);
```

---

## PART F: FORM-TO-DATABASE MAPPING

### Product Management Form → Database Mapping

```
┌────────────────────────────────────┬──────────────────────────────────┐
│     FRONTEND FORM FIELD            │   DATABASE TABLE.COLUMN          │
├────────────────────────────────────┼──────────────────────────────────┤
│ Brand Name                         │ products.branded_name            │
│ Generic Name                       │ products.generic_name            │
│ Dosage Form                        │ products.dosage_form             │
│ Strength (numeric)                 │ products.strength                │
│ Strength Unit (dropdown)           │ products.measurement_id (FK)     │
│ Strength per Quantity (numeric)    │ products.strength_per_quantity   │
│ Strength per Quantity Unit         │ products.strength_per_quantity_unit
│ Package Type (dropdown)            │ products.package_type            │
│ Units per Package                  │ products.units_per_package       │
│ Category (dropdown)                │ products.category_id (FK)        │
│ Barcode                            │ products.barcode                 │
│ Product Image                      │ products.imageproduct            │
│ Classification                     │ products.classification_id       │
│ Is Basic Necessity (checkbox)      │ products.is_basic_necessities    │
└────────────────────────────────────┴──────────────────────────────────┘

NOTES:
- Strength Unit: Store unit_id, not text (normalize to unit_measurement table)
- Category: Use category_id FK to product_categories
- Barcode: Auto-generated if blank, user can override
- Supplier info NOT stored here (belongs in batch/inventory only)
```

### Batch / Inventory Form → Database Mapping

```
┌────────────────────────────────────┬──────────────────────────────────┐
│     FRONTEND FORM FIELD            │   DATABASE TABLE.COLUMN          │
├────────────────────────────────────┼──────────────────────────────────┤
│ Product (dropdown)                 │ inventory.product_id (FK)        │
│ Supplier (dropdown)                │ inventory.supplier_id (FK)       │
│ Batch Number                       │ inventory.batch_number           │
│ Date Received (date picker)        │ inventory.date_received          │
│ Manufacture Date (date picker)     │ inventory.manufacture_date       │
│ Expiry Date (date picker)          │ inventory.expiry_date            │
│ Received Quantity (numeric)        │ inventory.received_quantity      │
│ Current Quantity (READ-ONLY)       │ inventory.current_quantity       │
│ Purchase Cost per Unit             │ inventory.purchase_cost          │
│ Markup (%) (numeric)               │ inventory.markup                 │
│ Calculated Sale Price (READ-ONLY)  │ inventory.sale_price (calculated)│
│ Manual Sale Price Override         │ inventory.sale_price (user-set)  │
└────────────────────────────────────┴──────────────────────────────────┘

BUSINESS LOGIC:
- Current Quantity: Auto-decremented by sales transactions
- Sale Price: = Purchase Cost × (1 + Markup/100), but allow override
- Received Quantity: Never decreases (audit trail via inventory_transactions)
- Current Quantity: = Received - Sold - Damaged - Returned
- Expiry Date: Critical for FEFO ordering in POS display
```

### Supplier Form → Database Mapping

```
┌────────────────────────────────────┬──────────────────────────────────┐
│     FRONTEND FORM FIELD            │   DATABASE TABLE.COLUMN          │
├────────────────────────────────────┼──────────────────────────────────┤
│ Supplier Name                      │ suppliers.supplier_name          │
│ Contact Person                     │ suppliers.contact_person         │
│ Contact Number                     │ suppliers.contact_number         │
│ Email                              │ suppliers.email                  │
│ Address                            │ suppliers.address                │
│ Supplier Type (dropdown)           │ suppliers.supplier_type          │
│ Is Active (checkbox/toggle)        │ suppliers.is_active              │
└────────────────────────────────────┴──────────────────────────────────┘

NOTES:
- Supplier Name: UNIQUE constraint
- Is Active: Soft delete flag (hide inactive suppliers from dropdowns)
- No pricing info here (belongs in batches, not supplier master)
```

---

## PART G: IMPLEMENTATION CHECKLIST

- [ ] **BACKUP** - Run backup SQL (Part B, Step 1)
- [ ] **TEST** - Run all ALTER TABLE statements on test database first
- [ ] **EXECUTE** - Run Part B, Steps 2-6 in order on production
- [ ] **VERIFY** - Check products, inventory, suppliers tables
- [ ] **UPDATE PHP** - Update application code to use new column names
  - [ ] Replace all `quantity` references with `current_quantity`
  - [ ] Remove references to `products.supplier_name`, etc.
  - [ ] Update INSERT statements for new column structure
- [ ] **TEST APPLICATION** - Verify POS and admin pages work
- [ ] **MIGRATE DATA** - Run sample INSERT statements to populate suppliers and test batches
- [ ] **AUDIT** - Review all data integrity

---

## PART H: DEPLOYMENT NOTES

### For Developers

1. **Column Renaming in PHP Code:**
   - `$row['quantity']` → `$row['current_quantity']`
   - Search codebase for `quantity` and verify each usage

2. **Supplier Removal from Products:**
   - Old code querying `products.supplier_name` will break
   - New code must JOIN `inventory` and `suppliers` tables
   - Example:
     ```php
     SELECT p.branded_name, i.batch_number, s.supplier_name
     FROM products p
     JOIN inventory i ON p.id = i.product_id
     JOIN suppliers s ON i.supplier_id = s.id
     WHERE p.id = ?
     ```

3. **Price Fields:**
   - Product has NO pricing now
   - All pricing is batch-level in `inventory` table
   - Update all sales queries to use `inventory.sale_price`

### For Testing

- Test product creation with and without strength_per_quantity
- Test batch creation with multiple suppliers for same product
- Test FEFO ordering (expiry_date sorting)
- Test inventory deduction during sales
- Test returns (current_quantity reduction tracking)

### Rollback Plan (if needed)

```sql
-- Restore from backup
TRUNCATE products;
TRUNCATE inventory;
INSERT INTO products SELECT * FROM products_backup;
INSERT INTO inventory SELECT * FROM inventory_backup;
```

---

## SUMMARY

✓ **Products:** Normalized (supplier info removed, pricing moved to batches)  
✓ **Inventory:** Clarified as batch management (quantity → current_quantity)  
✓ **Suppliers:** Table exists, ready to populate  
✓ **Units:** Measurement table exists and complete  
✓ **Categories:** Category table exists and complete  
✓ **Relationships:** All foreign keys properly configured  
✓ **Data Preservation:** Backup strategy included  
✓ **Scalability:** Structure supports multiple batches per product per supplier  
✓ **FEFO Support:** Expiry date tracking and indexing enabled  
✓ **Audit Trail:** Optional inventory_transactions table for history  

**Ready for implementation.**

---

**Document Generated:** August 15, 2026
**Database:** mmbpos
**MariaDB Version:** 10.4.32
