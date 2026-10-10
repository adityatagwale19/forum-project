<?php

declare(strict_types=1);

// Forum\Thing maps to src/Thing.php. Hand written so the app runs after a plain
// clone, without composer install. Composer is only needed for the tests.
spl_autoload_register(static function (string $class): void {
    $prefix = 'Forum\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
