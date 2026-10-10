<?php

declare(strict_types=1);

/**
 * Escape a value for HTML. Every echo in a template goes through this.
 */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Send a redirect and stop. Paths are relative to public/, so the app works
 * whatever folder it is served from.
 */
function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}
