ALTER TABLE transaction_items
    ADD COLUMN batch_id INT NULL AFTER product_id,
    ADD KEY idx_transaction_items_batch_id (batch_id),
    ADD CONSTRAINT fk_transaction_items_batch
        FOREIGN KEY (batch_id) REFERENCES inventory (id) ON DELETE SET NULL;

CREATE TABLE transaction_item_batches (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE return_items
    ADD COLUMN restocked TINYINT(1) NOT NULL DEFAULT 0 AFTER item_type;