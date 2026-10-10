<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;
use Forum\LoginThrottle;
use Forum\Validator;

$app = require __DIR__ . '/../config/bootstrap.php';

$auth = $app['auth'];
$throttle = $app['throttle'];
$ip = LoginThrottle::clientIp();

if ($auth->check()) {
    redirect('index.php');
}

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::checkRequest()) {
        Flash::error('That form expired. Please try again.');
        redirect('login.php');
    }

    if ($throttle->blocked($ip)) {
        Flash::error(sprintf(
            'Too many failed logins. Wait %d minutes and try again.',
            LoginThrottle::WINDOW_MINUTES
        ));
        redirect('login.php');
    }

    $validator = new Validator($_POST);
    $validator->email('email')->password('password', 1);
    $old['email'] = $validator->value('email');

    if ($validator->passes()) {
        if ($auth->attempt($old['email'], (string) $_POST['password'])) {
            $throttle->clear($ip);
            Flash::success('You are logged in.');
            redirect('index.php');
        }

        $throttle->recordFailure($ip);

        // one message for both cases, so the form does not say whether the
        // email exists
        $left = $throttle->attemptsLeft($ip);
        Flash::error(sprintf('Wrong email or password. %d attempt%s left.', $left, $left === 1 ? '' : 's'));
        redirect('login.php');
    }

    $errors = $validator->errors();
}

$pageTitle = 'Log in';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h2 class="mb-3">Log in</h2>

            <form action="login.php" method="post" novalidate>
                <?= Csrf::field() ?>

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
                    <?php if (isset($errors['password'])) : ?>
                        <div class="invalid-feedback"><?= e($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Log in</button>
                <a href="signup.php" class="btn btn-link">Create an account</a>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
