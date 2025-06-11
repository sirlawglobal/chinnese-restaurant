<?php
// get_orders.php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    // Fetch orders
    $ordersQuery = "SELECT *
                   FROM `orders` 
                   ORDER BY `created_at` DESC 
                   LIMIT 10";
    $ordersStmt = $pdo->query($ordersQuery);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch items for each order
    $result = [];
    foreach ($orders as $order) {
        $itemsQuery = "SELECT `item_name`, `price`, `quantity` 
                      FROM `order_items` 
                      WHERE `order_id` = ?";
        $itemsStmt = $pdo->prepare($itemsQuery);
        $itemsStmt->execute([$order['id']]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $order['items'] = $items;
        $result[] = $order;
    }
    
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>