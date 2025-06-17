<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    // Get daily orders for current week
    $query = "SELECT 
                DAYNAME(created_at) AS day,
                COUNT(id) AS order_count
              FROM orders
              WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
              GROUP BY DAYOFWEEK(created_at), DAYNAME(created_at)
              ORDER BY DAYOFWEEK(created_at)";
    
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for Chart.js
    $result = [
        'labels' => array_column($data, 'day'),
        'data' => array_column($data, 'order_count')
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>