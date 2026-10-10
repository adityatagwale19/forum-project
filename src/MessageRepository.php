<?php

declare(strict_types=1);

namespace Forum;

class MessageRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function create(string $name, string $email, string $message): int
    {
        return $this->db->insert(
            'INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)',
            [$name, $email, $message]
        );
    }
}
