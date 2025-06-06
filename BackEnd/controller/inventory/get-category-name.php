<?php
header('Content-Type: application/json');

require_once '../../config/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
    exit;
}

$categoryId = (int) $_GET['id'];

try {
    $query = "SELECT id, name FROM categories WHERE id = :id";
    $params = ['id' => $categoryId];

    $result = db_query($query, $params, 'assoc');

    if ($result && count($result) > 0) {
        echo json_encode([
            'status' => 'success',
            'data' => $result[0]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Category not found'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error',
        'error_info' => $e->getMessage()
    ]);
}
