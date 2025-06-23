<?php
require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

// Read raw JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data || !isset($data["email"])) {
    echo json_encode(["success" => false, "message" => "Invalid input or missing email."]);
    exit;
}

// Initialize DB
$db = new Database();
$pdo = $db->getConnection();
$teacher = new Teacher($pdo);


// Assign values from JSON
$teacher->first_name = htmlspecialchars(strip_tags($data["first_name"] ?? ""));
$teacher->last_name = htmlspecialchars(strip_tags($data["last_name"] ?? ""));
$teacher->email = htmlspecialchars(strip_tags($data["email"]));
$teacher->gender = htmlspecialchars(strip_tags($data["gender"] ?? ""));
$teacher->date_of_birth = htmlspecialchars(strip_tags($data["dob"] ?? ""));

// Check if email already exists
if ($teacher->existsByEmail()) {
    echo json_encode(["success" => false, "message" => "Teacher already exists."]);
    exit;
}

// Attempt to create
if ($teacher->create()) {
    echo json_encode(["success" => true, "message" => "Teacher added successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add teacher."]);
}
