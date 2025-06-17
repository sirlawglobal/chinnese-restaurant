<?php
// fetch_order_types.php

header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';



$pdo = db_connect();

// Get total number of orders
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get order types grouped
$typeStmt = $pdo->query("SELECT order_type2, COUNT(*) as count FROM orders GROUP BY order_type2");

$orderTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate percentage and return
$result = [];
foreach ($orderTypes as $type) {
    $count = (int)$type['count'];
    $percentage = $totalOrders > 0 ? round(($count / $totalOrders) * 100) : 0;
    $result[] = [
        'type' => $type['order_type2'],
        'count' => $count,
        'percentage' => $percentage
    ];
}

echo json_encode($result);
