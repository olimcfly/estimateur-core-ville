<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class RssSource
{
    public function findAll(): array
    {
        $sql = 'SELECT * FROM rss_sources WHERE website_id = :wid ORDER BY zone, category, name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':wid' => $this->websiteId()]);
        return $stmt->fetchAll();
    }

    public function findActive(): array
    {
        $sql = 'SELECT * FROM rss_sources WHERE website_id = :wid AND is_active = 1 ORDER BY zone, category, name';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':wid' => $this->websiteId()]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM rss_sources WHERE id = :id AND website_id = :wid LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO rss_sources (website_id, name, feed_url, site_url, category, zone, is_active, created_at)
                VALUES (:wid, :name, :feed_url, :site_url, :category, :zone, :is_active, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':wid' => $this->websiteId(),
            ':name' => $data['name'],
            ':feed_url' => $data['feed_url'],
            ':site_url' => $data['site_url'] ?? null,
            ':category' => $data['category'] ?? 'general',
            ':zone' => $data['zone'] ?? 'national',
            ':is_active' => (int) ($data['is_active'] ?? 1),
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE rss_sources SET name = :name, feed_url = :feed_url, site_url = :site_url,
                category = :category, zone = :zone, is_active = :is_active, updated_at = NOW()
                WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':wid' => $this->websiteId(),
            ':name' => $data['name'],
            ':feed_url' => $data['feed_url'],
            ':site_url' => $data['site_url'] ?? null,
            ':category' => $data['category'] ?? 'general',
            ':zone' => $data['zone'] ?? 'national',
            ':is_active' => (int) ($data['is_active'] ?? 1),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM rss_sources WHERE id = :id AND website_id = :wid');
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
    }

    public function updateLastFetched(int $id, ?string $error = null): void
    {
        $sql = 'UPDATE rss_sources SET last_fetched_at = NOW(), last_error = :error, updated_at = NOW() WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':error' => $error]);
    }

    public function toggleActive(int $id): void
    {
        $sql = 'UPDATE rss_sources SET is_active = NOT is_active, updated_at = NOW() WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
    }

    public function countByZone(): array
    {
        $sql = 'SELECT zone, COUNT(*) as total, SUM(is_active) as active FROM rss_sources WHERE website_id = :wid GROUP BY zone';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':wid' => $this->websiteId()]);
        return $stmt->fetchAll();
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
