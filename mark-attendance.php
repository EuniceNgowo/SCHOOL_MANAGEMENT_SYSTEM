<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance = $_POST['attendance'];
    $date = date('Y-m-d');

    foreach ($attendance as $student_id => $status) {
        $sql = "INSERT INTO attendance (student_id, status, date) VALUES ('$student_id', '$status', '$date')";
        if (!$conn->query($sql)) {
            echo "Error: " . $conn->error;
        }
    }

    echo "Attendance recorded successfully.";
    header("Location: view-attendance.php");
    exit();
}

$conn->close();
?>