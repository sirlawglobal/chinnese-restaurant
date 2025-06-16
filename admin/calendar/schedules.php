<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

ob_start(); // ✅ Buffer output to prevent header errors

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$user_id = UserSession::getId();

// ✅ GET: Fetch events
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT s.*, c.name AS category_name, c.slug AS category_slug
              FROM schedules s
              JOIN ev_categories c ON s.category_id = c.id
              WHERE s.user_id = :user_id
              ORDER BY s.date, s.start_time";

    $events = db_query($query, ['user_id' => $user_id], 'assoc');
    echo json_encode($events);
    ob_end_flush(); exit;
}

// ✅ POST: Create or Update event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $id          = (int)($data['id'] ?? 0);
    $title       = trim($data['title'] ?? '');
    $category_id = (int)($data['category_id'] ?? 0);
    $date        = $data['date'] ?? '';
    $start_time  = $data['startTime'] ?? '';
    $end_time    = $data['endTime'] ?? '';
    $team        = json_encode($data['team'] ?? []);
    $venue       = $data['venue'] ?? '';
    $notes       = $data['notes'] ?? '';

    if (!$title || !$category_id || !$date || !$start_time || !$end_time) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        ob_end_flush(); exit;
    }

    if ($id > 0) {
        // ✅ UPDATE
        $query = "UPDATE schedules SET
                    title = :title,
                    category_id = :category_id,
                    date = :date,
                    start_time = :start_time,
                    end_time = :end_time,
                    team = :team,
                    venue = :venue,
                    notes = :notes
                  WHERE id = :id AND user_id = :user_id";

        $params = compact(
            'title', 'category_id', 'date', 'start_time',
            'end_time', 'team', 'venue', 'notes', 'id', 'user_id'
        );

        $result = db_query($query, $params);

        if (!$result) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $GLOBALS['DB_STATE']['error'] ?: 'Update failed'
            ]);
            ob_end_flush(); exit;
        }

        echo json_encode(['status' => 'success', 'message' => 'Updated']);
        ob_end_flush(); exit;
    }

    // ✅ INSERT
    $query = "INSERT INTO schedules (user_id, title, category_id, date, start_time, end_time, team, venue, notes)
              VALUES (:user_id, :title, :category_id, :date, :start_time, :end_time, :team, :venue, :notes)";

    $params = compact(
        'user_id', 'title', 'category_id', 'date',
        'start_time', 'end_time', 'team', 'venue', 'notes'
    );

    $result = db_query($query, $params);

    if (!$result) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $GLOBALS['DB_STATE']['error'] ?: 'Insert failed'
        ]);
        ob_end_flush(); exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Created']);
    ob_end_flush(); exit;
}

// ✅ DELETE: Remove event
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = (int)($data['id'] ?? 0);

    if ($id > 0) {
        $query = "DELETE FROM schedules WHERE id = :id AND user_id = :user_id";
        $result = db_query($query, ['id' => $id, 'user_id' => $user_id]);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Deleted' : ($GLOBALS['DB_STATE']['error'] ?: 'Delete failed')
        ]);
        ob_end_flush(); exit;
    }

    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid event ID']);
    ob_end_flush(); exit;
}

// 🚫 Method Not Allowed
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
ob_end_flush(); exit;
