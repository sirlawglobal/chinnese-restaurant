<?php
ob_start();
session_start();
require_once '../../BackEnd/config/db.php';

header('Content-Type: application/json'); // Set JSON response

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

// Validate and sanitize form inputs
$full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$order_type = filter_input(INPUT_POST, 'order_type', FILTER_SANITIZE_STRING);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
$items = $_POST['items'] ?? [];

if (!$full_name || !$email || !$address || !$order_type || !$status || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields and add at least one item.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit();
}

$valid_order_types = ['delivery', 'pickup', 'dinein'];
$valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
if (!in_array($order_type, $valid_order_types) || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order type or status.']);
    exit();
}

$total_amount = 0;
$validated_items = [];
try {
    $db= db_connect();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($items as $index => $item) {
        $item_id = filter_var($item['item_id'], FILTER_VALIDATE_INT);
        $category_id = filter_var($item['category_id'], FILTER_VALIDATE_INT);
        $price = filter_var($item['price'], FILTER_VALIDATE_FLOAT);
        $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT);
        $total = filter_var($item['total'], FILTER_VALIDATE_FLOAT);



$tx_ref = 'ADM_ :TRF_'. strtoupper(substr(uniqid('', true), -7));

        if (!$item_id || !$category_id || !$price || !$quantity || !$total || $quantity < 1 || $price < 0 || $total < 0) {
            echo json_encode(['success' => false, 'message' => "Invalid item data at row " . ($index + 1) . "."]);
            exit();
        }

        $stmt = $db->prepare("SELECT name FROM items WHERE id = ? AND category_id = ?");
        $stmt->execute([$item_id, $category_id]);
        $item_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($item_data)) {
            error_log("Invalid item: item_id=$item_id, category_id=$category_id");
            echo json_encode(['success' => false, 'message' => "Invalid item selected at row " . ($index + 1) . "."]);
            exit();
        }

        if (abs($total - ($price * $quantity)) > 0.001) {
            error_log("Price mismatch at row " . ($index + 1) . ": total=$total, calculated=" . ($price * $quantity));
            echo json_encode(['success' => false, 'message' => "Price calculation mismatch at row " . ($index + 1) . "."]);
            exit();
        }

        $total_amount += $total;
        $validated_items[] = [
            'item_id' => $item_id,
            'item_name' => $item_data[0]['name'],
            'category_id' => $category_id,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $total
        ];
    }

    $db->beginTransaction();

    $sql = "INSERT INTO orders (
        user_id, tx_ref, delivery_address, order_notes, order_type, schedule_date, schedule_time,
        total_amount, status, transaction_id, guest_email, guest_name, guest_phone, created_at,
        user_email, user_name, user_phone
    ) VALUES (
        NULL, ?, ?, '', ?, NULL, NULL, ?, ?, NULL, ?, ?, ?, NOW(), NULL, NULL, NULL
    )";
    $stmt = $db->prepare($sql);
    $stmt->execute([$tx_ref, $address, $order_type, $total_amount, $status, $email, $full_name, $phone]);
    $order_id = $db->lastInsertId();

    // $sql = "INSERT INTO order_items (order_id, item_name,'portion', category_id, price, quantity) VALUES (?, ?, '', ?, ?, ?)";
    // $stmt = $db->prepare($sql);
    // foreach ($validated_items as $item) {
    //     $stmt->execute([$order_id, $item['item_name'], $item['category_id'], $item['price'], $item['quantity']]);
    // }


//     $sql = "INSERT INTO order_items (order_id, item_name, 'portion', category_id, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
// $stmt = $db->prepare($sql);
// foreach ($validated_items as $item) {
//     $stmt->execute([$order_id, $item['item_name'], null, $item['category_id'], $item['price'], $item['quantity']]);
// }

$sql = "INSERT INTO order_items (order_id, item_name, `portion`, category_id, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
foreach ($validated_items as $item) {
    $stmt->execute([$order_id, $item['item_name'], null, $item['category_id'], $item['price'], $item['quantity']]);
}


    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Order created successfully!']);
    exit();
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()]);
    exit();
}
ob_end_flush();
?>