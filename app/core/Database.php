<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $this->connection = self::connectionFromConfig();
    }

    public static function connectionFromConfig(): PDO
    {
        $host = (string) Config::get('db.host');
        $port = (int) Config::get('db.port', 3306);
        $name = (string) Config::get('db.name');
        $user = (string) Config::get('db.user');
        $pass = (string) Config::get('db.pass');
        $charset = (string) Config::get('db.charset', 'utf8mb4');

        if ($name === '') {
            throw new RuntimeException('La configuration DB_NAME est requise.');
        }

        return self::newPdoConnection($host, $port, $name, $user, $pass, $charset);
    }

    public static function connectWithCredentials(
        string $host,
        int $port,
        string $database,
        string $user,
        string $password,
        string $charset = 'utf8mb4'
    ): PDO {
        if (trim($database) === '') {
            throw new RuntimeException('Le nom de base est requis.');
        }

        return self::newPdoConnection($host, $port, $database, $user, $password, $charset);
    }

    private static function newPdoConnection(
        string $host,
        int $port,
        string $database,
        string $user,
        string $password,
        string $charset
    ): PDO {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $database, $charset);

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci",
            ]);
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
            throw new RuntimeException('Impossible de se connecter à la base de données.');
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function connection(): PDO
    {
        return self::getInstance()->connection;
    }

    public static function ping(): bool
    {
        try {
            $pdo = self::connection();
            $pdo->query('SELECT 1');
            return true;
        } catch (\Throwable) {
            self::$instance = null;
            return false;
        }
    }

    public static function tableExists(string $table): bool
    {
        $stmt = self::connection()->prepare(
            'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table'
        );
        $stmt->execute([
            'db' => (string) Config::get('db.name'),
            'table' => $table,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Execute a query with optional parameters (INSERT, UPDATE, DELETE, CREATE, etc.).
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Execute a SELECT query and return all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton.');
    }
}
