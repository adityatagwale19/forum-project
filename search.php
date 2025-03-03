<?php include 'partitions/db_connect.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>Search page</title>
    <style>
    #mainContainer {
        min-height: 85vh;
    }
    </style>
</head>

<body>

    <?php include 'partitions/db_connect.php'; ?>
    <!-- header section starts -->
    <?php include "partitions/header.php"; ?>

    <div class="container my-2" id="mainContainer">
        <h1>Result shown for <em>"<?php echo $_GET['search']; ?>"</em></h1>
        <?php
        $noResult = true;
        $query = $_GET['search'];
        // Sql query for fetch the data from databse and match the reesult
        $sql = "SELECT * FROM threads WHERE MATCH(thread_title, thread_desc) AGAINST('$query')";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $th_title = $row['thread_title'];
            $th_desc = $row['thread_desc'];
            $thread_id = $row['thread_id'];
            $noResult = false;
            echo '<div class="result py-2">
            <h3><a href="/php/Forum_project/thread.php?threadId=' . $thread_id . '" class="text-success">' . $th_title . '</a></h3>
            <p>' . $th_desc . '</p>
        </div>';
        }
        if ($noResult) {
            echo '<div class="jumbotron jumbotron-fluid">
                    <div class="container">
                    <h1 class="display-4">No results found</h1>
                    <p class="lead">Suggestions: 
                        <ul>
                            <li>Make sure that all words are spelled correctly.</li>
                            <li>Try different keywords.</li>
                            <li>Try more general keywords.</li>
                        </ul>
                    </p>
                    </div>
                </div>';
        }
        ?>
    </div>

    <!-- footer section starts -->
    <div class="container-fluid bg-dark text-light pb-2">
        <div class="text-center">Copyright iCoder online coding forum 2024 || All right reserved.</div>
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