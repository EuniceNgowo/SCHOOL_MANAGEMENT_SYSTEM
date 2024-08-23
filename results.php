<?php
include 'config.php';

// Handle Add Result
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $term = $_POST['term'];
    $year = $_POST['year'];
    $grade = $_POST['grade'];

    // Check if student and course IDs are valid
    $student_check = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $student_check->bind_param('i', $student_id);
    $student_check->execute();
    $student_check_result = $student_check->get_result();

    $course_check = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $course_check->bind_param('i', $course_id);
    $course_check->execute();
    $course_check_result = $course_check->get_result();

    if ($student_check_result->num_rows > 0 && $course_check_result->num_rows > 0) {
        $sql = "INSERT INTO results (student_id, course_id, term, year, grade) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisss', $student_id, $course_id, $term, $year, $grade);

        if ($stmt->execute()) {
            $message = "New result added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error: Invalid student ID or course ID.";
    }
    $student_check->close();
    $course_check->close();
}

// Handle Edit Result
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $term = $_POST['term'];
    $year = $_POST['year'];
    $grade = $_POST['grade'];

    // Check if student and course IDs are valid
    $student_check = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $student_check->bind_param('i', $student_id);
    $student_check->execute();
    $student_check_result = $student_check->get_result();

    $course_check = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $course_check->bind_param('i', $course_id);
    $course_check->execute();
    $course_check_result = $course_check->get_result();

    if ($student_check_result->num_rows > 0 && $course_check_result->num_rows > 0) {
        $sql = "UPDATE results SET student_id = ?, course_id = ?, term = ?, year = ?, grade = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisssi', $student_id, $course_id, $term, $year, $grade, $id);

        if ($stmt->execute()) {
            $message = "Result updated successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error: Invalid student ID or course ID.";
    }
    $student_check->close();
    $course_check->close();
}

// Handle Delete Result
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $sql = "DELETE FROM results WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $message = "Result deleted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle Export to CSV
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="results.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Student Name', 'Course Name', 'Term', 'Year', 'Grade']);

    $sql = "SELECT results.id, students.name as student_name, courses.name as course_name, results.term, results.year, results.grade
            FROM results
            JOIN students ON results.student_id = students.id
            JOIN courses ON results.course_id = courses.id";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $results_per_page;

// Sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Search and Filtering
$search = isset($_GET['search']) ? $_GET['search'] : '';
$term_filter = isset($_GET['term']) ? $_GET['term'] : '';
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';

$sql = "SELECT results.id, students.name as student_name, courses.name as course_name, results.term, results.year, results.grade
        FROM results
        JOIN students ON results.student_id = students.id
        JOIN courses ON results.course_id = courses.id
        WHERE (students.name LIKE ? OR courses.name LIKE ?)
        AND results.term LIKE ?
        AND results.year LIKE ?
        ORDER BY $sort_column $sort_order
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$termFilter = "%" . $term_filter . "%";
$yearFilter = "%" . $year_filter . "%";
$stmt->bind_param('siiiii', $searchTerm, $searchTerm, $termFilter, $yearFilter, $start, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Count total results for pagination
$total_sql = "SELECT COUNT(*) as total FROM results
              JOIN students ON results.student_id = students.id
              JOIN courses ON results.course_id = courses.id
              WHERE (students.name LIKE ? OR courses.name LIKE ?)
              AND results.term LIKE ?
              AND results.year LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('ssss', $searchTerm, $searchTerm, $termFilter, $yearFilter);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_results = $total_row['total'];
$total_pages = ceil($total_results / $results_per_page);

$stmt->close();
$total_stmt->close();

// Handle CSV Display
$csv_data = [];
if (isset($_POST['show_csv'])) {
    $csv_sql = "SELECT results.id, students.name as student_name, courses.name as course_name, results.term, results.year, results.grade
                FROM results
                JOIN students ON results.student_id = students.id
                JOIN courses ON results.course_id = courses.id";
    $csv_result = $conn->query($csv_sql);

    if ($csv_result->num_rows > 0) {
        while ($row = $csv_result->fetch_assoc()) {
            $csv_data[] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results</title>
    <link rel="stylesheet" href="../css/style2.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .pagination a {
            padding: 8px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
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
                    <li><a href="settings.php" class="active">Settings</a></li>
                    <li><a href="fees.php">Fees</a></li>
                    <li><a href="results.php">Results</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="manage-section">
            <h1>Manage Results</h1>

            <!-- Search and Filter Form -->
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by student or course" value="<?php echo htmlspecialchars($search); ?>">
                <select name="term">
                    <option value="">All Terms</option>
                    <option value="Term 1" <?php if ($term_filter === 'Term 1') echo 'selected'; ?>>Term 1</option>
                    <option value="Term 2" <?php if ($term_filter === 'Term 2') echo 'selected'; ?>>Term 2</option>
                    <option value="Term 3" <?php if ($term_filter === 'Term 3') echo 'selected'; ?>>Term 3</option>
                </select>
                <select name="year">
                    <option value="">All Years</option>
                    <option value="2022" <?php if ($year_filter === '2022') echo 'selected'; ?>>2022</option>
                    <option value="2023" <?php if ($year_filter === '2023') echo 'selected'; ?>>2023</option>
                </select>
                <input type="submit" value="Search">
            </form>

            <!-- Add Result Form -->
            <h2>Add Result</h2>
            <form method="POST" action="">
                <input type="hidden" name="add" value="1">
                <label for="student_id">Student ID:</label>
                <input type="number" id="student_id" name="student_id" required>
                <label for="course_id">Course ID:</label>
                <input type="number" id="course_id" name="course_id" required>
                <label for="term">Term:</label>
                <input type="text" id="term" name="term" required>
                <label for="year">Year:</label>
                <input type="text" id="year" name="year" required>
                <label for="grade">Grade:</label>
                <input type="text" id="grade" name="grade" required>
                <input type="submit" value="Add Result">
            </form>

            <!-- Edit Result Form -->
            <?php if (isset($editResult)) { ?>
                <h2>Edit Result</h2>
                <form method="POST" action="">
                    <input type="hidden" name="edit" value="1">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($editResult['id']); ?>">
                    <label for="student_id">Student ID:</label>
                    <input type="number" id="student_id" name="student_id" value="<?php echo htmlspecialchars($editResult['student_id']); ?>" required>
                    <label for="course_id">Course ID:</label>
                    <input type="number" id="course_id" name="course_id" value="<?php echo htmlspecialchars($editResult['course_id']); ?>" required>
                    <label for="term">Term:</label>
                    <input type="text" id="term" name="term" value="<?php echo htmlspecialchars($editResult['term']); ?>" required>
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year" value="<?php echo htmlspecialchars($editResult['year']); ?>" required>
                    <label for="grade">Grade:</label>
                    <input type="text" id="grade" name="grade" value="<?php echo htmlspecialchars($editResult['grade']); ?>" required>
                    <input type="submit" value="Update Result">
                </form>
            <?php } ?>

            <!-- Results Table -->
            <table>
                <thead>
                    <tr>
                        <th><a href="?sort=id&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
                        <th><a href="?sort=student_name&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Student Name</a></th>
                        <th><a href="?sort=course_name&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Course Name</a></th>
                        <th><a href="?sort=term&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Term</a></th>
                        <th><a href="?sort=year&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Year</a></th>
                        <th><a href="?sort=grade&order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Grade</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['term']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><?php echo htmlspecialchars($row['grade']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $row['id']; ?>">Edit</a> | 
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1) { ?>
                    <a href="?page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort_column); ?>&order=<?php echo htmlspecialchars($sort_order); ?>">Previous</a>
                <?php } ?>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <a href="?page=<?php echo $i; ?>&sort=<?php echo htmlspecialchars($sort_column); ?>&order=<?php echo htmlspecialchars($sort_order); ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php } ?>
                <?php if ($page < $total_pages) { ?>
                    <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort_column); ?>&order=<?php echo htmlspecialchars($sort_order); ?>">Next</a>
                <?php } ?>
            </div>

            <!-- Export to CSV -->
            <form method="POST" action="">
                <input type="hidden" name="export_csv" value="1">
                <input type="submit" value="Export to CSV">
            </form>

            <!-- Display CSV Data -->
            <?php if (isset($csv_data) && !empty($csv_data)) { ?>
                <h2>CSV Data</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Course Name</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($csv_data as $data) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['id']); ?></td>
                                <td><?php echo htmlspecialchars($data['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($data['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($data['term']); ?></td>
                                <td><?php echo htmlspecialchars($data['year']); ?></td>
                                <td><?php echo htmlspecialchars($data['grade']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <?php if (isset($message)) { ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php } ?>
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
