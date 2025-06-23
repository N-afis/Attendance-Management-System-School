<?php

require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $teacher = new Teacher($pdo);

    $stmt = $teacher->readall();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        "success" => true,
        "data" => $teachers,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Unexpected error: " . $e->getMessage()
    ]);
}
