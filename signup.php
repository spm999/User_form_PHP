<?php

require 'includes/snippet.php';
require 'includes/db_inc.php';

$registrationMessage = ''; // Initialize the message variable

if (isset($_POST['submit'])) {
    $username = sanitize(trim($_POST['username']));
    $age = sanitize(trim($_POST['age']));
    $email = sanitize(trim($_POST['email']));
    $dob = sanitize(trim($_POST['dob']));
    $contact_no = sanitize(trim($_POST['contact_no']));
    $password = sanitize(trim($_POST['password']));

    // Convert the date of birth to the MySQL date format
    $dob = date('Y-m-d', strtotime($dob));

    // Check if email and contact number are not in use
    $checkSql = "SELECT COUNT(*) FROM user WHERE email = '$email' OR contact_no = '$contact_no'";
    $checkQuery = mysqli_query($conn, $checkSql);

    if ($checkQuery) {
        $count = mysqli_fetch_row($checkQuery)[0];
        if ($count > 0) {
            $registrationMessage = 'Email or contact number is already in use. Please choose a different one.';
        } else {
            // Handle file upload
            $profile_image = $_FILES["profile_image"]["name"];

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
                // File has been successfully uploaded, now insert the data into the database.
                $sql = "INSERT INTO user(username, age, email, dob, contact_no, password, profile_image) VALUES ('$username', '$age', '$email', '$dob', '$contact_no', '$password', '$profile_image')";

                $query = mysqli_query($conn, $sql);

                if ($query) {
                    $registrationMessage = 'Registration successful!!';
                    echo " <script> alert('Registration successful!!'); window.location.href = 'login.php';</script>";
                    // You can add additional code here if needed
                } else {
                    $registrationMessage = 'Registration failed!! Try again.';
                }
            } else {
                $registrationMessage = 'File upload failed. Please try again.';
            }
        }
    } else {
        $registrationMessage = 'Database error. Please try again.';
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#signup-form').submit(function (event) {
                var age = $('#age').val();
                var email = $('#email').val();
                var dob = $('#dob').val();
                var contactNo = $('#contact_no').val();
                var profileImage = $('#profile_image').val();

                // Client-side validation
                if (!/^\d+$/.test(age) || age < 18) {
                    alert("Invalid age. Must be a number and at least 18 years old.");
                    event.preventDefault();
                } else if (!/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(email)) {
                    alert("Invalid email address.");
                    event.preventDefault();
                } else if (!/^\d{4}-\d{2}-\d{2}$/.test(dob)) {
                    alert("Invalid date of birth. Use yyyy-mm-dd format.");
                    event.preventDefault();
                } else if (!/^\d{10}$/.test(contactNo)) {
                    alert("Invalid contact number. Must be 10 digits.");
                    event.preventDefault();
                } else if (!/\.(jpg|jpeg|png)$/i.test(profileImage)) {
                    alert("Invalid profile image format. Use jpg, jpeg, or png.");
                    event.preventDefault();
                }
            });
        });
    </script>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <h1>Signup</h1>

    <?php
    // Display the registration message here
    if (!empty($registrationMessage)) {
        echo "<p>$registrationMessage</p>";
    }
    ?>
    <form id="signup-form" method="post" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Name" required><br>
        <input type="text" name="age" id="age" placeholder="Age" required><br>
        <input type="text" name="email" id="email" placeholder="Email" required><br>
        <input type="password" id="password" name="password" placeholder="Enter your password" required><br>
        <input type="text" name="dob" id="dob" placeholder="Date of Birth (yyyy-mm-dd)" required>
        <input type="text" name="contact_no" id="contact_no" placeholder="Contact Number" required><br>
        <input type="file" name="profile_image" id="profile_image" accept=".jpg, .jpeg, .png" required><br>
        <input type="submit" name="submit" value="Signup">
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
