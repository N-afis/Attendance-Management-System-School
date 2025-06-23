<?php

require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    
    if (
        !isset($data['first_name'], $data['last_name'], $data['email'], $data['gender'], $data['dob'], $data['class_id']) ||
        empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) ||
        empty($data['gender']) || empty($data['dob']) || empty($data['class_id'])
    ) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $student = new Student($pdo);

    $student->first_name = htmlspecialchars(trim($data["first_name"]));
    $student->last_name = htmlspecialchars(trim($data["last_name"]));
    $student->email = htmlspecialchars(trim($data["email"]));
    $student->gender = $data["gender"];
    $student->date_of_birth = $data["dob"];
    $student->class_id = $data["class_id"];

    if ($student->create()) {
        echo json_encode(["success" => true, "message" => "Student added successfully!"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Failed to add student."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Unexpected error: " . $e->getMessage()]);
}
