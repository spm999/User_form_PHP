<?php

session_start();
require 'includes/db_inc.php';
require 'includes/snippet.php';

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
   header("Location: login.php");
   exit();
}

// Prevent the page from being cached
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Get the email of the current user from the session
$email = $_SESSION['user'];

// Fetch user data based on email
$sql = "SELECT * FROM user WHERE email = '$email'";
$query = mysqli_query($conn, $sql);

if ($query && mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_assoc($query);
} else {
    exit();
}

if (isset($_POST['update'])) {
    // Handle user data update here
    $newName = sanitize(trim($_POST['new_name']));
    $newAge = sanitize(trim($_POST['new_age']));
    $newDob = sanitize(trim($_POST['new_dob']));
    $newContactNo = sanitize(trim($_POST['new_contact_no']));

    // Convert the date of birth to the MySQL date format
    $newDob = date('Y-m-d', strtotime(str_replace('-', '/', $newDob)));

    // Check if the new contact number already exists in the user table
    $checkSql = "SELECT COUNT(*) FROM user WHERE contact_no = '$newContactNo'";
    $checkQuery = mysqli_query($conn, $checkSql);

    if ($checkQuery) {
        $count = mysqli_fetch_row($checkQuery)[0];
        if ($count > 0) {
            echo "<script>alert('Contact number already in use. Please choose a different one.');</script>";
        } else {
            // Handle file upload for profile image
            if (!empty($_FILES["new_profile_image"]["name"])) {
                $newProfileImage = $_FILES["new_profile_image"]["name"];
                if (move_uploaded_file($_FILES["new_profile_image"]["tmp_name"], $newProfileImage)) {
                    // Update the profile image if a new one is uploaded
                    $updateSql = "UPDATE user SET ";
                    $updateSql .= !empty($newName) ? "username = '$newName', " : "";
                    $updateSql .= !empty($newAge) ? "age = '$newAge', " : "";
                    $updateSql .= !empty($newDob) ? "dob = '$newDob', " : "";
                    $updateSql .= "profile_image = '$newProfileImage' WHERE email = '$email'";
                } else {
                    echo "<script>alert('File upload for profile image failed. Please try again.');</script>";
                }
            } else {
                // Update other user data excluding the profile image
                $updateSql = "UPDATE user SET ";
                $updateSql .= !empty($newName) ? "username = '$newName', " : "";
                $updateSql .= !empty($newAge) ? "age = '$newAge', " : "";
                $updateSql .= !empty($newDob) ? "dob = '$newDob', " : "";
                $updateSql = rtrim($updateSql, ', '); // Remove the trailing comma
                $updateSql .= " WHERE email = '$email'";
            }

            $updateQuery = mysqli_query($conn, $updateSql);

            if ($updateQuery) {
                echo "<script>alert('Data updated successfully!'); window.location.href = 'index.php';</script>";
            } else {
                echo "<script>alert('Data update failed. Please try again.');</script>";
            }
        }
    } else {
        echo "<script>alert('Database error. Please try again.');";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="css/style3.css">
</head>
<body>
    <div class="header">
        <h1>User Profile</h1>
        <br>
        <h2>Welcome, <?php echo $user['username']; ?></h2>
    </div>
    
    <div class="user-details">
        <h3>Your Details</h3>
        <p>Name: <?php echo $user['username']; ?></p>
        <p>Age: <?php echo $user['age']; ?></p>
        <p>Email: <?php echo $user['email']; ?></p>
        <p>Date of Birth: <?php echo $user['dob']; ?></p>
        <p>Contact Number: <?php echo $user['contact_no']; ?></p>
        <img src="<?php echo $user['profile_image']; ?>" alt="Profile Image" width="150">
    </div>

    <div class="update-form">
        <h3>Update Your Details</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="new_name" placeholder="New Name"><br>
            <input type="text" name="new_age" placeholder="New Age" ><br>
            <input type="text" name="new_dob" placeholder="New Date of Birth (dd-mm-yyyy)"><br>
            <input type="text" name="new_contact_no" placeholder="New Contact Number" ><br>
            <input type="file" name="new_profile_image" accept=".jpg, .jpeg, .png"><br>
            <input type="submit" name="update" value="Update">
        </form>
    </div>

    <a class="logout-link" href="logout.php">Logout</a>
</body>
</html>

