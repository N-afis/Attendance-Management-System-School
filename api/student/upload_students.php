<?php
require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

// Read raw JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data || !isset($data["email"])) {
    echo json_encode(["success" => false, "message" => "Invalid input or missing email."]);
    exit;
}

function excelDateToMySQLDate($excelDate)
{
    if (is_numeric($excelDate)) {
        $timestamp = ($excelDate - 25569) * 86400;
        return date("Y-m-d", $timestamp);
    }
    return date("Y-m-d", strtotime($excelDate)); // fallback for already formatted dates
}


// Initialize DB
$db = new Database();
$pdo = $db->getConnection();
$student = new Student($pdo);

// Find class id by name
$stmt = $pdo->prepare('SELECT id FROM classes WHERE name = ?');
$stmt->execute([$data['class_name']]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

// Assign values from JSON
$student->first_name = htmlspecialchars(strip_tags($data["first_name"] ?? ""));
$student->last_name = htmlspecialchars(strip_tags($data["last_name"] ?? ""));
$student->email = htmlspecialchars(strip_tags($data["email"]));
$student->gender = htmlspecialchars(strip_tags($data["gender"] ?? ""));
$student->date_of_birth = $data['dob'];
$student->class_id = htmlspecialchars(strip_tags($class['id'] ?? ""));

// Check if email already exists
if ($student->existsByEmail()) {
    echo json_encode(["success" => false, "message" => "Student already exists."]);
    exit;
}

// Attempt to create
if ($student->create()) {
    echo json_encode(["success" => true, "message" => "Student added successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add student."]);
}
