<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::checkRequest()) {
    Flash::error('That request was not valid.');
    redirect('index.php');
}

$commentId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
$comment = $commentId > 0 ? $app['comments']->find($commentId) : null;

if ($comment === null) {
    Flash::error('That comment does not exist.');
    redirect('index.php');
}

$threadId = (int) $comment['thread_id'];

if (!$auth->owns((int) $comment['user_id'])) {
    Flash::error('You can only delete your own comments.');
    redirect('thread.php?id=' . $threadId);
}

$app['comments']->delete($commentId);

Flash::success('Comment deleted.');
redirect('thread.php?id=' . $threadId . '#comments');
