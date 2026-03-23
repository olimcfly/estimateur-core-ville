<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;

final class Article
{
    private const SEO_COLUMNS = 'id, title, slug, content, meta_title, meta_description, persona, awareness_level,
        focus_keyword, secondary_keywords, seo_score, semantic_score, keyword_density, keyword_count,
        word_count, h1_tag, og_title, og_description, og_image, canonical_url, faq_schema,
        internal_links_count, external_links_count, images_count, images_with_alt, reading_time_minutes,
        silo_id, article_type, target_audience, article_goal, seo_analysis_json,
        page_views, is_indexed, google_position, indexing_checked_at,
        status, published_at, created_at';

    private const LIST_COLUMNS = 'a.id, a.title, a.slug, a.persona, a.awareness_level, a.focus_keyword, a.seo_score,
        a.semantic_score, a.word_count, a.article_type, a.silo_id, a.page_views, a.is_indexed, a.google_position,
        a.status, a.published_at, a.created_at,
        s.name AS silo_name, s.color AS silo_color, s.city AS silo_city';

    public function findPublished(): array
    {
        $sql = 'SELECT ' . self::SEO_COLUMNS . '
                FROM articles
                WHERE website_id = :website_id
                  AND status = :status
                ORDER BY published_at DESC, created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':status' => 'published',
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Find published articles matching a category via silo name or keyword patterns.
     *
     * @param string   $siloPattern     SQL LIKE pattern to match silo name (e.g. '%march%')
     * @param string[] $keywordPatterns  Keywords to match in title or focus_keyword
     */
    public function findPublishedByCategory(string $category): array
    {
        $categoryKeywords = [
            'marche-immobilier' => ['marché', 'prix', 'immobilier bordeaux', 'tendance', 'évolution', 'transaction', 'quartier'],
            'vendre-son-bien' => ['vendre', 'vente', 'estimation', 'mandat', 'mise en vente', 'prix de vente'],
            'conseils-astuces' => ['conseil', 'astuce', 'erreur', 'guide', 'comment', 'optimiser', 'préparer'],
            'aspects-juridiques' => ['juridique', 'loi', 'dpe', 'diagnostic', 'notaire', 'fiscalité', 'taxe', 'réglementation'],
        ];

        $keywords = $categoryKeywords[$category] ?? [];
        if (empty($keywords)) {
            return [];
        }

        $conditions = [];
        $params = [
            ':website_id' => $this->websiteId(),
            ':status' => 'published',
        ];

        foreach ($keywords as $i => $keyword) {
            $paramName = ':kw' . $i;
            $conditions[] = "(LOWER(title) LIKE $paramName OR LOWER(focus_keyword) LIKE $paramName OR LOWER(secondary_keywords) LIKE $paramName)";
            $params[$paramName] = '%' . mb_strtolower($keyword) . '%';
        }

        $whereKeywords = '(' . implode(' OR ', $conditions) . ')';

        $sql = 'SELECT ' . self::SEO_COLUMNS . '
                FROM articles
                WHERE website_id = :website_id
                  AND status = :status
                  AND ' . $whereKeywords . '
                ORDER BY published_at DESC, created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $sql = 'SELECT ' . self::SEO_COLUMNS . '
                FROM articles
                WHERE website_id = :website_id
                  AND slug = :slug
                  AND status = :status
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':slug' => $slug,
            ':status' => 'published',
        ]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findAll(): array
    {
        $sql = 'SELECT ' . self::LIST_COLUMNS . '
                FROM articles a
                LEFT JOIN article_silos s ON a.silo_id = s.id
                WHERE a.website_id = :website_id
                ORDER BY a.silo_id ASC, a.article_type ASC, a.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll();
    }

    public function findBySilo(int $siloId): array
    {
        $sql = 'SELECT ' . self::LIST_COLUMNS . '
                FROM articles a
                LEFT JOIN article_silos s ON a.silo_id = s.id
                WHERE a.website_id = :website_id AND a.silo_id = :silo_id
                ORDER BY a.article_type ASC, a.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId(), ':silo_id' => $siloId]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT ' . self::SEO_COLUMNS . '
                FROM articles
                WHERE id = :id
                  AND website_id = :website_id
                LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO articles (
                    website_id, title, slug, content, meta_title, meta_description,
                    persona, awareness_level, focus_keyword, secondary_keywords,
                    seo_score, semantic_score, keyword_density, keyword_count, word_count,
                    h1_tag, og_title, og_description, og_image, canonical_url, faq_schema,
                    internal_links_count, external_links_count, images_count, images_with_alt,
                    reading_time_minutes, silo_id, article_type, target_audience, article_goal,
                    seo_analysis_json, status, published_at, created_at
                ) VALUES (
                    :website_id, :title, :slug, :content, :meta_title, :meta_description,
                    :persona, :awareness_level, :focus_keyword, :secondary_keywords,
                    :seo_score, :semantic_score, :keyword_density, :keyword_count, :word_count,
                    :h1_tag, :og_title, :og_description, :og_image, :canonical_url, :faq_schema,
                    :internal_links_count, :external_links_count, :images_count, :images_with_alt,
                    :reading_time_minutes, :silo_id, :article_type, :target_audience, :article_goal,
                    :seo_analysis_json, :status, :published_at, NOW()
                )';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($this->buildParams($data));

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $connection = Database::connection();
        $this->createRevisionSnapshot($id, $connection);

        $sql = 'UPDATE articles SET
                    title = :title, slug = :slug, content = :content,
                    meta_title = :meta_title, meta_description = :meta_description,
                    persona = :persona, awareness_level = :awareness_level,
                    focus_keyword = :focus_keyword, secondary_keywords = :secondary_keywords,
                    seo_score = :seo_score, semantic_score = :semantic_score,
                    keyword_density = :keyword_density, keyword_count = :keyword_count,
                    word_count = :word_count, h1_tag = :h1_tag,
                    og_title = :og_title, og_description = :og_description,
                    og_image = :og_image, canonical_url = :canonical_url, faq_schema = :faq_schema,
                    internal_links_count = :internal_links_count, external_links_count = :external_links_count,
                    images_count = :images_count, images_with_alt = :images_with_alt,
                    reading_time_minutes = :reading_time_minutes,
                    silo_id = :silo_id, article_type = :article_type,
                    target_audience = :target_audience, article_goal = :article_goal,
                    seo_analysis_json = :seo_analysis_json,
                    status = :status, published_at = :published_at
                WHERE id = :id AND website_id = :website_id';

        $params = $this->buildParams($data);
        $params[':id'] = $id;

        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
    }

    public function updateSeoScores(int $id, array $seoData): void
    {
        $sql = 'UPDATE articles SET
                    seo_score = :seo_score, semantic_score = :semantic_score,
                    keyword_density = :keyword_density, keyword_count = :keyword_count,
                    word_count = :word_count,
                    internal_links_count = :internal_links_count,
                    external_links_count = :external_links_count,
                    images_count = :images_count, images_with_alt = :images_with_alt,
                    reading_time_minutes = :reading_time_minutes,
                    seo_analysis_json = :seo_analysis_json
                WHERE id = :id AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':seo_score' => $seoData['seo_score'] ?? 0,
            ':semantic_score' => $seoData['semantic_score'] ?? 0,
            ':keyword_density' => $seoData['keyword_density'] ?? 0,
            ':keyword_count' => $seoData['keyword_count'] ?? 0,
            ':word_count' => $seoData['word_count'] ?? 0,
            ':internal_links_count' => $seoData['content_stats']['links_count'] ?? 0,
            ':external_links_count' => $seoData['content_stats']['links_count'] ?? 0,
            ':images_count' => $seoData['content_stats']['images_count'] ?? 0,
            ':images_with_alt' => $seoData['content_stats']['images_count'] ?? 0,
            ':reading_time_minutes' => $seoData['content_stats']['reading_time'] ?? 0,
            ':seo_analysis_json' => json_encode($seoData, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM articles WHERE id = :id AND website_id = :website_id');
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);
    }

    public function findRevisionsByArticleId(int $articleId): array
    {
        $sql = 'SELECT id, article_id, revision_number, title, slug, status, created_at
                FROM article_revisions
                WHERE article_id = :article_id
                ORDER BY revision_number DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':article_id' => $articleId]);

        return $stmt->fetchAll();
    }

    public function restoreRevision(int $articleId, int $revisionId): void
    {
        $connection = Database::connection();

        $sql = 'SELECT title, slug, content, meta_title, meta_description, persona, awareness_level, status
                FROM article_revisions
                WHERE id = :id AND article_id = :article_id
                LIMIT 1';

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id' => $revisionId, ':article_id' => $articleId]);
        $revision = $stmt->fetch();

        if (!is_array($revision)) {
            throw new \InvalidArgumentException('Révision introuvable.');
        }

        $this->createRevisionSnapshot($articleId, $connection);

        $updateSql = 'UPDATE articles
                       SET title = :title,
                           slug = :slug,
                           content = :content,
                           meta_title = :meta_title,
                           meta_description = :meta_description,
                           persona = :persona,
                           awareness_level = :awareness_level,
                           status = :status
                       WHERE id = :id AND website_id = :website_id';

        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->execute([
            ':id' => $articleId,
            ':website_id' => $this->websiteId(),
            ':title' => $revision['title'],
            ':slug' => $revision['slug'],
            ':content' => $revision['content'],
            ':meta_title' => $revision['meta_title'],
            ':meta_description' => $revision['meta_description'],
            ':persona' => $revision['persona'],
            ':awareness_level' => $revision['awareness_level'],
            ':status' => $revision['status'],
        ]);
    }

    // --- Silo methods ---

    public function findAllSilos(): array
    {
        $sql = 'SELECT s.*, COUNT(a.id) as article_count,
                       AVG(a.seo_score) as avg_seo_score
                FROM article_silos s
                LEFT JOIN articles a ON a.silo_id = s.id
                WHERE s.website_id = :website_id
                GROUP BY s.id
                ORDER BY s.name ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll();
    }

    public function createSilo(array $data): int
    {
        $sql = 'INSERT INTO article_silos (website_id, name, description, color, city)
                VALUES (:website_id, :name, :description, :color, :city)';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':color' => $data['color'] ?? '#8B1538',
            ':city' => $data['city'] ?? 'Bordeaux',
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function deleteSilo(int $id): void
    {
        // Unlink articles first
        $stmt = Database::connection()->prepare('UPDATE articles SET silo_id = NULL WHERE silo_id = :silo_id AND website_id = :website_id');
        $stmt->execute([':silo_id' => $id, ':website_id' => $this->websiteId()]);

        $stmt = Database::connection()->prepare('DELETE FROM article_silos WHERE id = :id AND website_id = :website_id');
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
    }

    // --- Page Views & Indexing ---

    public function incrementPageViews(int $id): void
    {
        $sql = 'UPDATE articles SET page_views = page_views + 1 WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
    }

    public function updateIndexingData(int $id, array $data): void
    {
        $sql = 'UPDATE articles SET
                    is_indexed = :is_indexed,
                    google_position = :google_position,
                    indexing_checked_at = NOW()
                WHERE id = :id AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':is_indexed' => $data['is_indexed'] ? 1 : 0,
            ':google_position' => $data['position'],
        ]);
    }

    // --- Statistics ---

    public function getSeoStats(): array
    {
        $sql = 'SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = \'published\' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN status = \'draft\' THEN 1 ELSE 0 END) as drafts,
                    ROUND(AVG(seo_score), 0) as avg_seo_score,
                    ROUND(AVG(semantic_score), 0) as avg_semantic_score,
                    ROUND(AVG(word_count), 0) as avg_word_count,
                    SUM(CASE WHEN seo_score >= 80 THEN 1 ELSE 0 END) as excellent_seo,
                    SUM(CASE WHEN seo_score >= 50 AND seo_score < 80 THEN 1 ELSE 0 END) as good_seo,
                    SUM(CASE WHEN seo_score < 50 THEN 1 ELSE 0 END) as poor_seo
                FROM articles
                WHERE website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetch() ?: [];
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }

    private function buildParams(array $data): array
    {
        return [
            ':website_id' => $this->websiteId(),
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':meta_title' => $data['meta_title'],
            ':meta_description' => $data['meta_description'],
            ':persona' => $data['persona'],
            ':awareness_level' => $data['awareness_level'],
            ':focus_keyword' => $data['focus_keyword'] ?? '',
            ':secondary_keywords' => $data['secondary_keywords'] ?? '',
            ':seo_score' => (int) ($data['seo_score'] ?? 0),
            ':semantic_score' => (int) ($data['semantic_score'] ?? 0),
            ':keyword_density' => (float) ($data['keyword_density'] ?? 0),
            ':keyword_count' => (int) ($data['keyword_count'] ?? 0),
            ':word_count' => (int) ($data['word_count'] ?? 0),
            ':h1_tag' => $data['h1_tag'] ?? '',
            ':og_title' => $data['og_title'] ?? '',
            ':og_description' => $data['og_description'] ?? null,
            ':og_image' => $data['og_image'] ?? null,
            ':canonical_url' => $data['canonical_url'] ?? null,
            ':faq_schema' => $data['faq_schema'] ?? null,
            ':internal_links_count' => (int) ($data['internal_links_count'] ?? 0),
            ':external_links_count' => (int) ($data['external_links_count'] ?? 0),
            ':images_count' => (int) ($data['images_count'] ?? 0),
            ':images_with_alt' => (int) ($data['images_with_alt'] ?? 0),
            ':reading_time_minutes' => (int) ($data['reading_time_minutes'] ?? 0),
            ':silo_id' => !empty($data['silo_id']) ? (int) $data['silo_id'] : null,
            ':article_type' => $data['article_type'] ?? 'standalone',
            ':target_audience' => $data['target_audience'] ?? null,
            ':article_goal' => $data['article_goal'] ?? null,
            ':seo_analysis_json' => $data['seo_analysis_json'] ?? null,
            ':status' => $data['status'],
            ':published_at' => ($data['status'] === 'published') ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null,
        ];
    }

    private function createRevisionSnapshot(int $articleId, \PDO $connection): void
    {
        $sql = 'SELECT id, title, slug, content, meta_title, meta_description, persona, awareness_level, status
                FROM articles
                WHERE id = :id
                LIMIT 1';
        $articleStmt = $connection->prepare($sql);
        $articleStmt->execute([':id' => $articleId]);
        $article = $articleStmt->fetch();

        if (!is_array($article)) {
            throw new \InvalidArgumentException('Article introuvable.');
        }

        $revisionSql = 'SELECT COALESCE(MAX(revision_number), 0) + 1
                        FROM article_revisions
                        WHERE article_id = :article_id';
        $revisionStmt = $connection->prepare($revisionSql);
        $revisionStmt->execute([':article_id' => $articleId]);
        $nextRevisionNumber = (int) $revisionStmt->fetchColumn();

        $insertSql = 'INSERT INTO article_revisions (
                            article_id, revision_number, title, slug, content,
                            meta_title, meta_description, persona, awareness_level,
                            status, created_at
                      ) VALUES (
                            :article_id, :revision_number, :title, :slug, :content,
                            :meta_title, :meta_description, :persona, :awareness_level,
                            :status, NOW()
                      )';

        $insertStmt = $connection->prepare($insertSql);
        $insertStmt->execute([
            ':article_id' => $articleId,
            ':revision_number' => $nextRevisionNumber,
            ':title' => $article['title'],
            ':slug' => $article['slug'],
            ':content' => $article['content'],
            ':meta_title' => $article['meta_title'],
            ':meta_description' => $article['meta_description'],
            ':persona' => $article['persona'],
            ':awareness_level' => $article['awareness_level'],
            ':status' => $article['status'],
        ]);
    }
}
