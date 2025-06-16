<?php
class UserSession
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    public static function requireLogin($redirectTo = 'login')
    {
        if (!self::isLoggedIn()) {
            $_SESSION['fail'] = "You must log in to access this page.";
            redirect($redirectTo);
            exit;
        }
    }


    public static function getId()
    {
        return self::get('id');
    }


    public static function get($key, $default = '')
    {
        return $_SESSION['user'][$key] ?? $default;
    }

    public static function getFirstName()
    {
        $name = self::get('name');
        return explode(' ', $name)[0] ?? '';
    }

    public static function getProfilePicture()
    {
        return self::get('profile_picture', 'https://picsum.photos/40');
    }

    public static function hasRole(...$roles)
    {
        $userRole = self::get('role');
        return in_array($userRole, $roles);
    }

    public static function requireRole(array $roles, $redirectTo = 'login')
    {
        if (!self::hasRole(...$roles)) {
            $_SESSION['fail'] = "Access denied. Insufficient permissions.";
            redirect($redirectTo);
            exit;
        }
    }
}

//how to use them
// require_once 'init.php';

// UserSession::requireLogin(); // Always check login first
// UserSession::requireRole(['admin']); // Only allow 'admin' users

// echo "Welcome, Admin " . UserSession::getFirstName();
// if (UserSession::hasRole('admin')) {
//     echo "You are an admin.";
// } else {
//     echo "You are not an admin.";
// }