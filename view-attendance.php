<?php
include 'config.php';

// Calculate summary data
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$summary_sql = "SELECT 
                    COUNT(CASE WHEN status = 'Present' THEN 1 END) AS present_count,
                    COUNT(CASE WHEN status = 'Absent' THEN 1 END) AS absent_count,
                    COUNT(CASE WHEN status = 'Late' THEN 1 END) AS late_count
                FROM attendance";

if ($date_filter) {
    $summary_sql .= " WHERE date = '$date_filter'";
}

$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
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
                    <li><a href="index.php">Home</a></li>
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
            <h1>View Attendance</h1>
            <form method="GET" action="">
                <label for="date">Select Date:</label>
                <input type="date" id="date" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>">
                <button type="submit">Filter</button>
            </form>

            <div class="attendance-summary">
                <h2>Attendance Summary</h2>
                <ul>
                    <li>Present: <?php echo $summary['present_count']; ?></li>
                    <li>Absent: <?php echo $summary['absent_count']; ?></li>
                    <li>Late: <?php echo $summary['late_count']; ?></li>
                </ul>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch attendance records
                    $sql = "SELECT attendance.*, students.name 
                            FROM attendance 
                            JOIN students ON attendance.student_id = students.id";

                    if ($date_filter) {
                        $sql .= " WHERE attendance.date = '$date_filter'";
                    }
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['student_id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['status'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No attendance records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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