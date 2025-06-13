<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../BackEnd/config/db.php';

header('Content-Type: application/json');

try {
    $pdo = db_connect();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get user_id from session or query parameter
session_start();
$user_id = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'fetch':
        // Fetch notifications for the user (or all if user_id is null for system-wide notifications)
        $query = "SELECT id, order_id, text, read, timestamp FROM notifications WHERE user_id = :user_id OR user_id IS NULL ORDER BY timestamp DESC LIMIT 50";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format timestamp for display
        foreach ($notifications as &$notification) {
            $notification['time'] = formatTime($notification['timestamp']);
            $notification['read'] = (bool)$notification['read'];
        }

        echo json_encode(['success' => true, 'notifications' => $notifications]);
        break;

    case 'mark_read':
        $notification_id = $_POST['notification_id'] ?? null;
        if (!$notification_id) {
            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE notifications SET read = TRUE WHERE id = :id AND (user_id = :user_id OR user_id IS NULL)");
        $stmt->execute([':id' => $notification_id, ':user_id' => $user_id]);
        $affected = $stmt->rowCount();
        echo json_encode(['success' => $affected > 0, 'message' => $affected > 0 ? 'Notification marked as read' : 'Notification not found']);
        break;

    case 'mark_all_read':
        $stmt = $pdo->prepare("UPDATE notifications SET read = TRUE WHERE read = FALSE AND (user_id = :user_id OR user_id IS NULL)");
        $stmt->execute([':user_id' => $user_id]);
        $affected = $stmt->rowCount();
        echo json_encode(['success' => true, 'message' => "$affected notifications marked as read"]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Helper function to format timestamp (similar to JavaScript's formatTime)
function formatTime($timestamp) {
    try {
        $now = new DateTime();
        $time = new DateTime($timestamp);
        $diff = $now->diff($time);
        $diffMins = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
        if ($diffMins < 1) return 'Just now';
        if ($diffMins < 60) return "$diffMins min" . ($diffMins > 1 ? 's' : '') . ' ago';
        $diffHours = round($diffMins / 60);
        if ($diffHours < 24) return "$diffHours hour" . ($diffHours > 1 ? 's' : '') . ' ago';
        return $time->format('M d, Y, H:i');
    } catch (Exception $e) {
        error_log("Error formatting time: " . $e->getMessage());
        return 'Just now';
    }
}
?>


CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NULL, -- NULL for guest users or system-wide notifications
    order_id BIGINT UNSIGNED NOT NULL, -- References the orders table
    text TEXT NOT NULL, -- Notification message (e.g., "New online order #123 for £50.00")
    read BOOLEAN DEFAULT FALSE, -- Read status
    timestamp DATETIME NOT NULL, -- When the notification was created
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id) -- For faster queries by user
);