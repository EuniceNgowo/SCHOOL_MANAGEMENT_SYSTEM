<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];
        $description = $_POST['description'];

        $sql = "UPDATE calendar SET event_name = ?, event_date = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $event_name, $event_date, $description, $id);
        $stmt->execute();

        header("Location: calendar.php"); // Redirect after update
        exit;
    } else {
        $sql = "SELECT * FROM calendar WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
    }
} else {
    echo "No event ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
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
            <h1>Edit Event</h1>
            <?php if ($event): ?>
                <form action="edit-event.php?id=<?php echo htmlspecialchars($event['id']); ?>" method="post">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>

                    <label for="event_date">Event Date:</label>
                    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($event['description']); ?></textarea>

                    <button type="submit">Update Event</button>
                </form>
            <?php else: ?>
                <p>Event not found.</p>
            <?php endif; ?>
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
