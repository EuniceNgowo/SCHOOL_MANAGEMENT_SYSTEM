<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Pagination settings
$limit = 10; // Number of notifications per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Mark notifications as read
if (isset($_GET['mark_read'])) {
    $notification_id = $_GET['mark_read'];
    $update_query = "UPDATE student_notifications SET is_read = TRUE WHERE id = '$notification_id' AND student_id = '$student_id'";
    mysqli_query($conn, $update_query);
}

// Fetch notifications
$query = "
    SELECT id, message, sent_date, is_read
    FROM student_notifications
    WHERE student_id = '$student_id' AND message LIKE '%$search%'
    ORDER BY sent_date DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $query);

// Count total notifications
$count_query = "SELECT COUNT(*) as total FROM student_notifications WHERE student_id = '$student_id' AND message LIKE '%$search%'";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_notifications = $count_row['total'];
$total_pages = ceil($total_notifications / $limit);

// Fetch count of unread notifications
$unread_count_query = "SELECT COUNT(*) as unread_count FROM student_notifications WHERE student_id = '$student_id' AND is_read = FALSE";
$unread_count_result = mysqli_query($conn, $unread_count_query);
$unread_count_row = mysqli_fetch_assoc($unread_count_result);
$unread_count = $unread_count_row['unread_count'];

if (!$result) {
    die('Error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .unread { font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>Your Notifications <?php if ($unread_count > 0) echo "<span>($unread_count unread)</span>"; ?></h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="view_notifications.php">Notifications <?php if ($unread_count > 0) echo "<span>($unread_count)</span>"; ?></a>
        <a href="settingssettings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <h2>Notifications</h2>

        <form action="view_notifications.php" method="post">
            <input type="text" name="search" placeholder="Search notifications" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($notification = mysqli_fetch_assoc($result)): ?>
                    <tr class="<?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                        <td><?php echo htmlspecialchars($notification['message']); ?></td>
                        <td><?php echo $notification['sent_date']; ?></td>
                        <td>
                            <?php if (!$notification['is_read']): ?>
                                <a href="view_notifications.php?mark_read=<?php echo $notification['id']; ?>">Mark as Read</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="view_notifications.php?page=<?php echo $page - 1; ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="view_notifications.php?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No notifications available.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
