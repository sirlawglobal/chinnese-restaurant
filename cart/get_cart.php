<?php

require_once __DIR__ . '/../../config/init.php';

header('Content-Type', 'application/json');

if (isLoggedIn()) {
    $cart = $_SESSION['cart'] ?? [];
    echo json_encode(['success' => true, 'cart' => $cart]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
}
?>