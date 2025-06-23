<?php
require_once '../../config/database.php';
require_once '../../classes/StudentAttendance.php';
require_once '../../classes/TeacherAttendance.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['justificationDoc'])) {
    echo json_encode(['success' => false, 'message' => 'Missing file']);
    exit;
}

$isStudent = isset($_POST['student_id']);
$isTeacher = isset($_POST['teacher_id']);

if (!$isStudent && !$isTeacher) {
    echo json_encode(['success' => false, 'message' => 'Missing student or teacher ID']);
    exit;
}

$id = $isStudent ? intval($_POST['student_id']) : intval($_POST['teacher_id']);
$file = $_FILES['justificationDoc'];

$allowed = ['pdf', 'png', 'jpg', 'jpeg'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

$uploadDir = $isStudent 
    ? '../../uploads/justifications/students/' 
    : '../../uploads/justifications/teachers/';

$fileName = uniqid("just_") . "." . $ext;
$targetFile = $uploadDir . $fileName;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    $db = new Database();
    $pdo = $db->getConnection();

    if ($isStudent) {
        $attendance = new StudentAttendance($pdo);
        $attendance->uploadJust($fileName, $id);
    } else {
        $attendance = new TeacherAttendance($pdo);
        $attendance->uploadJust($fileName, $id);
    }

    echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
}
