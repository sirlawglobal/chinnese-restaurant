<?php
// login.php - backend AJAX handler
require_once __DIR__ . '/../../config/init.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];
$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    $response['message'] = "Email and password are required.";
    echo json_encode($response);
    exit;
}

// Check for user in DB
$query = "SELECT * FROM users WHERE email = :email LIMIT 1";
$user = db_query($query, ['email' => $email], 'assoc');

if ($user && count($user) > 0) {
    $user = $user[0];

    if (password_verify($password, $user['password'])) {
        unset($user['password']);
        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        $response['success'] = true;
        $response['message'] = "Login successful!";

        // Redirect based on role
        if ($user['role'] === 'admin') {
            $response['redirect'] = ROOT . 'admin/dashboard';
        } else {
            $response['redirect'] = ROOT . 'menu';
        }

    } else {
        $response['message'] = "Invalid password.";
    }
} else {
    $response['message'] = "User not found.";
}

$response['data_type'] = 'login';
echo json_encode($response);
exit;
