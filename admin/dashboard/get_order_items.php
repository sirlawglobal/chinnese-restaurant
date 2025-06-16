<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    $orderId = $_GET['order_id'] ?? 0;
    
    if (!is_numeric($orderId) || $orderId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid order ID']);
        exit;
    }

    $query = "SELECT `id`, `order_id`, `item_name`, `portion`, `price`, `quantity` 
              FROM `order_items` 
              WHERE `order_id` = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll();
    
    echo json_encode($items);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>