<?php
require_once '../../config/database.php';
require_once '../../classes/Teachers.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $teacher = new Teacher($pdo);

    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;

    // Get total matching records
    $total = $teacher->countByKeyword($keyword);

    // Get paginated records
    $stmt = $teacher->searchByKeyword($keyword, $limit, $offset);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $teachers,
        "pagination" => [
            "current_page" => $page,
            "total_pages" => ceil($total / $limit)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
