<?php
require_once '../../BackEnd/config/init.php';

 $pdo = db_connect();
try {
   
    // Execute the SQL query
    $stmt = $pdo->query("SELECT id, category_id, name, description, price, has_options, is_set_menu, image_url, created_at FROM items WHERE 1");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set JSON header and output
    header('Content-Type: application/json');
    echo json_encode($items);
} catch (PDOException $e) {
    // Return error response
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>