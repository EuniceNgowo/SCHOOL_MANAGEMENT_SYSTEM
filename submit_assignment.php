<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$assignment_id = $_GET['assignment_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["assignment_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is a valid type
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Check if uploadOk is set to 1 (i.e., file type is valid)
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            $query = "
                INSERT INTO submissions (student_id, assignment_id, file_path, submission_date)
                VALUES ('$student_id', '$assignment_id', '$target_file', NOW())
            ";
            if (mysqli_query($conn, $query)) {
                // Redirect to view_submissions.php
                header("Location: view_submissions.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    // Display the file upload form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Submit Assignment</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <h1>Submit Assignment</h1>
        </header>
        
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="view_courses.php">My Courses</a>
            <a href="view_assignments.php">Assignments</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </nav>

        <div class="container">
            <h2>Submit Your Assignment</h2>
            <form action="submit_assignment.php?assignment_id=<?php echo htmlspecialchars($assignment_id); ?>" method="post" enctype="multipart/form-data">
                <label for="assignment_file">Choose file:</label>
                <input type="file" name="assignment_file" id="assignment_file" required>
                <button type="submit">Upload Assignment</button>
            </form>
        </div>

        <footer>
            <p>&copy; 2024 University Management System</p>
        </footer>
    </body>
    </html>
    <?php
}
?>
