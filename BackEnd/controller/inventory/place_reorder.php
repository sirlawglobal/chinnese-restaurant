<?php 
require_once __DIR__ . '/../../config/init.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$itemId = $input['item_id'] ?? null;
$reorderQuantity = $input['reorder_quantity'] ?? null;
$actionSe = (int)$input['recorD'];
//var_dump($itemId);die;
if (!$itemId || !is_numeric($reorderQuantity) || $reorderQuantity < 1) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$updated_at = date("Y-m-d H:i:s");

// ADD to existing record_qty instead of replacing it
$currentQtyRow = db_query("SELECT record_qty FROM stock WHERE id = :id", ['id' => $itemId], 'assoc');
$currentQty = (int)($currentQtyRow[0]['record_qty'] ?? 0);

if ($actionSe == 1) {
    // Subtracting: ensure stock is enough
    if ($reorderQuantity > $currentQty) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock to subtract']);
        exit;
    }

    $sql = "UPDATE stock SET record_qty = record_qty - :reorder_qty, updated_at = :updated_at WHERE id = :id";
} else {
    // Adding: no limit
    $sql = "UPDATE stock SET record_qty = record_qty + :reorder_qty, updated_at = :updated_at WHERE id = :id";
}

$result = db_query($sql, [
    'reorder_qty' => (int)$reorderQuantity, 
  'id' => (int)$itemId, 
  'updated_at' => $updated_at
]);

if ($result) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to place reorder']);
}
