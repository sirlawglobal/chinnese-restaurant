<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../BackEnd/config/db.php';
require __DIR__ . '/../vendor/autoload.php'; // Path to Pusher autoload

// Pusher Configuration
if (!defined('PUSHER_APP_ID'))     define('PUSHER_APP_ID', '2007065');
if (!defined('PUSHER_APP_KEY'))    define('PUSHER_APP_KEY', 'c0ccafac1819f2d1f85c');
if (!defined('PUSHER_APP_SECRET')) define('PUSHER_APP_SECRET', 'cd8261be462cd3147075');
if (!defined('PUSHER_APP_CLUSTER')) define('PUSHER_APP_CLUSTER', 'eu');

// Initialize Pusher
use Pusher\Pusher;

$options = [
    'cluster' => PUSHER_APP_CLUSTER,
    'useTLS' => true,
    'debug' => true // Enable Pusher debugging
];

$pusher = new Pusher(
    PUSHER_APP_KEY,
    PUSHER_APP_SECRET,
    PUSHER_APP_ID,
    $options
);

// Test Pusher connection
try {
    $pusher->get('/channels'); // Simple API call to test connection
    error_log("Pusher connection successful");
} catch (Exception $e) {
    error_log("Pusher connection failed: " . $e->getMessage());
    file_put_contents(__DIR__ . '/../pusher_error.log', date('Y-m-d H:i:s') . " - Pusher connection failed: " . $e->getMessage() . "\n", FILE_APPEND);
}

// Verify mail configuration constants
if (!defined('MAIL_HOST') || !defined('MAIL_USERNAME') || !defined('MAIL_PASSWORD') || 
    !defined('MAIL_PORT') || !defined('MAIL_ENCRYPTION') || 
    !defined('MAIL_FROM_EMAIL') || !defined('MAIL_FROM_NAME')) {
    die(json_encode(['success' => false, 'message' => 'Mail configuration is incomplete']));
}

// PHPMailer setup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../BackEnd/phpmail/vendor/autoload.php';

function sendMail($toEmail, $toName, $subject, $htmlBody, $plainText = '') {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        $mail->Timeout = 30;

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $plainText ?: strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = "Mailer Error: " . $e->getMessage() . "\n";
        $error .= "PHPMailer ErrorInfo: " . $mail->ErrorInfo . "\n";
        error_log($error);
        file_put_contents(__DIR__ . '/../mail.log', $error, FILE_APPEND);
        return $error;
    }
}

function send_order_email($recipient_email, $recipient_name, $order_id, $order_data, $cart) {
    if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid recipient email address";
    }

    $subject = "Order Confirmation - Order #{$order_id}";

    $items_html = "";
    foreach ($cart as $item) {
        $item_name = htmlspecialchars($item['name'] ?? 'N/A');
        $portion = htmlspecialchars($item['portion'] ?? 'N/A');
        $price = number_format(floatval($item['price'] ?? 0), 2);
        $qty = intval($item['quantity'] ?? 1);
        $items_html .= "<li>{$item_name} ({$portion}) - £{$price} x {$qty}</li>";
    }

    $schedule_info = "";
    if ($order_data['order_type'] === 'schedule') {
        $schedule_info = "<p><strong>Scheduled For:</strong> {$order_data['schedule_date']} at {$order_data['schedule_time']}</p>";
    }

    $body = "
        <h2>Dear {$recipient_name},</h2>
        <h3>Thank You for Your Order!</h3>
        <p><strong>Order ID:</strong> {$order_id}</p>
        <p><strong>Transaction Reference:</strong> {$order_data['tx_ref']}</p>
        <p><strong>Delivery Address:</strong> {$order_data['delivery_address']}</p>
        <p><strong>Order Type:</strong> " . ucfirst($order_data['order_type']) . "</p>
        <p><strong>Delivery Method:</strong> " . ucfirst($order_data['delivery_method']) . "</p>
        {$schedule_info}
        <p><strong>Total Amount:</strong> £" . number_format($order_data['total_amount'], 2) . "</p>
        <p><strong>Items:</strong></p>
        <ul>{$items_html}</ul>
        <p>We'll notify you when your order is being processed.</p>
    ";

    return sendMail($recipient_email, $recipient_name, $subject, $body);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log session data for debugging
error_log("Initiate Payment Session Data: " . print_r($_SESSION, true));

try {
    $pdo = db_connect();
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Log input data for debugging
error_log("Initiate Payment Input: " . print_r($input, true));

// Sanitize and validate input
$cart = $input['cart'] ?? [];
$delivery_address = trim(strip_tags($input['delivery_address'] ?? '')); 
$order_notes = trim(strip_tags($input['order_notes'] ?? '')); 
$order_type = trim($input['order_type'] ?? 'now'); 
$schedule_date = $input['schedule_date'] ?? null;
$schedule_time = $input['schedule_time'] ?? null;
$total_amount = floatval($input['total_amount'] ?? 0);
$tx_ref = trim(strip_tags($input['tx_ref'] ?? '')); 
$guest_email = trim($input['guest_email'] ?? '');
$guest_phone = trim($input['guest_phone'] ?? '');
$user_email = trim($input['user_email'] ?? '');
$user_name = trim(strip_tags($input['user_name'] ?? ''));
$user_phone = trim(strip_tags($input['user_phone'] ?? ''));
$user_id = trim(strip_tags($input['user_id'] ?? ''));
$delivery_method = trim(strip_tags($input['delivery_method'] ?? 'delivery'));

$guest_name = $user_id ? '' : 'Guest User';
$order_type2 = 'online';

// Validate order_type
if (!in_array($order_type, ['now', 'schedule'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order type']);
    exit;
}

// Validate input
if (empty($cart) || $total_amount <= 0 || ($delivery_method === 'delivery' && empty($delivery_address))) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}
if ($order_type === 'schedule' && (empty($schedule_date) || empty($schedule_time))) {
    echo json_encode(['success' => false, 'message' => 'Schedule date and time are required']);
    exit;
}

// Begin transaction
try {
    $pdo->beginTransaction();

    // Insert into orders table
    $stmt = $pdo->prepare("
        INSERT INTO `orders` (
            `user_id`, `tx_ref`, `delivery_address`, `order_notes`, 
            `order_type`, `schedule_date`, `schedule_time`, 
            `total_amount`, `status`, `transaction_id`, 
            `guest_email`, `guest_name`, `guest_phone`, 
            `user_email`, `user_name`, `user_phone`, `created_at`, `order_type2`
        ) VALUES (
            :user_id, :tx_ref, :delivery_address, :order_notes, 
            :order_type, :schedule_date, :schedule_time, 
            :total_amount, 'pending', NULL, 
            :guest_email, :guest_name, :guest_phone, 
            :user_email, :user_name, :user_phone, NOW(), :order_type2
        )
    ");

    $stmt->execute([
        ':user_id' => $user_id ?: null,
        ':tx_ref' => $tx_ref,
        ':delivery_address' => $delivery_address,
        ':order_notes' => $order_notes,
        ':order_type' => $order_type,
        ':schedule_date' => $schedule_date,
        ':schedule_time' => $schedule_time,
        ':total_amount' => $total_amount,
        ':guest_email' => $guest_email,
        ':guest_name' => $guest_name,
        ':guest_phone' => $guest_phone,
        ':user_email' => $user_email,
        ':user_name' => $user_name,
        ':user_phone' => $user_phone,
        ':order_type2' => $order_type2
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert into order_items table with category_id
    $stmt = $pdo->prepare("
        INSERT INTO `order_items` (
            `order_id`, `item_name`, `portion`, `category_id`, `price`, `quantity`
        ) VALUES (
            :order_id, :item_name, :portion, :category_id, :price, :quantity
        )
    ");
    foreach ($cart as $item) {
        $item_name = trim(strip_tags($item['name'] ?? '')); 
        $portion = trim(strip_tags($item['portion'] ?? '')); 
        $category_id = isset($item['category']) ? intval($item['category']) : 20;

        if (empty($item_name)) {
            throw new Exception("Item name cannot be empty");
        }
        if (is_null($category_id) || $category_id <= 0) {
            throw new Exception("Invalid or missing category ID for item: {$item_name}");
        }

        $stmt->execute([
            ':order_id' => $order_id,
            ':item_name' => $item_name,
            ':portion' => $portion,
            ':category_id' => $category_id,
            ':price' => floatval($item['price'] ?? 0),
            ':quantity' => intval($item['quantity'] ?? 1)
        ]);
    }

    $pdo->commit();

    // Prepare response
    $response = [
        'success' => true,
        'order_id' => $order_id,
        'tx_ref' => $tx_ref,
        'pusher_debug' => ['status' => 'pending']
    ];

    // Send Pusher notification
    $data = [
        'type' => 'new_order',
        'order_id' => $order_id,
        'total_amount' => number_format($total_amount, 2),
        'order_type' => $order_type,
        'order_type2' => $order_type2,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    try {
        $pusher_response = $pusher->trigger('orders-channel', 'new-order-event', $data);
        $response['pusher_debug']['status'] = 'success';
        $response['pusher_debug']['data'] = $data;
        error_log("Pusher notification sent successfully for order ID: {$order_id}, Data: " . json_encode($data));
        file_put_contents(__DIR__ . '/../pusher_error.log', date('Y-m-d H:i:s') . " - Pusher notification sent: " . json_encode($data) . "\n", FILE_APPEND);
    } catch (Exception $e) {
        $response['pusher_debug']['status'] = 'failed';
        $response['pusher_debug']['error'] = $e->getMessage();
        error_log("Pusher error: " . $e->getMessage());
        file_put_contents(__DIR__ . '/../pusher_error.log', date('Y-m-d H:i:s') . " - Pusher error: " . $e->getMessage() . "\n", FILE_APPEND);
        $response['notification_warning'] = 'Could not send real-time notification';
    }

    // Send email notification
    $recipient_email = $user_email ?: $guest_email;
    $recipient_name = $user_name ?: $guest_name;
    if ($recipient_email) {
        $order_data = [
            'tx_ref' => $tx_ref,
            'delivery_address' => $delivery_address,
            'order_type' => $order_type,
            'schedule_date' => $schedule_date,
            'schedule_time' => $schedule_time,
            'total_amount' => $total_amount,
            'delivery_method' => $delivery_method
        ];

        $mail_result = send_order_email($recipient_email, $recipient_name, $order_id, $order_data, $cart);
        if ($mail_result !== true) {
            error_log("Failed to send order email to: {$recipient_email} - {$mail_result}");
            if (ini_get('display_errors')) {
                $response['email_warning'] = 'Confirmation email could not be sent';
            }
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    $pdo->rollBack();
    $errorMessage = "Order processing error: " . $e->getMessage();
    error_log($errorMessage);
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}
?>