<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch all courses that the student is not already enrolled in
$query = "
    SELECT c.id, c.course_name, c.course_description 
    FROM student_courses c 
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.student_id = '$student_id' 
    WHERE e.course_id IS NULL
";
$result = mysqli_query($conn, $query);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    $course_id = $_POST['enroll'];

    // Check if the student is already enrolled
    $check_query = "
        SELECT * FROM enrollments 
        WHERE student_id = '$student_id' AND course_id = '$course_id'
    ";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Enroll the student in the course
        $enroll_query = "
            INSERT INTO enrollments (student_id, course_id, enrollment_date) 
            VALUES ('$student_id', '$course_id', CURDATE())
        ";
        if (mysqli_query($conn, $enroll_query)) {
            header("Location: view_courses.php?enrollment=success");
            exit();
        } else {
            $error = "Error enrolling in course: " . mysqli_error($conn);
        }
    } else {
        $error = "You are already enrolled in this course.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Courses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Enroll in Courses</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="enroll_course.php">Enroll in Courses</a>
        <a href="studentssettings.php">Settings</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Available Courses</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <form method="post" action="enroll_course.php">
                <table>
                    <tr>
                        <th>Course Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($course = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                            <td>
                                <button type="submit" name="enroll" value="<?php echo $course['id']; ?>">Enroll</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </form>
        <?php else: ?>
            <p>No courses available for enrollment.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
