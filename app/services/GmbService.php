<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GmbPublication;

final class GmbService
{
    /**
     * Generate a GMB publication from a blog article.
     *
     * @param array $article Article data (must contain id, title, slug, meta_description, content)
     * @return int The created GMB publication ID
     */
    public static function generateFromArticle(array $article): int
    {
        $gmbModel = new GmbPublication();

        $title = mb_substr((string) ($article['title'] ?? ''), 0, 58);
        $content = self::buildContent($article);
        $ctaType = $gmbModel->getSetting('default_cta_type', 'learn_more');
        $ctaUrl = '/blog/' . ($article['slug'] ?? '');
        $scheduledAt = $gmbModel->getNextAvailableSlot();

        return $gmbModel->create([
            'article_id' => (int) $article['id'],
            'actualite_id' => null,
            'post_type' => 'update',
            'title' => $title,
            'content' => $content,
            'cta_type' => $ctaType,
            'cta_url' => $ctaUrl,
            'image_path' => $article['og_image'] ?? null,
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Generate a GMB publication from an actualité.
     *
     * @param array $actualite Actualite data (must contain id, title, slug, excerpt, content)
     * @return int The created GMB publication ID
     */
    public static function generateFromActualite(array $actualite): int
    {
        $gmbModel = new GmbPublication();

        $title = mb_substr((string) ($actualite['title'] ?? ''), 0, 58);
        $content = self::buildContentFromActualite($actualite);
        $ctaType = $gmbModel->getSetting('default_cta_type', 'learn_more');
        $ctaUrl = '/actualites/' . ($actualite['slug'] ?? '');
        $scheduledAt = $gmbModel->getNextAvailableSlot();

        return $gmbModel->create([
            'article_id' => null,
            'actualite_id' => (int) $actualite['id'],
            'post_type' => 'update',
            'title' => $title,
            'content' => $content,
            'cta_type' => $ctaType,
            'cta_url' => $ctaUrl,
            'image_path' => $actualite['image_url'] ?? null,
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Build GMB post content from an article.
     * GMB posts are limited to ~1500 chars, so we create a concise summary.
     */
    private static function buildContent(array $article): string
    {
        $description = trim((string) ($article['meta_description'] ?? ''));
        if ($description !== '') {
            return $description;
        }

        // Fallback: extract from content
        $content = strip_tags((string) ($article['content'] ?? ''));
        $content = preg_replace('/\s+/', ' ', $content) ?? $content;

        return mb_substr(trim($content), 0, 300) . '...';
    }

    /**
     * Build GMB post content from an actualité.
     */
    private static function buildContentFromActualite(array $actualite): string
    {
        $excerpt = trim((string) ($actualite['excerpt'] ?? ''));
        if ($excerpt !== '') {
            return mb_substr($excerpt, 0, 1500);
        }

        $description = trim((string) ($actualite['meta_description'] ?? ''));
        if ($description !== '') {
            return $description;
        }

        $content = strip_tags((string) ($actualite['content'] ?? ''));
        $content = preg_replace('/\s+/', ' ', $content) ?? $content;

        return mb_substr(trim($content), 0, 300) . '...';
    }
}
