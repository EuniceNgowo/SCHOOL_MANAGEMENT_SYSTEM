<?php
session_start();
include('config.php');

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: studentslogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$query = "SELECT first_name, last_name, email, profile_picture, phone_number, notifications_enabled, email_updates, security_question, security_answer FROM studs WHERE id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle profile updates
    if (isset($_POST['update_profile'])) {
        $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
        $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
        $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $password = trim(mysqli_real_escape_string($conn, $_POST['password']));
        $phone_number = trim(mysqli_real_escape_string($conn, $_POST['phone_number']));
        $notifications_enabled = isset($_POST['notifications_enabled']) ? 1 : 0;
        $email_updates = isset($_POST['email_updates']) ? 1 : 0;

        // Handle file upload
        $profile_picture = $student['profile_picture'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
            
            // Check if uploads directory exists and create it if not
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = basename($_FILES["profile_picture"]["name"]);
            } else {
                $message = "Failed to upload file.";
            }
        }

        // Validate and update password if provided
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $update_query = "UPDATE studs SET first_name='$first_name', last_name='$last_name', email='$email', password='$password', phone_number='$phone_number', profile_picture='$profile_picture', notifications_enabled='$notifications_enabled', email_updates='$email_updates' WHERE id='$student_id'";
        } else {
            $update_query = "UPDATE studs SET first_name='$first_name', last_name='$last_name', email='$email', phone_number='$phone_number', profile_picture='$profile_picture', notifications_enabled='$notifications_enabled', email_updates='$email_updates' WHERE id='$student_id'";
        }

        if (mysqli_query($conn, $update_query)) {
            $message = "Profile updated successfully!";
            
            // Add notification for profile update
            $notification_message = "Your profile has been updated.";
            $insert_notification_query = "
                INSERT INTO student_notifications (student_id, message, sent_date)
                VALUES ('$student_id', '$notification_message', NOW())
            ";
            mysqli_query($conn, $insert_notification_query);
        } else {
            $message = "Failed to update profile.";
        }
    }

    // Handle account deletion
    if (isset($_POST['delete_account'])) {
        $delete_query = "DELETE FROM studs WHERE id='$student_id'";
        if (mysqli_query($conn, $delete_query)) {
            // Notify user of account deletion
            $notification_message = "Your account has been deleted.";
            $insert_notification_query = "
                INSERT INTO student_notifications (student_id, message, sent_date)
                VALUES ('$student_id', '$notification_message', NOW())
            ";
            mysqli_query($conn, $insert_notification_query);

            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $message = "Failed to delete account.";
        }
    }

    // Handle security question updates
    if (isset($_POST['security_questions'])) {
        $security_question = trim(mysqli_real_escape_string($conn, $_POST['security_question']));
        $security_answer = trim(mysqli_real_escape_string($conn, $_POST['security_answer']));

        $update_query = "UPDATE studs SET security_question='$security_question', security_answer='$security_answer' WHERE id='$student_id'";

        if (mysqli_query($conn, $update_query)) {
            $message = "Security questions updated successfully!";
            
            // Add notification for security questions update
            $notification_message = "Your security questions have been updated.";
            $insert_notification_query = "
                INSERT INTO student_notifications (student_id, message, sent_date)
                VALUES ('$student_id', '$notification_message', NOW())
            ";
            mysqli_query($conn, $insert_notification_query);
        } else {
            $message = "Failed to update security questions.";
        }
    }
}

// Fetch recent activity log
$activity_query = "SELECT * FROM activity_log WHERE student_id='$student_id' ORDER BY activity_date DESC LIMIT 10";
$activity_result = mysqli_query($conn, $activity_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Settings</h1>
    </header>
    
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_courses.php">My Courses</a>
        <a href="view_assignments.php">Assignments</a>
        <a href="studentssettings.php">Settings</a>
        <a href="studentslogout.php">Logout</a>
    </nav>

    <div class="container">
        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <h2>Update Profile</h2>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            <br>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            <br>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">
            <small>Leave blank if you don't want to change your password.</small>
            <br>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($student['phone_number']); ?>">
            <br>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
            <br>
            <?php if ($student['profile_picture']): ?>
                <img src="uploads/<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture" width="100">
            <?php endif; ?>
            <br>

            <label for="notifications_enabled">Enable Notifications:</label>
            <input type="checkbox" id="notifications_enabled" name="notifications_enabled" <?php echo $student['notifications_enabled'] ? 'checked' : ''; ?>>
            <br>

            <label for="email_updates">Receive Email Updates:</label>
            <input type="checkbox" id="email_updates" name="email_updates" <?php echo $student['email_updates'] ? 'checked' : ''; ?>>
            <br>

            <button type="submit" name="update_profile">Save Changes</button>
        </form>

        <form method="post" action="">
            <h2>Delete Account</h2>
            <p>This action cannot be undone. Proceed with caution.</p>
            <button type="submit" name="delete_account">Delete My Account</button>
        </form>

        <form method="post" action="">
            <h2>Change Security Questions</h2>
            <label for="security_question">Security Question:</label>
            <input type="text" id="security_question" name="security_question" value="<?php echo htmlspecialchars($student['security_question']); ?>">
            <br>

            <label for="security_answer">Answer:</label>
            <input type="text" id="security_answer" name="security_answer" value="<?php echo htmlspecialchars($student['security_answer']); ?>">
            <br>

            <button type="submit" name="security_questions">Update Security Questions</button>
        </form>

        <h2>Recent Activity</h2>
        <?php if (mysqli_num_rows($activity_result) > 0): ?>
            <ul>
                <?php while ($activity = mysqli_fetch_assoc($activity_result)): ?>
                    <li><?php echo htmlspecialchars($activity['activity_description']) . ' on ' . $activity['activity_date']; ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No recent activity.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 University Management System</p>
    </footer>
</body>
</html>
