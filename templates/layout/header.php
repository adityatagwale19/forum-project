<?php

declare(strict_types=1);

use Forum\Csrf;

/** @var array $app */

require __DIR__ . '/head.php';

$auth = $app['auth'];
$currentUser = $auth->user();
$menu = $app['categories']->menu();
$searchTerm = isset($_GET['search']) && is_string($_GET['search']) ? $_GET['search'] : '';

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php"><?= e($appName) ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav"
        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                    aria-expanded="false">Top Categories</a>
                <div class="dropdown-menu">
                    <?php foreach ($menu as $item) : ?>
                        <a class="dropdown-item" href="category.php?id=<?= (int) $item['id'] ?>"><?= e($item['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact us</a>
            </li>
        </ul>

        <form class="form-inline my-2 my-lg-0" action="search.php" method="get" role="search">
            <label class="sr-only" for="navbar-search">Search threads</label>
            <input class="form-control mr-sm-2" id="navbar-search" name="search" type="search"
                value="<?= e($searchTerm) ?>" placeholder="Search">
            <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
        </form>

        <?php if ($currentUser !== null) : ?>
            <a href="profile.php" class="btn btn-link text-light ml-2">
                <?= e($currentUser['username']) ?>
            </a>
            <?php // logout posts a token, so another site cannot log the user out through an image tag ?>
            <form action="logout.php" method="post" class="form-inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-outline-success mx-2">Logout</button>
            </form>
        <?php else : ?>
            <a href="login.php" class="btn btn-outline-success ml-2">Log in</a>
            <a href="signup.php" class="btn btn-outline-success mx-2">Sign up</a>
        <?php endif; ?>
    </div>
</nav>

<?php require __DIR__ . '/../partials/flash.php'; ?>

<main class="flex-fill">
