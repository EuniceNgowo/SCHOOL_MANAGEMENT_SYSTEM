<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the query
        $stmt = $conn->prepare("INSERT INTO studs (username, password, first_name, last_name, email, phone_number, enrollment_date, status) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'active')");
        $stmt->bind_param("ssssss", $username, $hashed_password, $first_name, $last_name, $email, $phone_number);
        
        if ($stmt->execute()) {
            $success = "Student registered successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Register a New Student</h2>
    
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number"><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <button type="submit">Register Student</button>
    </form>
</body>
</html>
