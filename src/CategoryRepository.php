<?php

declare(strict_types=1);

namespace Forum;

class CategoryRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * Categories plus how many threads each one holds, in a single query.
     * The home page used to run one query per card.
     */
    public function allWithThreadCounts(): array
    {
        return $this->db->all(
            'SELECT c.id, c.name, c.description, c.image, COUNT(t.id) AS thread_count
               FROM categories c
               LEFT JOIN threads t ON t.category_id = c.id
              GROUP BY c.id, c.name, c.description, c.image
              ORDER BY c.name'
        );
    }

    /**
     * Used by the navbar, so it stays short.
     */
    public function menu(int $limit = 5): array
    {
        // LIMIT will not take a placeholder, so the value is cast to an int instead
        return $this->db->all(
            'SELECT id, name FROM categories ORDER BY name LIMIT ' . (int) max(1, $limit)
        );
    }

    public function find(int $id): ?array
    {
        return $this->db->one(
            'SELECT id, name, description, image FROM categories WHERE id = ?',
            [$id]
        );
    }
}
