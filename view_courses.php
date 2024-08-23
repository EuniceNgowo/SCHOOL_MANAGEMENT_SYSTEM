<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the courses the student is enrolled in
$query = "
    SELECT c.course_name, c.course_description, e.enrollment_date, e.status 
    FROM student_courses c 
    INNER JOIN enrollments e ON c.id = e.course_id 
    WHERE e.student_id = '$student_id'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error: ' . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>My Courses</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="enroll_course.php">Enroll in Courses</a>
        <a href="studentssettings.php">Settings</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Your Enrolled Courses</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                </tr>
                <?php while ($course = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                        <td><?php echo $course['enrollment_date']; ?></td>
                        <td><?php echo ucfirst($course['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You are not enrolled in any courses.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
