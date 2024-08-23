<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the grades for the student's courses
$query = "SELECT c.course_code, c.course_name, g.grade 
          FROM grades g 
          JOIN courses c ON g.course_id = c.id 
          WHERE g.student_id = '$student_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Grades</title>
</head>
<body>
    <h2>Your Grades</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($grade = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $grade['course_code']; ?></td>
                        <td><?php echo $grade['course_name']; ?></td>
                        <td><?php echo $grade['grade']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no grades available.</p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
