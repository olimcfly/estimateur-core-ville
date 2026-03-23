#!/usr/bin/env php
<?php

/**
 * Automated weekly actualité generator (RSS-based).
 *
 * This script:
 * 1. Collects recent RSS articles
 * 2. Filters them by AI config (local priority, no agencies)
 * 3. Selects the best candidates via AI
 * 4. Generates a full actualité article
 * 5. Generates an AI image
 * 6. Publishes the article automatically (or as draft per config)
 *
 * Usage:
 *   php cron/generate-actualite.php [--source=rss|perplexity] [--query="custom search query"] [--dry-run]
 *
 * Crontab (weekly, every Monday at 8am):
 *   0 8 * * 1 /usr/bin/php /path/to/cron/generate-actualite.php >> /var/log/actualites-cron.log 2>&1
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Models\Actualite;
use App\Models\ActualiteAiConfig;
use App\Models\RssArticle;
use App\Services\ActualiteService;

echo "[" . date('Y-m-d H:i:s') . "] === Génération automatique d'actualité ===\n";

// Parse CLI arguments
$dryRun = in_array('--dry-run', $argv ?? [], true);
$customQuery = null;
$source = 'rss'; // Default to RSS pipeline

foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--query=')) {
        $customQuery = substr($arg, 8);
    }
    if (str_starts_with($arg, '--source=')) {
        $source = substr($arg, 9);
    }
}

$service = new ActualiteService();
$model = new Actualite();

try {
    if ($source === 'rss') {
        echo "  Mode: Pipeline RSS + IA\n";
        $result = $service->runRssPipeline();
    } else {
        echo "  Mode: Pipeline Perplexity\n";
        $result = $service->runAutomatedPipeline($customQuery);
    }

    if (!($result['success'] ?? false)) {
        $error = $result['error'] ?? 'Erreur inconnue';
        echo "  ERREUR: {$error}\n";
        $model->logCron($result['query'] ?? $source, 0, null, 'error', $error);
        exit(1);
    }

    $article = $result['article'];
    $ideasCount = $result['ideas_count'] ?? 0;
    $query = $result['query'] ?? $source;

    echo "  Source: {$query}\n";
    echo "  Articles analysés: {$ideasCount}\n";
    echo "  Article: {$article['title']}\n";
    echo "  Image: " . ($article['image_url'] ?? 'aucune') . "\n";

    if ($dryRun) {
        echo "\n  [DRY RUN] Article non sauvegardé.\n";
        echo "  Titre: {$article['title']}\n";
        echo "  Extrait: {$article['excerpt']}\n";
        exit(0);
    }

    // Check auto_publish config
    $configModel = new ActualiteAiConfig();
    $autoPublish = $configModel->get('auto_publish', '0') === '1';
    $status = $autoPublish ? 'published' : 'draft';

    // Save to database
    $articleId = $model->create([
        'title' => $article['title'],
        'slug' => slugify($article['title']),
        'content' => $article['content'],
        'excerpt' => $article['excerpt'],
        'meta_title' => $article['meta_title'],
        'meta_description' => $article['meta_description'],
        'image_url' => $article['image_url'],
        'image_prompt' => $article['image_prompt'],
        'source_query' => $article['source_query'],
        'source_results' => $result['source_results'] ?? null,
        'status' => $status,
        'generated_by' => 'cron',
    ]);

    echo "  Article " . ($autoPublish ? 'publié' : 'sauvegardé en brouillon') . " avec ID: {$articleId}\n";

    // Mark RSS articles as used
    if ($source === 'rss' && !empty($result['rss_article_ids'])) {
        $articleModel = new RssArticle();
        foreach ($result['rss_article_ids'] as $rssId) {
            $articleModel->markAsUsedForActualite((int) $rssId, $articleId);
        }
        echo "  " . count($result['rss_article_ids']) . " articles RSS marqués comme utilisés.\n";
    }

    // Log success
    $model->logCron($query, $ideasCount, $articleId, 'success');

    echo "[" . date('Y-m-d H:i:s') . "] === Terminé avec succès ===\n\n";

} catch (\Throwable $e) {
    echo "  EXCEPTION: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    try {
        $model->logCron($customQuery ?? $source, 0, null, 'error', $e->getMessage());
    } catch (\Throwable) {
        // Ignore logging errors
    }

    exit(1);
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
    $text = trim($text, '-');
    return $text !== '' ? $text : 'actualite';
}
