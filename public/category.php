<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Paginator;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$categoryId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
$category = $categoryId > 0 ? $app['categories']->find($categoryId) : null;

if ($category === null) {
    http_response_code(404);
    $pageTitle = 'Category not found';
    require __DIR__ . '/../templates/layout/header.php';
    echo '<div class="container my-5"><h2>That category does not exist.</h2>'
        . '<p><a href="index.php">Back to all categories</a></p></div>';
    require __DIR__ . '/../templates/layout/footer.php';
    exit;
}

$errors = [];
$old = ['title' => '', 'body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->requireLogin();

    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('category.php?id=' . $categoryId);
    }

    $validator = new Validator($_POST);
    $validator->text('title', 'Title', 5, 190)->text('body', 'Message', 10, 5000);

    $old['title'] = $validator->value('title');
    $old['body'] = $validator->value('body');

    if ($validator->passes()) {
        $threadId = $app['threads']->create(
            $categoryId,
            (int) $auth->id(),
            $old['title'],
            $old['body']
        );

        Flash::success('Thread posted.');
        redirect('thread.php?id=' . $threadId);
    }

    $errors = $validator->errors();
}

$perPage = (int) $app['config']['app']['per_page'];
$pagination = new Paginator(
    $app['threads']->countForCategory($categoryId),
    $perPage,
    $_GET['page'] ?? 1
);

$threads = $app['threads']->forCategory($categoryId, $pagination->perPage, $pagination->offset());

$pageLink = static fn (int $page): string => 'category.php?id=' . $categoryId . '&page=' . $page;

$pageTitle = $category['name'];

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <h2><?= e($category['name']) ?></h2>
    <p class="text-muted"><?= e($category['description']) ?></p>

    <?php if ($auth->check()) : ?>
        <div class="card my-4">
            <div class="card-body">
                <h5 class="card-title">Start a thread</h5>

                <form action="category.php?id=<?= $categoryId ?>" method="post" novalidate>
                    <?= Csrf::field() ?>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>"
                            id="title" name="title" value="<?= e($old['title']) ?>" required>
                        <?php if (isset($errors['title'])) : ?>
                            <div class="invalid-feedback"><?= e($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="body">Message</label>
                        <textarea class="form-control<?= isset($errors['body']) ? ' is-invalid' : '' ?>" id="body"
                            name="body" rows="4" required><?= e($old['body']) ?></textarea>
                        <?php if (isset($errors['body'])) : ?>
                            <div class="invalid-feedback"><?= e($errors['body']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Post thread</button>
                </form>
            </div>
        </div>
    <?php else : ?>
        <p><a href="login.php">Log in</a> to start a thread here.</p>
    <?php endif; ?>

    <hr>

    <?php if ($threads === []) : ?>
        <p class="text-muted">No threads in this category yet.</p>
    <?php else : ?>
        <div class="list-group mb-4">
            <?php foreach ($threads as $thread) : ?>
                <div class="list-group-item thread-row">
                    <h5 class="mb-1">
                        <a href="thread.php?id=<?= (int) $thread['id'] ?>"><?= e($thread['title']) ?></a>
                    </h5>
                    <p class="mb-1 text-muted"><?= e(mb_strimwidth((string) $thread['body'], 0, 160, '...')) ?></p>
                    <small class="text-muted">
                        by <?= e($thread['username']) ?>
                        on <?= e(date('j M Y, H:i', strtotime((string) $thread['created_at']))) ?>
                        - <?= (int) $thread['comment_count'] ?>
                        comment<?= (int) $thread['comment_count'] === 1 ? '' : 's' ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>

        <?php require __DIR__ . '/../templates/partials/pagination.php'; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
