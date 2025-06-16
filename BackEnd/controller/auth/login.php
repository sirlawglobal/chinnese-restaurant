<?php
require_once __DIR__ . '/../../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . ROOT . "login");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['fail'] = "Email and password are required.";
    header("Location: " . ROOT . "login");
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

        if ($user['role'] === 'admin') {
            header("Location: " . ROOT . "admin/dashboard");
        } else {
            header("Location: " . ROOT . "menu");
        }
        exit;

    } else {
        $_SESSION['fail'] = "Invalid password.";
    }
} else {
    $_SESSION['fail'] = "User not found.";
}

header("Location: " . ROOT . "login");
exit;
