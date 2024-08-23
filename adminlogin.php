<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt-> execute();
    $result = $stmt->get_result();
    $admins=$result->fetch_assoc();
     //print_r($user);
     //if($user && password_verify($password, $user['password'])){

    if($admins && $password==$admins['password']){
        $_SESSION['login'] = true;
        $_SESSION['user_data'] = $admins;
        echo "Login Successful";
      header("Location: index.php");
    } 

        
}
    

    


// Fetch login for the dropdown
$admins_result = $conn->query("SELECT email, password FROM admins");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>
    <form action="adminlogin.php" method="POST">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <label for="role">Role:</label>
        <select id="role" name="role">
        <option value="student">Student</option>
        <option value="admin">Admin</option>
      </select><br><br>
        <button type="submit">Login</button>
        <div id="error-message"></div>
    </form>
    <div id="error-message" style="color: red;"></div>
</div>

<script src="script.js"></script>
</body>
</html>


