<?php
header('Content-Type: application/json');
require_once '../../BackEnd/config/db.php';

try {
    $pdo = db_connect();
    
    $query = "SELECT `id`, `dish_id`, `dish_name`, `category_name`, `reviewer_name`, 
              `rating`, `review_text`, `review_date`, `created_at` 
              FROM `reviews` 
              ORDER BY `review_date` DESC 
              LIMIT 3";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reviews);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>