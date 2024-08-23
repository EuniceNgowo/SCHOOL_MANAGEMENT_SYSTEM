<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Initialize date range variables
$start_date = '';
$end_date = '';

// Initialize the base query for fetching attendance records
$query = "
    SELECT c.course_code, c.course_name, a.attendance_date, a.status 
    FROM attendance a 
    JOIN courses c ON a.course_id = c.id 
    WHERE a.student_id = '$student_id'
    ORDER BY a.attendance_date DESC
";

// Check if the form is submitted and date range is provided
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    if ($start_date && $end_date) {
        // Modify the query to filter by date range
        $query = "
            SELECT c.course_code, c.course_name, a.attendance_date, a.status 
            FROM attendance a 
            JOIN courses c ON a.course_id = c.id 
            WHERE a.student_id = '$student_id' 
            AND a.attendance_date BETWEEN '$start_date' AND '$end_date'
            ORDER BY a.attendance_date DESC
        ";
    }
}

// Execute the query
$result = mysqli_query($conn, $query);

// Fetch summary data
$summary_query = "
    SELECT status, COUNT(*) as count
    FROM attendance
    WHERE student_id = '$student_id'
    GROUP BY status
";
$summary_result = mysqli_query($conn, $summary_query);

$summary = [
    'Present' => 0,
    'Absent' => 0,
];

while ($row = mysqli_fetch_assoc($summary_result)) {
    $summary[$row['status']] = $row['count'];
}

// Calculate attendance percentage for each course
$percentage_query = "
    SELECT c.course_code, c.course_name,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(*) * 100 AS percentage
    FROM attendance a
    JOIN courses c ON a.course_id = c.id
    WHERE a.student_id = '$student_id'
    GROUP BY c.course_code, c.course_name
";
$percentage_result = mysqli_query($conn, $percentage_query);

// Export to CSV functionality
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=attendance_records.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Course Code', 'Course Name', 'Date', 'Status']);

    $csv_result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($csv_result)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Your Attendance</h2>

    <!-- Date Range Filter Form -->
    <form method="get" action="">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
        
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">

        <button type="submit">Filter</button>
    </form>

    <!-- Export CSV button -->
    <form method="post" action="">
        <button type="submit" name="export_csv">Export as CSV</button>
    </form>

    <!-- Attendance Summary -->
    <h3>Attendance Summary</h3>
    <ul>
        <li>Total Present: <?php echo $summary['Present']; ?></li>
        <li>Total Absent: <?php echo $summary['Absent']; ?></li>
    </ul>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($attendance = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $attendance['course_code']; ?></td>
                        <td><?php echo $attendance['course_name']; ?></td>
                        <td><?php echo $attendance['attendance_date']; ?></td>
                        <td>
                            <span class="<?php echo strtolower($attendance['status']); ?>">
                                <?php echo $attendance['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no attendance records available.</p>
    <?php endif; ?>

    <!-- Attendance Percentage -->
    <h3>Attendance Percentage</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Attendance Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($percentage = mysqli_fetch_assoc($percentage_result)): ?>
                <tr>
                    <td><?php echo $percentage['course_code']; ?></td>
                    <td><?php echo $percentage['course_name']; ?></td>
                    <td><?php echo round($percentage['percentage'], 2) . '%'; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
