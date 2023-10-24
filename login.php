<?php
session_start();


if ((isset($_SESSION['auth']) && $_SESSION['auth'] === true)) {
    header("Location: index.php");
    exit();
}

    if (isset($_GET['access'])) {
        $alert_user = true;
    }

require 'includes/snippet.php';
require 'includes/db_inc.php';



echo"<br>";
   
if(isset($_POST['submit'])){
   $email = sanitize(trim($_POST['email']));
   $password = sanitize(trim($_POST['password']));

   $sql_admin = "SELECT * from user where email = '$email' and  password = '$password' ";
   $query = mysqli_query($conn, $sql_admin);
   echo mysqli_error($conn);
   if(mysqli_num_rows($query) > 0)
   {
         
            while($row = mysqli_fetch_assoc($query)){
               $_SESSION['auth'] = true;
               $_SESSION['user'] = $row['email'];		
               }
               if ($_SESSION['auth'] === true) {
            header("Location: index.php");
            exit();
               }
   }
         else {
                  echo"<div class='alert alert-success alert-dismissable'>
                  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                  <strong style='text-align: center'> Wrong Email and Password.</strong>  </div>";
               }		  
         }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style2.css">
</head>
<body>
    <h1>Login</h1>
    <form method="post">
        <input type="text" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up</a></p>

</body>
</html>
