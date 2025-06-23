<?php
require_once '../config/database.php';

// Create database connection
$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['filiere_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'filiere_id parameter is required']);
    exit;
}

try {
    $filiere_id = intval($_GET['filiere_id']);
    $stmt = $pdo->prepare("SELECT id, name FROM classes WHERE filiere_id = ?");
    $stmt->execute([$filiere_id]);
    
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($classes);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>