<?php
session_start();
require_once '../../BackEnd/config/db.php'; // Make sure db_connect() is defined here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    $pdo = db_connect(); // This should return a PDO object

    $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        $_SESSION['error'] = "Invalid order status.";
        header("Location: orders.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':id' => $orderId
        ]);

        $_SESSION['success'] = "Order #$orderId status updated to '$status'.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // header("Location: orders.php");
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: orders.php");
    exit();
}
