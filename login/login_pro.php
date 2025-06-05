<?php
session_start();
require_once __DIR__ . '/../BackEnd/config/db.php';
require_once __DIR__ . '/../BackEnd/config/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['fail'] = "Email and password are required.";
        redirect(ROOT .'/login.php');
       
        exit;
    }

    // Fetch user from DB
    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = db_query($query, [$email], 'assoc');
   // var_dump($stmt);die;
    if ($stmt && count($stmt) > 0) {
        $user = $stmt[0]; // First match
   
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Remove password before storing in session
            unset($user['password']);

            // Store user data in session
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
           //var_dump( $_SESSION['user']);die;
           if (isAdmin()){
redirect('admin/index.php');
           }else{
redirect('user/index.php');
           }

           
            exit;
        } else {
           // var_dump($user['password']);die;
            $_SESSION['fail'] = "Invalid password.";
            redirect('user/login.php');
        }
    } else {
        $_SESSION['fail'] = "User not found.";
        redirect('user/login.php');
    }

redirect('index.php');
    exit;
}
?>
