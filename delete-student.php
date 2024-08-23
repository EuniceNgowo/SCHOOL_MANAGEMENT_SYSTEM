<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete student based on ID
    $sql = "DELETE FROM students WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Student deleted successfully";
        header("Location: student.php"); // Redirect to the students page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>