<?php

require_once '../../config/database.php';
require_once '../../classes/TeacherAttendance.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $teacherAttendance = new TeacherAttendance($pdo);

    $teachersRecords = $teacherAttendance->readAll();

    echo json_encode([
        "success" => true,
        "data" => $teachersRecords,
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
