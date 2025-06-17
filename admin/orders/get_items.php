<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

if ($category_id > 0) {
    $items = db_query("SELECT id, name, price FROM items WHERE category_id = ? ORDER BY name", [$category_id], 'assoc');
    echo json_encode($items);
} else {
    echo json_encode([]);
}
?>