<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

header('Content-Type: application/json');

$sql = "
    SELECT 
        o.id AS order_id,
        o.created_at AS order_date,
        oi.item_name AS item,
        c.name AS category,
        o.vendor_supplier AS vendor,
        o.status,
        oi.price AS unit_price,
        oi.quantity,
        (oi.price * oi.quantity) AS total_price,
        o.delivery_date as schedule_date
    FROM 
        inves_orders o
    INNER JOIN 
        inves_order_items oi ON o.id = oi.order_id
    LEFT JOIN 
        inves_categories c ON oi.category_id = c.id
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