<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete course based on ID
    $sql = "DELETE FROM courses WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Course deleted successfully";
        header("Location: course.php"); // Redirect to the course page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>