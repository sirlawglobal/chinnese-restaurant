<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../BackEnd/config/db.php';

// Verify mail configuration constants are defined
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
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function($str, $level) {
            file_put_contents(__DIR__.'/../mail.log', date('Y-m-d H:i:s')." [$level] $str\n", FILE_APPEND);
        };

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
        $error .= "SMTP Debug: " . print_r($mail->SMTPDebug, true) . "\n";
        
        error_log($error);
        file_put_contents(__DIR__.'/../mail.log', $error, FILE_APPEND);
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
        {$schedule_info}
        <p><strong>Total Amount:</strong> £" . number_format($order_data['total_amount'], 2) . "</p>
        <p><strong>Items:</strong></p>
        <ul>{$items_html}</ul>
        <p>We'll notify you when your order is being processed.</p>
    ";

    return sendMail($recipient_email, $recipient_name, $subject, $body);
}

function send_notification($order_id, $order_data, $cart) {
    $notification_data = [
        'type' => 'new_order',
        'order_id' => $order_id,
        'total_amount' => number_format($order_data['total_amount'], 2),
        'order_type' => ucfirst($order_data['order_type']),
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $ch = curl_init('http://localhost:8080/notify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Add timeout to prevent hanging
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    if ($response === false || $http_code !== 200) {
        $error_msg = "Failed to send notification: HTTP $http_code, Error: $curl_error, Response: " . ($response ?: 'No response');
        error_log($error_msg);
        error_log("Notification data sent: " . json_encode($notification_data));
        file_put_contents(__DIR__.'/../notification.log', date('Y-m-d H:i:s') . " - $error_msg\n", FILE_APPEND);
    } else {
        error_log("Notification sent successfully: HTTP $http_code, Response: $response");
        file_put_contents(__DIR__.'/../notification.log', date('Y-m-d H:i:s') . " - Notification sent successfully: HTTP $http_code\n", FILE_APPEND);
    }
    
    curl_close($ch);
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
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

error_log("Initiate Payment Input: " . print_r($input, true));

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

// Handle user_id: Set to NULL if not provided or invalid
$user_id = !empty($input['user_id']) ? trim(strip_tags($input['user_id'])) : null;


if(!$user_id){
$guest_name = 'Guest User';
}

if ($user_id !== null) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    if (!$stmt->fetch()) {
        $user_id = null; // Set to NULL if user_id doesn't exist
    }
}

if (!in_array($order_type, ['now', 'schedule'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order type']);
    exit;
}

if (empty($cart) || $total_amount <= 0 || empty($delivery_address) || empty($tx_ref)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}
if ($order_type === 'schedule' && (empty($schedule_date) || empty($schedule_time))) {
    echo json_encode(['success' => false, 'message' => 'Schedule date and time are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO `orders` (
            `user_id`, `tx_ref`, `delivery_address`, `order_notes`, 
            `order_type`, `schedule_date`, `schedule_time`, 
            `total_amount`, `status`, `transaction_id`, 
            `guest_email`, `guest_name`, `guest_phone`, 
            `user_email`, `user_name`, `user_phone`, `created_at`
        ) VALUES (
            :user_id, :tx_ref, :delivery_address, :order_notes, 
            :order_type, :schedule_date, :schedule_time, 
            :total_amount, 'pending', NULL, 
            :guest_email, :guest_name, :guest_phone, 
            :user_email, :user_name, :user_phone, NOW()
        )
    ");

    $stmt->execute([
        ':user_id' => $user_id,
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
        ':user_phone' => $user_phone
    ]);

    $order_id = $pdo->lastInsertId();

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
        $category_id = isset($item['category']) ? intval($item['category']) : null;

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

    $response = [
        'success' => true, 
        'order_id' => $order_id, 
        'tx_ref' => $tx_ref
    ];

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
        ];

        $mail_result = send_order_email($recipient_email, $recipient_name, $order_id, $order_data, $cart);
        if ($mail_result !== true) {
            error_log("Failed to send order email to: {$recipient_email}: {$mail_result}");
            if (ini_get('display_errors')) {
                $response['email_warning'] = 'Confirmation email could not be sent';
            }
        }
    }

    // Send WebSocket notification
    send_notification($order_id, [
        'total_amount' => $total_amount,
        'order_type' => $order_type
    ], $cart);

    echo json_encode($response);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()]);
}
?>