<?php
function redirect($url)
{
	header("Location: ". ROOT . $url);
	die;
}


// function isLoggedIn()
// {
//     return isset($_SESSION['user']);
// }

function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}



function requireLogin($redirectTo = "login")
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
function requireAdmin($redirectTo = "login")
{
    if (!isAdmin()) {
        $_SESSION['fail'] = "Access denied. Admins only.";
        redirect($redirectTo);
        exit;
    }
}


function formatOrderId($orderId, $orderDate) {
    // Add 100 to the order_id
    $formattedId = 100 + (int)$orderId; // Ensure order_id is treated as an integer
    // Parse the order date
    $date = new DateTime($orderDate);
    // Format the date as "Jun 06, 2025"
    $formattedDate = $date->format('M d, Y');
    // Return the combined string with a newline
    return "PO{$formattedId}\n{$formattedDate}";
}
