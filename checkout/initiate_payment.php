<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../BackEnd/config/db.php';

$pdo = db_connect();

//

header('Content-Type: application/json');



// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Sanitize and validate input
$cart = $input['cart'] ?? [];
$delivery_address = trim(strip_tags($input['delivery_address'] ?? '')); // Remove tags, trim whitespace
$order_notes = trim(strip_tags($input['order_notes'] ?? '')); // Remove tags, trim whitespace
$order_type = trim($input['order_type'] ?? 'now'); // No strip_tags, as it's a controlled value
$schedule_date = $input['schedule_date'] ?? null;
$schedule_time = $input['schedule_time'] ?? null;
$total_amount = floatval($input['total_amount'] ?? 0);
$tx_ref = trim(strip_tags($input['tx_ref'] ?? '')); // Remove tags, trim whitespace
$guest_email = filter_var($input['guest_email'] ?? '', FILTER_SANITIZE_EMAIL);
$user_id = null;

// Validate order_type
if (!in_array($order_type, ['now', 'schedule'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order type']);
    exit;
}

// Check if user is logged in
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $guest_email = null; // No guest email for logged-in users
} else if (empty($guest_email) || !filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Guest email is required for guest checkout']);
    exit;
}

// Validate input
if (empty($cart) || $total_amount <= 0 || empty($delivery_address) || empty($tx_ref)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}
if ($order_type === 'schedule' && (empty($schedule_date) || empty($schedule_time))) {
    echo json_encode(['success' => false, 'message' => 'Schedule date and time are required']);
    exit;
}

// Begin transaction
try {
    $pdo->beginTransaction();

    // Insert into orders table
    $stmt = $pdo->prepare("
        INSERT INTO `orders` (
            `user_id`, `tx_ref`, `delivery_address`, `order_notes`, 
            `order_type`, `schedule_date`, `schedule_time`, 
            `total_amount`, `status`, `transaction_id`, `guest_email`, `created_at`
        ) VALUES (
            :user_id, :tx_ref, :delivery_address, :order_notes, 
            :order_type, :schedule_date, :schedule_time, 
            :total_amount, 'pending', NULL, :guest_email, NOW()
        )
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':tx_ref' => $tx_ref,
        ':delivery_address' => $delivery_address,
        ':order_notes' => $order_notes,
        ':order_type' => $order_type,
        ':schedule_date' => $schedule_date,
        ':schedule_time' => $schedule_time,
        ':total_amount' => $total_amount,
        ':guest_email' => $guest_email
    ]);
    $order_id = $pdo->lastInsertId();

    // Insert into order_items table
    $stmt = $pdo->prepare("
        INSERT INTO `order_items` (
            `order_id`, `item_name`, `portion`, `price`, `quantity`
        ) VALUES (
            :order_id, :item_name, :portion, :price, :quantity
        )
    ");
    foreach ($cart as $item) {
        $item_name = trim(strip_tags($item['name'] ?? '')); // Remove tags, trim whitespace
        $portion = trim(strip_tags($item['portion'] ?? '')); // Remove tags, trim whitespace
        if (empty($item_name)) {
            throw new Exception("Item name cannot be empty");
        }
        $stmt->execute([
            ':order_id' => $order_id,
            ':item_name' => $item_name,
            ':portion' => $portion,
            ':price' => floatval($item['price'] ?? 0),
            ':quantity' => intval($item['quantity'] ?? 1)
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id, 'tx_ref' => $tx_ref]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>