<?php

declare(strict_types=1);

/**
 * Reads key=value lines out of .env and returns the settings the app needs.
 * The defaults keep a fresh clone working before anyone writes a .env.
 */

$env = [];
$envFile = dirname(__DIR__) . '/.env';

if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim(trim($value), "\"'");
    }
}

$read = static fn (string $key, string $default = ''): string => $env[$key] ?? $default;

return [
    'app' => [
        'name' => $read('APP_NAME', 'iDiscuss'),
        'debug' => $read('APP_DEBUG', 'false') === 'true',
        'per_page' => max(1, (int) $read('APP_PER_PAGE', '10')),
    ],
    'db' => [
        'host' => $read('DB_HOST', '127.0.0.1'),
        'port' => (int) $read('DB_PORT', '3306'),
        'name' => $read('DB_NAME', 'forum_website'),
        'user' => $read('DB_USER', 'root'),
        'pass' => $read('DB_PASS', ''),
    ],
];
