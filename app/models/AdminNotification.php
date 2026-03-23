<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminNotification
{
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';
    public const TYPE_LEAD = 'lead';
    public const TYPE_SYSTEM = 'system';

    public static function createTable(): void
    {
        Database::connection()->exec("
            CREATE TABLE IF NOT EXISTS admin_notifications (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL DEFAULT '',
                type ENUM('info', 'success', 'warning', 'error', 'lead', 'system') NOT NULL DEFAULT 'info',
                link VARCHAR(500) DEFAULT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                target_role ENUM('all', 'superuser', 'admin') NOT NULL DEFAULT 'all',
                target_user_id INT UNSIGNED DEFAULT NULL,
                created_by VARCHAR(180) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_notif_read (is_read),
                INDEX idx_notif_type (type),
                INDEX idx_notif_target (target_role),
                INDEX idx_notif_created (created_at),
                INDEX idx_notif_user (target_user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public static function ensureTable(): void
    {
        try {
            $pdo = Database::connection();
            $tables = $pdo->query("SHOW TABLES LIKE 'admin_notifications'")->fetchAll();
            if (empty($tables)) {
                self::createTable();
            }
        } catch (\PDOException $e) {
            self::createTable();
        }
    }

    /**
     * Create a new internal notification.
     */
    public static function create(
        string $title,
        string $message = '',
        string $type = self::TYPE_INFO,
        ?string $link = null,
        string $targetRole = 'all',
        ?int $targetUserId = null,
        ?string $createdBy = null
    ): int {
        self::ensureTable();

        $stmt = Database::connection()->prepare("
            INSERT INTO admin_notifications (title, message, type, link, target_role, target_user_id, created_by, created_at)
            VALUES (:title, :message, :type, :link, :target_role, :target_user_id, :created_by, NOW())
        ");
        $stmt->execute([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'target_role' => $targetRole,
            'target_user_id' => $targetUserId,
            'created_by' => $createdBy,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * Get notifications for the current user based on role.
     */
    public static function getForUser(string $role, ?int $userId = null, int $limit = 50, bool $unreadOnly = false): array
    {
        self::ensureTable();

        $conditions = ["(n.target_role = 'all' OR n.target_role = :role)"];
        $params = ['role' => $role];

        if ($userId !== null) {
            $conditions[0] = "(n.target_role = 'all' OR n.target_role = :role OR n.target_user_id = :user_id)";
            $params['user_id'] = $userId;
        }

        if ($unreadOnly) {
            $conditions[] = 'n.is_read = 0';
        }

        $where = implode(' AND ', $conditions);

        $stmt = Database::connection()->prepare("
            SELECT n.* FROM admin_notifications n
            WHERE {$where}
            ORDER BY n.created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count unread notifications for the current user.
     */
    public static function countUnread(string $role, ?int $userId = null): int
    {
        self::ensureTable();

        $conditions = ["(target_role = 'all' OR target_role = :role)", 'is_read = 0'];
        $params = ['role' => $role];

        if ($userId !== null) {
            $conditions[0] = "(target_role = 'all' OR target_role = :role OR target_user_id = :user_id)";
            $params['user_id'] = $userId;
        }

        $where = implode(' AND ', $conditions);

        $stmt = Database::connection()->prepare("SELECT COUNT(*) FROM admin_notifications WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function markAsRead(int $id): bool
    {
        self::ensureTable();
        $stmt = Database::connection()->prepare('UPDATE admin_notifications SET is_read = 1 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function markAllAsRead(string $role, ?int $userId = null): bool
    {
        self::ensureTable();

        $conditions = ["(target_role = 'all' OR target_role = :role)"];
        $params = ['role' => $role];

        if ($userId !== null) {
            $conditions[0] = "(target_role = 'all' OR target_role = :role OR target_user_id = :user_id)";
            $params['user_id'] = $userId;
        }

        $where = implode(' AND ', $conditions);

        $stmt = Database::connection()->prepare("UPDATE admin_notifications SET is_read = 1 WHERE {$where}");
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM admin_notifications WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function deleteOlderThan(int $days = 30): int
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM admin_notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)'
        );
        $stmt->execute(['days' => $days]);
        return $stmt->rowCount();
    }

    /**
     * Shortcut: notify about a new lead (internal notification).
     */
    public static function notifyNewLead(int $leadId, string $nom, string $ville, string $temperature): void
    {
        $tempLabel = match ($temperature) {
            'chaud' => 'CHAUD',
            'tiede', 'tiède' => 'TIEDE',
            default => 'FROID',
        };

        self::create(
            "Nouveau lead #{$leadId} - {$nom}",
            "Lead {$tempLabel} recu de {$ville}. Cliquez pour voir les details.",
            self::TYPE_LEAD,
            "/admin/leads/{$leadId}",
            'all'
        );
    }

    /**
     * Shortcut: system notification (for superuser only).
     */
    public static function notifySystem(string $title, string $message = '', ?string $link = null): void
    {
        self::create($title, $message, self::TYPE_SYSTEM, $link, 'superuser');
    }
}
