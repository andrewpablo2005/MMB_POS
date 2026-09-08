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

    $formName = trim((string) ($_POST['form_name'] ?? ''));
    $normalized = preg_replace('/\s+/', ' ', $formName);
    if ($normalized === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Product form is required.']);
        exit;
    }

    $duplicateStmt = $db->prepare('SELECT id, form_name FROM dosage_forms WHERE LOWER(TRIM(form_name)) = LOWER(?) LIMIT 1');
    $duplicateStmt->execute([$normalized]);
    $existing = $duplicateStmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        echo json_encode([
            'success' => false,
            'message' => 'This product form already exists.',
            'existing_id' => (int) $existing['id'],
            'existing_name' => trim((string) $existing['form_name'])
        ]);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO dosage_forms (form_name) VALUES (?)');
    $stmt->execute([$normalized]);
    echo json_encode([
        'success' => true,
        'message' => 'Product form saved successfully.',
        'id' => (int) $db->lastInsertId(),
        'name' => $normalized
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
