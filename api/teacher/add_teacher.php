<?php

require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);


    if (
        !isset($data['first_name'], $data['last_name'], $data['email'], $data['gender'], $data['dob']) ||
        empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) ||
        empty($data['gender']) || empty($data['dob'])
    ) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $teacher = new Teacher($pdo);

    $teacher->first_name = htmlspecialchars(trim($data["first_name"]));
    $teacher->last_name = htmlspecialchars(trim($data["last_name"]));
    $teacher->email = htmlspecialchars(trim($data["email"]));
    $teacher->gender = $data["gender"];
    $teacher->date_of_birth = $data["dob"];

    if ($teacher->create()) {
        echo json_encode(["success" => true, "message" => "Teacher added successfully!"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Failed to add teacher."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Unexpected error: " . $e->getMessage()]);
}
