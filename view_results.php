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
    SELECT r.id, c.course_code, c.course_name, c.credits, r.grade, t.first_name as teacher_first_name, t.last_name as teacher_last_name
    FROM results r
    JOIN courses c ON r.course_id = c.id
    JOIN teachers t ON r.teacher_id = t.id
    WHERE r.student_id = '$student_id'
";
$result = mysqli_query($conn, $query);

// Initialize variables for GPA calculation and grade distribution
$total_points = 0;
$total_credits = 0;
$grade_distribution = [
    'A' => 0,
    'B+' => 0,
    'B' => 0,
    'C+' => 0,
    'C' => 0,
    'D' => 0,
    'F' => 0,
];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Results</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
</head>
<body>
    <h2>Your Results</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table border="1">
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
                        
                        // Update grade distribution
                        if (isset($grade_distribution[$row['grade']])) {
                            $grade_distribution[$row['grade']]++;
                        }
                    ?>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- GPA Calculation -->
        <?php
        if ($total_credits > 0) {
            $gpa = $total_points / $total_credits;
            echo "<h3>Your GPA: " . number_format($gpa, 2) . "</h3>";
        } else {
            echo "<p>GPA cannot be calculated.</p>";
        }
        ?>

        <!-- Grade Distribution Chart -->
        <h3>Grade Distribution</h3>
        <canvas id="gradeChart" width="200" height="200"></canvas>
        <script>
            var ctx = document.getElementById('gradeChart').getContext('0d');
            var gradeChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['A', 'B+', 'B', 'C+', 'C', 'D', 'F'],
                    datasets: [{
                        data: [
                            <?php echo $grade_distribution['A']; ?>,
                            <?php echo $grade_distribution['B+']; ?>,
                            <?php echo $grade_distribution['B']; ?>,
                            <?php echo $grade_distribution['C+']; ?>,
                            <?php echo $grade_distribution['C']; ?>,
                            <?php echo $grade_distribution['D']; ?>,
                            <?php echo $grade_distribution['F']; ?>
                        ],
                        backgroundColor: [
                            '#4CAF50',
                            '#FFC107',
                            '#FF9800',
                            '#FF5722',
                            '#F44336',
                            '#9C27B0',
                            '#03A9F4'
                        ]
                    }]
                }
            });
        </script>

        <!-- Download Results as CSV -->
        <form method="post" action="download_results.php">
            <button type="submit">Download Results as CSV</button>
        </form>

        <!-- Download GPA Report as PDF -->
        <form method="post" action="download_gpa_report.php">
            <button type="submit">Download GPA Report as PDF</button>
        </form>

    <?php else: ?>
        <p>No results available.</p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
