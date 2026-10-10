<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$auth->requireLogin();

$threads = $app['threads'];
$threadId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
}

$thread = $threadId > 0 ? $threads->find($threadId) : null;

if ($thread === null) {
    Flash::error('That thread does not exist.');
    redirect('index.php');
}

// the owner check runs on the row from the database, not on anything the form sent
if (!$auth->owns((int) $thread['user_id'])) {
    Flash::error('You can only edit your own threads.');
    redirect('thread.php?id=' . $threadId);
}

$errors = [];
$old = ['title' => (string) $thread['title'], 'body' => (string) $thread['body']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('thread-edit.php?id=' . $threadId);
    }

    $validator = new Validator($_POST);
    $validator->text('title', 'Title', 5, 190)->text('body', 'Message', 10, 5000);

    $old['title'] = $validator->value('title');
    $old['body'] = $validator->value('body');

    if ($validator->passes()) {
        $threads->update($threadId, $old['title'], $old['body']);

        Flash::success('Thread updated.');
        redirect('thread.php?id=' . $threadId);
    }

    $errors = $validator->errors();
}

$pageTitle = 'Edit thread';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">Edit thread</h2>

            <form action="thread-edit.php" method="post" novalidate>
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= $threadId ?>">

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
                        name="body" rows="8" required><?= e($old['body']) ?></textarea>
                    <?php if (isset($errors['body'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['body']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="thread.php?id=<?= $threadId ?>" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
