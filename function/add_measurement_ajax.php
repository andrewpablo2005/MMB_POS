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

    $measurementName = trim((string) ($_POST['measurement_name'] ?? ''));
    if ($measurementName === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Measurement name is required.']);
        exit;
    }

    $normalized = preg_replace('/\s+/', ' ', $measurementName);
    if ($normalized === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Measurement name is required.']);
        exit;
    }

    $duplicateStmt = $db->prepare("SELECT unit_id, different_measurement FROM unit_measurement WHERE LOWER(TRIM(different_measurement)) = LOWER(?) LIMIT 1");
    $duplicateStmt->execute([$normalized]);
    $existing = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode([
            'success' => false,
            'message' => 'This measurement already exists.',
            'existing_id' => (int) ($existing['unit_id'] ?? 0),
            'existing_name' => trim((string) ($existing['different_measurement'] ?? ''))
        ]);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO unit_measurement (different_measurement) VALUES (?)");
    $inserted = $stmt->execute([$normalized]);

    if (!$inserted) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to insert measurement unit.']);
        exit;
    }

    $unitId = (int) $db->lastInsertId();
    echo json_encode([
        'success' => true,
        'message' => 'Measurement saved successfully.',
        'id' => $unitId,
        'name' => $normalized
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
