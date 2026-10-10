<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$auth->requireLogin();

// delete changes data, so a link cannot do it
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::checkRequest()) {
    Flash::error('That request was not valid.');
    redirect('index.php');
}

$threadId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : 0;
$thread = $threadId > 0 ? $app['threads']->find($threadId) : null;

if ($thread === null) {
    Flash::error('That thread does not exist.');
    redirect('index.php');
}

if (!$auth->owns((int) $thread['user_id'])) {
    Flash::error('You can only delete your own threads.');
    redirect('thread.php?id=' . $threadId);
}

$app['threads']->delete($threadId);

Flash::success('Thread deleted.');
redirect('category.php?id=' . (int) $thread['category_id']);
