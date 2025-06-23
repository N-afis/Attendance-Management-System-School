<?php

require_once '../../config/database.php';
require_once '../../classes/StudentAttendance.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

$studentAttendance = new StudentAttendance($pdo);

$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;
$pole_id = $_GET['pole_id'] ?? null;
$filiere_id = $_GET['filiere_id'] ?? null;
$class_id = $_GET['class_id'] ?? null;
$keyword = $_GET['keyword'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Base WHERE clause
$where = "WHERE 1";
$params = [];

if (!empty($from_date)) {
    $where .= " AND sa.date >= :from_date";
    $params[':from_date'] = $from_date;
}
if (!empty($to_date)) {
    $where .= " AND sa.date <= :to_date";
    $params[':to_date'] = $to_date;
}
if (!empty($pole_id)) {
    $where .= " AND p.id = :pole_id";
    $params[':pole_id'] = $pole_id;
}

if (!empty($filiere_id)) {
    $where .= " AND f.id = :filiere_id";
    $params[':filiere_id'] = $filiere_id;
}

if (!empty($class_id)) {
    $where .= " AND c.id = :class_id";
    $params[':class_id'] = $class_id;
}

if (!empty($keyword)) {
    $where .= " AND (s.first_name LIKE :kw 
               OR s.last_name LIKE :kw2)";
    $params[':kw'] = "%$keyword%";
    $params[':kw2'] = "%$keyword%";
}

// 🔢 1. Get total count for pagination
$totalRecords = $studentAttendance->filterCountAll($where, $params);
$totalPages = ceil($totalRecords / $limit);

// 📄 2. Fetch paginated results
$studentsRecords = $studentAttendance->filter($where, $params, $limit, $offset);

// 📦 Return structured JSON
echo json_encode([
    'students' => $studentsRecords,
    'total_pages' => $totalPages,
    'current_page' => $page
]);
