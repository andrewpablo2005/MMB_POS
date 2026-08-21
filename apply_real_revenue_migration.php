<?php
require_once __DIR__ . '/conn/database.php';

$columnStmt = $db->prepare(
    "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transaction_items' AND COLUMN_NAME = 'batch_id'"
);
$columnStmt->execute();

if (!(int)$columnStmt->fetchColumn()) {
    $db->exec("ALTER TABLE transaction_items
        ADD COLUMN batch_id INT NULL AFTER product_id,
        ADD KEY idx_transaction_items_batch_id (batch_id),
        ADD CONSTRAINT fk_transaction_items_batch
            FOREIGN KEY (batch_id) REFERENCES inventory (id) ON DELETE SET NULL");
}

$db->exec("CREATE TABLE IF NOT EXISTS transaction_item_batches (
    id INT NOT NULL AUTO_INCREMENT,
    transaction_item_id INT NOT NULL,
    inventory_id INT NOT NULL,
    quantity INT NOT NULL,
    purchase_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_transaction_item_batch (transaction_item_id, inventory_id),
    KEY idx_transaction_item_batches_inventory (inventory_id),
    CONSTRAINT fk_transaction_item_batches_item
        FOREIGN KEY (transaction_item_id) REFERENCES transaction_items (id) ON DELETE CASCADE,
    CONSTRAINT fk_transaction_item_batches_inventory
        FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

echo "Real revenue schema is ready." . PHP_EOL;