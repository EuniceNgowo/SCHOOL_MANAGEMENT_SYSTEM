<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete student based on ID
    $sql = "DELETE FROM notifications WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Notification deleted successfully";
        header("Location: notifications.php"); // Redirect to the students page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>