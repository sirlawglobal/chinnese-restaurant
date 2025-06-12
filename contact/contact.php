<?php
require_once __DIR__ . '/../BackEnd/config/db.php';
require_once __DIR__ . '/../BackEnd/helpers/mail_helper.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid input received.'
    ]);
    exit;
}

$name = htmlspecialchars(trim($data['name'] ?? 'Visitor'));
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$telephone = htmlspecialchars(trim($data['telephone'] ?? ''));
$message = htmlspecialchars(trim($data['message'] ?? ''));

if (!$email || !$message) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Email and message are required.'
    ]);
    exit;
}

// ✅ GET ADMIN EMAIL
$query = "SELECT email FROM users WHERE LOWER(role) = 'admin' LIMIT 1";
$result = db_query($query, [], 'assoc');
$adminEmail = $result[0]['email'] ?? null;

if (!$adminEmail) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'No admin email found.'
    ]);
    exit;
}

// ✅ Check if a message exists for this email
$checkQuery = "SELECT id FROM messages WHERE email = :email LIMIT 1";
$checkParams = ['email' => $email];
$existingMessage = db_query($checkQuery, $checkParams, 'assoc');
$messageId = $existingMessage ? $existingMessage[0]['id'] : null;

if (!$messageId) {
    // ✅ INSERT INTO messages if no existing record
    $insertMessageQuery = "INSERT INTO messages (name, email, telephone, message, status, created_at) 
                          VALUES (:name, :email, :telephone, :message, 'pending', NOW())";
    $messageParams = [
        'name' => $name,
        'email' => $email,
        'telephone' => $telephone,
        'message' => $message
    ];
    $insertedMessage = db_query($insertMessageQuery, $messageParams);

    // Check for messages insert error
    if (!$insertedMessage) {
        error_log("Messages insert failed: " . $GLOBALS['DB_STATE']['error']);
        echo json_encode([
            'data_type' => 'message',
            'message' => 'Failed to save your message. Error: ' . $GLOBALS['DB_STATE']['error']
        ]);
        exit;
    }

    // ✅ Get the last inserted message_id
    $messageId = $GLOBALS['DB_STATE']['insert_id'];
} else {
    $insertedMessage = true; // Assume success since no insert is needed
}

// ✅ INSERT INTO chat_messages using the determined message_id
$insertChatMessageQuery = "INSERT INTO chat_messages (message_id, text, is_admin, created_at, is_read) 
                          VALUES (:message_id, :message, 0, :created_at, 0)";
$chatMessageParams = [
    'message_id' => $messageId,
    'message' => $message,
    'created_at' => date('Y-m-d H:i:s')
];
$insertedChatMessage = db_query($insertChatMessageQuery, $chatMessageParams);

// Check for chat_messages insert error
if (!$insertedChatMessage) {
    error_log("Chat_messages insert failed: " . $GLOBALS['DB_STATE']['error']);
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to save your message. Error: ' . $GLOBALS['DB_STATE']['error']
    ]);
    exit;
}

// ✅ PREPARE EMAIL CONTENT
$subject = "New Contact Message from $name";
$htmlMessage = "
    <h2>New Contact Form Submission</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Telephone:</strong> $telephone</p>
    <p><strong>Message:</strong><br>$message</p>
";
$plainMessage = "Name: $name\nEmail: $email\nTelephone: $telephone\nMessage: $message";

// ✅ ATTEMPT TO SEND EMAIL
$mailStatus = sendMail($adminEmail, $name, $subject, $htmlMessage, $plainMessage) ? 'sent' : 'failed';

echo json_encode([
    'data_type' => 'message',
    'message' => $mailStatus === 'sent'
        ? 'Your message was sent and saved!'
        : 'Message saved, but email delivery failed.'
]);