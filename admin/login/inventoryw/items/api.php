<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost'); // Restrict to localhost
header('Access-Control-Allow-Methods: GET, POST, PATCH');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/chinnese-restaurant/logs/api_errors.log');

require_once '../../BackEnd/config/db.php'; // Adjusted path to db.php

// Helper function to send error response
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Ensure uploads directory exists
$uploadDir = 'C:/xampp/htdocs/chinnese-restaurant/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle GET request to fetch items
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
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
    $params = [];
    if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
        $query .= ' WHERE i.category_id = :category_id';
        $params[':category_id'] = $_GET['category_id'];
    }
    if (isset($_GET['status']) && $_GET['status'] !== '') {
        $query .= (strpos($query, 'WHERE') ? ' AND' : ' WHERE') . ' (
            (:status = \'Available\' AND i.stock_quantity > i.reorder_quantity) OR
            (:status = \'Low\' AND i.stock_quantity <= i.reorder_quantity AND i.stock_quantity > 0) OR
            (:status = \'Out of Stock\' AND i.stock_quantity = 0)
        )';
        $params[':status'] = $_GET['status'];
    }
    $result = db_query($query, $params, 'array');
    
    if ($result === false) {
        sendError('Query failed: ' . $GLOBALS['DB_STATE']['error']);
    }
    echo json_encode(['success' => true, 'data' => $result]);
    exit;
}

// Handle GET request to fetch categories
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'categories') {
    $query = 'SELECT id, name FROM categories';
    $result = db_query($query, [], 'array');
    if ($result === false) {
        sendError('Query failed: ' . $GLOBALS['DB_STATE']['error']);
    }
    echo json_encode(['success' => true, 'data' => $result]);
    exit;
}

// Handle POST request to add an item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    $files = $_FILES;

    // Validate required fields
    if (!isset($input['category_id'], $input['name'], $input['stock_quantity'], $input['reorder_quantity'])) {
        sendError('Missing required fields', 400);
    }

    // Handle image upload
    $imageUrl = null;
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $fileExt = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExt;
        $filePath = $uploadDir . $fileName;
        $fileUrl = '/uploads/' . $fileName;
        if (move_uploaded_file($files['image']['tmp_name'], $filePath)) {
            $imageUrl = $fileUrl;
        } else {
            sendError('Failed to upload image', 500);
        }
    }

    // Prepare item data
    $data = [
        ':category_id' => $input['category_id'],
        ':name' => $input['name'],
        ':description' => $input['description'] ?? null,
        ':price' => isset($input['basePrice']) && $input['basePrice'] !== '' ? $input['basePrice'] : null,
        ':has_options' => isset($input['has_options']) ? 1 : 0,
        ':is_set_menu' => isset($input['is_set_menu']) ? 1 : 0,
        ':image_url' => $imageUrl,
        ':stock_quantity' => (int)$input['stock_quantity'],
        ':reorder_quantity' => (int)$input['reorder_quantity'],
        ':created_at' => date('Y-m-d H:i:s')
    ];

    // Insert item
    $query = "
        INSERT INTO items (category_id, name, description, price, has_options, is_set_menu, image_url, created_at, stock_quantity, reorder_quantity)
        VALUES (:category_id, :name, :description, :price, :has_options, :is_set_menu, :image_url, :created_at, :stock_quantity, :reorder_quantity)
    ";
    $result = db_query($query, $data);
    
    if ($result === false) {
        sendError('Failed to add item: ' . $GLOBALS['DB_STATE']['error']);
    }
    $itemId = $GLOBALS['DB_STATE']['insert_id'];

    // Handle portion options
    if (isset($input['options']) && is_array($input['options'])) {
        foreach ($input['options'] as $option) {
            if (isset($option['portion'], $option['price'])) {
                $query = "
                    INSERT INTO item_portions (item_id, portion, price)
                    VALUES (:item_id, :portion, :price)
                ";
                $result = db_query($query, [
                    ':item_id' => $itemId,
                    ':portion' => $option['portion'],
                    ':price' => $option['price']
                ]);
                if ($result === false) {
                    sendError('Failed to add portion: ' . $GLOBALS['DB_STATE']['error']);
                }
            }
        }
    }

    // Handle set menu
    if (isset($input['set_menu']) && is_array($input['set_menu']) && isset($input['set_menu']['name'], $input['set_menu']['price'], $input['set_menu']['items'])) {
        $items = is_array($input['set_menu']['items']) ? implode(',', $input['set_menu']['items']) : $input['set_menu']['items'];
        $query = "
            INSERT INTO set_menu_items (item_id, name, price, items)
            VALUES (:item_id, :name, :price, :items)
        ";
        $result = db_query($query, [
            ':item_id' => $itemId,
            ':name' => $input['set_menu']['name'],
            ':price' => $input['set_menu']['price'],
            ':items' => $items
        ]);
        if ($result === false) {
            sendError('Failed to add set menu: ' . $GLOBALS['DB_STATE']['error']);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Item added successfully', 'item_id' => $itemId, 'image_url' => $imageUrl]);
    exit;
}

// Handle PATCH request to update stock
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['id'], $input['stock_quantity'])) {
        sendError('Missing required fields', 400);
    }
    $query = 'UPDATE items SET stock_quantity = :stock_quantity WHERE id = :id';
    $result = db_query($query, [
        ':id' => $input['id'],
        ':stock_quantity' => (int)$input['stock_quantity']
    ]);
    if ($result === false) {
        sendError('Failed to update stock: ' . $GLOBALS['DB_STATE']['error']);
    }
    echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    exit;
}
?>