<?php

require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

try {
    // Get and decode the raw JSON
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Validate JSON structure
    if (!$data || !isset($data["id"])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "success" => false,
            "message" => "Invalid or missing JSON data.",
            "raw" => $rawData // Helpful for debugging
        ]);
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $teacher = new Teacher($pdo);

    // Assign properties safely
    $teacher->id = intval($data["id"]);
    $teacher->first_name = htmlspecialchars(trim($data["first_name"] ?? ""));
    $teacher->last_name = htmlspecialchars(trim($data["last_name"] ?? ""));
    $teacher->email = filter_var($data["email"] ?? "", FILTER_SANITIZE_EMAIL);
    $teacher->gender = $data["gender"] ?? "";
    $teacher->date_of_birth = $data["date_of_birth"] ?? "";

    // Perform update
    if ($teacher->update()) {
        echo json_encode(["success" => true, "message" => "Teacher updated successfully!"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Failed to update teacher."]);
    }
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
