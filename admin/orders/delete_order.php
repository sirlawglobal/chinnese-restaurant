<?php
session_start();
require_once '../../BackEnd/config/db.php'; // Ensure db_connect() is defined

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    $_SESSION['error'] = "Invalid order ID.";
    header("Location: " . dirname($_SERVER['PHP_SELF']) . '/');
    exit();
}

$pdo = db_connect();

try {
    // Check if the order exists
    $checkStmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
    $checkStmt->execute([':id' => $orderId]);

    if ($checkStmt->rowCount() === 0) {
        $_SESSION['error'] = "Order not found.";
        header("Location: " . dirname($_SERVER['PHP_SELF']) . '/');
        exit();
    }

    // Delete the order
    $deleteStmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $deleteStmt->execute([':id' => $orderId]);

    $_SESSION['success'] = "Order #$orderId has been successfully deleted.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: " . dirname($_SERVER['PHP_SELF']) . '/');
exit();
