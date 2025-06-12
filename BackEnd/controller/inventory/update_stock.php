<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
require_once __DIR__ . '/../../config/init.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['item_id'], $input['new_stock'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$itemId = (int)$input['item_id'];
$newStock = (int)$input['new_stock'];

$sql = "UPDATE items SET stock_quantity = :stock WHERE id = :id";
$result = db_query($sql, ['stock' => $newStock, 'id' => $itemId]);

echo json_encode([
    'success' => $result !== false,
    'message' => $result ? 'Stock updated.' : $GLOBALS['DB_STATE']['error']
]);
