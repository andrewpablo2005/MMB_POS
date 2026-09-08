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

    $name = preg_replace('/\s+/', ' ', trim((string) ($_POST['category_name'] ?? '')));
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Category name is required.']);
        exit;
    }

    $duplicateStmt = $db->prepare('SELECT id FROM product_categories WHERE LOWER(TRIM(category_name)) = LOWER(?) LIMIT 1');
    $duplicateStmt->execute([$name]);
    if ($duplicateStmt->fetchColumn()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'This category already exists.']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO product_categories (category_name, has_vat, senior_discount, pwd_discount) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $name,
        !empty($_POST['has_vat']) ? 1 : 0,
        !empty($_POST['senior_discount']) ? 1 : 0,
        !empty($_POST['pwd_discount']) ? 1 : 0
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully.',
        'id' => (int) $db->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
