<?php
require_once __DIR__ . '/../../config/init.php';
header('Content-Type: application/json');

$categoryId = $_POST['category_id'] ?? null;
$name = trim($_POST['name'] ?? '');
$qty = $_POST['qty'] ?? 0;
$recordQty = $_POST['record_qty'] ?? 0;
$updatedAt = date("Y-m-d H:i:s");

if (!$categoryId || !$name || $qty < 0 || $recordQty < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$sql = "INSERT INTO stock (category_id, qty, record_qty, updated_at, name)
        VALUES (:category_id, :qty, :record_qty, :updated_at, :name)";

$result = db_query($sql, [
    'category_id' => $categoryId,
    'qty' => $qty,
    'record_qty' => $recordQty,
    'updated_at' => $updatedAt,
    'name' => $name
]);

echo json_encode([
    'success' => $result !== false,
    'message' => $result ? 'Stock item added successfully.' : 'Database error'
]);
