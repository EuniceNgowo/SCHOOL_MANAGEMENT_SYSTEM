<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch teacher details based on ID
    $sql = "SELECT * FROM teachers WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $teacher = $result->fetch_assoc();
    } else {
        echo "Teacher not found";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    
    // Update teacher details
    $sql = "UPDATE teachers SET 
            name='$name', email='$email', subject='$subject', 
            contact='$contact', address='$address'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Teacher updated successfully";
        header("Location: teacher.php"); // Redirect to the teachers page
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
    <title>Edit Teacher</title>
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
        <h1>Edit Teacher</h1>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $teacher['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?php echo $teacher['subject']; ?>" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" value="<?php echo $teacher['contact']; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo $teacher['address']; ?></textarea>
            </div>
            <button type="submit">Update Teacher</button>
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