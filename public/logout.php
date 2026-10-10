<?php

declare(strict_types=1);

use Forum\Csrf;
use Forum\Flash;

$app = require __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::checkRequest()) {
    redirect('index.php');
}

$app['auth']->logout();

// the old session is gone, so the goodbye message needs a fresh one to live in
session_start();
Flash::success('You are logged out.');

redirect('index.php');
