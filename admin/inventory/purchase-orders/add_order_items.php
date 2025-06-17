<?php
require_once __DIR__ . '/../../../BackEnd/config/init.php';

header("Content-Type: application/json");

// Get and log input data for debugging
$rawInput = file_get_contents('php://input');
error_log("Raw input: " . $rawInput);
$input = json_decode($rawInput, true);
error_log("Decoded input: " . print_r($input, true));

// Validate input
if (!isset($input['items']) || empty($input['items'])) {
    error_log("Items not found or empty in input");
    echo json_encode(['success' => false, 'message' => 'No items provided']);
    exit;
}

$items = $input['items'];
$success = true;
$message = "Items added successfully";
$addedItems = [];
///var_dump($items);die;
try {
    foreach ($items as $item) {
        // Validate required fields, including item_name
        if (!isset($item['item_name'], $item['unit_price'], $item['vendor'], $item['category'], $item['quantity'])) {
            throw new Exception("Missing required fields for an item: " . print_r($item, true));
        }

        $itemName = $item['item_name'];
        $unitPrice = floatval($item['unit_price']);
        $vendor = $item['vendor'];
        $categoryId = intval($item['category']); // Use category as category_id
        $quantity = intval($item['quantity']);
        $totalPrice = $unitPrice * $quantity;

        // Insert into inves_orders
        $orderSql = "INSERT INTO inves_orders (vendor_supplier, status, delivery_date, unit_price, quantity, total_price, received, created_at) VALUES (:vendor, :status, :delivery_date, :unit_price, :quantity, :total_price, :received, NOW())";
        $orderParams = [
            ':vendor' => $vendor,
            ':status' => 'pending',
            ':delivery_date' => date('Y-m-d'),
            ':unit_price' => $unitPrice,
            ':quantity' => $quantity,
            ':total_price' => $totalPrice,
            ':received' => 0
        ];
        $orderResult = db_query($orderSql, $orderParams);
        if ($orderResult === false) {
            throw new Exception("Failed to insert order: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        }
        $orderId = $GLOBALS['DB_STATE']['insert_id'];
        error_log("Inserted order ID: " . $orderId);

        // Validate order_id before proceeding
        if (!$orderId) {
            throw new Exception("Invalid order ID retrieved: " . $orderId);
        }

        // Insert into inves_order_items
        $itemSql = "INSERT INTO inves_order_items (order_id, item_name, category_id, price, quantity) VALUES (:order_id, :item_name, :category_id, :price, :quantity)";
        $itemParams = [
            ':order_id' => $orderId,
            ':item_name' => $itemName,
            ':category_id' => $categoryId,
            ':price' => $unitPrice,
            ':quantity' => $quantity
        ];
        error_log("Item params for order_id $orderId: " . print_r($itemParams, true)); // Log params
        $itemResult = db_query($itemSql, $itemParams);
        if ($itemResult === false) {
            error_log("Item insert failed for order_id $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details') . " with params: " . print_r($itemParams, true));
            throw new Exception("Failed to insert item for order_id $orderId: " . ($GLOBALS['DB_STATE']['error'] ?? 'No error details'));
        }

        $addedItems[] = array_merge($item, ['order_id' => $orderId]);
    }

    echo json_encode(['success' => $success, 'data' => $addedItems, 'message' => $message]);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>