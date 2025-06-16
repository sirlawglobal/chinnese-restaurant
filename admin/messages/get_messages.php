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

$query = "SELECT cm.id, cm.text, cm.is_admin, DATE_FORMAT(cm.created_at, '%Y-%m-%d %h:%i %p') AS time
          FROM chat_messages cm
          WHERE cm.message_id = :chatId
          ORDER BY cm.created_at ASC";
$params = ['chatId' => $chatId];
$messages = db_query($query, $params, 'assoc');

$query = "SELECT m.name, m.email, m.status
          FROM messages m
          WHERE m.id = :chatId";
$chat = db_query($query, $params, 'assoc');

if ($chat) {
    echo json_encode([
        'data_type' => 'chat',
        'data' => [
            'messages' => $messages,
            'name' => $chat[0]['name'],
            'email' => $chat[0]['email'],
            'status' => $chat[0]['status']
        ]
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Chat not found.'
    ]);
}