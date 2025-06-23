<?php

session_start();

require_once '../../config/database.php';
require_once '../../classes/TeacherAttendance.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

$teacherAttendance = new TeacherAttendance($pdo);

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['data'])) {
    echo json_encode(["success" => false, "message" => "No data provided"]);
    exit;
}

foreach ($input['data'] as $attendance) {
    $teacherAttendance->teacher_id = $attendance['teacher_id'];
    $teacherAttendance->date = $attendance['date'];
    $teacherAttendance->status = $attendance['status'];
    $teacherAttendance->absence_start_time = $attendance['absence_start_time'];
    $teacherAttendance->absence_end_time = $attendance['absence_end_time'];
    $teacherAttendance->marked_by = $_SESSION['user_id'];

    if (!$teacherAttendance->mark()) {
        echo json_encode(["success" => false, "message" => "Failed to mark attendance for teacher ID: " . $attendance['studentId']]);
        exit;
    }
}

echo json_encode(["success" => true, "message" => "Attendance marked successfully"]);