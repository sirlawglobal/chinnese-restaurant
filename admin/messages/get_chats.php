<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

$search = filter_var($_GET['search'] ?? '', FILTER_SANITIZE_STRING);
$search = '%' . $search . '%';

$query = "SELECT m.id, m.name, m.email, m.message AS lastMessage, m.status, 
                 COALESCE(COUNT(cm.id), 0) AS unread, 
                 DATE_FORMAT(m.created_at, '%h:%i %p') AS time
          FROM messages m
          LEFT JOIN chat_messages cm ON cm.message_id = m.id AND cm.is_admin = 0 AND cm.is_read = 0
          WHERE m.name LIKE :search OR m.message LIKE :search OR m.email LIKE :search
          GROUP BY m.id
          ORDER BY m.created_at DESC";
$params = ['search' => $search];
$chats = db_query($query, $params, 'assoc');

foreach ($chats as &$chat) {
    $chat['initials'] = strtoupper(substr($chat['name'], 0, 1) . (strpos($chat['name'], ' ') !== false ? substr($chat['name'], strpos($chat['name'], ' ') + 1, 1) : ""));
}

echo json_encode([
    'data_type' => 'chats',
    'data' => $chats
]);
?>