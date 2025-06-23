<?php

require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $teacher = new Teacher($pdo);

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    if ($limit > 20) $limit = 20;


    $start = ($page - 1) * $limit;
    if ($start < 0) $start = 0;

    $stmt = $teacher->readPaginated($start, $limit);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = $teacher->countAll();
    $totalPages = ceil($total / $limit);

    echo json_encode([
        "success" => true,
        "data" => $teachers,
        "pagination" => [
            "current_page" => $page,
            "limit" => $limit,
            "total_records" => $total,
            "total_pages" => $totalPages
        ]
    ]);
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
