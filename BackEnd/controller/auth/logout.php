<?php
session_start();
$_SESSION = [];
session_destroy();
session_regenerate_id(true);

// Get the host dynamically (localhost or your domain)
$host = $_SERVER['HTTP_HOST'];

// Build absolute URL to your login page
$redirectUrl = "http://$host/chinnese-restaurant/";

// Redirect
header("Location: $redirectUrl");
exit;
