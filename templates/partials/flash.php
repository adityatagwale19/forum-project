<?php

declare(strict_types=1);

use Forum\Flash;

$messages = Flash::take();

if ($messages === []) {
    return;
}

?>
<div class="container mt-3">
    <?php foreach ($messages as $message) : ?>
        <div class="alert <?= $message['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show"
            role="alert">
            <?= e($message['message']) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endforeach; ?>
</div>
