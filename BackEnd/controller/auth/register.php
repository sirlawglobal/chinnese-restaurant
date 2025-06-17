<?php
// register.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../../config/init.php';
require_once '../../helpers/mail_helper.php'; // Ensure this path is correct

header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['data_type' => 'register', 'success' => false, 'message' => 'Invalid input format.']);
    exit;
}

$name     = trim(filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING));
$phone    = trim(filter_var($data['phone'] ?? '', FILTER_SANITIZE_STRING));
$email    = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $data['password'] ?? '';
$role = trim(filter_var($data['role'] ?? 'user', FILTER_SANITIZE_STRING));


$allowedRoles = ['admin', 'staff', 'chef', 'waiter', 'cashier', 'manager', 'dishwasher', 'delivery', 'bartender','user'];
if (!in_array($role, $allowedRoles)) {
    $role = 'user'; // fallback
}

$response = ['data_type' => 'register', 'success' => false, 'message' => ''];

if (!$name || !$phone || !$email || empty($password)) {
    $response['message'] = 'Please fill all required fields correctly.';
    echo json_encode($response);
    exit;
}
try {
    // Check if email already exists

$check = db_query("SELECT id FROM users WHERE email = :email", ['email' => $email]);

if (!empty($check)) {
    $response['message'] = 'Email already registered.';
    echo json_encode($response);
    exit;
}
//var_dump( $check );die;
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
$insert = db_query("
    INSERT INTO users (name, phone, email, password, role)
    VALUES (:name, :phone, :email, :password, :role)
", [
    'name'     => $name,
    'phone'    => $phone,
    'email'    => $email,
    'password' => $hashedPassword,
    'role'     => $role
]);

    if ($insert) {
        // Prepare welcome email
        $subject = "Welcome to Our Restaurant!";
        $htmlMessage = "<h3>Hello $name,</h3><p>Thank you for registering at our Chinese Restaurant. We're excited to serve you!</p><p>Enjoy your experience!</p>";
        $plainMessage = "Hello $name,\n\nThank you for registering at our Chinese Restaurant. We're excited to serve you!\n\nEnjoy your experience!";

        // Send email using mail_helper.php
        sendMail($email, 'Chinese Restaurant', $subject, $htmlMessage, $plainMessage);
 if (isAdmin()) {
        $response['redirect'] = ROOT . 'admin/dashboard';
    } else {
        $response['redirect'] = ROOT . 'menu';
    }

        $response['success'] = true;
        $response['message'] = 'Registration successful! A welcome email has been sent.';
    } else {
        $response['message'] = 'Database error: ' . $GLOBALS['DB_STATE']['error'];
    }

} catch (Exception $e) {
    error_log("Server error: " . $e->getMessage());
    $response['message'] = 'Server error. Please try again later.';
}

echo json_encode($response);
exit;
