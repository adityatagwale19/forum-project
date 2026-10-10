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
    $id = $_GET['threadId'];
    $sql = "SELECT * FROM threads WHERE thread_id = $id";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $title = $row['thread_title'];
        $desc = $row['thread_desc'];
        $thread_user_id = $row['thread_user_id'];

        // Sql query to know who is post this thread
        $sql2 = "SELECT user_email FROM signup WHERE sno = '$thread_user_id'";
        $result2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($result2);
        $posted_by = $row2['user_email'];
    }
    ?>

    <?php
    $method = $_SERVER['REQUEST_METHOD'];
    $successAlert = false;
    if ($method == "POST") {
        $comments = $_POST['comment'];
        //replace < and > by &lt; for defend js execution
        $comments = str_replace("<", "&lt;", $comments);
        $comments = str_replace(">", "&gt;", $comments);
        $sno = $_POST['sno'];
        $sql = "INSERT INTO `comments` (`comment_content`, `thread_id`, `comment_by`, `comment_time`)
                VALUES ('$comments', '$id', '$sno', now());";
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
                <strong>Success!</strong> Your comment is added.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
    ?>

    <!-- Discusoon starts here -->
    <div class="container my-3">
        <div class="jumbotron">
            <h1 class="display-4"> <?php echo  $title  ?> </h1>
            <p class="lead"><?php echo $desc  ?></p>
            <p>Posted By - <em><?php echo $posted_by ?></em></p>
            <hr class="my-4">
            <p>Never post personal information about another forum participant. <br>
                Don't post anything that threatens or harms the reputation of any person or organization.<br>
                Don't post anything that could be considered intolerant of a person's race, culture, appearance, gender,
                sexual preference, religion or age.</p>

        </div>
        <hr>
    </div>
    <!-- Form starts here -->

    <?php
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
        echo '<div class="container">
        <h1>Post a Comment</h1>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="form-group">
                <label for="exampleForDesc">Add Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                <input type="hidden" name="sno" value="' . $_SESSION["sno"] . '">
            </div>
            <button type="submit" class="btn btn-success">Post</button>
        </form>
        <hr>
    </div>';
    } else {
        echo '<div class="container">
        <h1>Post a comment</h1>
        <p class="lead">Please login to Post a comments.</p>
        <hr>
        </div>';
    }

    ?>
    <div class="container">
        <h1>Discussions</h1>
        <?php
        $id = $_GET['threadId'];
        $sql = "SELECT * FROM comments  WHERE thread_id = $id";
        $result = mysqli_query($conn, $sql);
        $noResult = true;
        while ($row = mysqli_fetch_assoc($result)) {
            $noResult = false;
            $id = $row['comment_id'];
            $comment = $row['comment_content'];
            $comment_by = $row['comment_by'];
            $sql2 = "SELECT user_email FROM signup WHERE sno = '$comment_by'";
            $result2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($result2);

            echo '<div class="media py-3">
                    <img src="assets/images/boy.png" width="64px" class="mr-3" alt="...">
                    <div class="media-body">
                    <p class="font-weight-bold my-0">' . $row2['user_email'] . '</p>
                    <p>' . $comment . '</p>
                    </div>
                    </div>';
        }

        if ($noResult) {
            echo '<div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">No Comments found</h1>
                <p class="lead">Be the first one to comment.</p>
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