<?php
// mail_helper.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer autoloader using relative path
require_once __DIR__ . '/../phpmail/vendor/autoload.php';

function sendMail($toEmail, $toName, $subject, $htmlBody, $plainText = '') {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration (assuming constants are defined elsewhere)
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;

        // Sender and recipient
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $plainText ?: strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}



function send_order_email($recipient_email, $order_id, $order_data, $cart) {
    $subject = "Order Confirmation - Order #{$order_id}";

    $items_html = "";
    foreach ($cart as $item) {
        $item_name = htmlspecialchars($item['name'] ?? 'N/A');
        $portion = htmlspecialchars($item['portion'] ?? 'N/A');
        $price = number_format(floatval($item['price'] ?? 0), 2);
        $qty = intval($item['quantity'] ?? 1);
        $items_html .= "<li>{$item_name} ({$portion}) - ₦{$price} x {$qty}</li>";
    }

    $schedule_info = "";
    if ($order_data['order_type'] === 'schedule') {
        $schedule_info = "<p><strong>Scheduled For:</strong> {$order_data['schedule_date']} at {$order_data['schedule_time']}</p>";
    }

    $body = "
        <h2>Thank You for Your Order!</h2>
        <p><strong>Order ID:</strong> {$order_id}</p>
        <p><strong>Transaction Reference:</strong> {$order_data['tx_ref']}</p>
        <p><strong>Delivery Address:</strong> {$order_data['delivery_address']}</p>
        <p><strong>Order Type:</strong> " . ucfirst($order_data['order_type']) . "</p>
        {$schedule_info}
        <p><strong>Total Amount:</strong> ₦" . number_format($order_data['total_amount'], 2) . "</p>
        <p><strong>Items:</strong></p>
        <ul>{$items_html}</ul>
        <p>We’ll notify you when your order is being processed.</p>
    ";

   return sendMail($recipient_email, 'Customer', $subject, $body);

}
