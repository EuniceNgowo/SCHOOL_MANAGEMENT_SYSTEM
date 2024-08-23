<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO admins (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "registration Successful";
        header("Location: /Eunice/login/adminlogin.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    

    
}

// Fetch adminregistration for the dropdown
$admins_result = $conn->query("SELECT username, email, password FROM admins");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Admin Registration</h2>
    <form action="adminregistration.php" method="POST">

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="username" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
</div>

<script src="script.js"></script>
</body>
</html>


