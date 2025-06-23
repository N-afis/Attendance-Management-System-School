<?php

require_once '../../config/database.php';
require_once '../../classes/TeacherAttendance.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

$teacherAttendance = new TeacherAttendance($pdo);

$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;
$keyword = $_GET['keyword'] ?? null;

// Base WHERE clause
$where = "WHERE 1";
$params = [];

if (!empty($from_date)) {
    $where .= " AND ta.date >= :from_date";
    $params[':from_date'] = $from_date;
}

if (!empty($to_date)) {
    $where .= " AND ta.date <= :to_date";
    $params[':to_date'] = $to_date;
}

if (!empty($keyword)) {
    $where .= " AND (t.first_name LIKE :kw 
               OR t.last_name LIKE :kw2)";
    $params[':kw'] = "%$keyword%";
    $params[':kw2'] = "%$keyword%";
}


// 📄 2. Fetch paginated results
$teachersRecords = $teacherAttendance->filter($where, $params);

// 📦 Return structured JSON
echo json_encode([
    'teachers' => $teachersRecords,
]);
