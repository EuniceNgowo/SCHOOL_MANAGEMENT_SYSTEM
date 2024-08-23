<?php
include 'config.php';

if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];

    // Fetch the notification details from the database
    $sql = "SELECT * FROM notifications WHERE id = $notification_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $notification = $result->fetch_assoc();
    } else {
        echo "Notification not found";
        exit;
    }
}

// Update the notification details when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $message = $_POST['message'];

    $sql = "UPDATE notifications SET title = '$title', message = '$message' WHERE id = $notification_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: notifications.php"); // Redirect to the notifications page after successful update
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Notification</title>
    <link rel="stylesheet" href="../css/style.css">
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
            <section class="manage-section">
                <h1>Edit Notification</h1>
                <form method="POST" action="">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $notification['title']; ?>" required>
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" required><?php echo $notification['message']; ?></textarea>
                    <button type="submit">Update Notification</button>
                </form>
            </section>
        </main>
        <footer>
        <div class="footer-content">
            <p class="contact-heading">Contact Information:</p>
            <ul>
                <li>Phone: +237650675076, +237675254348</li>
                <li>Address: Opposite Presbyterian Church, Molyko, Buea</li>
                <li>Social Media Links:
                    <ul>
                        <li><a href="https://www.facebook.com/chitechma.buea.3">Facebook</a></li>
                        <li><a href="mailto:chitechma@gmail.com">Gmail</a></li>
                        <li>P.O. Box 218</li>
                    </ul>
                </li>
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
