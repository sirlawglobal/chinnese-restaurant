<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    // Get monthly revenue data (last 8 months)
    $query = "SELECT 
                DATE_FORMAT(created_at, '%b') AS month,
                SUM(total_amount) AS revenue,
                SUM(total_amount * 0.5) AS expense -- Assuming 50% expense for demo
              FROM orders
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 8 MONTH)
              GROUP BY DATE_FORMAT(created_at, '%Y-%m')
              ORDER BY created_at DESC
              LIMIT 8";
    
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for Chart.js
    $result = [
        'labels' => array_column($data, 'month'),
        'revenue' => array_column($data, 'revenue'),
        'expense' => array_column($data, 'expense')
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>