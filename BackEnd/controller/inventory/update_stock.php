<?php
require_once __DIR__ . '/../../config/init.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['item_id'], $input['new_stock'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}
//var_dump($input);die;
$itemId = (int)$input['item_id'];
$newStock = (int)$input['new_stock'];
$actionSe = (int)$input['actionSe'];

if ($itemId < 1 || $newStock < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid values']);
    exit;
}

$updated_at = date("Y-m-d H:i:s");

// Increment qty by the submitted newStock value

$currentQtyRow = db_query("SELECT qty FROM stock WHERE id = :id", ['id' => $itemId], 'assoc');
$currentQty = (int)($currentQtyRow[0]['qty'] ?? 0);

if ($actionSe == 1) {
    // Subtracting: ensure stock is enough
    if ($newStock > $currentQty) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock to subtract']);
        exit;
    }

    $sql = "UPDATE stock SET qty = qty - :stock, updated_at = :updated_at WHERE id = :id";
} else {
    // Adding: no limit
    $sql = "UPDATE stock SET qty = qty + :stock, updated_at = :updated_at WHERE id = :id";
}

$result = db_query($sql, [
    'stock' => $newStock,
    'id' => $itemId,
    'updated_at' => $updated_at
]);


echo json_encode([
    'success' => $result !== false,
    'message' => $result ? 'Stock updated.' : ($GLOBALS['DB_STATE']['error'] ?? 'Unknown error')
]);
