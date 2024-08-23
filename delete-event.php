<?php
include 'config.php';

// Get the event ID from the URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Delete the event from the database
    $sql = "DELETE FROM calendar WHERE id = $event_id";

    if ($conn->query($sql) === TRUE) {
        echo "Event deleted successfully";
        header("Location: calendar.php"); // Redirect to the calendar page after deletion
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request";
}
?>