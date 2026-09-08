<?php
/**
 * TEMPORARY diagnostic endpoint (Task 35 investigation).
 * Owner-only. Reports which tables/queries are broken after manual DB edits.
 * WILL BE DELETED in the fix commit — do not link anywhere.
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/plain; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Forbidden: not logged in.");
}
$position = strtolower(trim((string)($_SESSION['position'] ?? '')));
if ($position !== 'owner' && $position !== 'admin') {
    http_response_code(403);
    exit("Forbidden: owner/admin only.");
}

require_once __DIR__ . '/conn/database.php';

echo "=== 1. TABLE EXISTENCE + ROW COUNTS ===\n";
$expected = [
    'products', 'product_categories', 'unit_measurement', 'suppliers',
    'inventory', 'transactions', 'transaction_items', 'discounts',
    'users', 'users_info', 'returns', 'return_items', 'override_log',
    'store_settings', 'notifications',
];
try {
    $rows = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $existing = array_flip($rows);
} catch (Throwable $e) {
    $existing = [];
    echo "SHOW TABLES FAILED: " . $e->getMessage() . "\n";
}
foreach ($expected as $t) {
    if (!isset($existing[$t])) {
        // maybe table name differs (prefix etc.) — check case-insensitively
        $found = null;
        foreach ($rows ?? [] as $r) {
            if (strtolower($r) === strtolower($t)) { $found = $r; break; }
        }
        echo sprintf("%-22s MISSING%s\n", $t, $found ? " (found as: $found)" : '');
        continue;
    }
    try {
        $c = (int)$db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo sprintf("%-22s exists, %d rows\n", $t, $c);
    } catch (Throwable $e) {
        echo sprintf("%-22s exists, COUNT FAILED: %s\n", $t, $e->getMessage());
    }
}

echo "\n=== 2. ALL TABLES PRESENT (SHOW TABLES) ===\n";
foreach ($rows ?? [] as $r) { echo "  $r\n"; }

echo "\n=== 3. BROKEN-PAGE QUERY TESTS ===\n";
function diag_try(string $label, callable $fn): void {
    try {
        $r = $fn();
        $n = is_array($r) ? count($r) : 'n/a';
        echo "OK    $label (returned $n rows)\n";
    } catch (Throwable $e) {
        echo "FAIL  $label\n      -> " . get_class($e) . ": " . $e->getMessage() . "\n";
    }
}

diag_try('workingpos Product::getProducts()', function () use ($db) {
    require_once __DIR__ . '/function/workingpos.php';
    $p = new Product($db);
    return $p->getProducts();
});
diag_try('addprodfunct ProductManagement::getAllInventoryBatches()', function () use ($db) {
    require_once __DIR__ . '/function/addprodfunct.php';
    $m = new Classes\ProductManagement($db);
    return $m->getAllInventoryBatches();
});
diag_try('addprodfunct ProductManagement::getDisposedBatches()', function () use ($db) {
    require_once __DIR__ . '/function/addprodfunct.php';
    $m = new Classes\ProductManagement($db);
    return $m->getDisposedBatches();
});
diag_try('addprodfunct ProductManagement::getReturnedProducts()', function () use ($db) {
    require_once __DIR__ . '/function/addprodfunct.php';
    $m = new Classes\ProductManagement($db);
    return $m->getReturnedProducts();
});
diag_try('Reports::getAllReports()', function () use ($db) {
    require_once __DIR__ . '/function/Reports.php';
    $r = new Classes\Reports($db);
    return $r->getAllReports();
});
diag_try('ProductManagement::getAllProducts()', function () use ($db) {
    require_once __DIR__ . '/function/addprodfunct.php';
    $m = new Classes\ProductManagement($db);
    return $m->getAllProducts();
});
diag_try('ProductManagement::getUnitMeasurements()', function () use ($db) {
    require_once __DIR__ . '/function/addprodfunct.php';
    $m = new Classes\ProductManagement($db);
    return $m->getUnitMeasurements();
});

echo "\n=== 4. DISCOUNT ROWS (POS needs the rate-0 default) ===\n";
try {
    foreach ($db->query("SELECT id, discount_name, discount_rate, is_vat_exempt FROM discounts ORDER BY id")->fetchAll(PDO::FETCH_ASSOC) as $d) {
        echo "  #" . $d['id'] . " {$d['discount_name']} rate={$d['discount_rate']} vat_exempt={$d['is_vat_exempt']}\n";
    }
} catch (Throwable $e) {
    echo "  discounts query failed: " . $e->getMessage() . "\n";
}

echo "\nDONE\n";
