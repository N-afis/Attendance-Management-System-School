<?php
require_once '../config/database.php';

// Create database connection
$db = new Database();
$pdo = $db->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['pole_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'pole_id parameter is required']);
    exit;
}

try {
    $pole_id = intval($_GET['pole_id']);
    $stmt = $pdo->prepare("SELECT id, name FROM filieres WHERE pole_id = ?");
    $stmt->execute([$pole_id]);
    
    $filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($filieres);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>