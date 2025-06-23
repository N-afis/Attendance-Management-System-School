<?php

require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();
$teacher = new Teacher($pdo);

try {
    $input = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($input['id'])) {
            // single delete
            $teacher->id = intval($input['id']);
            if ($teacher->delete()) {
                echo json_encode(["success" => true, "message" => "Teacher deleted successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to delete teacher."]);
            }
        } elseif (isset($input['ids']) && is_array($input['ids'])) {
            // multiple delete
            $errors = 0;
            foreach ($input['ids'] as $id) {
                if (!is_numeric($id)) continue;
                $teacher->id = intval($id);
                if (!$teacher->delete()) {
                    $errors++;
                }
            }
            if ($errors === 0) {
                echo json_encode(["success" => true, "message" => "All selected teachers deleted successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "$errors teacher(s) could not be deleted."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid request data."]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
