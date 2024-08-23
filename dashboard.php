<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: studentslogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details for the welcome message
$query = "SELECT first_name FROM studs WHERE id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);
$student_name = htmlspecialchars($student['first_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

header {
    background-color: #4CAF50;
    color: white;
    text-align: center;
    padding: 20px;
}

nav {
    background-color: #f2f2f2;
    padding: 10px;
    text-align: center;
}

nav a {
    margin: 0 15px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

nav a:hover {
    color: #4CAF50;
}

.container {
    padding: 40px;
}

h2 {
    color: #333;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    margin: 10px 0;
}

ul li a {
    text-decoration: none;
    color: #4CAF50;
}

ul li a:hover {
    text-decoration: underline;
}

footer {
    background-color: #4CAF50;
    color: white;
    text-align: center;
    padding: 10px;
    position: fixed;
    bottom: 0;
    width: 100%;
}

/* Responsive Styles */
@media (max-width: 768px) {
    nav {
        padding: 10px 0;
    }
    
    nav a {
        display: block;
        margin: 5px 0;
    }
    
    .container {
        padding: 10px;
    }
    
    footer {
        padding: 5px;
    }
}

    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $student_name; ?>!</h1>
    </header>
    
    <nav>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="view_submissions.php">My Submissions</a>
        <a href="studentssettings.php">Settings</a>
        <a href="chat.php">Chat</a>
        <a href="studentscalendar.php">calendar</a>
        <a href="view_notifications.php">Notifications</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Dashboard</h2>
        <p>From this dashboard, you can access all the features available to you:</p>
        <ul>
            <li><a href="view_courses.php">View Courses</a> - See the courses you are enrolled in and view course details.</li>
            <li><a href="view_assignments.php">Assignments</a> - View and submit your assignments.</li>
            <li><a href="view_submissions.php">My Submissions</a> - Check the assignments you have submitted and view feedback.</li>
            <li><a href="studentssettings.php">Settings</a> - Update your profile and other settings.</li>
            <li><a href="chat.php">Chat</a> - Communicate with your teachers and peers.</li>
            <li><a href="view_results.php">Results</a>View Your outstanding results here!!</li>
            <li><a href="view_attendance.php">Attendance</a>View your attendance here!</li>
            <li><a href="logout.php">Logout</a> - End your current session.</li>
            <li><a href="HomePage.php">HomePage</a>Click here to go back to the main page</li>
        </ul>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
