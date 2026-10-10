<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Paginator;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$comments = $app['comments'];

$threadId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;
$thread = $threadId > 0 ? $app['threads']->find($threadId) : null;

if ($thread === null) {
    http_response_code(404);
    $pageTitle = 'Thread not found';
    require __DIR__ . '/../templates/layout/header.php';
    echo '<div class="container my-5"><h2>That thread does not exist.</h2>'
        . '<p><a href="index.php">Back to all categories</a></p></div>';
    require __DIR__ . '/../templates/layout/footer.php';
    exit;
}

$perPage = (int) $app['config']['app']['per_page'];
$errors = [];
$old = ['body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->requireLogin();

    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('thread.php?id=' . $threadId);
    }

    $validator = new Validator($_POST);
    $validator->text('body', 'Comment', 2, 5000);
    $old['body'] = $validator->value('body');

    if ($validator->passes()) {
        $comments->create($threadId, (int) $auth->id(), $old['body']);

        // comments run oldest first, so the new one sits on the last page
        $lastPage = (int) ceil(($comments->countForThread($threadId)) / max(1, $perPage));

        Flash::success('Comment added.');
        redirect('thread.php?id=' . $threadId . '&page=' . max(1, $lastPage) . '#comments');
    }

    $errors = $validator->errors();
}

$pagination = new Paginator($comments->countForThread($threadId), $perPage, $_GET['page'] ?? 1);
$threadComments = $comments->forThread($threadId, $pagination->perPage, $pagination->offset());

$pageLink = static fn (int $page): string => 'thread.php?id=' . $threadId . '&page=' . $page . '#comments';

$pageTitle = $thread['title'];

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">
                <a href="category.php?id=<?= (int) $thread['category_id'] ?>"><?= e($thread['category_name']) ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($thread['title']) ?></li>
        </ol>
    </nav>

    <article class="card mb-4">
        <div class="card-body">
            <h2 class="card-title h4"><?= e($thread['title']) ?></h2>
            <p class="text-muted small">
                by <?= e($thread['username']) ?>
                on <?= e(date('j M Y, H:i', strtotime((string) $thread['created_at']))) ?>
                <?php if ($thread['updated_at'] !== null) : ?>
                    (edited <?= e(date('j M Y, H:i', strtotime((string) $thread['updated_at']))) ?>)
                <?php endif; ?>
            </p>
            <div class="post-body"><?= e($thread['body']) ?></div>

            <?php if ($auth->owns((int) $thread['user_id'])) : ?>
                <div class="mt-3">
                    <a href="thread-edit.php?id=<?= (int) $thread['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>

                    <form action="thread-delete.php" method="post" class="d-inline"
                        onsubmit="return confirm('Delete this thread and its comments?');">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= (int) $thread['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </article>

    <h3 class="h5" id="comments">
        <?= $pagination->total ?> comment<?= $pagination->total === 1 ? '' : 's' ?>
    </h3>

    <?php if ($threadComments === []) : ?>
        <p class="text-muted">No comments yet.</p>
    <?php else : ?>
        <div class="list-group mb-4">
            <?php foreach ($threadComments as $comment) : ?>
                <div class="list-group-item">
                    <p class="text-muted small mb-2">
                        <?= e($comment['username']) ?>
                        on <?= e(date('j M Y, H:i', strtotime((string) $comment['created_at']))) ?>
                        <?php if ($comment['updated_at'] !== null) : ?>
                            (edited)
                        <?php endif; ?>
                    </p>
                    <div class="post-body"><?= e($comment['body']) ?></div>

                    <?php if ($auth->owns((int) $comment['user_id'])) : ?>
                        <div class="mt-2">
                            <a href="comment-edit.php?id=<?= (int) $comment['id'] ?>"
                                class="btn btn-sm btn-outline-primary">Edit</a>

                            <form action="comment-delete.php" method="post" class="d-inline"
                                onsubmit="return confirm('Delete this comment?');">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php require __DIR__ . '/../templates/partials/pagination.php'; ?>
    <?php endif; ?>

    <?php if ($auth->check()) : ?>
        <div class="card my-4">
            <div class="card-body">
                <h5 class="card-title">Add a comment</h5>

                <form action="thread.php?id=<?= $threadId ?>" method="post" novalidate>
                    <?= Csrf::field() ?>

                    <div class="form-group">
                        <label for="body">Comment</label>
                        <textarea class="form-control<?= isset($errors['body']) ? ' is-invalid' : '' ?>" id="body"
                            name="body" rows="3" required><?= e($old['body']) ?></textarea>
                        <?php if (isset($errors['body'])) : ?>
                            <div class="invalid-feedback"><?= e($errors['body']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Post comment</button>
                </form>
            </div>
        </div>
    <?php else : ?>
        <p><a href="login.php">Log in</a> to comment.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
