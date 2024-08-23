<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete teacher based on ID
    $sql = "DELETE FROM teachers WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Teacher deleted successfully";
        header("Location: teacher.php"); // Redirect to the teachers page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>