<?php
require_once __DIR__ . '/../../BackEnd/config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['data_type' => 'message', 'message' => 'Invalid request.']);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['id'])) {
  echo json_encode(['data_type' => 'message', 'message' => 'Missing event ID.']);
  exit;
}

$params = [
  'title' => $data['title'],
  'type' => $data['type'],
  'event_date' => $data['date'],
  'start_time' => $data['startTime'],
  'end_time' => $data['endTime'],
  'venue' => $data['venue'],
  'notes' => $data['notes'],
  'id' => $data['id'],
  'user_id' => $_SESSION['user_id']
];

$query = "
  UPDATE events
  SET title = :title, type = :type, event_date = :event_date,
      start_time = :start_time, end_time = :end_time,
      venue = :venue, notes = :notes
  WHERE id = :id AND user_id = :user_id
";

if (db_query($query, $params)) {
  echo json_encode(['data_type' => 'event_updated', 'message' => 'Updated successfully.']);
} else {
  echo json_encode(['data_type' => 'message', 'message' => 'Update failed.']);
}

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'], $data['title'], $data['type'], $data['date'], $data['startTime'])) {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Missing required fields.'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "
    UPDATE events
    SET title = :title, type = :type, event_date = :event_date, start_time = :start_time,
        end_time = :end_time, venue = :venue, notes = :notes
    WHERE id = :id AND user_id = :user_id
";
$params = [
    'id' => $data['id'],
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
    $query = "DELETE FROM event_team WHERE event_id = :event_id";
    db_query($query, ['event_id' => $data['id']]);
    
    if (!empty($data['team'])) {
        foreach ($data['team'] as $member) {
            $query = "INSERT INTO event_team (event_id, team_member) VALUES (:event_id, :team_member)";
            db_query($query, ['event_id' => $data['id'], 'team_member' => $member]);
        }
    }
    echo json_encode([
        'data_type' => 'event_updated',
        'message' => 'Event updated successfully.'
    ]);
} else {
    echo json_encode([
        'data_type' => 'message',
        'message' => 'Failed to update event: ' . $GLOBALS['DB_STATE']['error']
    ]);
}
?>