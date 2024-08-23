<?php
session_start();
include('config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode([]);
    exit();
}

$student_id = $_SESSION['student_id'];

$query = "
    SELECT id, title, event_date as start, description
    FROM calendar_events
    WHERE student_id = '$student_id'
    ORDER BY event_date ASC
";
$result = mysqli_query($conn, $query);

$events = [];
while ($event = mysqli_fetch_assoc($result)) {
    $events[] = [
        'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['start'],
        'description' => $event['description']
    ];
}

echo json_encode($events);
?>
