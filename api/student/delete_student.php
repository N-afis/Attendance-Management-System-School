<?php

require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();
$student = new Student($pdo);

try {
    // DELETE method for single student (via query param)
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $deleteVars = json_decode(file_get_contents("php://input"), true);

        if (!isset($deleteVars['id']) || !is_numeric($deleteVars['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid student ID."]);
            exit;
        }

        $student->id = intval($deleteVars['id']);

        if ($student->delete()) {
            echo json_encode(["success" => true, "message" => "Student deleted successfully!"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete student."]);
        }
    }


    // POST method for multiple deletions (via JSON)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['ids']) || !is_array($input['ids'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "No student IDs provided."]);
            exit;
        }

        $errors = 0;
        foreach ($input['ids'] as $id) {
            if (!is_numeric($id)) continue;
            $student->id = intval($id);
            if (!$student->delete()) {
                $errors++;
            }
        }

        if ($errors === 0) {
            echo json_encode(["success" => true, "message" => "All selected students deleted successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "$errors student(s) could not be deleted."]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
