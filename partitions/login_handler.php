<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include "db_connect.php";
    $user_email = $_POST['loginEmail'];
    $pass = $_POST['loginPass'];

    // Fetch data from database to check username and password is correct or not
    $sql = "SELECT * FROM `signup` WHERE user_email = '$user_email'";
    $result = mysqli_query($conn, $sql);
    $numRows = mysqli_num_rows($result);
    if ($numRows = 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($pass, $row['user_pass'])) {
            session_start();
            $_SESSION['loggedIn'] = true;
            $_SESSION['sno'] = $row['sno'];
            $_SESSION['signEmail'] = $user_email;
            // echo "logged in";
            // If person logged in so redirect to index.php page
            header("Location: /php/Forum_project/index.php");
            exit();
        } else {
            echo "unable to login";
        }
    }
}