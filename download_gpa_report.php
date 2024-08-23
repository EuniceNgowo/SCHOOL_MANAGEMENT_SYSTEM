<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the results
$query = "
    SELECT c.course_code, c.course_name, c.credits, r.grade, t.first_name as teacher_first_name, t.last_name as teacher_last_name
    FROM results r
    JOIN courses c ON r.course_id = c.id
    JOIN teachers t ON r.teacher_id = t.id
    WHERE r.student_id = '$student_id'
";
$result = mysqli_query($conn, $query);

// Initialize variables for GPA calculation
$total_points = 0;
$total_credits = 0;

// Function to convert grades to points
function gradeToPoints($grade) {
    switch ($grade) {
        case 'A': return 4.0;
        case 'B+': return 3.5;
        case 'B': return 3.0;
        case 'C+': return 2.5;
        case 'C': return 2.0;
        case 'D': return 1.0;
        case 'F': return 0.0;
        default: return 0.0;
    }
}

// Start output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GPA Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .gpa { margin-top: 20px; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>GPA Report</h1>
    <h3>Student ID: <?php echo htmlspecialchars($student_id); ?></h3>

    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Teacher</th>
                <th>Credits</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['teacher_first_name'] . ' ' . $row['teacher_last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['credits']); ?></td>
                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                </tr>
                <?php
                    // Calculate total points and credits
                    $total_points += gradeToPoints($row['grade']) * $row['credits'];
                    $total_credits += $row['credits'];
                ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    if ($total_credits > 0) {
        $gpa = $total_points / $total_credits;
        echo "<p class='gpa'>Your GPA: " . number_format($gpa, 2) . "</p>";
    } else {
        echo "<p>GPA cannot be calculated.</p>";
    }
    ?>

</body>
</html>

<?php
// Capture the HTML content
$html = ob_get_clean();

// Output the HTML content
echo $html;

// To download as PDF, you can convert this HTML to PDF using tools like dompdf
?>
