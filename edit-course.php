<?php
include 'config.php';

// Get the course ID from the URL parameter
$course_id = $_GET['id'];

// Fetch the course details from the database
$sql = "SELECT courses.id, courses.name, teachers.name as teacher_name, courses.description, courses.teacher_id 
        FROM courses 
        JOIN teachers ON courses.teacher_id = teachers.id 
        WHERE courses.id = '$course_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
} else {
    header('Location: course.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $teacher_id = $_POST['teacher_id'];
    $description = $_POST['description'];

    $sql = "UPDATE courses SET name = '$name', teacher_id = '$teacher_id', description = '$description' WHERE id = '$course_id'";
    $result = $conn->query($sql);

    if ($result) {
        header('Location: course.php');
        exit;
    } else {
        $error = 'Error updating course: ' . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="../css/style2.css">
</head>
<body>
<<header>
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
        <h1>Edit Course</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="name">Course Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $course['name']; ?>"><br><br>
            <label for="teacher_id">Teacher:</label>
            <select id="teacher_id" name="teacher_id">
                <?php
                $sql = "SELECT id, name FROM teachers";
                $result = $conn->query($sql);
                while ($teacher = $result->fetch_assoc()) {
                    echo "<option value='" . $teacher['id'] . "'" . ($teacher['id'] == $course['teacher_id'] ? ' selected' : '') . ">" . $teacher['name'] . "</option>";
                }
                ?>
            </select><br><br>
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo $course['description']; ?></textarea><br><br>
            <input type="submit" value="Update Course">
        </form>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
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