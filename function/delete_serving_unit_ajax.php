<?php
header('Content-Type: application/json');
session_start();

try {
    require_once __DIR__ . '/../conn/database.php';
    $db = Database::getConnection();
    if (empty($_SESSION['user_id']) || !in_array(strtolower((string) ($_SESSION['position'] ?? '')), ['owner', 'admin'], true)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Not authorized']);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT serving_unit_name FROM serving_unit WHERE id = ?');
    $stmt->execute([$id]);
    $name = (string) ($stmt->fetchColumn() ?: '');
    if ($id <= 0 || $name === '') {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Serving unit not found.']);
        exit;
    }
    $usageStmt = $db->prepare('SELECT COUNT(*) FROM products WHERE measurement_id = ?');
    $usageStmt->execute([$id]);
    if ((int) $usageStmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'This serving unit is used by a product and cannot be deleted.']);
        exit;
    }
    $deleteStmt = $db->prepare('DELETE FROM serving_unit WHERE id = ?');
    $deleteStmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Serving unit deleted successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
