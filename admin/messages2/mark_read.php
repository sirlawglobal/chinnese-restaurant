<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$chatId = filter_var($data['chatId'] ?? 0, FILTER_VALIDATE_INT);

if (!$chatId) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid chat ID.'
    ]);
    exit;
}

$query = "UPDATE chat_messages SET is_read = 1 WHERE message_id = :chatId AND is_admin = 0 AND is_read = 0";
$params = ['chatId' => $chatId];
$updated = db_query($query, $params, 'update');

// Optionally update messages status
if ($updated) {
    $statusQuery = "UPDATE messages SET status = 'replied' WHERE id = :chatId AND status = 'pending'";
    db_query($statusQuery, $params);
}

echo json_encode([
    'data_type' => 'message',
    'message' => $updated ? 'Messages marked as read.' : 'No messages to mark as read.'
]);
?>