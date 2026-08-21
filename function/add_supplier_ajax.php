<?php
header('Content-Type: application/json');
session_start();

try {
    require_once __DIR__ . '/../conn/database.php';
    
    $db = Database::getConnection();
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    // Get POST data
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Validate required field
    if (empty($supplier_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Supplier name is required']);
        exit;
    }
    
    // Check for duplicate (case-insensitive)
    $stmt = $db->prepare("SELECT id, supplier_name FROM suppliers WHERE LOWER(TRIM(supplier_name)) = LOWER(TRIM(:supplier_name))");
    $stmt->execute(['supplier_name' => $supplier_name]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        http_response_code(409); // Conflict - Already exists
        echo json_encode([
            'error' => 'Supplier already exists',
            'message' => 'A supplier with the name "' . htmlspecialchars($existing['supplier_name']) . '" already exists in the database.',
            'existing_id' => $existing['id']
        ]);
        exit;
    }
    
    // Insert new supplier
    $stmt = $db->prepare("
        INSERT INTO suppliers 
        (supplier_name, contact_number, email, address, is_active, created_at, updated_at) 
        VALUES 
        (:supplier_name, :contact_number, :email, :address, 1, NOW(), NOW())
    ");
    
    $result = $stmt->execute([
        'supplier_name' => $supplier_name,
        'contact_number' => $contact_number ?: null,
        'email' => $email ?: null,
        'address' => $address ?: null
    ]);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add supplier']);
        exit;
    }
    
    $supplier_id = $db->lastInsertId();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Supplier added successfully',
        'supplier_id' => $supplier_id,
        'supplier_name' => $supplier_name
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
