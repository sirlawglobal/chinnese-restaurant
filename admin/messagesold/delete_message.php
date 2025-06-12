<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';


header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$chatId = filter_var($data['chatId'] ?? 0, FILTER_VALIDATE_INT);
$messageId = filter_var($data['messageId'] ?? 0, FILTER_VALIDATE_INT);

if (!$chatId || !$messageId) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid chat or message ID.'
    ]);
    exit;
}

$query = "DELETE FROM chat_messages WHERE id = :messageId AND message_id = :chatId";
$params = ['messageId' => $messageId, 'chatId' => $chatId];
$deleted = db_query($query, $params, 'insert');

if ($deleted) {
    $query = "SELECT text, DATE_FORMAT(created_at, '%h:%i %p') AS time
              FROM chat_messages
              WHERE message_id = :chatId
              ORDER BY created_at DESC
              LIMIT 1";
    $lastMessage = db_query($query, ['chatId' => $chatId], 'assoc');

    $updateQuery = "UPDATE messages SET message = :message, created_at = NOW() WHERE id = :chatId";
    $updateParams = [
        'message' => $lastMessage ? $lastMessage[0]['text'] : '',
        'chatId' => $chatId
    ];
    db_query($updateQuery, $updateParams, 'insert');
}

echo json_encode([
    'data_type' => 'message',
    'message' => $deleted ? 'Message deleted.' : 'Failed to delete message.'
]);
?>