<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the results
$query = "
    SELECT c.course_code, c.course_name, r.grade, t.first_name as teacher_first_name, t.last_name as teacher_last_name
    FROM results r
    JOIN courses c ON r.course_id = c.id
    JOIN teachers t ON r.teacher_id = t.id
    WHERE r.student_id = '$student_id'
";
$result = mysqli_query($conn, $query);

// Generate CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="results.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Course Code', 'Course Name', 'Teacher', 'Grade']);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['course_code'],
            $row['course_name'],
            $row['teacher_first_name'] . ' ' . $row['teacher_last_name'],
            $row['grade']
        ]);
    }
}

fclose($output);
exit();
?>
