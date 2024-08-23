<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch student details based on ID
    $sql = "SELECT * FROM students WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();
    } else {
        echo "Student not found";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $class = $_POST['class'];
    $address = $_POST['address'];
    $parent_contact = $_POST['parent_contact'];

    // Update student details
    $sql = "UPDATE students SET 
            name='$name', email='$email', dob='$dob', 
            class='$class', address='$address', parent_contact='$parent_contact' 
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Student updated successfully";
        header("Location: student.php"); // Redirect to the students page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
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
            <h1>Edit Student</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $student['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $student['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo $student['dob']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="class">Class:</label>
                    <input type="text" id="class" name="class" value="<?php echo $student['class']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required><?php echo $student['address']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="parent_contact">Parent Contact:</label>
                    <input type="text" id="parent_contact" name="parent_contact" value="<?php echo $student['parent_contact']; ?>" required>
                </div>
                <button type="submit">Update Student</button>
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