<?php

require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

$db = new Database();
$pdo = $db->getConnection();

$student = new Student($pdo);

$pole_id = $_GET['pole_id'] ?? null;
$filiere_id = $_GET['filiere_id'] ?? null;
$class_id = $_GET['class_id'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Base WHERE clause
$where = "WHERE 1";
$params = [];

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

// 🔢 1. Get total count for pagination
$totalRecords = $student->filterCountAll($where, $params);
$totalPages = ceil($totalRecords / $limit);

// 📄 2. Fetch paginated results
$students = $student->filter($where, $params, $limit, $offset);

// 📦 Return structured JSON
echo json_encode([
    'students' => $students,
    'total_pages' => $totalPages,
    'current_page' => $page
]);
