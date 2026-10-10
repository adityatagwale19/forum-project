  <!-- header section starts -->
  <?php include 'partitions/db_connect.php'; ?>
  <?php
    session_start();
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">iCoder</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                        Top Categories
                    </a>
                    <div class="dropdown-menu">';
    // Sql query for display only limited categories
    $sql = "SELECT category_name, category_id FROM `categories` LIMIT 5";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<a class="dropdown-item" href="threadList.php?Id=' . $row['category_id'] . '">' . $row['category_name'] . '</a>';
    }
    echo '</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact us</a>
                </li>
            </ul>
            <div class="row mx-2">';
    //Here if person login so display name and logout option 
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
        echo '<form class="form-inline my-2 my-lg-0" action="search.php" method="get">
                    <input class="form-control mr-sm-2" name ="search" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
                    <p class="text-light my-0 mx-2">Welcome, ' . $_SESSION['signEmail'] . '</p>
                    <a href="logout.php" class="btn btn-outline-success mx-2">Logout</a>
                </form>';
    } else {
        echo '<form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                <button class="btn btn-outline-success ml-2" data-toggle="modal" data-target="#loginModal">Log in</button>
                <button class="btn btn-outline-success mx-2" data-toggle="modal" data-target="#signupModal">Sign up</button>';
    }
    echo '</div>

        </div>
    </nav>';
    ?>
  <?php include 'login.php'; ?>
  <?php include 'signup.php'; ?>