<?php
session_start(); // Start the session

// Include the database connection file
require_once 'db_connect.php';
$user_data = $_SESSION['user_data']; // Get the user data from the session


// Check if the user is logged in



// Get the user's data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

// Update profile information
if (isset($_POST['update_profile'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $phone_number = $_POST['phone_number'];
  $alternative_email = $_POST['alternative_email'];
  $profile_bio = $_POST['profile_bio'];

  // Update the user's data in the database
  $query = "UPDATE users SET username = '$username', email = '$email', phone_number = '$phone_number', alternative_email = '$alternative_email', profile_bio = '$profile_bio' WHERE id = '$user_id'";
  mysqli_query($conn, $query);

  // Display a success message
  echo '<div class="alert alert-success">Profile updated successfully!</div>';
}

// Change profile picture
if (isset($_POST['change_picture'])) {
  $profile_picture = $_FILES['profile_picture'];

  // Upload the new profile picture
  $target_dir = 'uploads/';
  $target_file = $target_dir . basename($profile_picture['name']);
  move_uploaded_file($profile_picture['tmp_name'], $target_file);

  // Update the user's profile picture in the database
  $query = "UPDATE users SET profile_picture = '$target_file' WHERE id = '$user_id'";
  mysqli_query($conn, $query);

  // Display a success message
  echo '<div class="alert alert-success">Profile picture updated successfully!</div>';
}

// Set security questions and answers
if (isset($_POST['set_security_questions'])) {
  $security_question1 = $_POST['security_question1'];
  $security_answer1 = $_POST['security_answer1'];
  $security_question2 = $_POST['security_question2'];
  $security_answer2 = $_POST['security_answer2'];

  // Update the user's security questions and answers in the database
  $query = "UPDATE users SET security_question1 = '$security_question1', security_answer1 = '$security_answer1', security_question2 = '$security_question2', security_answer2 = '$security_answer2' WHERE id = '$user_id'";
  mysqli_query($conn, $query);

  // Display a success message
  echo '<div class="alert alert-success">Security questions and answers updated successfully!</div>';
}
?>

<!-- Account Settings Form -->
<form action="" method="post" enctype="multipart/form-data">
  <h2>Account Settings</h2>
  <div class="form-group">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $user_data['username']; ?>">
  </div>
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo $user_data['email']; ?>">
  </div>
  <div class="form-group">
    <label for="phone_number">Phone Number:</label>
    <input type="text" id="phone_number" name="phone_number" value="<?php echo $user_data['phone_number']; ?>">
  </div>
  <div class="form-group">
    <label for="alternative_email">Alternative Email:</label>
    <input type="email" id="alternative_email" name="alternative_email" value="<?php echo $user_data['alternative_email']; ?>">
  </div>
  <div class="form-group">
    <label for="profile_bio">Profile Bio:</label>
    <textarea id="profile_bio" name="profile_bio"><?php echo $user_data['profile_bio']; ?></textarea>
  </div>
  <div class="form-group">
    <label for="profile_picture">Profile Picture:</label>
    <input type="file" id="profile_picture" name="profile_picture">
  </div>
  <div class="form-group">
    <label for="security_question1">Security Question 1:</label>
    <input type="text" id="security_question1" name="security_question1" value="<?php echo $user_data['security_question1']; ?>">
  </div>
  <div class="form-group">
    <label for="security_answer1">Security Answer 1:</label>
    <input type="text" id="security_answer1" name="security_answer1" value="<?php echo $user_data['security_answer1']; ?>">
  </div>
  <div class="form-group">
    <label for="security_question2">Security Question 2:</label>
    <input type="text" id="security_question2" name="security_question2" value="<?php echo $user_data['security_question2']; ?>">
  </div>
  <div class="form-group">
    <label for="security_answer2">Security Answer 2:</label>
    <input type="text" id="security_answer2" name="security_answer2" value="<?php echo $user_data['security_answer2']; ?>">
  </div>
  <button type="submit" name="update_profile">Update Profile</button>
  <button type="submit" name="change_picture">Change Profile Picture</button>
  <button type="submit" name="set_security_questions">Set Security Questions</button>
</form>