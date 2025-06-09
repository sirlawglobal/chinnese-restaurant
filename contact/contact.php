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

// Get admin email (assuming stored in a config or hardcoded for simplicity)
$adminEmail = 'admin@example.com'; // Replace with actual admin email or fetch from config

if (!$adminEmail) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'No admin email configured.'
    ]);
    exit;
}

// Prepare email content
$subject = "New Contact Message from $name";
$htmlMessage = "
    <h2>New Contact Form Submission</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Telephone:</strong> $telephone</p>
    <p><strong>Message:</strong><br>$message</p>
";
$plainMessage = "Name: $name\nEmail: $email\nTelephone: $telephone\nMessage: $message";

// Attempt to send email
$mailStatus = sendMail($adminEmail, $name, $subject, $htmlMessage, $plainMessage) ? 'sent' : 'failed';

// Insert into messages
$insertMessageQuery = "INSERT INTO messages (name, email, telephone, message, status) 
                      VALUES (:name, :email, :telephone, :message, :status)";
$messageParams = [
    'name' => $name,
    'email' => $email,
    'telephone' => $telephone,
    'message' => $message,
    'status' => $mailStatus === 'sent' ? 'pending' : $mailStatus
];
$insertedMessage = db_query($insertMessageQuery, $messageParams, 'insert');
$message_id = $insertedMessage ? db_query("SELECT LAST_INSERT_ID() AS id", [], 'assoc')[0]['id'] : null;

if ($insertedMessage && $message_id) {
    // Insert initial chat message
    $insertChatQuery = "INSERT INTO chat_messages (message_id, text, is_admin) VALUES (:message_id, :text, FALSE)";
    $chatParams = [
        'message_id' => $message_id,
        'text' => $message
    ];
    db_query($insertChatQuery, $chatParams, 'insert');

    echo json_encode([
        'data_type' => 'message',
        'message' => $mailStatus === 'sent'
            ? 'Your message was sent and saved!'
            : 'Message saved, but email delivery failed.'
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to save your message.'
    ]);
}
?>