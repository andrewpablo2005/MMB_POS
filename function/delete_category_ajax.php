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
    if ($categoryId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'A valid category is required.']);
        exit;
    }

    $productStmt = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $productStmt->execute([$categoryId]);
    $productCount = (int) $productStmt->fetchColumn();
    if ($productCount > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => "This category cannot be deleted because {$productCount} product(s) use it."
        ]);
        exit;
    }

    $stmt = $db->prepare('DELETE FROM product_categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Category not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
