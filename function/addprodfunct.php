<?php

namespace Classes;

require_once __DIR__ . '/../conn/database.php';
require_once __DIR__ . '/file_upload.php';
require_once __DIR__ . "/../conn/activity_log.php"; // audit trail (Task 42)

/**
 * Audit helper: resolve a product's display name safely (never throws).
 * Used by the activity-log hooks so descriptions stay human-readable.
 */
if (!function_exists('mmb_audit_product_name')) {
    function mmb_audit_product_name(\PDO $con, int $productId): string
    {
        try {
            $stmt = $con->prepare("SELECT COALESCE(NULLIF(branded_name, ''), generic_name, CONCAT('product #', ?)) AS name FROM products WHERE id = ?");
            $stmt->execute([$productId, $productId]);
            $name = $stmt->fetchColumn();
            return $name !== false && $name !== null && $name !== '' ? (string) $name : ('product #' . $productId);
        } catch (\Throwable $e) {
            return 'product #' . $productId;
        }
    }
}

class ProductManagement
{
    public int $id;
    public string $generic_name;
    public string $branded_name;
    public string $strength;
    public int $unit_measurement;
    public string $barcode;
    public int $category_id;
    public int $units_per_package;
    public int $is_basic_necessities;
    public string $package_type;
    public string $dosage_form;
    public int $dosage_form_id;
    public float $strength_per_quantity;
    public ?float $strength_per_quantity_normalized = null;
    public string $strength_per_quantity_unit;
    // Batch/Inventory fields (no longer in products table)
    public string $expiry_date;
    public string $batch_number;
    public int $supplier_id;
    public float $purchase_cost;
    public float $markup;
    public float $sale_price;
    public int $received_quantity;

    private $con;
    private string $response = "";

    public function __construct($db)
    {
        $this->con = $db;
    }

    // Helper: check if a column exists in the current database
    private function hasColumn(string $table, string $column): bool
    {
        $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$table, $column]);
        return (int)$stmt->fetchColumn() > 0;
    }
    
    private function tableExists(string $tableName): bool
    {
        $stmt = $this->con->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
        $stmt->execute([$tableName]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function generateBatchNumber(int $productId): string
    {
        $tables = ['inventory'];
        if ($this->tableExists('inventory_no_stock')) {
            $tables[] = 'inventory_no_stock';
        }
        if ($this->tableExists('inventory_disposals')) {
            $tables[] = 'inventory_disposals';
        }

        $highestNumber = 0;
        $existingNames = [];

        foreach ($tables as $tableName) {
            $stmt = $this->con->prepare("SELECT batch_number FROM " . $tableName . " WHERE product_id = ?");
            $stmt->execute([$productId]);

            while ($row = $stmt->fetch()) {
                $existingName = trim((string) ($row['batch_number'] ?? ''));
                if ($existingName === '') {
                    continue;
                }

                $existingNames[strtolower($existingName)] = true;

                if (preg_match('/^batch-(\d+)$/i', $existingName, $matches)) {
                    $highestNumber = max($highestNumber, (int) $matches[1]);
                }
            }
        }

        do {
            $highestNumber++;
            $batchNumber = 'Batch-' . $highestNumber;
        } while (isset($existingNames[strtolower($batchNumber)]));

        return $batchNumber;
    }

    private function ensureMeasurementColumnOptional(): void
    {
        $stmt = $this->con->prepare("SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'measurement_id'");
        $stmt->execute();
        $isNullable = strtoupper((string) ($stmt->fetchColumn() ?? 'YES')) === 'YES';

        if (!$isNullable) {
            $this->con->exec("ALTER TABLE products MODIFY measurement_id INT NULL");
        }
    }

    // Supplier columns are no longer in products table - they belong in suppliers table
    // Pricing columns are no longer in products table - they belong in inventory table

    public function getUnitMeasurements(): array
    {
        try {
            $stmt = $this->con->prepare("SELECT id, serving_unit_name FROM serving_unit ORDER BY serving_unit_name ASC");
            $stmt->execute();

            $units = [];
            while ($row = $stmt->fetch()) {
                $id = (int) ($row['id'] ?? 0);
                $value = trim($row['serving_unit_name'] ?? '');

                if ($id > 0 && $value !== '') {
                    $units[] = [
                        'id' => $id,
                        'name' => $value,
                    ];
                }
            }

            return $units;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getPackageUnitMeasurements(): array
    {
        try {
            $stmt = $this->con->prepare("SELECT unit_id, different_measurement FROM unit_measurement ORDER BY different_measurement ASC");
            $stmt->execute();
            $units = [];
            while ($row = $stmt->fetch()) {
                $id = (int) ($row['unit_id'] ?? 0);
                $value = trim($row['different_measurement'] ?? '');
                if ($id > 0 && $value !== '') {
                    $units[] = ['id' => $id, 'name' => $value];
                }
            }
            return $units;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function addMeasurement(string $measurementName): array
    {
        $name = trim((string) $measurementName);
        if ($name === '') {
            return ['success' => false, 'message' => 'Measurement name is required.'];
        }

        $normalized = preg_replace('/\s+/', ' ', $name);
        if ($normalized === '') {
            return ['success' => false, 'message' => 'Measurement name is required.'];
        }

        try {
            $existingStmt = $this->con->prepare("SELECT unit_id, different_measurement FROM unit_measurement WHERE LOWER(TRIM(different_measurement)) = LOWER(?) LIMIT 1");
            $existingStmt->execute([$normalized]);
            $existing = $existingStmt->fetch();

            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'This measurement already exists.',
                    'existing_id' => (int) ($existing['unit_id'] ?? 0),
                    'existing_name' => trim((string) ($existing['different_measurement'] ?? ''))
                ];
            }

            $insertStmt = $this->con->prepare("INSERT INTO unit_measurement (different_measurement) VALUES (?)");
            $insertResult = $insertStmt->execute([$normalized]);

            if (!$insertResult) {
                return ['success' => false, 'message' => 'Failed to save the measurement unit.'];
            }

            $unitId = (int) $this->con->lastInsertId();
            return [
                'success' => true,
                'message' => 'Measurement saved successfully.',
                'id' => $unitId,
                'name' => $normalized,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getDosageForms(): array
    {
        try {
            $stmt = $this->con->prepare("SELECT id, form_name FROM dosage_forms ORDER BY form_name ASC");
            $stmt->execute();

            $forms = [];
            while ($row = $stmt->fetch()) {
                $id = (int) ($row['id'] ?? 0);
                $value = trim((string) ($row['form_name'] ?? ''));

                if ($id > 0 && $value !== '') {
                    $forms[] = [
                        'id' => $id,
                        'name' => $value,
                    ];
                }
            }

            return $forms;
        } catch (\Exception $e) {
            return [];
        }
    }

    // GET POST DATA - PRODUCT MASTER ONLY
    public function getPost()
    {
        if (!empty($_POST)) {
            $this->generic_name = $_POST['generic_name'] ?? '';
            $this->branded_name = $_POST['branded_name'] ?? '';
            $this->strength = $_POST['strength'] ?? '';
            $this->unit_measurement = (int) ($_POST['unit_measurement'] ?? 0);
            $this->barcode = $_POST['barcode'] ?? '';
            $this->category_id = (int) ($_POST['category_id'] ?? 0);
            $this->units_per_package = (int) ($_POST['units_per_package'] ?? $_POST['pcs'] ?? $_POST['unit'] ?? 0);
            $this->is_basic_necessities = isset($_POST['is_basic_necessities']) ? 1 : 0;
            $this->package_type = trim($_POST['package_type'] ?? '');
            $this->dosage_form = trim($_POST['dosage_form'] ?? '');
            $this->dosage_form_id = (int) ($_POST['dosage_form_id'] ?? 0);

            if ($this->dosage_form_id <= 0 && $this->dosage_form !== '') {
                try {
                    $stmt = $this->con->prepare("SELECT id FROM dosage_forms WHERE LOWER(form_name) = LOWER(?) LIMIT 1");
                    $stmt->execute([$this->dosage_form]);
                    $this->dosage_form_id = (int) ($stmt->fetchColumn() ?? 0);
                } catch (\Exception $e) {
                    $this->dosage_form_id = 0;
                }
            }

            // ISSUE #6 (1): Package Size — whole numbers >= 1 only.
            // Anything negative, decimal, or non-numeric normalizes to NULL
            // (the products column is nullable) so bad values can never be
            // persisted server-side, no matter what the client sends.
            $spqRaw = isset($_POST['strength_per_quantity']) && $_POST['strength_per_quantity'] !== '' ? $_POST['strength_per_quantity'] : null;
            if (is_numeric($spqRaw)) {
                $spqVal = (float) $spqRaw;
                $this->strength_per_quantity = $spqVal;
                $this->strength_per_quantity_normalized = ($spqVal >= 1.0 && floor($spqVal) === $spqVal) ? $spqVal : null;
            } else {
                $this->strength_per_quantity = 0.0;
                $this->strength_per_quantity_normalized = null;
            }
            $this->strength_per_quantity_unit = trim($_POST['strength_per_quantity_unit'] ?? '');
            if ($this->strength_per_quantity_normalized === null) {
                $this->strength_per_quantity_unit = '';
            }
            
            // ISSUE #6 (1): serving amount can never be negative either
            if (is_numeric($this->strength) && (float) $this->strength < 0) {
                $this->strength = '0';
            }
            
            // Batch/Inventory fields (for inventory table)
            $this->expiry_date = $_POST['expiry_date'] ?? '';
            $this->batch_number = trim($_POST['batch_number'] ?? '');
            $this->supplier_id = (int) ($_POST['supplier_id'] ?? 0);
            $this->purchase_cost = (float) ($_POST['purchase_cost'] ?? 0);
            $this->markup = (float) ($_POST['markup'] ?? 0);
            $this->sale_price = (float) ($_POST['sale_price'] ?? 0);
            $this->received_quantity = (int) ($_POST['received_quantity'] ?? 0);
        }
    }

    private function handleImageUpload()
    {
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            // Use the fileupload class
            $uploadDir = __DIR__ . '/../img/'; // Save to img folder
            
            // Ensure directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $upload = new fileupload($_FILES['product_image'], $uploadDir);
            
            if ($upload->upload()) {
                return $upload->filename; // Return the generated filename
            }
        }
        return '';
    }

    // ADD PRODUCT (WITH TRANSACTION)
   public function addProduct()
{
    if (isset($_POST['addProduct'])) {
        $this->getPost();
        // VALIDATE REQUIRED FIELDS WITH FK CONSTRAINTS
        if ($this->category_id <= 0) {
            $this->response = "Category is required. Please select a valid category.";
            return false;
        }
        try {
            $this->ensureMeasurementColumnOptional();
            $this->con->beginTransaction();

            // INSERT THIS BLOCK HERE
            if (empty($this->barcode)) {
                do {
                    $this->barcode = time() . rand(100, 999);

                    $check = $this->con->prepare("SELECT id FROM products WHERE barcode = ?");
                    $check->execute([$this->barcode]);

                } while ($check->fetch());
            } else {
                $check = $this->con->prepare("SELECT id FROM products WHERE barcode = ?");
                $check->execute([$this->barcode]);

                if ($check->fetch()) {
                    $this->response = "Barcode already exists!";
                    return false;
                }
            }

            $imagePath = $this->handleImageUpload();

            $insertFields = [
                'generic_name',
                'branded_name',
                'strength',
                'measurement_id',
                'barcode',
                'category_id',
                'units_per_package',
                'package_type',
                'dosage_form',
                'strength_per_quantity',
                'strength_per_quantity_unit',
                'imageproduct',
                'is_basic_necessities'
            ];
            $insertValues = [
                $this->generic_name,
                $this->branded_name,
                $this->strength,
                $this->unit_measurement > 0 ? $this->unit_measurement : null,
                $this->barcode,
                $this->category_id,
                $this->units_per_package,
                $this->package_type,
                $this->dosage_form,
                $this->strength_per_quantity_normalized,
                $this->strength_per_quantity_unit,
                $imagePath,
                $this->is_basic_necessities
            ];

            if ($this->hasColumn('products', 'dosage_form_id')) {
                $insertFields[] = 'dosage_form_id';
                $insertValues[] = $this->dosage_form_id > 0 ? $this->dosage_form_id : null;
            }



            $placeholders = implode(', ', array_fill(0, count($insertFields), '?'));

            $stmt = $this->con->prepare("INSERT INTO products (" . implode(', ', $insertFields) . ") VALUES (" . $placeholders . ")");
            $stmt->execute($insertValues);

            $productId = $this->con->lastInsertId();

            $addBatch = isset($_POST['add_batch_prompt']) && strtolower((string) $_POST['add_batch_prompt']) === 'yes';

            if ($addBatch) {
                if ($this->received_quantity <= 0) {
                    $this->con->rollBack();
                    $this->response = "Quantity received must be greater than zero when adding a batch.";
                    return false;
                }

                $this->batch_number = $this->generateBatchNumber((int) $productId);

                $stmt = $this->con->prepare("
                    INSERT INTO inventory (product_id, supplier_id, batch_number, date_received, expiry_date, purchase_cost, markup, sale_price, received_quantity, current_quantity)
                    VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $productId,
                    ($this->supplier_id > 0) ? $this->supplier_id : null,
                    $this->batch_number ?: null,
                    $this->expiry_date ?: null,
                    $this->purchase_cost,
                    $this->markup,
                    $this->sale_price,
                    $this->received_quantity,
                    $this->received_quantity
                ]);
            }

            $this->con->commit();

            // AUDIT (Task 42)
            $auditName = trim(($this->branded_name ?? '') !== '' ? $this->branded_name : ($this->generic_name ?? ''));
            $auditDesc = "Added product '" . ($auditName !== '' ? $auditName : ('#' . $productId)) . "'";
            if ($addBatch) {
                $auditDesc .= " with initial batch '" . ($this->batch_number ?? '') . "' (" . (int) ($this->received_quantity ?? 0) . " packs)";
            }
            mmb_log_activity($this->con, 'products', 'product_add', $auditDesc, 'product', (int) $productId);

            $this->response = "Success";
            return true;

        } catch (\Exception $e) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            $this->response = "Transaction failed: " . $e->getMessage();
            return false;
        }
    }
    return false;
}
    // GET ALL PRODUCTS (IMPROVED - GROUPED INVENTORY)
    public function getAllProducts(bool $includeHidden = false)
    {
        $hasBasic = $this->hasColumn('products', 'is_basic_necessities');
        $basicSelect = $hasBasic ? 'p.is_basic_necessities,' : '';

        $hasGeneric  = $this->hasColumn('products', 'generic_name');
        $hasBranded  = $this->hasColumn('products', 'branded_name');
        $hasStrength = $this->hasColumn('products', 'strength');
        $hasPackageType = $this->hasColumn('products', 'package_type');
        $hasDosageForm = $this->hasColumn('products', 'dosage_form');
        $hasStrengthPerQuantity = $this->hasColumn('products', 'strength_per_quantity');
        $hasBarcode     = $this->hasColumn('products', 'barcode');
        $hasMeasurement = $this->hasColumn('products', 'measurement_id');
        $hasCategoryId  = $this->hasColumn('products', 'category_id');
        $hasImage       = $this->hasColumn('products', 'imageproduct');

        $nameFields = "";
        if ($hasGeneric && $hasBranded) {
            $nameFields .= "p.generic_name, p.branded_name, CONCAT(COALESCE(p.branded_name,''), ' ', COALESCE(p.generic_name,'')) AS product_name, ";
        } else if ($hasGeneric) {
            $nameFields .= "p.generic_name, '' AS branded_name, p.generic_name AS product_name, ";
        } else if ($this->hasColumn('products', 'product_name')) {
            $nameFields .= "p.product_name AS generic_name, '' AS branded_name, p.product_name AS product_name, ";
        } else {
            $nameFields .= "'' AS generic_name, '' AS branded_name, '' AS product_name, ";
        }

        $strengthField    = $hasStrength    ? "p.strength," : "'' AS strength,";
        $packageTypeField = $hasPackageType ? "p.package_type," : "'' AS package_type,";
        $dosageFormField  = $hasDosageForm ? "p.dosage_form," : "'' AS dosage_form,";
        $strengthPerQuantityField = $hasStrengthPerQuantity ? "p.strength_per_quantity," : "0.00 AS strength_per_quantity,";
        $barcodeField     = $hasBarcode     ? "p.barcode,"  : "'' AS barcode,";
        $measurementField = $hasMeasurement ? "p.measurement_id," : "0 AS measurement_id,";
        $categoryIdField  = $hasCategoryId  ? "p.category_id," : "0 AS category_id,";
        $imageField       = $hasImage       ? "p.imageproduct," : "'' AS imageproduct,";
        $hiddenField      = $this->hasColumn('products', 'is_hidden') ? "COALESCE(p.is_hidden, 0) AS is_hidden," : "0 AS is_hidden,";
        $categoryNameField= $this->hasColumn('product_categories', 'category_name') ? "COALESCE(pc.category_name, 'N/A') AS category_name," : "'N/A' AS category_name,";
        $batchField = $this->hasColumn('inventory', 'batch_number') ? "MAX(i.batch_number) AS batch_number," : "'' AS batch_number,";

        $orderBy          = $hasGeneric     ? "p.generic_name ASC" : ($this->hasColumn('products', 'product_name') ? "p.product_name ASC" : "p.id ASC");

        $sql = "SELECT 
                p.id,
                {$nameFields}
                {$strengthField}
                {$packageTypeField}
                {$dosageFormField}
                {$strengthPerQuantityField}
                p.strength_per_quantity_unit,
                {$measurementField}
                {$categoryNameField}
                {$barcodeField}
                p.units_per_package,
                {$categoryIdField}
                {$imageField}
                {$hiddenField}
                {$batchField} ";

        $sql .= $basicSelect . " COALESCE(SUM(i.received_quantity), 0) AS received_quantity, COALESCE(SUM(i.current_quantity), 0) AS current_quantity, MIN(i.expiry_date) AS expiry_date
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN inventory i ON p.id = i.product_id
            WHERE " . (!$includeHidden && $this->hasColumn('products', 'is_hidden') ? "COALESCE(p.is_hidden, 0) = 0" : "1 = 1") . "
            GROUP BY p.id
            ORDER BY {$orderBy}";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll();

        $measurementNames = [];
        try {
            $measurementStmt = $this->con->prepare("SELECT id, serving_unit_name FROM serving_unit");
            $measurementStmt->execute();

            while ($row = $measurementStmt->fetch()) {
                $measurementNames[(int) ($row['id'] ?? 0)] = trim($row['serving_unit_name'] ?? '');
            }
        } catch (\Exception $e) {
            $measurementNames = [];
        }

        $inventoryByProduct = [];
        $productIds = [];

        foreach ($products as $productRow) {
            $productIds[] = (int) ($productRow['id'] ?? 0);
        }

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $batchStmt = $this->con->prepare("
                SELECT i.id, i.product_id, i.batch_number, i.current_quantity, i.received_quantity,
                       i.expiry_date, i.purchase_cost, i.markup, i.sale_price, i.date_received,
                       COALESCE(s.supplier_name, 'N/A') AS supplier_name,
                       COALESCE(s.contact_number, 'N/A') AS supplier_contact,
                       COALESCE(s.email, 'N/A') AS supplier_email,
                       COALESCE(s.address, 'N/A') AS supplier_address
                FROM inventory i
                LEFT JOIN suppliers s ON s.id = i.supplier_id
                WHERE i.product_id IN ({$placeholders})
                ORDER BY i.product_id ASC, i.expiry_date ASC, i.id ASC
            ");
            $batchStmt->execute($productIds);

            while ($batch = $batchStmt->fetch()) {
                $productId = (int) ($batch['product_id'] ?? 0);
                $inventoryByProduct[$productId][] = $batch;
            }
        }

        foreach ($products as &$product) {
            $measurementId = (int) ($product['measurement_id'] ?? 0);
            $product['measurement_name'] = $measurementNames[$measurementId] ?? '';
            $product['inventory_batches'] = $inventoryByProduct[(int) ($product['id'] ?? 0)] ?? [];

            $latestBatch = null;
            foreach ($product['inventory_batches'] as $batch) {
                if ($latestBatch === null
                    || (string) ($batch['date_received'] ?? '') > (string) ($latestBatch['date_received'] ?? '')
                    || ((string) ($batch['date_received'] ?? '') === (string) ($latestBatch['date_received'] ?? '')
                        && (int) ($batch['id'] ?? 0) > (int) ($latestBatch['id'] ?? 0))) {
                    $latestBatch = $batch;
                }
            }

            $product['net_price'] = isset($latestBatch['purchase_cost']) ? (float) $latestBatch['purchase_cost'] : 0.0;
            $product['total_price'] = isset($latestBatch['sale_price']) ? (float) $latestBatch['sale_price'] : 0.0;
            $product['display_price'] = $product['total_price'];
            $product['batch_number'] = !empty($product['inventory_batches']) ? implode(', ', array_filter(array_map(static function ($item) {
                return trim((string) ($item['batch_number'] ?? ''));
            }, $product['inventory_batches']))) : '';
        }

        return $products;
    }

    public function moveExpiredBatchesToDisposed(): int
    {
        $selectStmt = $this->con->prepare(
            "SELECT id, product_id, batch_number, current_quantity, expiry_date FROM inventory WHERE expiry_date IS NOT NULL AND TRIM(expiry_date) <> '' AND expiry_date < CURDATE() FOR UPDATE"
        );
        $selectStmt->execute();
        $expiredBatches = $selectStmt->fetchAll();

        if (empty($expiredBatches)) {
            return 0;
        }

        $insertStmt = $this->con->prepare(
            "INSERT INTO inventory_disposals (product_id, batch_number, quantity, expiry_date, reason, disposed_at) VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $updateStmt = $this->con->prepare("UPDATE inventory SET current_quantity = 0 WHERE id = ?");

        $processed = 0;
        foreach ($expiredBatches as $batch) {
            $batchId = (int) ($batch['id'] ?? 0);
            $productId = (int) ($batch['product_id'] ?? 0);
            $quantity = max((int) ($batch['current_quantity'] ?? 0), 0);
            $expiryDate = $batch['expiry_date'] ?? null;
            $batchNumber = $batch['batch_number'] ?? null;

            $insertStmt->execute([
                $productId,
                $batchNumber,
                $quantity,
                $expiryDate,
                'Expired'
            ]);

            $updateStmt->execute([$batchId]);
            $processed++;
        }

        return $processed;
    }

    public function getAllInventoryBatches(): array
    {
        $this->ensureInventoryNoStockTable();

        $sql = "
            SELECT 
                i.id,
                i.batch_number,
                i.product_id,
                p.generic_name,
                p.branded_name,
                p.imageproduct,
                p.strength,
                p.measurement_id,
                COALESCE(um.serving_unit_name, '') AS measurement_name,
                p.category_id,
                pc.category_name,
                p.barcode,
                i.supplier_id,
                COALESCE(s.supplier_name, 'N/A') AS supplier_name,
                i.purchase_cost,
                i.markup,
                i.sale_price,
                i.current_quantity,
                i.received_quantity,
                i.date_received,
                i.expiry_date
            FROM inventory i
            LEFT JOIN products p ON p.id = i.product_id
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN serving_unit um ON um.id = p.measurement_id
            LEFT JOIN suppliers s ON i.supplier_id = s.id
                        WHERE NOT EXISTS (
                                SELECT 1
                                FROM inventory_no_stock n
                                WHERE n.product_id = i.product_id
                                    AND n.batch_number <=> i.batch_number
                                    AND n.expiry_date <=> i.expiry_date
                        )
            ORDER BY p.generic_name ASC,
                     CASE
                         WHEN i.batch_number REGEXP '^Batch-[0-9]+$' THEN 0
                         ELSE 1
                     END ASC,
                     CAST(SUBSTRING_INDEX(i.batch_number, '-', -1) AS UNSIGNED) ASC,
                     i.expiry_date ASC,
                     i.id ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getInventoryBatchesForArchiveSelection(): array
    {
        $sql = "
            SELECT 
                i.id,
                i.batch_number,
                i.product_id,
                p.generic_name,
                p.branded_name,
                p.imageproduct,
                p.strength,
                p.measurement_id,
                COALESCE(um.serving_unit_name, '') AS measurement_name,
                p.category_id,
                pc.category_name,
                p.barcode,
                i.supplier_id,
                COALESCE(s.supplier_name, 'N/A') AS supplier_name,
                i.purchase_cost,
                i.markup,
                i.sale_price,
                i.current_quantity,
                i.received_quantity,
                i.date_received,
                i.expiry_date
            FROM inventory i
            LEFT JOIN products p ON p.id = i.product_id
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN serving_unit um ON um.id = p.measurement_id
            LEFT JOIN suppliers s ON i.supplier_id = s.id
            ORDER BY p.generic_name ASC,
                     CASE
                         WHEN i.batch_number REGEXP '^Batch-[0-9]+$' THEN 0
                         ELSE 1
                     END ASC,
                     CAST(SUBSTRING_INDEX(i.batch_number, '-', -1) AS UNSIGNED) ASC,
                     i.expiry_date ASC,
                     i.id ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllInventoryBatchNumbersForSequence(): array
    {
        $sql = "
            SELECT id, product_id, batch_number
            FROM inventory
            WHERE batch_number IS NOT NULL
              AND TRIM(batch_number) <> ''
            ORDER BY id ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function ensureInventoryNoStockTable(): void
    {
        $this->con->exec("
            CREATE TABLE IF NOT EXISTS inventory_no_stock (
                id INT NOT NULL AUTO_INCREMENT,
                product_id INT NOT NULL,
                batch_number VARCHAR(100) DEFAULT NULL,
                current_quantity INT NOT NULL DEFAULT 0,
                received_quantity INT NOT NULL DEFAULT 0,
                expiry_date DATE DEFAULT NULL,
                reason VARCHAR(100) NOT NULL DEFAULT 'No stock',
                moved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function moveZeroStockBatchesToNoStock(): int
    {
        $this->ensureInventoryNoStockTable();

        $selectStmt = $this->con->prepare(
            "SELECT id, product_id, batch_number, current_quantity, received_quantity, expiry_date FROM inventory WHERE current_quantity <= 0 FOR UPDATE"
        );
        $selectStmt->execute();
        $zeroStockBatches = $selectStmt->fetchAll();

        if (empty($zeroStockBatches)) {
            return 0;
        }

        $checkStmt = $this->con->prepare(
            "SELECT 1 FROM inventory_no_stock WHERE product_id = ? AND batch_number = ? AND expiry_date <=> ? LIMIT 1"
        );
        $insertStmt = $this->con->prepare(
            "INSERT INTO inventory_no_stock (product_id, batch_number, current_quantity, received_quantity, expiry_date, reason, moved_at) VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $updateStmt = $this->con->prepare("UPDATE inventory SET current_quantity = 0 WHERE id = ?");

        $processed = 0;
        foreach ($zeroStockBatches as $batch) {
            $batchId = (int) ($batch['id'] ?? 0);
            $productId = (int) ($batch['product_id'] ?? 0);
            $currentQuantity = max((int) ($batch['current_quantity'] ?? 0), 0);
            $receivedQuantity = max((int) ($batch['received_quantity'] ?? 0), 0);
            $expiryDate = $batch['expiry_date'] ?? null;
            $batchNumber = $batch['batch_number'] ?? null;

            $checkStmt->execute([$productId, $batchNumber, $expiryDate]);
            if ($checkStmt->fetchColumn()) {
                $updateStmt->execute([$batchId]);
                continue;
            }

            $insertStmt->execute([
                $productId,
                $batchNumber,
                $currentQuantity,
                $receivedQuantity,
                $expiryDate,
                'No stock'
            ]);

            $updateStmt->execute([$batchId]);
            $processed++;
        }

        return $processed;
    }

    public function moveNoStockBatch(): bool
    {
        if (!isset($_POST['moveNoStockBatch'])) {
            return false;
        }

        $inventoryId = (int) ($_POST['inventory_id'] ?? 0);
        if ($inventoryId <= 0) {
            $this->response = "Invalid inventory batch";
            return false;
        }

        try {
            $this->ensureInventoryNoStockTable();
            $this->con->beginTransaction();

            $batchStmt = $this->con->prepare("SELECT id, product_id, batch_number, current_quantity, received_quantity, expiry_date FROM inventory WHERE id = ? FOR UPDATE");
            $batchStmt->execute([$inventoryId]);
            $batch = $batchStmt->fetch();

            if (!$batch) {
                $this->con->rollBack();
                $this->response = "Inventory batch not found";
                return false;
            }

            if ((int) ($batch['current_quantity'] ?? 0) > 0) {
                $this->con->rollBack();
                $this->response = "Only batches with zero stock can be moved to no-stock history.";
                return false;
            }

            $checkStmt = $this->con->prepare(
                "SELECT 1 FROM inventory_no_stock WHERE product_id = ? AND batch_number = ? AND expiry_date <=> ? LIMIT 1"
            );
            $checkStmt->execute([
                (int) ($batch['product_id'] ?? 0),
                $batch['batch_number'] ?? null,
                $batch['expiry_date'] ?? null
            ]);

            if (!$checkStmt->fetchColumn()) {
                $insertStmt = $this->con->prepare("INSERT INTO inventory_no_stock (product_id, batch_number, current_quantity, received_quantity, expiry_date, reason, moved_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $insertStmt->execute([
                    (int) ($batch['product_id'] ?? 0),
                    $batch['batch_number'] ?? null,
                    max((int) ($batch['current_quantity'] ?? 0), 0),
                    max((int) ($batch['received_quantity'] ?? 0), 0),
                    $batch['expiry_date'] ?? null,
                    'No stock'
                ]);
            }

            $this->con->prepare("UPDATE inventory SET current_quantity = 0 WHERE id = ?")->execute([$inventoryId]);
            $this->con->commit();
            $this->response = "Zero-stock batch moved to no-stock table successfully";
            return true;
        } catch (\Exception $e) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            $this->response = "Failed to move zero-stock batch: " . $e->getMessage();
            return false;
        }
    }

    public function getDisposedBatches(): array
    {
        $sql = "
            SELECT 
                d.id,
                d.batch_number,
                d.product_id,
                p.generic_name,
                p.branded_name,
                p.imageproduct,
                p.strength,
                p.measurement_id,
                COALESCE(um.serving_unit_name, '') AS measurement_name,
                p.category_id,
                pc.category_name,
                p.barcode,
                d.quantity,
                d.expiry_date,
                d.reason,
                d.disposed_at
            FROM inventory_disposals d
            LEFT JOIN products p ON p.id = d.product_id
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN serving_unit um ON um.id = p.measurement_id
            ORDER BY d.id ASC, p.generic_name ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNoStockBatches(): array
    {
        $this->ensureInventoryNoStockTable();

        $sql = "
            SELECT 
                n.id,
                n.batch_number,
                n.product_id,
                p.generic_name,
                p.branded_name,
                p.imageproduct,
                p.strength,
                p.measurement_id,
                COALESCE(um.serving_unit_name, '') AS measurement_name,
                p.category_id,
                pc.category_name,
                p.barcode,
                n.current_quantity,
                n.received_quantity,
                n.expiry_date,
                n.reason,
                n.moved_at
            FROM inventory_no_stock n
            LEFT JOIN products p ON p.id = n.product_id
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            LEFT JOIN serving_unit um ON um.id = p.measurement_id
            ORDER BY n.id ASC, p.generic_name ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getReturnedProducts(): array
    {
        $sql = "
            SELECT
                ri.id,
                ri.return_transaction_id,
                rt.original_transaction_id,
                rt.refund_amount,
                rt.reason,
                rt.refund_method,
                rt.created_at AS return_date,
                ri.product_id,
                ri.quantity,
                ri.price,
                ri.subtotal,
                p.generic_name,
                p.branded_name,
                p.imageproduct,
                p.strength,
                p.measurement_id,
                COALESCE(um.serving_unit_name, '') AS measurement_name,
                p.barcode
            FROM return_items ri
            JOIN return_transactions rt ON ri.return_transaction_id = rt.id
            LEFT JOIN products p ON ri.product_id = p.id
            LEFT JOIN serving_unit um ON um.id = p.measurement_id
            ORDER BY ri.id ASC, rt.created_at DESC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function toggleProductStatus($productId): bool
    {
        if (!$productId || !isset($_POST['toggleProductStatus'])) {
            $this->response = "Invalid product ID";
            return false;
        }

        try {
            if (!$this->hasColumn('products', 'is_hidden')) {
                $this->con->exec("ALTER TABLE products ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0");
            }

            $stmt = $this->con->prepare("SELECT is_hidden FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $currentState = $stmt->fetchColumn();
            if ($currentState === false) {
                $this->response = "Product not found";
                return false;
            }

            $newState = ((int) $currentState) === 1 ? 0 : 1;
            $this->con->prepare("UPDATE products SET is_hidden = ? WHERE id = ?")->execute([$newState, $productId]);
            $status = $newState === 1 ? 'disabled' : 'enabled';
            mmb_log_activity($this->con, 'products', 'product_status',
                ucfirst($status) . " product " . mmb_audit_product_name($this->con, (int) $productId),
                'product', (int) $productId);

            $this->response = "Product {$status} successfully";
            return true;
        } catch (\Exception $e) {
            error_log('toggleProductStatus failed: ' . $e->getMessage());
            $this->response = "Unable to change product status";
            return false;
        }
    }

    // GET ALL CATEGORIES
    public function getCategories()
    {
        $stmt = $this->con->prepare("SELECT * FROM product_categories");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE PRODUCT
    public function updateProduct()
    {
        if (isset($_POST['updateProduct'])) {
            $this->getPost();
            $this->id = (int) $_POST['id'];
            $oldImage = $_POST['old_image'] ?? '';

            try {
                $this->con->beginTransaction();

                // CHECK BARCODE (exclude current product)
                if (!empty($this->barcode)) {
                    $stmt = $this->con->prepare("
                    SELECT COUNT(*) 
                    FROM products 
                    WHERE barcode = ? AND id != ?
                ");
                    $stmt->execute([$this->barcode, $this->id]);

                    if ($stmt->fetchColumn() > 0) {
                        $this->con->rollBack();
                        $this->response = "Barcode already exists";
                        return false;
                    }
                }

                $imagePath = $this->handleImageUpload();

                // If new image uploaded, delete old image
                if ($imagePath !== '' && !empty($oldImage)) {
                    $oldImagePath = __DIR__ . '/../img/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $setClauses = [
                    'generic_name = ?',
                    'branded_name = ?',
                    'strength = ?',
                    'measurement_id = ?',
                    'barcode = ?',
                    'category_id = ?',
                    'units_per_package = ?',
                    'package_type = ?',
                    'dosage_form = ?',
                    'strength_per_quantity = ?',
                    'strength_per_quantity_unit = ?',
                    'is_basic_necessities = ?'
                ];
                $values = [
                    $this->generic_name,
                    $this->branded_name,
                    $this->strength,
                    $this->unit_measurement,
                    $this->barcode,
                    $this->category_id,
                    $this->units_per_package,
                    $this->package_type,
                    $this->dosage_form,
                    $this->strength_per_quantity_normalized,
                    $this->strength_per_quantity_unit,
                    $this->is_basic_necessities
                ];

                if ($this->hasColumn('products', 'dosage_form_id')) {
                    $setClauses[] = 'dosage_form_id = ?';
                    $values[] = $this->dosage_form_id > 0 ? $this->dosage_form_id : null;
                }

                if ($imagePath !== '') {
                    $setClauses[] = 'imageproduct = ?';
                    $values[] = $imagePath;
                }

                $values[] = $this->id;

                $sql = 'UPDATE products SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
                $stmt = $this->con->prepare($sql);
                $stmt->execute($values);

                $addBatch = isset($_POST['add_batch_prompt']) && strtolower((string) $_POST['add_batch_prompt']) === 'yes';
                if ($addBatch) {
                    if ((int) ($_POST['received_quantity'] ?? 0) <= 0) {
                        $this->con->rollBack();
                        $this->response = "Quantity received must be greater than zero when adding a batch.";
                        return false;
                    }

                    $this->batch_number = $this->generateBatchNumber($this->id);

                    $batchStmt = $this->con->prepare("
                        INSERT INTO inventory (product_id, supplier_id, batch_number, date_received, expiry_date, purchase_cost, markup, sale_price, received_quantity, current_quantity)
                        VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
                    ");

                    $batchStmt->execute([
                        $this->id,
                        ((int) ($_POST['supplier_id'] ?? 0) > 0) ? (int) $_POST['supplier_id'] : null,
                        $this->batch_number,
                        $_POST['expiry_date'] ?? null,
                        isset($_POST['purchase_cost']) && $_POST['purchase_cost'] !== '' ? (float) $_POST['purchase_cost'] : 0,
                        isset($_POST['markup']) && $_POST['markup'] !== '' ? (float) $_POST['markup'] : 0,
                        isset($_POST['sale_price']) && $_POST['sale_price'] !== '' ? (float) $_POST['sale_price'] : 0,
                        (int) ($_POST['received_quantity'] ?? 0),
                        (int) ($_POST['received_quantity'] ?? 0),
                    ]);
                }

                $this->con->commit();

                // AUDIT (Task 42)
                $auditName = trim(($this->branded_name ?? '') !== '' ? $this->branded_name : ($this->generic_name ?? ''));
                mmb_log_activity($this->con, 'products', 'product_update',
                    "Updated product '" . ($auditName !== '' ? $auditName : ('#' . $this->id)) . "'",
                    'product', (int) $this->id);

                $this->response = "Updated successfully";
                return true;

            } catch (\Exception $e) {
                $this->con->rollBack();
                $this->response = "Update failed: " . $e->getMessage();
                return false;
            }
        }

        return false;
    }

    // GET SINGLE PRODUCT (FOR EDIT)
    public function getProductById($id)
    {
        $selectFields = 'p.*, p.units_per_package AS pcs, i.current_quantity as quantity, i.expiry_date, i.batch_number';
        $metadataFields = [];
        $supplierFields = [];

        foreach (['package_type', 'dosage_form', 'dosage_form_id', 'strength_per_quantity', 'strength_per_unit'] as $field) {
            if ($this->hasColumn('products', $field)) {
                $metadataFields[] = 'p.' . $field;
            }
        }

        foreach (['supplier_name', 'supplier_contact', 'supplier_address', 'supplier_email'] as $field) {
            if ($this->hasColumn('products', $field)) {
                $supplierFields[] = 'p.' . $field;
            }
        }

        $allExtraFields = array_merge($metadataFields, $supplierFields);
        if (!empty($allExtraFields)) {
            $selectFields = 'p.*, ' . implode(', ', $allExtraFields) . ', p.units_per_package AS pcs, i.current_quantity as quantity, i.expiry_date, i.batch_number';
        }

        $stmt = $this->con->prepare("
        SELECT {$selectFields}
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        WHERE p.id = ?
    ");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // UPDATE STOCK (AUTO HANDLE POST)
    public function updateStock()
    {
        if (isset($_POST['updateStock'])) {

            $id = (int) $_POST['id'];
            $quantity = (int) $_POST['quantity'];
            $expiry = $_POST['expiry_date'];

            try {
                $stmt = $this->con->prepare("
                UPDATE inventory 
                SET current_quantity = ?, expiry_date = ?
                WHERE product_id = ?
            ");

                if ($stmt->execute([$quantity, $expiry, $id])) {
                    // AUDIT (Task 42)
                    mmb_log_activity($this->con, 'inventory', 'stock_update',
                        "Updated stock of " . mmb_audit_product_name($this->con, (int) $id) . " to {$quantity} packs (expiry {$expiry})",
                        'product', (int) $id);
                    $this->response = "success";
                    return true;
                } else {
                    $this->response = "error";
                    return false;
                }

            } catch (\Exception $e) {
                $this->response = "error: " . $e->getMessage();
                return false;
            }
        }

        return false;
    }

    public function addInventoryBatch()
    {
        if (!isset($_POST['addInventoryBatch'])) {
            return false;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $expiryDate = $_POST['expiry_date'] ?? null;
        $supplierId = (int) ($_POST['supplier_id'] ?? 0);
        $purchaseCost = isset($_POST['purchase_cost']) && $_POST['purchase_cost'] !== '' ? (float) $_POST['purchase_cost'] : 0;
        $markup = isset($_POST['markup']) && $_POST['markup'] !== '' ? (float) $_POST['markup'] : 0;
        $salePrice = isset($_POST['sale_price']) && $_POST['sale_price'] !== '' ? (float) $_POST['sale_price'] : 0;

        // VALIDATE REQUIRED FIELDS
        if ($productId <= 0) {
            $this->response = "Please select a valid product before adding a batch.";
            return false;
        }

        if ($quantity <= 0) {
            $this->response = "Quantity received must be greater than zero.";
            return false;
        }

        if ($supplierId > 0) {
            $supplierCheck = $this->con->prepare("SELECT id FROM suppliers WHERE id = ?");
            $supplierCheck->execute([$supplierId]);
            if (!$supplierCheck->fetch()) {
                $this->response = "Selected supplier is invalid. Please choose a valid supplier or leave it blank.";
                return false;
            }
        }

        try {
            $this->con->beginTransaction();

            $batchNumber = $this->generateBatchNumber($productId);

            $stmt = $this->con->prepare("
                INSERT INTO inventory 
                (product_id, supplier_id, batch_number, date_received, expiry_date, 
                 purchase_cost, markup, sale_price, received_quantity, current_quantity) 
                VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $productId,
                $supplierId > 0 ? $supplierId : null,
                $batchNumber,
                $expiryDate,
                $purchaseCost,
                $markup,
                $salePrice,
                $quantity,
                $quantity
            ]);

            $this->con->commit();

            // AUDIT (Task 42)
            mmb_log_activity($this->con, 'inventory', 'batch_add',
                "Added batch '{$batchNumber}' ({$quantity} packs) to " . mmb_audit_product_name($this->con, $productId),
                'product', $productId);

            $this->response = "Stock batch added successfully";
            return true;
        } catch (\Exception $e) {
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            $this->response = "Failed to add stock batch: " . $e->getMessage();
            return false;
        }
    }

    public function disposeInventoryBatch()
    {
        if (!isset($_POST['disposeInventoryBatch'])) {
            return false;
        }

        $inventoryId = (int) ($_POST['inventory_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Disposed');

        if ($inventoryId <= 0 || $quantity < 0) {
            $this->response = "Invalid inventory batch or quantity";
            return false;
        }

        try {
            $this->con->beginTransaction();

            $batchStmt = $this->con->prepare("SELECT id, product_id, batch_number, current_quantity, expiry_date FROM inventory WHERE id = ? FOR UPDATE");
            $batchStmt->execute([$inventoryId]);
            $batch = $batchStmt->fetch();

            if (!$batch) {
                $this->con->rollBack();
                $this->response = "Inventory batch not found";
                return false;
            }

            $availableQty = (int) ($batch['current_quantity'] ?? 0);
            if ($availableQty > 0 && $quantity > $availableQty) {
                $this->con->rollBack();
                $this->response = "Disposal quantity exceeds available stock";
                return false;
            }

            if ($availableQty <= 0 && $quantity > 0) {
                $this->con->rollBack();
                $this->response = "This batch has no current stock available to dispose. Use 0 quantity to record an empty-batch disposal.";
                return false;
            }

            if ($availableQty <= 0) {
                $this->con->prepare("UPDATE inventory SET current_quantity = 0 WHERE id = ?")->execute([$inventoryId]);
            } elseif ($quantity === $availableQty) {
                $this->con->prepare("UPDATE inventory SET current_quantity = 0 WHERE id = ?")->execute([$inventoryId]);
            } elseif ($quantity > 0) {
                $this->con->prepare("UPDATE inventory SET current_quantity = current_quantity - ? WHERE id = ?")->execute([$quantity, $inventoryId]);
            }

            $insertStmt = $this->con->prepare("INSERT INTO inventory_disposals (product_id, batch_number, quantity, expiry_date, reason, disposed_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([
                (int) ($batch['product_id'] ?? 0),
                $batch['batch_number'] ?? null,
                $quantity,
                $batch['expiry_date'] ?? null,
                $reason ?: 'Disposed'
            ]);

            $this->con->commit();

            // AUDIT (Task 42)
            $disposalName = mmb_audit_product_name($this->con, (int) ($batch['product_id'] ?? 0));
            mmb_log_activity($this->con, 'inventory', 'batch_disposal',
                "Disposed {$quantity} packs from batch '" . ($batch['batch_number'] ?? '') . "' of {$disposalName} (reason: " . ($reason ?: 'Disposed') . ")",
                'inventory', $inventoryId);

            $this->response = "Inventory batch disposed successfully";
            return true;
        } catch (\Exception $e) {
            $this->con->rollBack();
            $this->response = "Failed to dispose inventory batch: " . $e->getMessage();
            return false;
        }
    }

    // LOW STOCK ALERT (FULL HTML OUTPUT)
    public function getLowStockAlertItems($limit = null)
    {
        $this->ensureInventoryNoStockTable();
        $lowStockThreshold = $this->getInventoryAlertSetting('low_stock_threshold', 15, 1, 100000);
        $hasGeneric = $this->hasColumn('products', 'generic_name');
        $hasBranded = $this->hasColumn('products', 'branded_name');

        if ($hasGeneric && $hasBranded) {
            $nameSelect = "p.generic_name, p.branded_name, ";
        } else if ($hasGeneric) {
            $nameSelect = "p.generic_name, '' AS branded_name, ";
        } else if ($this->hasColumn('products', 'product_name')) {
            $nameSelect = "p.product_name AS generic_name, '' AS branded_name, ";
        } else {
            $nameSelect = "'' AS generic_name, '' AS branded_name, ";
        }

        $stmt = $this->con->prepare("
            SELECT 
                p.id,
                {$nameSelect}
                COALESCE(SUM(i.current_quantity), 0) AS quantity
            FROM products p
            INNER JOIN inventory i ON p.id = i.product_id
            WHERE NOT EXISTS (
                SELECT 1
                FROM inventory_no_stock n
                WHERE n.product_id = i.product_id
                    AND n.batch_number <=> i.batch_number
                    AND n.expiry_date <=> i.expiry_date
            )
              AND NOT EXISTS (
                SELECT 1
                FROM inventory_disposals d
                WHERE d.product_id = i.product_id
                    AND d.batch_number <=> i.batch_number
                    AND d.expiry_date <=> i.expiry_date
            )
            GROUP BY p.id
            HAVING quantity <= ? OR SUM(CASE WHEN i.current_quantity <= 0 THEN 1 ELSE 0 END) > 0
        ");

        $stmt->execute([$lowStockThreshold]);
        $rows = $stmt->fetchAll();

        $batchNumberSelect = $this->hasColumn('inventory', 'batch_number') ? ', i.batch_number' : '';
        $batchStmt = $this->con->prepare("SELECT i.id, i.current_quantity{$batchNumberSelect}
            FROM inventory i
            WHERE i.product_id = ? AND i.current_quantity >= 0
                AND NOT EXISTS (
                    SELECT 1
                    FROM inventory_no_stock n
                    WHERE n.product_id = i.product_id
                        AND n.batch_number <=> i.batch_number
                        AND n.expiry_date <=> i.expiry_date
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM inventory_disposals d
                    WHERE d.product_id = i.product_id
                        AND d.batch_number <=> i.batch_number
                        AND d.expiry_date <=> i.expiry_date
                )
            ORDER BY i.id ASC");

        foreach ($rows as &$row) {
            $branded = trim($row['branded_name'] ?? '');
            $generic = trim($row['generic_name'] ?? '');
            if ($branded !== '' && $generic !== '') {
                $row['product_name'] = $branded . ' (' . $generic . ')';
            } else if ($generic !== '') {
                $row['product_name'] = $generic;
            } else if ($branded !== '') {
                $row['product_name'] = $branded;
            } else {
                $row['product_name'] = 'Product #' . $row['id'];
            }

            $batchStmt->execute([(int) $row['id']]);
            $allBatches = $batchStmt->fetchAll();
            $zeroStockBatches = array_values(array_filter($allBatches, static function (array $batch): bool {
                return (int) ($batch['current_quantity'] ?? 0) <= 0;
            }));
            $row['has_no_stock_batch'] = !empty($zeroStockBatches);
            $row['no_stock_batch_names'] = array_values(array_filter(array_map(static function (array $batch): string {
                $batchNumber = trim((string) ($batch['batch_number'] ?? ''));
                return $batchNumber !== '' ? $batchNumber : 'Batch #' . (int) $batch['id'];
            }, $zeroStockBatches)));
            $row['batches'] = $row['has_no_stock_batch']
                ? $zeroStockBatches
                : $allBatches;
        }

        return $rows;
    }

    public function renderLowStockAlert($limit = 50)
    {
        $items = $this->getLowStockAlertItems($limit);

        if (empty($items)) {
            return ""; // no alert
        }

        $html = '<div id="lowStockAlert">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<h5 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock</h5>';
        $html .= '<button type="button" class="btn-close btn-close-sm" onclick="this.parentElement.parentElement.remove()"></button>';
        $html .= '</div><div class="alert-items-container">';

        foreach ($items as $item) {
            $displayName = $item['product_name'];
            $html .= '<div class="alert-item d-flex justify-content-between mb-2 pb-2 border-bottom">';
            $html .= '<span class="text-truncate pe-2">' . htmlspecialchars($displayName) . '</span>';
            $html .= '<span class="badge bg-danger">' . $item['quantity'] . ' left</span>';
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function getExpiryAlertItems()
    {
        $nearExpiryDays = $this->getInventoryAlertSetting('near_expiry_days', 60, 1, 3650);
        if (!$this->hasColumn('inventory', 'batch_number')) {
            $this->con->exec("ALTER TABLE inventory ADD COLUMN batch_number VARCHAR(100) DEFAULT NULL");
        }

        // One-time guarded migration for soft-delete support
        if (!$this->hasColumn('products', 'is_hidden')) {
            $this->con->exec("ALTER TABLE products ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0");
        }

        $hasGeneric = $this->hasColumn('products', 'generic_name');
        $hasBranded = $this->hasColumn('products', 'branded_name');
        $hasStrength = $this->hasColumn('products', 'strength');

        if ($hasGeneric && $hasBranded) {
            $nameSelect = "p.generic_name, p.branded_name, ";
        } else if ($hasGeneric) {
            $nameSelect = "p.generic_name, '' AS branded_name, ";
        } else if ($this->hasColumn('products', 'product_name')) {
            $nameSelect = "p.product_name AS generic_name, '' AS branded_name, ";
        } else {
            $nameSelect = "'' AS generic_name, '' AS branded_name, ";
        }

        $strengthSelect = $hasStrength ? "p.strength" : "'' AS strength";

        $sql = "
            SELECT
                i.id,
                i.product_id,
                i.expiry_date,
                i.batch_number,
                {$nameSelect}
                {$strengthSelect}
            FROM inventory i
            LEFT JOIN products p ON p.id = i.product_id
            WHERE i.expiry_date IS NOT NULL
              AND TRIM(i.expiry_date) <> ''
                            AND i.current_quantity > 0
                            AND NOT EXISTS (
                                SELECT 1
                                FROM inventory_no_stock n
                                WHERE n.product_id = i.product_id
                                        AND n.batch_number <=> i.batch_number
                                        AND n.expiry_date <=> i.expiry_date
                            )
                            AND NOT EXISTS (
                                SELECT 1
                                FROM inventory_disposals d
                                WHERE d.product_id = i.product_id
                                        AND d.batch_number <=> i.batch_number
                                        AND d.expiry_date <=> i.expiry_date
                            )
            ORDER BY i.expiry_date ASC, i.id ASC
        ";

        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        $batches = $stmt->fetchAll();

        $items = [];
        $today = new \DateTime('today');

        foreach ($batches as $batch) {
            $expiryDate = trim((string) ($batch['expiry_date'] ?? ''));
            if ($expiryDate === '') {
                continue;
            }

            try {
                $expDate = new \DateTime($expiryDate);
                $expDate->setTime(0, 0, 0);
                $today->setTime(0, 0, 0);
            } catch (\Exception $e) {
                continue;
            }

            $interval = $today->diff($expDate);
            $daysLeft = (int) $interval->days;

            $brandedName = trim($batch['branded_name'] ?? '');
            $genericName = trim($batch['generic_name'] ?? '');
            $strength = trim($batch['strength'] ?? '');
            $batchNumber = trim((string) ($batch['batch_number'] ?? ''));

            if ($brandedName !== '' && $genericName !== '') {
                $displayName = $brandedName . ' - ' . $genericName . ' (' . $strength . ')';
            } elseif ($brandedName !== '') {
                $displayName = $brandedName . ' (' . $strength . ')';
            } elseif ($genericName !== '') {
                $displayName = $genericName . ' (' . $strength . ')';
            } else {
                $displayName = 'Unnamed Product';
            }

            $productName = $displayName;
            if ($batchNumber !== '') {
                $displayName .= ' (' . $batchNumber . ')';
            }

            if ($expDate <= $today) {
                $items[] = [
                    'product_id' => (int) ($batch['product_id'] ?? 0),
                    'batch_id' => (int) ($batch['id'] ?? 0),
                    'product_name' => $productName,
                    'name' => $displayName,
                    'status' => 'Expired',
                    'days_left' => 0,
                    'expiry_date' => $expiryDate
                ];
            } elseif ($daysLeft <= $nearExpiryDays && !$interval->invert) {
                $items[] = [
                    'product_id' => (int) ($batch['product_id'] ?? 0),
                    'batch_id' => (int) ($batch['id'] ?? 0),
                    'product_name' => $productName,
                    'name' => $displayName,
                    'status' => 'Near Expiry',
                    'days_left' => $daysLeft,
                    'expiry_date' => $expiryDate
                ];
            }
        }

        return $items;
    }

    private function getInventoryAlertSetting(string $key, int $default, int $min, int $max): int
    {
        try {
            $stmt = $this->con->prepare('SELECT setting_value FROM store_settings WHERE setting_key = ?');
            $stmt->execute([$key]);
            $value = $stmt->fetchColumn();
            if (is_numeric($value) && (int) $value >= $min && (int) $value <= $max) {
                return (int) $value;
            }
        } catch (\Throwable $e) {
            // Use the default when settings storage is unavailable.
        }
        return $default;
    }

    public function renderExpiryAlert()
    {
        $items = $this->getExpiryAlertItems();

        if (empty($items)) {
            return '';
        }

        $html = '<div id="expiryAlert">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<h5 class="mb-0 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Expiring Soon</h5>';
        $html .= '<button type="button" class="btn-close btn-close-sm" onclick="this.parentElement.parentElement.remove()"></button>';
        $html .= '</div><div class="alert-items-container">';

        foreach ($items as $item) {
            if ($item['status'] === 'Expired') {
                $html .= '<div class="alert-item d-flex justify-content-between mb-2 pb-2 border-bottom">';
                $html .= '<span class="text-truncate pe-2">' . htmlspecialchars($item['name']) . '</span>';
                $html .= '<span class="badge bg-danger">Expired</span></div>';
            } else {
                $html .= '<div class="alert-item d-flex justify-content-between mb-2 pb-2 border-bottom">';
                $html .= '<span class="text-truncate pe-2">' . htmlspecialchars($item['name']) . '</span>';
                $html .= '<span class="badge bg-info text-dark">Near Expiry</span></div>';
            }
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function getExpiryStatus(array $products): array
    {
        $expired = [];
        $near = [];

        $today = new \DateTime();

        foreach ($products as $prod) {
            if (empty($prod['expiry_date']))
                continue;

            $expDate = new \DateTime($prod['expiry_date']);
            $interval = $today->diff($expDate);

            if ($expDate <= $today) {
                $expired[] = $prod;
            } elseif ($interval->days <= 90 && !$interval->invert) {
                $near[] = $prod;
            }
        }

        return [
            'expired' => $expired,
            'near' => $near
        ];
    }

    public function getResponse()
    {
        return $this->response;
    }
}