<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class GmbPublication
{
    // ──────────────────────────────────────────────
    // CRUD — Publications
    // ──────────────────────────────────────────────

    public function findById(int $id): ?array
    {
        $sql = 'SELECT gp.*, a.title AS article_title, a.slug AS article_slug,
                       act.title AS actualite_title, act.slug AS actualite_slug
                FROM gmb_publications gp
                LEFT JOIN articles a ON gp.article_id = a.id
                LEFT JOIN actualites act ON gp.actualite_id = act.id
                WHERE gp.id = :id
                  AND gp.website_id = :website_id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function getAll(int $limit = 50, int $offset = 0, array $filters = []): array
    {
        $where = 'gp.website_id = :website_id';
        $params = [':website_id' => $this->websiteId()];

        if (!empty($filters['status'])) {
            $where .= ' AND gp.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['post_type'])) {
            $where .= ' AND gp.post_type = :post_type';
            $params[':post_type'] = $filters['post_type'];
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where .= ' AND MONTH(gp.scheduled_at) = :month AND YEAR(gp.scheduled_at) = :year';
            $params[':month'] = $filters['month'];
            $params[':year'] = $filters['year'];
        }

        $sql = "SELECT gp.id, gp.post_type, gp.title, LEFT(gp.content, 120) AS content_preview,
                       gp.cta_type, gp.status, gp.scheduled_at, gp.published_at, gp.created_at,
                       a.title AS article_title, act.title AS actualite_title
                FROM gmb_publications gp
                LEFT JOIN articles a ON gp.article_id = a.id
                LEFT JOIN actualites act ON gp.actualite_id = act.id
                WHERE {$where}
                ORDER BY gp.scheduled_at DESC, gp.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $where = 'website_id = :website_id';
        $params = [':website_id' => $this->websiteId()];

        if (!empty($filters['status'])) {
            $where .= ' AND status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['post_type'])) {
            $where .= ' AND post_type = :post_type';
            $params[':post_type'] = $filters['post_type'];
        }

        $sql = "SELECT COUNT(*) FROM gmb_publications WHERE {$where}";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO gmb_publications
                    (website_id, article_id, actualite_id, post_type, title, content,
                     cta_type, cta_url, image_path, event_start, event_end,
                     offer_code, offer_terms, status, scheduled_at, created_at)
                VALUES
                    (:website_id, :article_id, :actualite_id, :post_type, :title, :content,
                     :cta_type, :cta_url, :image_path, :event_start, :event_end,
                     :offer_code, :offer_terms, :status, :scheduled_at, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id'   => $this->websiteId(),
            ':article_id'   => $data['article_id'] ?? null,
            ':actualite_id' => $data['actualite_id'] ?? null,
            ':post_type'    => $data['post_type'] ?? 'update',
            ':title'        => $data['title'] ?? null,
            ':content'      => $data['content'],
            ':cta_type'     => $data['cta_type'] ?? null,
            ':cta_url'      => $data['cta_url'] ?? null,
            ':image_path'   => $data['image_path'] ?? null,
            ':event_start'  => $data['event_start'] ?? null,
            ':event_end'    => $data['event_end'] ?? null,
            ':offer_code'   => $data['offer_code'] ?? null,
            ':offer_terms'  => $data['offer_terms'] ?? null,
            ':status'       => $data['status'] ?? 'draft',
            ':scheduled_at' => $data['scheduled_at'] ?? null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE gmb_publications SET
                    article_id = :article_id, actualite_id = :actualite_id,
                    post_type = :post_type, title = :title, content = :content,
                    cta_type = :cta_type, cta_url = :cta_url, image_path = :image_path,
                    event_start = :event_start, event_end = :event_end,
                    offer_code = :offer_code, offer_terms = :offer_terms,
                    status = :status, scheduled_at = :scheduled_at
                WHERE id = :id AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id'           => $id,
            ':website_id'   => $this->websiteId(),
            ':article_id'   => $data['article_id'] ?? null,
            ':actualite_id' => $data['actualite_id'] ?? null,
            ':post_type'    => $data['post_type'] ?? 'update',
            ':title'        => $data['title'] ?? null,
            ':content'      => $data['content'],
            ':cta_type'     => $data['cta_type'] ?? null,
            ':cta_url'      => $data['cta_url'] ?? null,
            ':image_path'   => $data['image_path'] ?? null,
            ':event_start'  => $data['event_start'] ?? null,
            ':event_end'    => $data['event_end'] ?? null,
            ':offer_code'   => $data['offer_code'] ?? null,
            ':offer_terms'  => $data['offer_terms'] ?? null,
            ':status'       => $data['status'] ?? 'draft',
            ':scheduled_at' => $data['scheduled_at'] ?? null,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM gmb_publications WHERE id = :id AND website_id = :website_id'
        );
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
    }

    // ──────────────────────────────────────────────
    // Scheduling & Notifications
    // ──────────────────────────────────────────────

    public function getScheduledForDate(string $date): array
    {
        $sql = 'SELECT id, post_type, title, LEFT(content, 120) AS content_preview, status, scheduled_at
                FROM gmb_publications
                WHERE website_id = :website_id
                  AND DATE(scheduled_at) = :date
                ORDER BY scheduled_at ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':date' => $date,
        ]);

        return $stmt->fetchAll();
    }

    public function getScheduledForToday(): array
    {
        return $this->getScheduledForDate(date('Y-m-d'));
    }

    public function getPendingNotifications(): array
    {
        $sql = 'SELECT gp.*, a.title AS article_title, a.slug AS article_slug,
                       act.title AS actualite_title, act.slug AS actualite_slug
                FROM gmb_publications gp
                LEFT JOIN articles a ON gp.article_id = a.id
                LEFT JOIN actualites act ON gp.actualite_id = act.id
                WHERE gp.website_id = :website_id
                  AND gp.status = :status
                  AND gp.scheduled_at <= NOW()
                  AND gp.notified_at IS NULL
                ORDER BY gp.scheduled_at ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':status' => 'scheduled',
        ]);

        return $stmt->fetchAll();
    }

    public function markAsNotified(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE gmb_publications SET notified_at = NOW(), status = :status
             WHERE id = :id AND website_id = :website_id'
        );
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':status' => 'notified',
        ]);
    }

    public function markAsPublished(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE gmb_publications SET published_at = NOW(), status = :status
             WHERE id = :id AND website_id = :website_id'
        );
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':status' => 'published',
        ]);
    }

    public function getByArticle(int $articleId): ?array
    {
        $sql = 'SELECT * FROM gmb_publications
                WHERE article_id = :article_id AND website_id = :website_id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':article_id' => $articleId,
            ':website_id' => $this->websiteId(),
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function getByActualite(int $actualiteId): ?array
    {
        $sql = 'SELECT * FROM gmb_publications
                WHERE actualite_id = :actualite_id AND website_id = :website_id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':actualite_id' => $actualiteId,
            ':website_id' => $this->websiteId(),
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    /**
     * Find the next available publication slot based on posting_days setting.
     * Returns a date string (Y-m-d) for the next open day.
     */
    public function getNextAvailableSlot(): string
    {
        $postingDays = $this->getSetting('posting_days', '1,3,5');
        $allowedDays = array_map('intval', explode(',', $postingDays));

        $notificationHour = (int) $this->getSetting('notification_hour', '8');
        $date = new \DateTime('tomorrow');

        // Look up to 30 days ahead
        for ($i = 0; $i < 30; $i++) {
            $dayOfWeek = (int) $date->format('N'); // 1=Monday, 7=Sunday

            if (in_array($dayOfWeek, $allowedDays, true)) {
                // Check how many publications already scheduled that day
                $existing = $this->getScheduledForDate($date->format('Y-m-d'));
                if (count($existing) < 2) {
                    return $date->format('Y-m-d') . ' ' . str_pad((string) $notificationHour, 2, '0', STR_PAD_LEFT) . ':00:00';
                }
            }

            $date->modify('+1 day');
        }

        // Fallback: tomorrow at notification hour
        $tomorrow = new \DateTime('tomorrow');
        return $tomorrow->format('Y-m-d') . ' ' . str_pad((string) $notificationHour, 2, '0', STR_PAD_LEFT) . ':00:00';
    }

    // ──────────────────────────────────────────────
    // Calendar & Stats
    // ──────────────────────────────────────────────

    public function getCalendarData(int $month, int $year): array
    {
        $sql = 'SELECT id, post_type, title, LEFT(content, 60) AS content_preview,
                       status, scheduled_at, published_at,
                       DAY(scheduled_at) AS day_num
                FROM gmb_publications
                WHERE website_id = :website_id
                  AND MONTH(scheduled_at) = :month
                  AND YEAR(scheduled_at) = :year
                ORDER BY scheduled_at ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':website_id', $this->websiteId(), \PDO::PARAM_INT);
        $stmt->bindValue(':month', $month, \PDO::PARAM_INT);
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        // Group by day
        $calendar = [];
        foreach ($rows as $row) {
            $day = (int) $row['day_num'];
            $calendar[$day][] = $row;
        }

        return $calendar;
    }

    public function getStats(): array
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $sql = 'SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = :published THEN 1 ELSE 0 END) AS published,
                    SUM(CASE WHEN status IN (:scheduled, :notified) THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = :expired THEN 1 ELSE 0 END) AS expired
                FROM gmb_publications
                WHERE website_id = :website_id
                  AND MONTH(COALESCE(scheduled_at, created_at)) = :month
                  AND YEAR(COALESCE(scheduled_at, created_at)) = :year';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':published' => 'published',
            ':scheduled' => 'scheduled',
            ':notified' => 'notified',
            ':expired' => 'expired',
            ':month' => $currentMonth,
            ':year' => $currentYear,
        ]);

        $row = $stmt->fetch();

        $total = (int) ($row['total'] ?? 0);
        $published = (int) ($row['published'] ?? 0);

        return [
            'total' => $total,
            'published' => $published,
            'pending' => (int) ($row['pending'] ?? 0),
            'expired' => (int) ($row['expired'] ?? 0),
            'completion_rate' => $total > 0 ? round(($published / $total) * 100) : 0,
        ];
    }

    /**
     * Expire publications that were scheduled more than 7 days ago and never published.
     */
    public function expireOldScheduled(): int
    {
        $sql = 'UPDATE gmb_publications
                SET status = :expired
                WHERE website_id = :website_id
                  AND status IN (:scheduled, :notified)
                  AND scheduled_at < DATE_SUB(NOW(), INTERVAL 7 DAY)';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':expired' => 'expired',
            ':scheduled' => 'scheduled',
            ':notified' => 'notified',
        ]);

        return $stmt->rowCount();
    }

    // ──────────────────────────────────────────────
    // Settings
    // ──────────────────────────────────────────────

    public function getSetting(string $key, ?string $default = null): ?string
    {
        $sql = 'SELECT setting_value FROM gmb_settings
                WHERE website_id = :website_id AND setting_key = :key
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':key' => $key,
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? ($row['setting_value'] ?? $default) : $default;
    }

    public function saveSetting(string $key, ?string $value): void
    {
        $sql = 'INSERT INTO gmb_settings (website_id, setting_key, setting_value)
                VALUES (:website_id, :key, :value)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':key' => $key,
            ':value' => $value,
        ]);
    }

    public function getAllSettings(): array
    {
        $sql = 'SELECT setting_key, setting_value FROM gmb_settings
                WHERE website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    // ──────────────────────────────────────────────
    // Private
    // ──────────────────────────────────────────────

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
