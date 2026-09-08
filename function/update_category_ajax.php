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

    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $categoryName = trim((string) ($_POST['category_name'] ?? ''));
    $categoryName = preg_replace('/\s+/', ' ', $categoryName);
    if ($categoryId <= 0 || $categoryName === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'A valid category and name are required.']);
        exit;
    }

    $duplicateStmt = $db->prepare('SELECT id FROM product_categories WHERE LOWER(TRIM(category_name)) = LOWER(?) AND id <> ? LIMIT 1');
    $duplicateStmt->execute([$categoryName, $categoryId]);
    if ($duplicateStmt->fetchColumn()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'A category with this name already exists.']);
        exit;
    }

    $stmt = $db->prepare('UPDATE product_categories SET category_name = ?, has_vat = ?, senior_discount = ?, pwd_discount = ? WHERE id = ?');
    $stmt->execute([
        $categoryName,
        !empty($_POST['has_vat']) ? 1 : 0,
        !empty($_POST['senior_discount']) ? 1 : 0,
        !empty($_POST['pwd_discount']) ? 1 : 0,
        $categoryId
    ]);

    if ($stmt->rowCount() === 0) {
        $existsStmt = $db->prepare('SELECT id FROM product_categories WHERE id = ?');
        $existsStmt->execute([$categoryId]);
        if (!$existsStmt->fetchColumn()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Category not found.']);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Category settings saved successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
