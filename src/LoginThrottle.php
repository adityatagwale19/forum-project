<?php

declare(strict_types=1);

namespace Forum;

/**
 * Counts failed logins per IP address. Without this, a script can try
 * passwords against one email as fast as the server answers.
 */
class LoginThrottle
{
    public const MAX_ATTEMPTS = 5;
    public const WINDOW_MINUTES = 15;

    public function __construct(private readonly Database $db)
    {
    }

    public function blocked(string $ip): bool
    {
        return $this->recentAttempts($ip) >= self::MAX_ATTEMPTS;
    }

    public function attemptsLeft(string $ip): int
    {
        return max(0, self::MAX_ATTEMPTS - $this->recentAttempts($ip));
    }

    public function recordFailure(string $ip): void
    {
        $this->db->run('INSERT INTO login_attempts (ip, attempted_at) VALUES (?, NOW())', [$ip]);
        $this->forget();
    }

    public function clear(string $ip): void
    {
        $this->db->run('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
    }

    /**
     * Reads the address behind a proxy only if the app is told to trust one,
     * because a client can send any X-Forwarded-For value it likes.
     */
    public static function clientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        return is_string($ip) && $ip !== '' ? substr($ip, 0, 45) : 'unknown';
    }

    private function recentAttempts(string $ip): int
    {
        return (int) $this->db->value(
            'SELECT COUNT(*) FROM login_attempts
              WHERE ip = ?
                AND attempted_at > DATE_SUB(NOW(), INTERVAL ' . self::WINDOW_MINUTES . ' MINUTE)',
            [$ip]
        );
    }

    /**
     * Rows outside the window are no longer read, so they are dropped on write
     * instead of needing a cron job.
     */
    private function forget(): void
    {
        $this->db->run(
            'DELETE FROM login_attempts
              WHERE attempted_at < DATE_SUB(NOW(), INTERVAL ' . (self::WINDOW_MINUTES * 4) . ' MINUTE)'
        );
    }
}
