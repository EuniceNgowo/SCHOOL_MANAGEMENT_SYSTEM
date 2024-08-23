<?php
session_start();
include 'config.php';
// Check if the student is logged in



// Fetch totals
$students_count_query = "SELECT COUNT(*) AS total FROM students";
$teachers_count_query = "SELECT COUNT(*) AS total FROM teachers";
$courses_count_query = "SELECT COUNT(*) AS total FROM courses";
$attendance_query = "SELECT COUNT(*) AS total FROM attendance WHERE WEEK(date) = WEEK(CURDATE())";

// Execute queries
$students_result = mysqli_query($conn, $students_count_query);
$teachers_result = mysqli_query($conn, $teachers_count_query);
$courses_result = mysqli_query($conn, $courses_count_query);
$attendance_result = mysqli_query($conn, $attendance_query);

// Fetch data
$students_count = mysqli_fetch_assoc($students_result)['total'];
$teachers_count = mysqli_fetch_assoc($teachers_result)['total'];
$courses_count = mysqli_fetch_assoc($courses_result)['total'];
$attendance_count = mysqli_fetch_assoc($attendance_result)['total'];

// Fetch recent activities
$recent_activities_query = "SELECT activity FROM activities ORDER BY timestamp DESC LIMIT 5";
$recent_activities_result = mysqli_query($conn, $recent_activities_query);

$recent_activities = [];
while ($activity = mysqli_fetch_assoc($recent_activities_result)) {
    $recent_activities[] = $activity['activity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style2.css">
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="../assets/images/log.png" alt="School Logo">
        </div>
        <button class="nav-toggle" onclick="toggleNav()">☰</button>
        <nav class="nav-links" id="nav-links">
            <ul>
                <li><a href="HomePage.php">Home</a></li>
                <li><a href="student.php">Students</a></li>
                <li><a href="teacher.php">Teachers</a></li>
                <li><a href="course.php">Courses</a></li>
                <li><a href="attendance.php">Attendance</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="notifications.php">Notifications</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="fees.php">Fees</a></li>
                <li><a href="results.php">Results</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="welcome-message">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Manage the school system efficiently.</p>
    </section>

    <section class="dashboard-widgets">
        <div class="widget">
            <h3>Students</h3>
            <p>Total: <?php echo $students_count; ?></p>
        </div>
        <div class="widget">
            <h3>Teachers</h3>
            <p>Total: <?php echo $teachers_count; ?></p>
        </div>
        <div class="widget">
            <h3>Courses</h3>
            <p>Total: <?php echo $courses_count; ?></p>
        </div>
        <div class="widget">
            <h3>Attendance</h3>
            <p>Current Week: <?php echo $attendance_count; ?></p>
        </div>
    </section>

    <section class="recent-activities">
        <h2>Recent Activities</h2>
        <ul>
            <?php foreach ($recent_activities as $activity): ?>
                <li><?php echo htmlspecialchars($activity); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>

<footer>
       <div class="footer-content">
            <p class="contact-heading">Contact Information:</p>
            <ul>
                <li>Phone: +237650242757, +237650443410</li>
                <li>Address: Opposite Njeiforbi, Molyko, Buea</li>
                    <li>P.O. Box 318</li>
                </ul>
       </div>
</footer>

<script>
    function toggleNav() {
        const nav = document.getElementById('nav-links');
        nav.classList.toggle('show');
    }
</script>
</body>
</html>
