<?php
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/error.log');
//require_once __DIR__ . '/../../../config/init.php';
require_once __DIR__ . '/../../config/init.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$itemId = $input['item_id'] ?? null;
$reorderQuantity = $input['reorder_quantity'] ?? null;

if (!$itemId || !is_numeric($reorderQuantity) || $reorderQuantity < 1) {
  echo json_encode(['success' => false, 'message' => 'Invalid input']);
  exit;
}

$sql = "UPDATE items SET reorder_quantity = :reorder_qty WHERE id = :id";
$result = db_query($sql, ['reorder_qty' => (int)$reorderQuantity, 'id' => (int)$itemId]);

// var_dump($result);
// die;
if ($result) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to place reorder']);
}