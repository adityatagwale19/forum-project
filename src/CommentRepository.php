<?php

declare(strict_types=1);

namespace Forum;

class CommentRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function find(int $id): ?array
    {
        return $this->db->one(
            'SELECT id, thread_id, user_id, body, created_at, updated_at FROM comments WHERE id = ?',
            [$id]
        );
    }

    public function countForThread(int $threadId): int
    {
        return (int) $this->db->value(
            'SELECT COUNT(*) FROM comments WHERE thread_id = ?',
            [$threadId]
        );
    }

    public function forThread(int $threadId, int $limit, int $offset): array
    {
        return $this->db->all(
            'SELECT cm.id, cm.user_id, cm.body, cm.created_at, cm.updated_at, u.username
               FROM comments cm
               JOIN users u ON u.id = cm.user_id
              WHERE cm.thread_id = ?
              ORDER BY cm.created_at ASC, cm.id ASC
              LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            [$threadId]
        );
    }

    public function create(int $threadId, int $userId, string $body): int
    {
        return $this->db->insert(
            'INSERT INTO comments (thread_id, user_id, body) VALUES (?, ?, ?)',
            [$threadId, $userId, $body]
        );
    }

    public function update(int $id, string $body): void
    {
        $this->db->run(
            'UPDATE comments SET body = ?, updated_at = NOW() WHERE id = ?',
            [$body, $id]
        );
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM comments WHERE id = ?', [$id]);
    }
}
