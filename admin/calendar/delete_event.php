<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Unauthorized.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Missing event ID.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "DELETE FROM events WHERE id = :id AND user_id = :user_id";
$params = ['id' => $data['id'], 'user_id' => $user_id];

if (db_query($query, $params)) {
    echo json_encode([
        'data_type' => 'event_deleted',
        'message' => 'Event deleted successfully.'
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to delete event: ' . $GLOBALS['DB_STATE']['error']
    ]);
}
?>