<?php

declare(strict_types=1);

/**
 * Every page starts with this file. It loads the classes, starts the session
 * and returns the objects a page needs.
 */

use Forum\Auth;
use Forum\CategoryRepository;
use Forum\CommentRepository;
use Forum\Database;
use Forum\LoginThrottle;
use Forum\MessageRepository;
use Forum\ThreadRepository;
use Forum\UserRepository;

$root = dirname(__DIR__);

require $root . '/src/autoload.php';
require $root . '/src/helpers.php';

$config = require __DIR__ . '/config.php';

error_reporting(E_ALL);
ini_set('display_errors', $config['app']['debug'] ? '1' : '0');

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => '/',
        // javascript cannot read the cookie, and the browser does not send it
        // with a request started by another site
        'httponly' => true,
        'samesite' => 'Lax',
        // some servers send HTTPS as an empty string on plain http
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

$db = new Database($config['db']);
$users = new UserRepository($db);

return [
    'config' => $config,
    'db' => $db,
    'users' => $users,
    'auth' => new Auth($users),
    'categories' => new CategoryRepository($db),
    'threads' => new ThreadRepository($db),
    'comments' => new CommentRepository($db),
    'messages' => new MessageRepository($db),
    'throttle' => new LoginThrottle($db),
];
