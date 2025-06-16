<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

header("Content-Type: application/json");

// Get and log input data
$rawInput = file_get_contents('php://input');
error_log("Raw input for delete: " . $rawInput);
$input = json_decode($rawInput, true);
error_log("Decoded input for delete: " . print_r($input, true));

// Validate input
if (!isset($input['order_id'])) {
    error_log("Invalid input: order_id missing");
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

// Parse order_id
$orderIdInput = trim($input['order_id']);
error_log("Received order_id: " . $orderIdInput);

// Extract numeric part (e.g., "PO123" -> "123")
if (preg_match('/^PO(\d+)/', $orderIdInput, $matches)) {
    $formattedId = intval($matches[1]);
    $orderId = $formattedId - 100; // Subtract 100 to get database ID
    error_log("Parsed order_id: PO{$formattedId} to database ID $orderId");
} elseif (is_numeric($orderIdInput)) {
    $orderId = intval($orderIdInput);
    error_log("Using numeric order_id: $orderId");
} else {
    error_log("Invalid order_id format: " . $orderIdInput);
    echo json_encode(['success' => false, 'message' => 'Invalid order ID format']);
    exit;
}

try {
    // Validate order exists
    $checkSql = "SELECT COUNT(*) AS count FROM inves_orders WHERE id = :order_id";
    $checkResult = db_query($checkSql, [':order_id' => $orderId]);
    if ($checkResult === false) {
        error_log("Database query failed for order ID $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        throw new Exception("Database query failed");
    }
    if (empty($checkResult) || !isset($checkResult[0]) || $checkResult[0]->count == 0) {
        error_log("Order ID $orderId not found in database");
        throw new Exception("Order ID $orderId not found");
    }

    // Delete from inves_order_items
    $itemSql = "DELETE FROM inves_order_items WHERE order_id = :order_id";
    $itemResult = db_query($itemSql, [':order_id' => $orderId]);
    if ($itemResult === false) {
        error_log("Failed to delete items for order_id $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error'));
        throw new Exception("Failed to delete order items: " . ($GLOBALS['DB_STATE']['error'] ?? 'Unknown error'));
    }
    error_log("Deleted items for order ID: $orderId");

    // Delete from inves_orders
    $orderSql = "DELETE FROM inves_orders WHERE id = :order_id";
    $orderResult = db_query($orderSql, [':order_id' => $orderId]);
    if ($orderResult === false) {
        error_log("Failed to delete order $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        throw new Exception("Failed to delete order: " . ($GLOBALS['DB_STATE']['error'] ?? 'Unknown error'));
    }
    error_log("Deleted order ID: $orderId");

    echo json_encode([
        'success' => true,
        'message' => 'Order deleted successfully'
    ]);
} catch (Exception $e) {
    error_log("Exception in delete_order_item: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>