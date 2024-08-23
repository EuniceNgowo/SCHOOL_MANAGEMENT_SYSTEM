<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: studentssettings.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch submitted assignments and feedback
$query = "
    SELECT s.id, a.title, s.submission_date, s.file_path, s.feedback, s.grade 
    FROM submissions s
    INNER JOIN assignments a ON s.assignment_id = a.id
    WHERE s.student_id = '$student_id'
    ORDER BY s.submission_date DESC
";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submissions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Your Submissions</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="view_submissions.php">My Submissions</a>
        <a href="studentssettings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Submitted Assignments</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Submission Date</th>
                    <th>File</th>
                    <th>Feedback</th>
                    <th>Grade</th>
                </tr>
                <?php while ($submission = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['title']); ?></td>
                        <td><?php echo $submission['submission_date']; ?></td>
                        <td><a href="<?php echo $submission['file_path']; ?>" download>Download</a></td>
                        <td><?php echo htmlspecialchars($submission['feedback']); ?></td>
                        <td><?php echo htmlspecialchars($submission['grade']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have not submitted any assignments yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p></footer>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>

