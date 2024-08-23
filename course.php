<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
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
        <section class="manage-section">
            <h1>Manage Courses</h1>
            <a href="add-course.php">Add New Course</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Teacher</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching courses from the database
                    $sql = "SELECT courses.id, courses.name, teachers.name as teacher_name, courses.description 
                            FROM courses 
                            JOIN teachers ON courses.teacher_id = teachers.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['teacher_name'] . "</td>";
                            echo "<td>" . $row['description'] . "</td>";
                            echo "<td><a href='edit-course.php?id=" . $row['id'] . "'>Edit</a> | <a href='delete-course.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No courses found</td></tr>";
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