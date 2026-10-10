<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$users = $app['users'];

if ($auth->check()) {
    redirect('index.php');
}

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('signup.php');
    }

    $validator = new Validator($_POST);
    $validator->username('username')
        ->email('email')
        ->password('password')
        ->sameAs('password_confirm', 'password', 'The two passwords do not match.');

    $old['username'] = $validator->value('username');
    $old['email'] = $validator->value('email');

    if ($validator->passes()) {
        if ($users->usernameTaken($old['username'])) {
            $validator->fail('username', 'That username is taken.');
        }

        if ($users->emailTaken($old['email'])) {
            $validator->fail('email', 'That email already has an account.');
        }
    }

    if ($validator->passes()) {
        $userId = $users->create($old['username'], $old['email'], (string) $_POST['password']);

        $auth->start($userId);
        Flash::success('Welcome, ' . $old['username'] . '. Your account is ready.');
        redirect('index.php');
    }

    $errors = $validator->errors();
}

$pageTitle = 'Sign up';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-3">Sign up</h2>

            <form action="signup.php" method="post" novalidate>
                <?= Csrf::field() ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>"
                        id="username" name="username" value="<?= e($old['username']) ?>" required>
                    <small class="form-text text-muted">3 to 30 letters, numbers or underscores.</small>
                    <?php if (isset($errors['username'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['username']) ?></div>
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
                    <label for="password">Password</label>
                    <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                        id="password" name="password" required>
                    <small class="form-text text-muted">At least 8 characters.</small>
                    <?php if (isset($errors['password'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Repeat password</label>
                    <input type="password"
                        class="form-control<?= isset($errors['password_confirm']) ? ' is-invalid' : '' ?>"
                        id="password_confirm" name="password_confirm" required>
                    <?php if (isset($errors['password_confirm'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['password_confirm']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Create account</button>
                <a href="login.php" class="btn btn-link">I already have an account</a>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
