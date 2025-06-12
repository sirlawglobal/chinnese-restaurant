<?php
require_once __DIR__ . '/../../config/init.php';
header('Content-Type: application/json');

try {
  $input = json_decode(file_get_contents('php://input'), true);
  $itemId = $input['item_id'] ?? null;
  $reorderQuantity = $input['reorder_quantity'] ?? null;

  if (!$itemId || !is_numeric($reorderQuantity) || $reorderQuantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
  }

  $sql = "UPDATE items SET reorder_quantity = :reorder_qty WHERE id = :id";
  $result = db_query($sql, ['reorder_qty' => (int)$reorderQuantity, 'id' => (int)$itemId]);
//var_dump($result);die;
  if ($result) {
    echo json_encode(['success' => true, 'message' => 'Reorder placed successfully']);
  } else {
    $error = $GLOBALS['DB_STATE']['error'] ?? 'Unknown database error';
    error_log("Failed to update reorder: $error, itemId: $itemId, quantity: $reorderQuantity");
    echo json_encode(['success' => false, 'message' => 'Failed to place reorder', 'error' => $error]);
  }
} catch (Exception $e) {
  error_log("Exception in place_reorder.php: " . $e->getMessage());
  echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}