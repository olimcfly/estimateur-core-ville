<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;
use App\Models\Article;
use App\Models\Actualite;
use App\Models\GmbPublication;

final class GmbService
{
    private const MAX_CONTENT_LENGTH = 1500;
    private const MAX_TITLE_LENGTH = 58;

    /**
     * Generate a GMB publication from the latest published article or actualite.
     * Prioritizes content that doesn't already have a GMB publication.
     *
     * @return array{success: bool, publication?: array, error?: string}
     */
    public function generateFromLatestContent(): array
    {
        $gmbModel = new GmbPublication();

        // Try latest article first
        $publication = $this->generateFromLatestArticle($gmbModel);
        if ($publication !== null) {
            return ['success' => true, 'publication' => $publication];
        }

        // Then try latest actualite
        $publication = $this->generateFromLatestActualite($gmbModel);
        if ($publication !== null) {
            return ['success' => true, 'publication' => $publication];
        }

        return ['success' => false, 'error' => 'Aucun article ou actualite recente sans publication GMB.'];
    }

    /**
     * Generate a GMB publication draft from a specific article.
     */
    public function generateFromArticle(int $articleId): ?array
    {
        $articleModel = new Article();
        $article = $articleModel->findById($articleId);
        if ($article === null) {
            return null;
        }

        return $this->buildPublicationFromArticle($article);
    }

    /**
     * Generate a GMB publication draft from a specific actualite.
     */
    public function generateFromActualite(int $actualiteId): ?array
    {
        $actualiteModel = new Actualite();
        $actualite = $actualiteModel->findById($actualiteId);
        if ($actualite === null) {
            return null;
        }

        return $this->buildPublicationFromActualite($actualite);
    }

    /**
     * Create and schedule a GMB publication from an article (auto-generate hook).
     */
    public function autoGenerateFromArticle(int $articleId): ?int
    {
        $gmbModel = new GmbPublication();
        $settings = $gmbModel->getAllSettings();

        if (($settings['auto_generate'] ?? '1') !== '1') {
            return null;
        }

        // Check if publication already exists for this article
        $existing = $gmbModel->getByArticle($articleId);
        if ($existing !== null) {
            return null;
        }

        $publicationData = $this->generateFromArticle($articleId);
        if ($publicationData === null) {
            return null;
        }

        $publicationData['status'] = 'scheduled';
        $publicationData['scheduled_at'] = $gmbModel->getNextAvailableSlot();

        // Apply default CTA from settings
        if (empty($publicationData['cta_type']) && !empty($settings['default_cta_type'])) {
            $publicationData['cta_type'] = $settings['default_cta_type'];
        }
        if (empty($publicationData['cta_url']) && !empty($settings['default_cta_url'])) {
            $publicationData['cta_url'] = $settings['default_cta_url'];
        }

        return $gmbModel->create($publicationData);
    }

    /**
     * Create and schedule a GMB publication from an actualite (auto-generate hook).
     */
    public function autoGenerateFromActualite(int $actualiteId): ?int
    {
        $gmbModel = new GmbPublication();
        $settings = $gmbModel->getAllSettings();

        if (($settings['auto_generate'] ?? '1') !== '1') {
            return null;
        }

        $existing = $gmbModel->getByActualite($actualiteId);
        if ($existing !== null) {
            return null;
        }

        $publicationData = $this->generateFromActualite($actualiteId);
        if ($publicationData === null) {
            return null;
        }

        $publicationData['status'] = 'scheduled';
        $publicationData['scheduled_at'] = $gmbModel->getNextAvailableSlot();

        if (empty($publicationData['cta_type']) && !empty($settings['default_cta_type'])) {
            $publicationData['cta_type'] = $settings['default_cta_type'];
        }
        if (empty($publicationData['cta_url']) && !empty($settings['default_cta_url'])) {
            $publicationData['cta_url'] = $settings['default_cta_url'];
        }

        return $gmbModel->create($publicationData);
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function generateFromLatestArticle(GmbPublication $gmbModel): ?array
    {
        $articleModel = new Article();
        $articles = $articleModel->findPublished();

        foreach ($articles as $article) {
            $existing = $gmbModel->getByArticle((int) $article['id']);
            if ($existing === null) {
                $pub = $this->buildPublicationFromArticle($article);
                $pub['scheduled_at'] = $gmbModel->getNextAvailableSlot();
                $pub['status'] = 'draft';
                return $pub;
            }
        }

        return null;
    }

    private function generateFromLatestActualite(GmbPublication $gmbModel): ?array
    {
        $actualiteModel = new Actualite();
        $actualites = $actualiteModel->findPublished(10, 0);

        foreach ($actualites as $actualite) {
            $existing = $gmbModel->getByActualite((int) $actualite['id']);
            if ($existing === null) {
                $pub = $this->buildPublicationFromActualite($actualite);
                $pub['scheduled_at'] = $gmbModel->getNextAvailableSlot();
                $pub['status'] = 'draft';
                return $pub;
            }
        }

        return null;
    }

    private function buildPublicationFromArticle(array $article): array
    {
        $siteUrl = rtrim((string) Config::get('app.url', ''), '/');
        $slug = $article['slug'] ?? '';
        $articleUrl = $siteUrl . '/blog/' . $slug;

        $content = $this->buildGmbContent(
            $article['title'] ?? '',
            $article['meta_description'] ?? $article['content'] ?? '',
            $articleUrl
        );

        return [
            'article_id'   => (int) $article['id'],
            'actualite_id' => null,
            'post_type'    => 'update',
            'title'        => mb_substr($article['title'] ?? '', 0, self::MAX_TITLE_LENGTH),
            'content'      => $content,
            'cta_type'     => 'learn_more',
            'cta_url'      => $articleUrl,
            'image_path'   => $article['image_url'] ?? null,
        ];
    }

    private function buildPublicationFromActualite(array $actualite): array
    {
        $siteUrl = rtrim((string) Config::get('app.url', ''), '/');
        $slug = $actualite['slug'] ?? '';
        $actualiteUrl = $siteUrl . '/actualites/' . $slug;

        $content = $this->buildGmbContent(
            $actualite['title'] ?? '',
            $actualite['excerpt'] ?? $actualite['meta_description'] ?? $actualite['content'] ?? '',
            $actualiteUrl
        );

        return [
            'actualite_id' => (int) $actualite['id'],
            'article_id'   => null,
            'post_type'    => 'update',
            'title'        => mb_substr($actualite['title'] ?? '', 0, self::MAX_TITLE_LENGTH),
            'content'      => $content,
            'cta_type'     => 'learn_more',
            'cta_url'      => $actualiteUrl,
            'image_path'   => $actualite['image_url'] ?? null,
        ];
    }

    /**
     * Build the GMB post content (max 1500 chars).
     * Format: Hook line + summary + link + CTA.
     */
    private function buildGmbContent(string $title, string $description, string $url): string
    {
        // Clean HTML from description
        $cleanDesc = strip_tags($description);
        $cleanDesc = html_entity_decode($cleanDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc) ?? $cleanDesc;
        $cleanDesc = trim($cleanDesc);

        $hook = $title;
        $footer = "\n\nLire la suite : " . $url;
        $footerLen = mb_strlen($footer);

        // Reserve space for hook + newlines + footer
        $maxDescLen = self::MAX_CONTENT_LENGTH - mb_strlen($hook) - 2 - $footerLen;
        if ($maxDescLen < 50) {
            $maxDescLen = 50;
        }

        if (mb_strlen($cleanDesc) > $maxDescLen) {
            $cleanDesc = mb_substr($cleanDesc, 0, $maxDescLen - 3) . '...';
        }

        $content = $hook . "\n\n" . $cleanDesc . $footer;

        // Final safety trim
        if (mb_strlen($content) > self::MAX_CONTENT_LENGTH) {
            $content = mb_substr($content, 0, self::MAX_CONTENT_LENGTH - 3) . '...';
        }

        return $content;
    }
}
