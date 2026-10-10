<?php

declare(strict_types=1);

$app = require __DIR__ . '/../config/bootstrap.php';

$categories = $app['categories']->allWithThreadCounts();
$latestThreads = $app['threads']->latest(5);

$pageTitle = 'Home';

require __DIR__ . '/../templates/layout/header.php';

?>
<div id="homeCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#homeCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#homeCarousel" data-slide-to="1"></li>
        <li data-target="#homeCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/banner-1.jpg" class="d-block w-100" alt="Developers talking about code">
        </div>
        <div class="carousel-item">
            <img src="assets/images/banner-2.jpg" class="d-block w-100" alt="A laptop with an editor open">
        </div>
        <div class="carousel-item">
            <img src="assets/images/banner-3.jpg" class="d-block w-100" alt="Notes next to a keyboard">
        </div>
    </div>
    <a class="carousel-control-prev" href="#homeCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#homeCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<div class="container">
    <h2 class="text-center my-4"><?= e($appName) ?> - Categories</h2>

    <?php if ($categories === []) : ?>
        <p class="text-center text-muted">No categories yet. Load database/seed.sql to get some demo content.</p>
    <?php else : ?>
        <div class="row">
            <?php foreach ($categories as $category) : ?>
                <div class="col-md-4 my-3">
                    <div class="card category-card h-100">
                        <?php // basename keeps a bad value in the column from pointing somewhere else ?>
                        <img class="card-img-top" src="assets/images/<?= e(basename((string) $category['image'])) ?>"
                            alt="<?= e($category['name']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="category.php?id=<?= (int) $category['id'] ?>"><?= e($category['name']) ?></a>
                            </h5>
                            <p class="card-text">
                                <?= e(mb_strimwidth((string) $category['description'], 0, 120, '...')) ?>
                            </p>
                            <p class="text-muted small mb-3">
                                <?= (int) $category['thread_count'] ?>
                                thread<?= (int) $category['thread_count'] === 1 ? '' : 's' ?>
                            </p>
                            <a href="category.php?id=<?= (int) $category['id'] ?>"
                                class="btn btn-primary mt-auto align-self-start">View threads</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($latestThreads !== []) : ?>
        <h2 class="text-center my-4">Latest threads</h2>
        <div class="list-group mb-5">
            <?php foreach ($latestThreads as $thread) : ?>
                <a class="list-group-item list-group-item-action" href="thread.php?id=<?= (int) $thread['id'] ?>">
                    <div class="d-flex justify-content-between">
                        <strong><?= e($thread['title']) ?></strong>
                        <small class="text-muted"><?= e($thread['category_name']) ?></small>
                    </div>
                    <small class="text-muted">
                        by <?= e($thread['username']) ?> on <?= e(date('j M Y', strtotime((string) $thread['created_at']))) ?>
                    </small>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
