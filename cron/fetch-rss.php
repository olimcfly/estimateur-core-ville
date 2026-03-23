<?php

/**
 * Cron job: Fetch all active RSS feeds.
 *
 * Usage:
 *   php cron/fetch-rss.php
 *
 * Recommended cron schedule (every 4 hours):
 *   0 0,4,8,12,16,20 * * * php /path/to/cron/fetch-rss.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Services\RssFeedService;

echo "=== RSS Feed Fetch - " . date('Y-m-d H:i:s') . " ===\n\n";

$service = new RssFeedService();
$results = $service->fetchAllFeeds();

$totalNew = 0;
$errors = 0;

foreach ($results as $r) {
    $status = $r['result']['success'] ? 'OK' : 'ERREUR';
    $new = $r['result']['new_articles'] ?? 0;
    $totalNew += $new;

    echo "  [{$status}] {$r['source_name']} : {$new} nouveaux articles";
    if (!$r['result']['success']) {
        echo " - " . ($r['result']['error'] ?? 'Erreur inconnue');
        $errors++;
    }
    echo "\n";
}

echo "\n=== Resultat : {$totalNew} nouveaux articles, " . count($results) . " flux traites, {$errors} erreurs ===\n";
