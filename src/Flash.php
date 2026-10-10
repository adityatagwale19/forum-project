<?php

declare(strict_types=1);

namespace Forum;

/**
 * Messages that survive one redirect. A post handler saves the message and
 * redirects, the next page shows it once and the session is cleared.
 */
class Flash
{
    private const SESSION_KEY = 'flash';

    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    /**
     * @return list<array{type: string, message: string}>
     */
    public static function take(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);

        return is_array($messages) ? $messages : [];
    }

    private static function add(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY][] = ['type' => $type, 'message' => $message];
    }
}
