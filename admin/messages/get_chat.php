<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

$chatId = filter_var($_GET['chatId'] ?? 0, FILTER_VALIDATE_INT);

if (!$chatId) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid chat ID.'
    ]);
    exit;
}

$query = "SELECT m.name, m.email, m.status, COALESCE(COUNT(cm.id), 0) AS unread
          FROM messages m
          LEFT JOIN chat_messages cm ON cm.message_id = m.id AND cm.is_admin = 0 AND cm.is_read = 0
          WHERE m.id = :chatId
          GROUP BY m.id";
$params = ['chatId' => $chatId];
$chat = db_query($query, $params, 'assoc');

if ($chat) {
    $chat = $chat[0];
    $chat['initials'] = strtoupper(substr($chat['name'], 0, 1) . (strpos($chat['name'], ' ') !== false ? substr($chat['name'], strpos($chat['name'], ' ') + 1, 1) : ""));
    echo json_encode([
        'data_type' => 'chat',
        'data' => $chat
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Chat not found.'
    ]);
}