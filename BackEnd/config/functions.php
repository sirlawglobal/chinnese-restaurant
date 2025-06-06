<?php
function redirect($url)
{
	header("Location: ". ROOT . $url);
	die;
}

// function validate_user_signup(array $data, array &$errors): bool
// { 
//     //var_dump($data['token']);die;
//    // var_dump(db_get_row("SELECT name FROM token WHERE name = :token LIMIT 1", ['token' => $data['token']]));die;
    
//     // Validate email
//     if (empty($data['email'])) {
//         $errors['email'] = 'Email is required';
//     } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//         $errors['email'] = 'Invalid email format';
//     } elseif (db_get_row("SELECT id FROM users WHERE email = :email LIMIT 1", ['email' => $data['email']])) {
//         $errors['email'] = 'Email is already in use';
//     }
//   if (empty($data['name'])) {
//         $errors['name'] = 'Name is required';
//     }
//  // Validate phone number (Nigerian format example)
//     $cleaned = preg_replace('/[\s\-\(\)]/', '', $data['phone']);
//     if (empty($data['phone'])) {
//         $errors['phone'] = 'Phone number is required';
//     } else
//     if (!preg_match('/^\+?[0-9]{10,15}$/', $cleaned)) {
//         $errors['phone'] = 'Invalid  phone number';
//     }
 
//     if (empty($data['password'])) {
//         $errors['password'] = 'Password is required';
//     } elseif (strlen($data['password']) < 6) {
//         $errors['password'] = 'Password must be at least 6 characters long';
//     } elseif (!preg_match("/[A-Z]/", $data['password']) || !preg_match("/[0-9]/", $data['password'])) {
//         $errors['password'] = 'Password must contain at least one uppercase letter and one number';
//     }

//     return empty($errors);
// }

function isLoggedIn()
{
    return isset($_SESSION['user']);
}


function requireLogin($redirectTo = ROOT ."/login")
{
    if (!isLoggedIn()) {
        $_SESSION['fail'] = "You must log in to access this page.";
        redirect($redirectTo);
        exit;
    }
}
function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] == "admin";
}
function requireAdmin($redirectTo = ROOT ."/login")
{
    if (!isAdmin()) {
        $_SESSION['fail'] = "Access denied. Admins only.";
        redirect($redirectTo);
        exit;
    }
}

