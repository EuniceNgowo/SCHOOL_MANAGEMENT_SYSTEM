<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch all teachers for the dropdown
$query_teachers = "SELECT id, first_name, last_name FROM steachers";
$result_teachers = mysqli_query($conn, $query_teachers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Teacher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Chat with Teacher</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="studentssettings.php">Settings</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <form method="post" action="handle_massage.php">
            <label for="receiver_id">Select Teacher:</label>
            <select id="receiver_id" name="receiver_id" required>
                <option value="">Select a teacher</option>
                <?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
                    <option value="<?php echo $teacher['id']; ?>">
                        <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" rows="4" cols="50" required></textarea>
            <br>
            <button type="submit">Send Message</button>
        </form>

        <h2>Messages</h2>
        <?php
        $query_messages = "SELECT m.message, m.sent_at, t.first_name, t.last_name 
                           FROM messages m
                           JOIN teachers t ON m.receiver_id = t.id
                           WHERE m.sender_id = '$student_id'
                           ORDER BY m.sent_at DESC";
        $result_messages = mysqli_query($conn, $query_messages);

        if (mysqli_num_rows($result_messages) > 0) {
            while ($row = mysqli_fetch_assoc($result_messages)) {
                echo "<div class='message'>";
                echo "<p>" . htmlspecialchars($row['message']) . "</p>";
                echo "<small>Sent to: " . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . " at " . $row['sent_at'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No messages to display.</p>";
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
