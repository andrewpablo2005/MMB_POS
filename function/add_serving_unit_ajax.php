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

    $name = preg_replace('/\s+/', ' ', trim((string) ($_POST['measurement_name'] ?? '')));
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Serving unit name is required.']);
        exit;
    }
    $duplicateStmt = $db->prepare('SELECT id, serving_unit_name FROM serving_unit WHERE LOWER(TRIM(serving_unit_name)) = LOWER(?) LIMIT 1');
    $duplicateStmt->execute([$name]);
    $existing = $duplicateStmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'This serving unit already exists.', 'existing_id' => (int) $existing['id'], 'existing_name' => trim((string) $existing['serving_unit_name'])]);
        exit;
    }
    $stmt = $db->prepare('INSERT INTO serving_unit (serving_unit_name) VALUES (?)');
    $stmt->execute([$name]);
    echo json_encode(['success' => true, 'message' => 'Serving unit saved successfully.', 'id' => (int) $db->lastInsertId(), 'name' => $name]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
