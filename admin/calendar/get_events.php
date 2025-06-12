<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Unauthorized.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "
    SELECT e.id, e.title, e.type, e.event_date, 
           DATE_FORMAT(e.start_time, '%h:%i %p') AS start_time,
           DATE_FORMAT(e.end_time, '%h:%i %p') AS end_time,
           e.venue, e.notes, GROUP_CONCAT(t.team_member) AS team
    FROM events e
    LEFT JOIN event_team t ON e.id = t.event_id
    WHERE e.user_id = :user_id
    GROUP BY e.id
    ORDER BY e.event_date, e.start_time
";
$params = ['user_id' => $user_id];
$events = db_query($query, $params, 'assoc');

if ($events === false) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to fetch events: ' . $GLOBALS['DB_STATE']['error']
    ]);
    exit;
}

$formatted_events = array_map(function($event) {
    return [
        'id' => $event['id'],
        'title' => $event['title'],
        'type' => $event['type'],
        'event_date' => $event['event_date'],
        'time' => $event['start_time'] . ($event['end_time'] ? ' - ' . $event['end_time'] : ''),
        'start_time' => $event['start_time'],
        'end_time' => $event['end_time'],
        'venue' => $event['venue'],
        'notes' => $event['notes'],
        'team' => $event['team'] ? explode(',', $event['team']) : []
    ];
}, $events);

echo json_encode([
    'data_type' => 'events',
    'data' => $formatted_events
]);
?>