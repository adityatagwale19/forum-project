<?php

declare(strict_types=1);

namespace Forum;

/**
 * One token per session, checked on every post. Without it, another site can
 * make the browser send a logged in request on the user's behalf.
 */
class Csrf
{
    public const FIELD = '_token';

    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            self::rotate();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function rotate(): string
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));

        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="' . e(self::token()) . '">';
    }

    /**
     * Takes mixed, because a form can post an array under a field name and
     * hash_equals would then throw.
     */
    public static function check(mixed $sent): bool
    {
        $stored = $_SESSION[self::SESSION_KEY] ?? '';

        if (!is_string($stored) || $stored === '' || !is_string($sent) || $sent === '') {
            return false;
        }

        // compares in constant time, so the result leaks nothing about the token
        return hash_equals($stored, $sent);
    }

    public static function checkRequest(): bool
    {
        return self::check($_POST[self::FIELD] ?? '');
    }
}
