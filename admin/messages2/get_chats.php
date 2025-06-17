<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

$search = filter_var($_GET['search'] ?? '', FILTER_SANITIZE_STRING);
$search = '%' . $search . '%';

$query = "SELECT 
    MAX(m.id) AS id, 
    MAX(m.name) AS name, 
    m.email, 
    MAX(cm.text) AS lastMessage, 
    MAX(m.status) AS status, 
    COALESCE(COUNT(CASE WHEN cm.is_admin = 0 AND cm.is_read = 0 THEN cm.id END), 0) AS unread, 
    DATE_FORMAT(MAX(cm.created_at), '%h:%i %p') AS time
          FROM messages m
          LEFT JOIN chat_messages cm ON cm.message_id = m.id
          WHERE m.name LIKE :search OR m.message LIKE :search OR m.email LIKE :search
          GROUP BY m.email
          ORDER BY MAX(cm.created_at) DESC";
$params = ['search' => $search];
$chats = db_query($query, $params, 'assoc');

if ($chats === false) {
    error_log("get_chats.php query failed: " . $GLOBALS['DB_STATE']['error']);
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to retrieve chats. Error: ' . $GLOBALS['DB_STATE']['error']
    ]);
    exit;
}

if (empty($chats)) {
    echo json_encode([
        'data_type' => 'chats',
        'data' => []
    ]);
    exit;
}

foreach ($chats as &$chat) {
    $chat['initials'] = strtoupper(substr($chat['name'], 0, 1) . (strpos($chat['name'], ' ') !== false ? substr($chat['name'], strpos($chat['name'], ' ') + 1, 1) : ""));
}

echo json_encode([
    'data_type' => 'chats',
    'data' => $chats
]);