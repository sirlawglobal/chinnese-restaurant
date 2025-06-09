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
    // Verify PDO is defined
    if (!isset($pdo)) {
        error_log("PDO object not defined in db.php");
        throw new Exception("Database connection not initialized");
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        error_log("Invalid JSON input: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }

    // Validate input
    if (
        !isset($input['itemId']) ||
        !isset($input['itemName']) ||
        !isset($input['categoryName']) ||
        !isset($input['reviewerName']) ||
        !isset($input['rating']) ||
        !isset($input['reviewText']) ||
        !isset($input['reviewDate'])
    ) {
        error_log("Missing required fields: " . print_r($input, true));
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Additional validation
    if (!is_numeric($input['itemId']) || $input['itemId'] <= 0) {
        error_log("Invalid itemId: " . $input['itemId']);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit;
    }
    if (!in_array($input['rating'], ['1', '2', '3', '4', '5'])) {
        error_log("Invalid rating: " . $input['rating']);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid rating']);
        exit;
    }

    // Prepare and execute insert query
    $stmt = $pdo->prepare('
        INSERT INTO reviews (dish_id, dish_name, category_name, reviewer_name, rating, review_text, review_date)
        VALUES (:dish_id, :dish_name, :category_name, :reviewer_name, :rating, :review_text, :review_date)
    ');
    $stmt->execute([
        'dish_id' => (int)$input['itemId'],
        'dish_name' => $input['itemName'],
        'category_name' => $input['categoryName'],
        'reviewer_name' => $input['reviewerName'],
        'rating' => (int)$input['rating'],
        'review_text' => $input['reviewText'],
        'review_date' => $input['reviewDate']
    ]);

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Review saved successfully']);
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