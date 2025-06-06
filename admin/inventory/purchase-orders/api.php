<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

header('Content-Type: application/json');

$sql = "
    SELECT 
        o.id AS order_id,
        o.created_at AS order_date,
        oi.item_name AS item,
        (SELECT u.name FROM users u WHERE u.id = o.user_id LIMIT 1) AS vendor,
        o.status,
        o.delivery_address AS delivery_info,
        oi.price AS unit_price,
        oi.quantity,
        o.total_amount AS total_price,
        o.schedule_date,
        o.schedule_time
    FROM 
        orders o
    INNER JOIN 
        order_items oi ON o.id = oi.order_id
    ORDER BY 
        o.created_at DESC
";

try {
    $orders = db_query($sql); // uses your PDO wrapper

    if (!$orders) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to fetch orders.",
            "error" => $GLOBALS['DB_STATE']['error']
        ]);
        exit;
    }

foreach ($orders as &$row) {
$row->order_id = formatOrderId($row->order_id, $row->order_date);
    $status = strtolower($row->status);
    switch ($status) {
        case 'pending':
            $row->delivery_progress = 20;
            break;
        case 'shipped':
            $row->delivery_progress = 60;
            break;
        case 'delivered':
            $row->delivery_progress = 100;
            break;
        default:
            $row->delivery_progress = 0;
    }
}


    echo json_encode([
        "success" => true,
        "data" => $orders
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error.",
        "error" => $e->getMessage()
    ]);
}