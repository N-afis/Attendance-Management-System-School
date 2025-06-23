<?php

require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid ID"]);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$student = new Student($pdo);
$student->id = $id;

$row = $student->readOne();

if ($row) {
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Student not found."]);
}


?>