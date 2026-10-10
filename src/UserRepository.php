<?php

declare(strict_types=1);

namespace Forum;

class UserRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function find(int $id): ?array
    {
        return $this->db->one(
            'SELECT id, username, email, created_at FROM users WHERE id = ?',
            [$id]
        );
    }

    /**
     * Returns the password hash too, so this one is only for the login check.
     */
    public function findForLogin(string $email): ?array
    {
        return $this->db->one(
            'SELECT id, username, email, password_hash FROM users WHERE email = ?',
            [$email]
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->one(
            'SELECT id, username, email, created_at FROM users WHERE username = ?',
            [$username]
        );
    }

    public function emailTaken(string $email): bool
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM users WHERE email = ?', [$email]) > 0;
    }

    public function usernameTaken(string $username): bool
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM users WHERE username = ?', [$username]) > 0;
    }

    public function create(string $username, string $email, string $password): int
    {
        return $this->db->insert(
            'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)',
            [$username, $email, password_hash($password, PASSWORD_DEFAULT)]
        );
    }

    /**
     * @return array{threads: int, comments: int}
     */
    public function activity(int $userId): array
    {
        return [
            'threads' => (int) $this->db->value('SELECT COUNT(*) FROM threads WHERE user_id = ?', [$userId]),
            'comments' => (int) $this->db->value('SELECT COUNT(*) FROM comments WHERE user_id = ?', [$userId]),
        ];
    }
}
