<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO registration (firstname, lastname, email, password) VALUES ('$firstname', '$lastname', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "registration Successful";
        header("Location: /FinalCode/SMSCodes/login/admin/login.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    //$conn->close();
}

// Fetch registration for the dropdown
$registration_result = $conn->query("SELECT firstname, lastname, email, password FROM registration");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
 <div class="registration-form">
    <h1>Registration Form</h1>
    <form action="registration.php" method="POST">
     <p>First Name:</p>
     <input type="text" name="firstname" placeholder="First Name">
     <p>Last Name:</p>
     <input type="text" name="lastname" placeholder="Last Name">
     <p>Email:</p>
     <input type="text" name="email" placeholder="Email">
     <p>Password:</p>
     <input type="text" name="password" placeholder="password">
     <button type="submit">Register</button>
    </form>
 </div>
</body>
</html>