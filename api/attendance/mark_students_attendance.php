<?php

session_start();

require_once '../../config/database.php';
require_once '../../classes/StudentAttendance.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

$studentAttendance = new StudentAttendance($pdo);

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['data'])) {
    echo json_encode(["success" => false, "message" => "No data provided"]);
    exit;
}

foreach ($input['data'] as $attendance) {
    $studentAttendance->student_id = $attendance['studentId'];
    $studentAttendance->date = $attendance['date'];
    $studentAttendance->status = $attendance['status'];
    $studentAttendance->absence_start_time = $attendance['absence_start_time'];
    $studentAttendance->absence_end_time = $attendance['absence_end_time'];
    $studentAttendance->marked_by = $_SESSION['user_id'];

    if (!$studentAttendance->mark()) {
        echo json_encode(["success" => false, "message" => "Failed to mark attendance for student ID: " . $attendance['studentId']]);
        exit;
    }
}

echo json_encode(["success" => true, "message" => "Attendance marked successfully"]);