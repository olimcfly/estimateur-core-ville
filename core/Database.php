<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private array $config;

    /*
    |--------------------------------------------------------------------------
    | Singleton
    |--------------------------------------------------------------------------
    */
    private function __construct()
    {
        $this->config = require ROOT_PATH . '/config/database.php';
        $this->connect();
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /*
    |--------------------------------------------------------------------------
    | Connexion
    |--------------------------------------------------------------------------
    */
    private function connect(): void
    {
        $cfg = $this->config['connections'][$this->config['default']];

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $cfg['driver'],
            $cfg['host'],
            $cfg['port'],
            $cfg['database'],
            $cfg['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $cfg['username'],
                $cfg['password'],
                $cfg['options']
            );
        } catch (PDOException $e) {
            throw new \RuntimeException(
                'Connexion base de données échouée : ' . $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes requêtes
    |--------------------------------------------------------------------------
    */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)->fetch() ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return $this->connection->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function getPdo(): PDO
    {
        return $this->connection;
    }
}
