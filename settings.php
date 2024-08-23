<?php
include 'config.php';

// Retrieve settings from database
$sql = "SELECT * FROM settings WHERE id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
} else {
    $settings = [
        'school_name' => '',
        'school_email' => '',
        'school_phone' => '',
        'admin_username' => '',
        'admin_password' => '',
        'notification_email' => '',
        'notification_phone' => '',
        'payment_method' => '',
        'payment_api_key' => '',
        'logo' => ''
    ];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = $_POST['school_name'] ?? '';
    $school_email = $_POST['school_email'] ?? '';
    $school_phone = $_POST['school_phone'] ?? '';
    $admin_username = $_POST['admin_username'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';
    $notification_email = $_POST['notification_email'] ?? '';
    $notification_phone = $_POST['notification_phone'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_api_key = $_POST['payment_api_key'] ?? '';

    // Validate and sanitize user input
    $school_name = filter_var($school_name, FILTER_SANITIZE_STRING);
    $school_email = filter_var($school_email, FILTER_SANITIZE_EMAIL);
    $school_phone = filter_var($school_phone, FILTER_SANITIZE_NUMBER_INT);
    $admin_username = filter_var($admin_username, FILTER_SANITIZE_STRING);
    $admin_password = filter_var($admin_password, FILTER_SANITIZE_STRING);
    $notification_email = filter_var($notification_email, FILTER_SANITIZE_EMAIL);
    $notification_phone = filter_var($notification_phone, FILTER_SANITIZE_NUMBER_INT);
    $payment_method = filter_var($payment_method, FILTER_SANITIZE_STRING);
    $payment_api_key = filter_var($payment_api_key, FILTER_SANITIZE_STRING);

    // Hash the admin password
    $admin_password = password_hash($admin_password, PASSWORD_BCRYPT);

    // Update the settings in the database
    $sql = "UPDATE settings SET school_name = ?, school_email = ?, school_phone = ?, admin_username = ?, admin_password = ?, notification_email = ?, notification_phone = ?, payment_method = ?, payment_api_key = ? WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $school_name, $school_email, $school_phone, $admin_username, $admin_password, $notification_email, $notification_phone, $payment_method, $payment_api_key);
    if ($stmt->execute()) {
        $message = "Settings saved successfully!";
    } else {
        $message = "Error updating settings: " . $conn->error;
    }
}

// Handle file upload
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    if (in_array($file_extension, $allowed_extensions)) {
        $upload_directory = '../assets/images/';
        $upload_file = $upload_directory . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_file)) {
            $sql = "UPDATE settings SET logo = ? WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_FILES['logo']['name']);
            $stmt->execute();
            $message = "Logo uploaded successfully!";
        } else {
            $message = "Error uploading logo.";
        }
    } else {
        $message = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .settings-container {
            display: flex;
        }

        .settings-sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .settings-sidebar a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            margin-bottom: 5px;
        }

        .settings-sidebar a.active, .settings-sidebar a:hover {
            background-color: #007bff;
            color: white;
        }

        .settings-content {
            flex: 1;
            padding: 20px;
        }

        .settings-content form {
            margin-bottom: 20px;
        }

        .settings-content label {
            display: block;
            margin: 10px 0 5px;
        }

        .settings-content input, .settings-content select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .settings-content button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .settings-content button:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px;
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
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
        <section class="settings-container">
            <aside class="settings-sidebar">
                <a href="#general-settings" class="active">General Settings</a>
                <a href="#security-settings">Security Settings</a>
                <a href="#notification-settings">Notification Settings</a>
                <a href="#payment-settings">Payment Settings</a>
                <a href="#upload-logo">Upload Logo</a>
            </aside>

            <div class="settings-content">
                <?php if (isset($message)): ?>
                    <div class="<?php echo strpos($message, 'Error') !== false ? 'error' : 'message'; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <h2 id="general-settings">General Settings</h2>
                <form action="" method="post">
                    <label for="school_name">School Name:</label>
                    <input type="text" id="school_name" name="school_name" value="<?php echo htmlspecialchars($settings['school_name']); ?>">

                    <label for="school_email">School Email:</label>
                    <input type="email" id="school_email" name="school_email" value="<?php echo htmlspecialchars($settings['school_email']); ?>">

                    <label for="school_phone">School Phone:</label>
                    <input type="text" id="school_phone" name="school_phone" value="<?php echo htmlspecialchars($settings['school_phone']); ?>">

                    <button type="submit">Save Changes</button>
                </form>

                <h2 id="security-settings">Security Settings</h2>
                <form action="" method="post">
                    <label for="admin_username">Admin Username:</label>
                    <input type="text" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($settings['admin_username']); ?>">

                    <label for="admin_password">Admin Password:</label>
                    <input type="password" id="admin_password" name="admin_password" placeholder="Leave blank to keep current password">

                    <button type="submit">Save Changes</button>
                </form>

                <h2 id="notification-settings">Notification Settings</h2>
                <form action="" method="post">
                    <label for="notification_email">Notification Email:</label>
                    <input type="email" id="notification_email" name="notification_email" value="<?php echo htmlspecialchars($settings['notification_email']); ?>">

                    <label for="notification_phone">Notification Phone:</label>
                    <input type="text" id="notification_phone" name="notification_phone" value="<?php echo htmlspecialchars($settings['notification_phone']); ?>">

                    <button type="submit">Save Changes</button>
                </form>

                <h2 id="payment-settings">Payment Settings</h2>
                <form action="" method="post">
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method">
                        <option value="paypal" <?php if ($settings['payment_method'] == 'paypal') echo 'selected'; ?>>PayPal</option>
                        <option value="stripe" <?php if ($settings['payment_method'] == 'stripe') echo 'selected'; ?>>Stripe</option>
                        <option value="mtn-mobile-money" <?php if ($settings['payment_method'] == 'mtn-mobile-money') echo 'selected'; ?>>MTN Mobile Money</option>
                        <option value="orange-mobile-money" <?php if ($settings['payment_method'] == 'orange-mobile-money') echo 'selected'; ?>>Orange Mobile Money</option>
                        <option value="bank-transfer" <?php if ($settings['payment_method'] == 'bank-transfer') echo 'selected'; ?>>Bank Transfer</option>
                    </select>

                    <label for="payment_api_key">Payment API Key:</label>
                    <input type="text" id="payment_api_key" name="payment_api_key" value="<?php echo htmlspecialchars($settings['payment_api_key']); ?>">

                    <button type="submit">Save Changes</button>
                </form>

                <h2 id="upload-logo">Upload School Logo</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="logo">Upload Logo:</label>
                    <input type="file" id="logo" name="logo">

                    <button type="submit">Upload Logo</button>
                </form>
            </div>
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
