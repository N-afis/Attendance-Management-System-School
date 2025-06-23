<?php

require_once '../../config/database.php';
require_once '../../classes/StudentAttendance.php';

header('Content-Type: application/json');

// Create database connection
$db = new Database();
$pdo = $db->getConnection();

$studentAttendance = new StudentAttendance($pdo);

echo json_encode($studentAttendance->topAbsent());

?>