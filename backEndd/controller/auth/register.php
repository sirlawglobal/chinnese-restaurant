<?php
// register.php

// Turn off error display in production, but log errors instead
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../../config/db.php';  // Your PDO connection setup

file_put_contents(__DIR__ . '/debug.log', "POST data:\n" . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    // Basic input sanitization and validation
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$name || !$phone || !$email || empty($password)) {
        $response['message'] = 'Please fill all required fields correctly.';
        echo json_encode($response);
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            $response['message'] = 'Email already registered.';
            echo json_encode($response);
            exit;
        }

        // Hash password securely
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (name, phone, email, password)
            VALUES (:name, :phone, :email, :password)
        ");

        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':password' => $passwordHash
        ]);

        $response['success'] = true;
        $response['message'] = 'Registration successful! You can now login.';

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $response['message'] = 'Server error. Please try again later.';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
