<?php

require_once '../../config/database.php';
require_once '../../classes/Students.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $student = new Student($pdo);

    // Get page and limit from GET (default to page 1, 10 records)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    if ($limit < 1) $limit = 20;
    if ($limit > 100) $limit = 100;  // prevent abuse and overflow

    $start = ($page - 1) * $limit;
    if ($start < 0) $start = 0;

    // Get paginated data
    $stmt = $student->readPaginated($start, $limit);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total for pagination info
    $total = $student->countAll();
    $totalPages = ceil($total / $limit);

    echo json_encode([
        "success" => true,
        "data" => $students,
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
