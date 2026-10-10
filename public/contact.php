<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'message' => ''];

$currentUser = $app['auth']->user();

if ($currentUser !== null) {
    $old['name'] = (string) $currentUser['username'];
    $old['email'] = (string) $currentUser['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('contact.php');
    }

    $validator = new Validator($_POST);
    $validator->text('name', 'Name', 2, 80)
        ->email('email')
        ->text('message', 'Message', 10, 2000);

    $old['name'] = $validator->value('name');
    $old['email'] = $validator->value('email');
    $old['message'] = $validator->value('message');

    if ($validator->passes()) {
        $app['messages']->create($old['name'], $old['email'], $old['message']);

        Flash::success('Thanks, your message is saved. I will read it.');
        redirect('contact.php');
    }

    $errors = $validator->errors();
}

$pageTitle = 'Contact us';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">Contact us</h2>

            <p class="text-muted">
                Questions, bug reports or anything else about the forum. Messages are stored for me to read,
                there is no automatic reply.
            </p>

            <form action="contact.php" method="post" novalidate>
                <?= Csrf::field() ?>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                        id="name" name="name" value="<?= e($old['name']) ?>" required>
                    <?php if (isset($errors['name'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                        id="email" name="email" value="<?= e($old['email']) ?>" required>
                    <?php if (isset($errors['email'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control<?= isset($errors['message']) ? ' is-invalid' : '' ?>"
                        id="message" name="message" rows="6" required><?= e($old['message']) ?></textarea>
                    <?php if (isset($errors['message'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['message']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Send message</button>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
