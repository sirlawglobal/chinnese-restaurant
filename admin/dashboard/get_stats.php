<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    // Query for Total Orders
    $orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
    $orders_stmt = $pdo->query($orders_query);
    $total_orders = $orders_stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
    
    // Query for Total Customers (distinct user_id or guest_email)
    $customers_query = "
        SELECT COUNT(DISTINCT COALESCE(user_id, guest_email)) AS total_customers 
        FROM orders 
        WHERE user_id IS NOT NULL OR guest_email IS NOT NULL";
    $customers_stmt = $pdo->query($customers_query);
    $total_customers = $customers_stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];
    
    // Query for Total Revenue (only completed orders)
    $revenue_query = "
        SELECT SUM(total_amount) AS total_revenue 
        FROM orders 
        WHERE status = 'completed'";
    $revenue_stmt = $pdo->query($revenue_query);
    $total_revenue = $revenue_stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    
    // Optional: Calculate percentage change (e.g., compared to previous week)
    // For simplicity, this example uses a static percentage. You can modify it to compare with a previous period.
    $orders_percentage = 1.58; // Placeholder
    $customers_percentage = 0.42; // Placeholder
    $revenue_percentage = 1.58; // Placeholder
    
    // Format response
    $result = [
        'total_orders' => (int)$total_orders,
        'total_customers' => (int)$total_customers,
        'total_revenue' => number_format((float)$total_revenue, 2, '.', ''),
        'orders_percentage' => $orders_percentage,
        'customers_percentage' => $customers_percentage,
        'revenue_percentage' => $revenue_percentage
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>