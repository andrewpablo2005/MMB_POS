<?php
require_once __DIR__ . '/conn/database.php';
$db = Database::getConnection();

// Get a valid category
$r = $db->query('SELECT id FROM product_categories LIMIT 1');
$cat = $r->fetch(PDO::FETCH_ASSOC);
echo "First category ID: " . ($cat['id'] ?? 'NONE') . "\n";

// Get a valid unit
$r = $db->query('SELECT id FROM units LIMIT 1');
$unit = $r->fetch(PDO::FETCH_ASSOC);
echo "First unit ID: " . ($unit['id'] ?? 'NONE') . "\n";
?>
