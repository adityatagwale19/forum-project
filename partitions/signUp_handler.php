<?php
$showAlert = 'false';
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST') {

    //Make a connection
    include "db_connect.php";
    // create a variables
    $user_email = $_POST['signEmail'];
    $pass = $_POST['password'];
    $Cpass = $_POST['Cpassword'];

    // sql query for checking email already alrready exists or not
    $existSql = "SELECT * FROM `signup` WHERE `user_email` = '$user_email'";
    $result = mysqli_query($conn, $existSql);
    $numRowExists = mysqli_num_rows($result);
    if ($numRowExists > 0) {
        echo "Email already exists";
    } else {
        // Check password and cpassoword is match or not
        if ($pass == $Cpass) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `signup` (`user_email`, `user_pass`, `timestamp`)
            VALUES ('$user_email', '$hash', now());";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $showAlert = 'true';
                header("Location: /php/Forum_project/index.php?signupsuccess=true");
                exit();
            } else {
                // Signup failure (consider using JavaScript alert here)
                echo '<script>alert("Failed to Sign Up!")</script>';
            }
        } else {
            // Password Do not match (consider using JavaScript alert here)
            echo '<script>alert("Password Do not match!")</script>';
        }
    }
    // header("Location: /Forum_project/index.php?signupsuccess=false&error=$showError");
}