<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = $_POST['receiver_id'];
    $message = trim(mysqli_real_escape_string($conn, $_POST['message']));

    // Check if both receiver and message are provided
    if (!empty($receiver_id) && !empty($message)) {
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$student_id', '$receiver_id', '$message')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Message sent successfully!'); window.location.href = 'chat.php';</script>";
        } else {
            echo "<script>alert('Failed to send message.'); window.location.href = 'chat.php';</script>";
        }
    } else {
        echo "<script>alert('Both receiver and message are required.'); window.location.href = 'chat.php';</script>";
    }
}
?>
