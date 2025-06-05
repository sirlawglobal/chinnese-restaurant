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

// ✅ INSERT INTO DB WITH STATUS
$insertQuery = "INSERT INTO messages (name, email, telephone, message, status) 
                VALUES (:name, :email, :telephone, :message, :status)";
$params = [
    'name' => $name,
    'email' => $email,
    'telephone' => $telephone,
    'message' => $message,
    'status' => $mailStatus
];

$inserted = db_query($insertQuery, $params);

if ($inserted) {
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
