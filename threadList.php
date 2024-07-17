<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>Discuss page</title>
</head>

<body>
    <?php include 'partitions/db_connect.php' ?>
    <?php

    //Fetch data from database
    $id = $_GET['Id']; // Id get from the index.php line no 81
    $sql = "SELECT * FROM categories WHERE category_id = $id";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $catName = $row['category_name'];
        $catDiscription = $row['category_discription'];
    }
    ?>

    <!-- create a php script for insert data in database -->
    <?php
    $successAlert = false;
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "POST") {
        $th_title = $_POST['title'];
        $th_desc = $_POST['desc'];
        //replace < and > by &lt; for defend js execution
        $th_title = str_replace("<", "&lt;", $th_title);
        $th_title = str_replace(">", "&gt;", $th_title);
        //replace < and > by &lt; for defend js execution
        $th_desc = str_replace("<", "&lt;", $th_desc);
        $th_desc = str_replace(">", "&gt;", $th_desc);

        $sno = $_POST['sno'];
        $sql = "INSERT INTO `threads` (`thread_title`, `thread_desc`, `thread_cat_id`, `thread_user_id`, `timestamp`)
                VALUES ('$th_title', '$th_desc', '$id', '$sno', now());";
        $result = mysqli_query($conn, $sql);
        $successAlert = true;
    }
    ?>
    <!-- Nvabar starts here -->
    <?php include "partitions/header.php"; ?>

    <!-- PHP script for success msg -->
    <?php
    if ($successAlert) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your thread has been added! Please wait community will respond you.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
    ?>

    <!-- Discusoon starts here -->
    <div class="container my-3">
        <div class="jumbotron">
            <h1 class="display-4">Welcome to <?php echo  $catName  ?> Discussion</h1>
            <p class="lead"><?php echo $catDiscription  ?></p>
            <hr class="my-4">
            <p>Never post personal information about another forum participant. <br>
                Don't post anything that threatens or harms the reputation of any person or organization.<br>
                Don't post anything that could be considered intolerant of a person's race, culture, appearance, gender,
                sexual preference, religion or age.</p>
            <a class="btn btn-success btn-lg" href="#" role="button">Learn more</a>
        </div>
        <hr>
    </div>
    <!-- Form starts here -->
    <!-- REQUEST_URI is used for The URI which was given in order to access this page; for instance, '/index.php'. -->
    <?php
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
        echo '<div class="container">
        <h1>Start a discussion</h1>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="form-group">
                <label for="exampleInputTitle">Thread title</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp">
                <small id="emailHelp" class="form-text text-muted">Make sure that the title is short and crisp.</small>
            </div>
                <input type="hidden" name="sno" value="' . $_SESSION["sno"] . '">
            <div class="form-group">
                <label for="exampleForDesc">Title Discription</label>
                <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <hr>
    </div>';
    } else {
        echo '
        <div class="container">
        <h1>Start a discussion</h1>
        <p class="lead">Please login to start a disscusions.</p>
        <hr>
        </div>
        ';
    }
    ?>

    <div class="container">
        <h1>Browse Questions</h1>
        <?php
        $id = $_GET['Id'];
        $sql = "SELECT * FROM threads  WHERE thread_cat_id = $id";
        $result = mysqli_query($conn, $sql);
        $noResult = true;
        while ($row = mysqli_fetch_assoc($result)) {
            $noResult = false;
            $id = $row['thread_id'];
            $title = $row['thread_title'];
            $desc = $row['thread_desc'];
            $thread_user_id = $row['thread_user_id'];

            $sql2 = "SELECT user_email FROM signup WHERE sno = '$thread_user_id'";
            $result2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($result2);

            echo '<div class="media py-3">
            <img src="assets/images/boy.png" width="64px" class="mr-3" alt="...">
            <div class="media-body">
                <h5 class="mt-0"><a href="thread.php?threadId=' . $id . '" class="text-dark">' . $title . '</a></h5>
                <p>' . $desc . '</p>
            </div>
             <p class="font-weight-bold my-0">' . $row2["user_email"] . '</p>
        </div>';
        }
        // echo var_dump($noResult);
        if ($noResult) {
            echo '<div class="jumbotron jumbotron-fluid">
                    <div class="container">
                    <h1 class="display-4">No threads found</h1>
                    <p class="lead">Be the first one to ask a question.</p>
                    </div>
                </div>';
        }
        ?>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    -->
</body>

</html>