<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';
require_once __DIR__ . '/../../BackEnd/helpers/mail_helper.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$chatId = filter_var($data['chatId'] ?? 0, FILTER_VALIDATE_INT);
$message = htmlspecialchars(trim($data['message'] ?? ''));

if (!$chatId || !$message) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Chat ID and message are required.'
    ]);
    exit;
}

// Get user email and name
$query = "SELECT m.email, m.name FROM messages m WHERE m.id = :chatId";
$params = ['chatId' => $chatId];
$result = db_query($query, $params, 'assoc');

if (!$result) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Chat not found.'
    ]);
    exit;
}

$userEmail = $result[0]['email'];
$userName = $result[0]['name'];

// Insert admin message
$insertQuery = "INSERT INTO chat_messages (message_id, text, is_admin) VALUES (:message_id, :text, TRUE)";
$insertParams = [
    'message_id' => $chatId,
    'text' => $message
];
$inserted = db_query($insertQuery, $insertParams, 'insert');

if (!$inserted) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to save message.'
    ]);
    exit;
}

// Update message status and last message
$updateQuery = "UPDATE messages SET message = :message, status = 'replied', created_at = NOW() WHERE id = :chatId";
$updateParams = [
    'message' => $message,
    'chatId' => $chatId
];
db_query($updateQuery, $updateParams, 'insert');

// Prepare email to user
$subject = "Response to Your Inquiry";
$htmlMessage = "
    <h2>Response from Support Team</h2>
    <p><strong>Dear $userName,</strong></p>
    <p>$message</p>
    <p>Best regards,<br>Support Team</p>
";
$plainMessage = "Dear $userName,\n\n$message\n\nBest regards,\nSupport Team";

$mailStatus = sendMail($userEmail, "Support Team", $subject, $htmlMessage, $plainMessage);

echo json_encode([
    'data_type' => 'message',
    'message' => $mailStatus
        ? 'Message sent and email delivered.'
        : 'Message sent, but email delivery failed.'
]);
?>