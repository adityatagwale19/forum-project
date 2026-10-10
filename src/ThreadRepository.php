<?php

declare(strict_types=1);

namespace Forum;

class ThreadRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * Turns a user's search words into a LIKE pattern. The three characters
     * LIKE treats as special are escaped first, so a search for "100%" looks
     * for the text and not for "anything starting with 100".
     */
    public static function likePattern(string $term): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($term));

        return '%' . $escaped . '%';
    }

    public function find(int $id): ?array
    {
        return $this->db->one(
            'SELECT t.id, t.category_id, t.user_id, t.title, t.body, t.created_at, t.updated_at,
                    u.username, c.name AS category_name
               FROM threads t
               JOIN users u ON u.id = t.user_id
               JOIN categories c ON c.id = t.category_id
              WHERE t.id = ?',
            [$id]
        );
    }

    public function countForCategory(int $categoryId): int
    {
        return (int) $this->db->value(
            'SELECT COUNT(*) FROM threads WHERE category_id = ?',
            [$categoryId]
        );
    }

    public function forCategory(int $categoryId, int $limit, int $offset): array
    {
        return $this->db->all(
            'SELECT t.id, t.title, t.body, t.created_at, u.username,
                    (SELECT COUNT(*) FROM comments cm WHERE cm.thread_id = t.id) AS comment_count
               FROM threads t
               JOIN users u ON u.id = t.user_id
              WHERE t.category_id = ?
              ORDER BY t.created_at DESC, t.id DESC
              LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            [$categoryId]
        );
    }

    public function latest(int $limit): array
    {
        return $this->db->all(
            'SELECT t.id, t.title, t.created_at, u.username, c.name AS category_name
               FROM threads t
               JOIN users u ON u.id = t.user_id
               JOIN categories c ON c.id = t.category_id
              ORDER BY t.created_at DESC, t.id DESC
              LIMIT ' . (int) $limit
        );
    }

    public function latestForUser(int $userId, int $limit): array
    {
        return $this->db->all(
            'SELECT t.id, t.title, t.created_at, c.name AS category_name
               FROM threads t
               JOIN categories c ON c.id = t.category_id
              WHERE t.user_id = ?
              ORDER BY t.created_at DESC, t.id DESC
              LIMIT ' . (int) $limit,
            [$userId]
        );
    }

    public function countSearch(string $term): int
    {
        $pattern = self::likePattern($term);

        return (int) $this->db->value(
            'SELECT COUNT(*) FROM threads WHERE title LIKE ? OR body LIKE ?',
            [$pattern, $pattern]
        );
    }

    public function search(string $term, int $limit, int $offset): array
    {
        $pattern = self::likePattern($term);

        return $this->db->all(
            'SELECT t.id, t.title, t.body, t.created_at, u.username, c.name AS category_name
               FROM threads t
               JOIN users u ON u.id = t.user_id
               JOIN categories c ON c.id = t.category_id
              WHERE t.title LIKE ? OR t.body LIKE ?
              ORDER BY t.created_at DESC, t.id DESC
              LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            [$pattern, $pattern]
        );
    }

    public function create(int $categoryId, int $userId, string $title, string $body): int
    {
        return $this->db->insert(
            'INSERT INTO threads (category_id, user_id, title, body) VALUES (?, ?, ?, ?)',
            [$categoryId, $userId, $title, $body]
        );
    }

    public function update(int $id, string $title, string $body): void
    {
        $this->db->run(
            'UPDATE threads SET title = ?, body = ?, updated_at = NOW() WHERE id = ?',
            [$title, $body, $id]
        );
    }

    /**
     * Comments go with it, through the foreign key.
     */
    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM threads WHERE id = ?', [$id]);
    }
}
