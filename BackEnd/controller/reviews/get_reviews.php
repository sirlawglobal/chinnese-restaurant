<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php_errors.log'); // Adjust path to your server's log file

require_once '../../config/db.php';
  $pdo = db_connect();

try {
    // Prepare and execute query
    $stmt = $pdo->prepare('
        SELECT id, dish_id, dish_name, category_name, reviewer_name, rating, review_text, review_date, created_at
        FROM reviews
        WHERE 1
        ORDER BY review_date DESC
    ');
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate overall stats
    $total_reviews = count($reviews);
    $average_rating = $total_reviews > 0
        ? round(array_sum(array_column($reviews, 'rating')) / $total_reviews, 1)
        : 0;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'reviews' => $reviews,
            'total_reviews' => $total_reviews,
            'average_rating' => $average_rating
        ]
    ]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Server error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>