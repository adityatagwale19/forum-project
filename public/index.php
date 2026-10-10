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

    <title>iForum - project</title>
</head>

<body>

    <!-- header section starts -->
    <?php include "partitions/header.php"; ?>

    <!-- Show alert message if person is signup -->
    <?php
    if (isset($_GET['signupsuccess']) && $_GET['signupsuccess'] == 'true') {
        echo '<div class="alert alert-success alert-dismissible fade show my-0" role="alert">
                <strong>Signup Succesful!</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>';
    }
    ?>
    <!-- slider starts here -->

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/banner-1.jpg" height="600" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner-2.jpg" height="600" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner-3.jpg" height="600" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-target="#carouselExampleIndicators" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-target="#carouselExampleIndicators" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </button>
    </div>
    <!-- Slider ends here -->

    <div class="container">
        <h2 class="text-center my-2">iDiscuss - Categories</h2>
        <div class="row">
            <!-- Fetch the all categories and use a loop for iterate through categories-->
            <?php
            $sql = 'SELECT * FROM `categories`';
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
                $catId = $row['category_id'];
                $catName = $row['category_name'];
                $catDiscription = $row['category_discription'];
                echo '
            <div class="col-md-4 my-3">
                <div class="card" style="width: 18rem;">
                    <img src="assets/images/banner-1.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><a href = "threadList.php?Id=' . $catId . '">' . $catName . '</a></h5>
                        <p class="card-text">' . substr($catDiscription, 0, 100) . '...</p>
                        <a href="threadList.php?Id=' . $catId . '" class="btn btn-primary">View Threds</a>
                    </div>
                </div>
            </div>';
            }
            ?>
        </div>
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