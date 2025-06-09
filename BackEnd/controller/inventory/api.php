<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow CORS for development
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'C:/xampp/htdocs/chinnese-restaurant/BackEnd/config/db.php';

// Helper function to send error response
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Handle GET request to fetch items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "
        SELECT i.id, c.name AS category, i.name, i.description, i.price, i.has_options, i.is_set_menu, i.image_url, i.created_at, 
               i.stock_quantity, i.reorder_quantity,
               CASE 
                   WHEN i.stock_quantity = 0 THEN 'Out of Stock'
                   WHEN i.stock_quantity <= i.reorder_quantity THEN 'Low'
                   ELSE 'Available'
               END AS status
        FROM items i
        LEFT JOIN categories c ON i.category_id = c.id
    ";
    $result = db_query($query, [], 'array');
    
    if ($result === false) {
        sendError('Query failed: ' . $GLOBALS['DB_STATE']['error']);
    }
    echo json_encode($result);
    exit;
}

// Handle POST request to add an item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name'], $input['category_id'], $input['stock_quantity'], $input['reorder_quantity'])) {
        sendError('Missing required fields', 400);
    }

    $data = [
        ':category_id' => $input['category_id'],
        ':name' => $input['name'],
        ':description' => $input['description'] ?? null,
        ':price' => $input['price'] ?? null,
        ':has_options' => isset($input['has_options']) ? (int)$input['has_options'] : 0,
        ':is_set_menu' => isset($input['is_set_menu']) ? (int)$input['is_set_menu'] : 0,
        ':image_url' => $input['image_url'] ?? null,
        ':stock_quantity' => (int)$input['stock_quantity'],
        ':reorder_quantity' => (int)$input['reorder_quantity'],
        ':created_at' => date('Y-m-d H:i:s')
    ];

    $query = "
        INSERT INTO items (category_id, name, description, price, has_options, is_set_menu, image_url, created_at, stock_quantity, reorder_quantity)
        VALUES (:category_id, :name, :description, :price, :has_options, :is_set_menu, :image_url, :created_at, :stock_quantity, :reorder_quantity)
    ";

    $result = db_query($query, $data);
    
    if ($result === false) {
        sendError('Failed to add item: ' . $GLOBALS['DB_STATE']['error']);
    }
    
    echo json_encode(['message' => 'Item added successfully', 'id' => $GLOBALS['DB_STATE']['insert_id']]);
    exit;
}
