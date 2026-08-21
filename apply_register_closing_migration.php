<?php
require_once __DIR__ . '/conn/database.php';

$db->exec("CREATE TABLE IF NOT EXISTS register_closings (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    business_date DATE NOT NULL,
    system_cash DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    counted_cash DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    variance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes VARCHAR(255) DEFAULT NULL,
    closed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_register_closing_user_date (user_id, business_date),
    KEY idx_register_closings_date (business_date),
    CONSTRAINT fk_register_closings_user FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

echo "Register closing schema is ready." . PHP_EOL;