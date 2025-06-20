<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

// if (!isset($_SESSION['user_id'])) {
//     echo json_encode([
//         'data_type' => 'message',
//         'message' => 'Unauthorized.'
//     ]);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['title'], $data['type'], $data['date'], $data['startTime'])) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Missing required fields.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "
    INSERT INTO events (user_id, title, type, event_date, start_time, end_time, venue, notes)
    VALUES (:user_id, :title, :type, :event_date, :start_time, :end_time, :venue, :notes)
";
$params = [
    'user_id' => $user_id,
    'title' => $data['title'],
    'type' => $data['type'],
    'event_date' => $data['date'],
    'start_time' => $data['startTime'],
    'end_time' => $data['endTime'] ?? null,
    'venue' => $data['venue'] ?? null,
    'notes' => $data['notes'] ?? null
];

if (db_query($query, $params)) {
    $event_id = $GLOBALS['DB_STATE']['insert_id'];
    if (!empty($data['team'])) {
        foreach ($data['team'] as $member) {
            $query = "INSERT INTO event_team (event_id, team_member) VALUES (:event_id, :team_member)";
            db_query($query, ['event_id' => $event_id, 'team_member' => $member]);
        }
    }
    echo json_encode([
        'data_type' => 'event_created',
        'message' => 'Event created successfully.',
        'event_id' => $event_id
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to create event: ' . $GLOBALS['DB_STATE']['error']
    ]);
}
?>