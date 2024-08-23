<?php

session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch assignments for the courses the student is enrolled in
$query = "
    SELECT a.id, a.title, a.description, a.due_date 
    FROM assignments a
    INNER JOIN enrollments e ON a.course_id = e.course_id
    WHERE e.student_id = '$student_id'
    ORDER BY a.due_date ASC
";
$result = mysqli_query($conn, $query);

// Add a notification for each assignment
if (mysqli_num_rows($result) > 0) {
    while ($assignment = mysqli_fetch_assoc($result)) {
        $notification_message = "New assignment: " . htmlspecialchars($assignment['title']) . " is available.";
        
        // Insert notification for each assignment
        $insert_notification_query = "
            INSERT INTO notifications (student_id, message, sent_date)
            VALUES ('$student_id', '$notification_message', NOW())
        ";
        mysqli_query($conn, $insert_notification_query);
    }

    // Reset the result pointer to the beginning
    mysqli_data_seek($result, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Your Assignments</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="studentssettings.php">Settings</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Assignments</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($assignment = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                        <td><?php echo $assignment['due_date']; ?></td>
                        <td>
                            <a href="submit_assignment.php?assignment_id=<?php echo $assignment['id']; ?>">Submit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No assignments available.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
