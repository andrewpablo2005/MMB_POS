<?php
require_once __DIR__ . '/conn/database.php';

$column = $db->query("SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'return_items'
      AND COLUMN_NAME = 'restocked'")->fetchColumn();

if (!(int)$column) {
    $db->exec("ALTER TABLE return_items
        ADD COLUMN restocked TINYINT(1) NOT NULL DEFAULT 0 AFTER item_type");
}

echo "Return restock tracking is ready." . PHP_EOL;