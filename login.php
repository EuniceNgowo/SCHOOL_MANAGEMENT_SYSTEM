<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM registration WHERE email = ?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt-> execute();
    $result = $stmt->get_result();
    $user=$result->fetch_assoc();
     //print_r($user);
     //if($user && password_verify($password, $user['password'])){

    
        if($user && $password==$user['password']){
            $_SESSION['login'] = true;
            $_SESSION['user_data'] = $user;
            echo "Login Successful";
          header("Location: /FinalCode/SMSCodes/Login/admin/HomePage.php");
         } 
            
         else {
            echo "You do not have permission to access the dashboard.";
        }
    

    

}

// Fetch login for the dropdown
$login_result = $conn->query("SELECT email, password FROM login");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>

<script src="script.js"></script>
</body>
</html>


