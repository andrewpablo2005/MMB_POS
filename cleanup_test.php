<?php
require_once __DIR__ . '/conn/database.php';
$db = Database::getConnection();
$db->prepare('DELETE FROM inventory WHERE batch_number LIKE ?')->execute(['TEST-BATCH-%']);
echo "✅ Cleaned up test data\n";
?>
