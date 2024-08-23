<?php
include('config.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School management system</title>
    <link rel="stylesheet" href="HomePage.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="log.png" alt="School Logo" >
            <p class="logo-name">BERVELY-WISE INTERNATIONAL HIGHER INSTITUTE</p>
        </div>
        <nav class="nav-links">
            <ul>
                <li><a href="adminlogin.php">AdminLogin</a></li>
                <li><a href="studentslogin.php">StudentsDashboard</a></li>
                
            </ul>
        </nav>
    </header>
    <div class="search-bar">
        <input type="text" placeholder="Search bar">
    </div>
    <main>
        <section class="welcome-message">
            <h1>Welcome to BIHI Buea</h1>
            <p class="greetings">Hello Sir/ Madam we're glad you're here!</p>
        </section>
        <section class="to-do-list">
            <h2>To-Do-List</h2>
            <ul>
                <li><a href="#task1">Assignment Deadlines</a></li>
                <li><a href="#task2">Meeting Reminders</a></li>
                <li><a href="#task3">Event Planning</a></li>
                <li><a href="#task4">Grading Tasks</a></li>
                <li><a href="#task5">Attendance Records</a></li>
                <li><a href="#task6">Administrative Duties</a></li>
                <li><a href="#task7">Communication Follow-ups</a></li>
            </ul>
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
</body>
</html>