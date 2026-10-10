<?php

declare(strict_types=1);

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$auth->requireLogin();

$user = $auth->user();
$activity = $app['users']->activity((int) $user['id']);
$latest = $app['threads']->latestForUser((int) $user['id'], 5);

$pageTitle = 'Profile';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">My profile</h2>

            <div class="card mb-4">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Username</dt>
                        <dd class="col-sm-9"><?= e($user['username']) ?></dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?= e($user['email']) ?></dd>

                        <dt class="col-sm-3">Joined</dt>
                        <dd class="col-sm-9"><?= e(date('j M Y', strtotime((string) $user['created_at']))) ?></dd>

                        <dt class="col-sm-3">Threads</dt>
                        <dd class="col-sm-9"><?= $activity['threads'] ?></dd>

                        <dt class="col-sm-3">Comments</dt>
                        <dd class="col-sm-9"><?= $activity['comments'] ?></dd>
                    </dl>
                </div>
            </div>

            <h4 class="mb-3">My latest threads</h4>

            <?php if ($latest === []) : ?>
                <p class="text-muted">You have not posted a thread yet.</p>
            <?php else : ?>
                <div class="list-group">
                    <?php foreach ($latest as $thread) : ?>
                        <a class="list-group-item list-group-item-action"
                            href="thread.php?id=<?= (int) $thread['id'] ?>">
                            <?= e($thread['title']) ?>
                            <small class="text-muted d-block">
                                in <?= e($thread['category_name']) ?>
                                on <?= e(date('j M Y', strtotime((string) $thread['created_at']))) ?>
                            </small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
