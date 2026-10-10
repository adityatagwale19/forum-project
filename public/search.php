<?php

declare(strict_types=1);

use Forum\Paginator;

$app = require __DIR__ . '/../config/bootstrap.php';

$term = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
$perPage = (int) $app['config']['app']['per_page'];

$results = [];
$pagination = null;

if ($term !== '') {
    $pagination = new Paginator($app['threads']->countSearch($term), $perPage, $_GET['page'] ?? 1);
    $results = $app['threads']->search($term, $pagination->perPage, $pagination->offset());

    $pageLink = static fn (int $page): string =>
        'search.php?search=' . rawurlencode($term) . '&page=' . $page;
}

$pageTitle = $term === '' ? 'Search' : 'Search: ' . $term;

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <h2 class="mb-3">Search</h2>

    <form action="search.php" method="get" class="form-inline mb-4" role="search">
        <label class="sr-only" for="search">Search threads</label>
        <input type="search" class="form-control mr-2" id="search" name="search" value="<?= e($term) ?>"
            placeholder="Words in a title or a message">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if ($term === '') : ?>
        <p class="text-muted">Type something to search the threads.</p>
    <?php elseif ($results === []) : ?>
        <p class="text-muted">Nothing found for "<?= e($term) ?>".</p>
    <?php else : ?>
        <p class="text-muted">
            <?= $pagination->total ?> result<?= $pagination->total === 1 ? '' : 's' ?> for "<?= e($term) ?>"
        </p>

        <div class="list-group mb-4">
            <?php foreach ($results as $thread) : ?>
                <div class="list-group-item">
                    <h5 class="mb-1">
                        <a href="thread.php?id=<?= (int) $thread['id'] ?>"><?= e($thread['title']) ?></a>
                    </h5>
                    <p class="mb-1 text-muted"><?= e(mb_strimwidth((string) $thread['body'], 0, 160, '...')) ?></p>
                    <small class="text-muted">
                        in <?= e($thread['category_name']) ?>
                        by <?= e($thread['username']) ?>
                        on <?= e(date('j M Y', strtotime((string) $thread['created_at']))) ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php require __DIR__ . '/../templates/partials/pagination.php'; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
