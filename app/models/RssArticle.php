<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class RssArticle
{
    public function findAll(int $limit = 50, int $offset = 0, ?int $sourceId = null, ?bool $starred = null, ?bool $used = null): array
    {
        $where = ['ra.website_id = :wid'];
        $params = [':wid' => $this->websiteId()];

        if ($sourceId !== null) {
            $where[] = 'ra.rss_source_id = :sid';
            $params[':sid'] = $sourceId;
        }
        if ($starred !== null) {
            $where[] = 'ra.is_starred = :starred';
            $params[':starred'] = (int) $starred;
        }
        if ($used !== null) {
            $where[] = 'ra.is_used = :used';
            $params[':used'] = (int) $used;
        }

        $sql = 'SELECT ra.*, rs.name AS source_name, rs.zone AS source_zone
                FROM rss_articles ra
                JOIN rss_sources rs ON rs.id = ra.rss_source_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY ra.pub_date DESC, ra.created_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(?int $sourceId = null, ?bool $starred = null, ?bool $used = null): int
    {
        $where = ['website_id = :wid'];
        $params = [':wid' => $this->websiteId()];

        if ($sourceId !== null) {
            $where[] = 'rss_source_id = :sid';
            $params[':sid'] = $sourceId;
        }
        if ($starred !== null) {
            $where[] = 'is_starred = :starred';
            $params[':starred'] = (int) $starred;
        }
        if ($used !== null) {
            $where[] = 'is_used = :used';
            $params[':used'] = (int) $used;
        }

        $sql = 'SELECT COUNT(*) FROM rss_articles WHERE ' . implode(' AND ', $where);
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT ra.*, rs.name AS source_name, rs.zone AS source_zone, rs.site_url AS source_site_url
                FROM rss_articles ra
                JOIN rss_sources rs ON rs.id = ra.rss_source_id
                WHERE ra.id = :id AND ra.website_id = :wid LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT ra.*, rs.name AS source_name, rs.zone AS source_zone
                FROM rss_articles ra
                JOIN rss_sources rs ON rs.id = ra.rss_source_id
                WHERE ra.id IN ($placeholders) AND ra.website_id = ?";
        $stmt = Database::connection()->prepare($sql);
        $params = array_values(array_map('intval', $ids));
        $params[] = $this->websiteId();
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function insertIfNew(array $data): ?int
    {
        $sql = 'INSERT IGNORE INTO rss_articles (website_id, rss_source_id, guid, title, link, description, content, author, pub_date, image_url, created_at)
                VALUES (:wid, :sid, :guid, :title, :link, :description, :content, :author, :pub_date, :image_url, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':wid' => $this->websiteId(),
            ':sid' => $data['rss_source_id'],
            ':guid' => $data['guid'],
            ':title' => $data['title'],
            ':link' => $data['link'],
            ':description' => $data['description'] ?? null,
            ':content' => $data['content'] ?? null,
            ':author' => $data['author'] ?? null,
            ':pub_date' => $data['pub_date'] ?? null,
            ':image_url' => $data['image_url'] ?? null,
        ]);
        $id = (int) Database::connection()->lastInsertId();
        return $id > 0 ? $id : null;
    }

    public function toggleStarred(int $id): void
    {
        $sql = 'UPDATE rss_articles SET is_starred = NOT is_starred WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
    }

    public function markAsRead(int $id): void
    {
        $sql = 'UPDATE rss_articles SET is_read = 1 WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
    }

    public function markAsUsed(int $id, ?int $blogArticleId = null): void
    {
        $sql = 'UPDATE rss_articles SET is_used = 1, blog_article_id = :blog_id WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId(), ':blog_id' => $blogArticleId]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM rss_articles WHERE id = :id AND website_id = :wid');
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId()]);
    }

    /**
     * Find recent unused articles suitable for Actualite generation.
     * Prioritizes local (Bordeaux) sources and filters by age.
     */
    public function findForActualite(int $maxAgeDays = 7, ?string $zonePriority = 'local_first', int $limit = 30): array
    {
        $sql = 'SELECT ra.*, rs.name AS source_name, rs.zone AS source_zone, rs.category AS source_category
                FROM rss_articles ra
                JOIN rss_sources rs ON rs.id = ra.rss_source_id
                WHERE ra.website_id = :wid
                  AND ra.is_used = 0
                  AND ra.actualite_id IS NULL
                  AND rs.is_active = 1
                  AND ra.pub_date >= DATE_SUB(NOW(), INTERVAL :days DAY)
                ORDER BY
                  CASE WHEN rs.zone = :local_zone THEN 0 ELSE 1 END,
                  ra.pub_date DESC
                LIMIT :limit';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':wid', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':days', $maxAgeDays, \PDO::PARAM_INT);
        $stmt->bindValue(':local_zone', 'Bordeaux/Nouvelle-Aquitaine', \PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsUsedForActualite(int $id, ?int $actualiteId = null): void
    {
        $sql = 'UPDATE rss_articles SET is_used = 1, actualite_id = :actu_id WHERE id = :id AND website_id = :wid';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':wid' => $this->websiteId(), ':actu_id' => $actualiteId]);
    }

    public function deleteOlderThan(int $days): int
    {
        $sql = 'DELETE FROM rss_articles WHERE website_id = :wid AND is_starred = 0 AND is_used = 0 AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':wid', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function logGeneration(array $articleIds, ?int $blogArticleId, ?string $promptUsed, string $status, ?string $error = null): void
    {
        $sql = 'INSERT INTO rss_blog_generation_log (website_id, rss_article_ids, blog_article_id, prompt_used, status, error_message, created_at)
                VALUES (:wid, :ids, :blog_id, :prompt, :status, :error, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':wid' => $this->websiteId(),
            ':ids' => json_encode($articleIds),
            ':blog_id' => $blogArticleId,
            ':prompt' => $promptUsed,
            ':status' => $status,
            ':error' => $error,
        ]);
    }

    public function getGenerationLogs(int $limit = 20): array
    {
        $sql = 'SELECT * FROM rss_blog_generation_log WHERE website_id = :wid ORDER BY created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':wid', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
