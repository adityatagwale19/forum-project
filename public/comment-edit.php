<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$auth->requireLogin();

$comments = $app['comments'];
$commentId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
}

$comment = $commentId > 0 ? $comments->find($commentId) : null;

if ($comment === null) {
    Flash::error('That comment does not exist.');
    redirect('index.php');
}

$threadId = (int) $comment['thread_id'];

if (!$auth->owns((int) $comment['user_id'])) {
    Flash::error('You can only edit your own comments.');
    redirect('thread.php?id=' . $threadId);
}

$errors = [];
$old = ['body' => (string) $comment['body']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('comment-edit.php?id=' . $commentId);
    }

    $validator = new Validator($_POST);
    $validator->text('body', 'Comment', 2, 5000);
    $old['body'] = $validator->value('body');

    if ($validator->passes()) {
        $comments->update($commentId, $old['body']);

        Flash::success('Comment updated.');
        redirect('thread.php?id=' . $threadId . '#comments');
    }

    $errors = $validator->errors();
}

$pageTitle = 'Edit comment';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">Edit comment</h2>

            <form action="comment-edit.php" method="post" novalidate>
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= $commentId ?>">

                <div class="form-group">
                    <label for="body">Comment</label>
                    <textarea class="form-control<?= isset($errors['body']) ? ' is-invalid' : '' ?>" id="body"
                        name="body" rows="6" required><?= e($old['body']) ?></textarea>
                    <?php if (isset($errors['body'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['body']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="thread.php?id=<?= $threadId ?>#comments" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
