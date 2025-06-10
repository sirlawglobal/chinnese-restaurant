<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    $query = "SELECT `id`, `user_id`, `tx_ref`, `delivery_address`, `order_notes`, `order_type`, 
              `schedule_date`, `schedule_time`, `total_amount`, `status`, `transaction_id`, 
              `guest_email`, `created_at`, `user_email` 
              FROM `orders` 
              ORDER BY `created_at` DESC 
              LIMIT 10";
    
    $stmt = $pdo->query($query);
    $orders = $stmt->fetchAll();
    
    echo json_encode($orders);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>