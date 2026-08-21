<?php
chdir(__DIR__ . '/function');
require_once __DIR__ . '/conn/database.php';
require_once __DIR__ . '/function/addprodfunct.php';

$connection = Database::getConnection();
$pm = new \Classes\ProductManagement($connection);
$items = $pm->getLowStockAlertItems();

if (!empty($items)) {
    echo "✅ getLowStockAlertItems returned " . count($items) . " items\n";
    echo "Array keys in first item: " . implode(', ', array_keys($items[0])) . "\n";
    echo "Quantity value: " . ($items[0]['quantity'] ?? 'NOT FOUND') . "\n";
    echo "\nFirst item data:\n";
    foreach ($items[0] as $key => $value) {
        echo "  $key: $value\n";
    }
}
?>
