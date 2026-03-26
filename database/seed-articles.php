#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;
use App\Core\Config;

$city = strtolower((string) (site('city_slug', '') ?: ($_ENV['SITE_CITY_SLUG'] ?? '')));
if ($city === '') {
    $city = 'default';
}

$fixturePath = __DIR__ . '/fixtures/' . $city . '/articles.php';
if (!is_file($fixturePath)) {
    $fixturePath = __DIR__ . '/fixtures/default/articles.php';
}

if (!is_file($fixturePath)) {
    fwrite(STDERR, "Aucune fixture d'articles trouvée pour la ville '{$city}'.\n");
    exit(1);
}

echo "=== Seed articles ({$city}) ===\n\n";

$pdo = Database::connection();
$websiteId = (int) Config::get('website.id', 1);
$articles = require $fixturePath;
$inserted = 0;

$sql = 'INSERT INTO articles (website_id, title, slug, content, meta_title, meta_description, persona, awareness_level, status, created_at)
        VALUES (:website_id, :title, :slug, :content, :meta_title, :meta_description, :persona, :awareness_level, :status, NOW())';
$stmt = $pdo->prepare($sql);

foreach ($articles as $i => $article) {
    echo "  " . ($i + 1) . ". {$article['title']}... ";
    $check = $pdo->prepare('SELECT id FROM articles WHERE website_id = :wid AND slug = :slug LIMIT 1');
    $check->execute([':wid' => $websiteId, ':slug' => $article['slug']]);

    if ($check->fetch()) {
        echo "existe déjà, ignoré\n";
        continue;
    }

    $stmt->execute([
        ':website_id' => $websiteId,
        ':title' => $article['title'],
        ':slug' => $article['slug'],
        ':content' => $article['content'],
        ':meta_title' => $article['meta_title'],
        ':meta_description' => $article['meta_description'],
        ':persona' => $article['persona'],
        ':awareness_level' => $article['awareness_level'],
        ':status' => $article['status'],
    ]);

    echo "OK (ID: " . $pdo->lastInsertId() . ")\n";
    $inserted++;
}

echo "\n=== Résultat : {$inserted} article(s) inséré(s) ===\n";
