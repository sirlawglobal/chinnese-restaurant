<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

header("Content-Type: application/json");

// Get and log input data
$rawInput = file_get_contents('php://input');
error_log("Raw input for update: " . $rawInput);
$input = json_decode($rawInput, true);
error_log("Decoded input for update: " . print_r($input, true));
//var_dump($input);die;
// Validate input
if (!isset($input['order_id']) || !isset($input['items']) || empty($input['items'])) {
    error_log("Invalid input: order_id or items missing/empty");
    echo json_encode(['success' => false, 'message' => 'Invalid order ID or no items provided']);
    exit;
}

// Parse order_id
$orderIdInput = trim($input['order_id']); // Trim whitespace/newlines
error_log("Received order_id: " . $orderIdInput);

// Extract numeric part (e.g., "PO123\nJun 11, 2025" -> "123")
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

$items = $input['items'];
$updatedItems = [];

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

    // Process the first item
    $item = $items[0];

    // Validate required fields
    if (!isset($item['item_name'], $item['unit_price'], $item['vendor'], $item['category'], $item['quantity'])) {
        error_log("Missing required fields for item: " . print_r($item, true));
        throw new Exception("Missing required fields for item");
    }

    $itemName = $item['item_name'];
    $unitPrice = floatval($item['unit_price']);
    $vendor = $item['vendor'];
    $categoryId = is_numeric($item['category']) ? intval($item['category']) : $item['category'];
    $quantity = intval($item['quantity']);
    $totalPrice = $unitPrice * $quantity;

    if ($unitPrice <= 0 || $quantity <= 0) {
        error_log("Invalid unit_price ($unitPrice) or quantity ($quantity)");
        throw new Exception("Unit price and quantity must be positive");
    }

    // Update inves_orders
    $orderSql = "UPDATE inves_orders SET 
        vendor_supplier = :vendor, 
        unit_price = :unit_price, 
        quantity = :quantity, 
        total_price = :total_price, 
        delivery_date = :delivery_date 
        WHERE id = :order_id";
    $orderParams = [
        ':vendor' => $vendor,
        ':unit_price' => $unitPrice,
        ':quantity' => $quantity,
        ':total_price' => $totalPrice,
        ':delivery_date' => date('Y-m-d'), // Today in UTC
        ':order_id' => $orderId
    ];
    $orderResult = db_query($orderSql, $orderParams);
    if ($orderResult === false) {
        error_log("Failed to update order $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        throw new Exception("Failed to update order: " . ($GLOBALS['DB_STATE']['error'] ?? 'Unknown error'));
    }
    error_log("Updated order ID: $orderId");

    // Update inves_order_items
    $itemSql = "UPDATE inves_order_items SET 
        item_name = :item_name, 
        category_id = :category_id, 
        price = :price, 
        quantity = :quantity 
        WHERE order_id = :order_id";
    $itemParams = [
        ':item_name' => $itemName,
        ':category_id' => $categoryId,
        ':price' => $unitPrice,
        ':quantity' => $quantity,
        ':order_id' => $orderId
    ];
    error_log("Item params for update order_id $orderId: " . print_r($itemParams, true));
    $itemResult = db_query($itemSql, $itemParams);
    if ($itemResult === false) {
        error_log("Failed to update item for order_id $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        throw new Exception("Failed to update item: " . ($GLOBALS['DB_STATE']['error'] ?? 'Unknown error'));
    }

    $updatedItems[] = array_merge($item, ['order_id' => $orderId]);

    echo json_encode([
        'success' => true,
        'data' => $updatedItems,
        'message' => 'Order updated successfully'
    ]);
} catch (Exception $e) {
    error_log("Exception in update: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>