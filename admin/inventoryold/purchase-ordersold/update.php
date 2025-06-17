<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';
header('Content-Type: application/json');

$response = [
    "success" => false,
    "message" => "Something went wrong.",
    "error"   => null
];

try {
    $orderId = isset($_POST['order_id']) ? (int)str_replace('PO', '', $_POST['order_id']) : null;
    $newStatus = trim($_POST['status'] ?? '');

    if (!$orderId || !$newStatus) {
        $response['message'] = "Missing or invalid 'order_id' or 'status'.";
        echo json_encode($response);
        exit;
    }

    $sql = "UPDATE orders SET status = :status WHERE id = :id";
    $success = db_query($sql, [
        'status' => $newStatus,
        'id'     => $orderId
    ]);

    if ($success) {
        $response['success'] = true;
        $response['message'] = "Order status updated successfully.";
    } else {
        $response['message'] = "Failed to update order.";
        $response['error'] = $GLOBALS['DB_STATE']['error'] ?? 'Unknown error';
    }
} catch (Exception $e) {
    $response['message'] = "Server error.";
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;