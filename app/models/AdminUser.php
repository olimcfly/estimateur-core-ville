<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminUser
{
    private const CODE_TTL_MINUTES = 10;

    public const ROLE_SUPERUSER = 'superuser';
    public const ROLE_ADMIN = 'admin';

    public static function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));

        try {
            $stmt = Database::connection()->prepare(
                'SELECT * FROM admin_users WHERE LOWER(email) = :email LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if (str_contains($e->getMessage(), 'admin_users') || str_contains($e->getMessage(), '1146')) {
                self::createTable();
                self::seedDefaultAdmin($email);
                error_log('AdminUser: auto-created admin_users table and seeded ' . $email);
                return self::findByEmailDirect($email);
            }
            throw $e;
        }

        return $row !== false ? $row : null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM admin_users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public static function findAll(): array
    {
        try {
            $stmt = Database::connection()->query('SELECT * FROM admin_users ORDER BY created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    private static function findByEmailDirect(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM admin_users WHERE LOWER(email) = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public static function storeLoginCode(string $email, string $code): void
    {
        $email = strtolower(trim($email));
        $expiresAt = date('Y-m-d H:i:s', time() + self::CODE_TTL_MINUTES * 60);

        $stmt = Database::connection()->prepare(
            'UPDATE admin_users SET login_code = :code, login_code_expires_at = :expires WHERE LOWER(email) = :email'
        );
        $stmt->execute([
            'code' => password_hash($code, PASSWORD_BCRYPT, ['cost' => 10]),
            'expires' => $expiresAt,
            'email' => $email,
        ]);
    }

    public static function verifyLoginCode(string $email, string $code): bool
    {
        $user = self::findByEmail($email);
        if ($user === null) {
            return false;
        }

        $hash = (string) ($user['login_code'] ?? '');
        $expiresAt = (string) ($user['login_code_expires_at'] ?? '');

        if ($hash === '' || $expiresAt === '') {
            return false;
        }

        if (strtotime($expiresAt) < time()) {
            return false;
        }

        return password_verify($code, $hash);
    }

    public static function clearLoginCode(string $email): void
    {
        $email = strtolower(trim($email));
        $stmt = Database::connection()->prepare(
            'UPDATE admin_users SET login_code = NULL, login_code_expires_at = NULL WHERE LOWER(email) = :email'
        );
        $stmt->execute(['email' => $email]);
    }

    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createTable(): void
    {
        Database::connection()->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(180) NOT NULL UNIQUE,
                name VARCHAR(120) NOT NULL DEFAULT '',
                role ENUM('superuser', 'admin') NOT NULL DEFAULT 'admin',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                login_code VARCHAR(255) DEFAULT NULL,
                login_code_expires_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_admin_email (email),
                INDEX idx_admin_role (role)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Ensure role column exists on existing tables
        self::migrateAddRoleColumn();
    }

    private static function migrateAddRoleColumn(): void
    {
        $pdo = Database::connection();
        try {
            $columns = $pdo->query('SHOW COLUMNS FROM admin_users')->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('role', $columns, true)) {
                $pdo->exec("ALTER TABLE admin_users ADD COLUMN role ENUM('superuser', 'admin') NOT NULL DEFAULT 'admin' AFTER name");
                $pdo->exec("ALTER TABLE admin_users ADD INDEX idx_admin_role (role)");
            }
            if (!in_array('is_active', $columns, true)) {
                $pdo->exec("ALTER TABLE admin_users ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role");
            }
        } catch (\PDOException $e) {
            error_log('AdminUser migration: ' . $e->getMessage());
        }
    }

    public static function seedDefaultAdmin(string $email): void
    {
        $email = strtolower(trim($email));
        $existing = self::findByEmail($email);
        if ($existing !== null) {
            return;
        }

        $stmt = Database::connection()->prepare(
            'INSERT INTO admin_users (email, name, role, created_at) VALUES (:email, :name, :role, NOW())'
        );
        $stmt->execute([
            'email' => $email,
            'name' => 'Administrateur',
            'role' => self::ROLE_SUPERUSER,
        ]);
    }

    /**
     * Get the role of the currently logged-in user.
     */
    public static function currentRole(): string
    {
        $email = (string) ($_SESSION['admin_user_email'] ?? '');
        if ($email === '') {
            return self::ROLE_ADMIN;
        }

        $user = self::findByEmail($email);
        return (string) ($user['role'] ?? self::ROLE_ADMIN);
    }

    /**
     * Check if current user is superuser.
     */
    public static function isSuperUser(): bool
    {
        return self::currentRole() === self::ROLE_SUPERUSER;
    }

    /**
     * Determine role for an email based on env config.
     * ADMIN_EMAIL = superuser, others = admin.
     */
    public static function determineRoleForEmail(string $email): string
    {
        $email = strtolower(trim($email));
        $superuserEmail = strtolower(trim((string) ($_ENV['ADMIN_EMAIL'] ?? '')));

        if ($superuserEmail !== '' && $email === $superuserEmail) {
            return self::ROLE_SUPERUSER;
        }

        return self::ROLE_ADMIN;
    }

    public static function updateUser(int $id, array $data): bool
    {
        $sets = [];
        $params = ['id' => $id];

        if (isset($data['name'])) {
            $sets[] = 'name = :name';
            $params['name'] = $data['name'];
        }
        if (isset($data['role'])) {
            $sets[] = 'role = :role';
            $params['role'] = $data['role'];
        }
        if (isset($data['is_active'])) {
            $sets[] = 'is_active = :is_active';
            $params['is_active'] = (int) $data['is_active'];
        }

        if (empty($sets)) {
            return false;
        }

        $sql = 'UPDATE admin_users SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function deleteUser(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM admin_users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function createUser(string $email, string $name, string $role): bool
    {
        $email = strtolower(trim($email));
        $existing = self::findByEmail($email);
        if ($existing !== null) {
            return false;
        }

        $stmt = Database::connection()->prepare(
            'INSERT INTO admin_users (email, name, role, created_at) VALUES (:email, :name, :role, NOW())'
        );
        return $stmt->execute([
            'email' => $email,
            'name' => $name,
            'role' => $role,
        ]);
    }
}
