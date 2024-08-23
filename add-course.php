<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $description = $_POST['description'];

    $sql = "INSERT INTO courses (name, teacher_id, description) VALUES ('$name', '$teacher_id', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "New course added successfully";
        header("Location: course.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

// Fetch teachers for the dropdown
$teacher_result = $conn->query("SELECT id, name FROM teachers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
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
        <section class="form-section">
            <h1>Add New Course</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Course Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="teacher_id">Teacher:</label>
                    <select id="teacher_id" name="teacher_id" required>
                        <option value="">Select Teacher</option>
                        <?php while($teacher = $teacher_result->fetch_assoc()): ?>
                            <option value="<?php echo $teacher['id']; ?>"><?php echo $teacher['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <button type="submit">Add Course</button>
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