<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    // Query to get the top 5 categories by order item count
    $query = "
        SELECT 
            c.name AS category,
            COUNT(oi.id) AS item_count
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN categories c ON oi.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY item_count DESC
        LIMIT 10";
    
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check for empty results
    if (empty($data)) {
        echo json_encode(['message' => 'No order data found']);
        exit;
    }
    
    // Format data for frontend
    $result = [
        'labels' => array_column($data, 'category'),
        'data' => array_column($data, 'item_count')
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>