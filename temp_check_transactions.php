<?php
require_once __DIR__ . '/conn/database.php';
$stmt = $db->prepare('SELECT t.id, t.total_amount, t.discount_total, t.total_vat_exemption, ti.product_id, ti.quantity, ti.price, ti.subtotal FROM transactions t JOIN transaction_items ti ON ti.transaction_id = t.id ORDER BY t.id DESC LIMIT 20');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
