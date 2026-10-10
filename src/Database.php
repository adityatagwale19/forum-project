<?php

declare(strict_types=1);

namespace Forum;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * Thin wrapper over PDO. Every method takes the SQL and the parameters
 * separately, so there is no way to build a query by concatenation.
 */
class Database
{
    private ?PDO $connection = null;

    /**
     * @param array{host: string, port: int, name: string, user: string, pass: string} $settings
     */
    public function __construct(private readonly array $settings)
    {
    }

    /**
     * Connects on first use, so a page that never reads the database still
     * renders when MySQL is down.
     */
    public function pdo(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->settings['host'],
            $this->settings['port'],
            $this->settings['name']
        );

        try {
            $this->connection = new PDO($dsn, $this->settings['user'], $this->settings['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // off means MySQL parses the query and the values separately,
                // which is what makes a placeholder safe
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed.', 0, $e);
        }

        return $this->connection;
    }

    public function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    public function value(string $sql, array $params = []): mixed
    {
        $value = $this->run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    public function insert(string $sql, array $params = []): int
    {
        $this->run($sql, $params);

        return (int) $this->pdo()->lastInsertId();
    }
}
