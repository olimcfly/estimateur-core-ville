<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    public function index(): void
    {
        try {
            $articleModel = new Article();
            $articles = $articleModel->findPublished();
        } catch (\Throwable $e) {
            error_log('Blog index error: ' . $e->getMessage());
            $articles = [];
        }

        View::render('blog/index', [
            'articles' => $articles,
            'page_title' => 'Blog Immobilier Bordeaux | Conseils, Prix et Tendances du Marché',
            'meta_description' => 'Conseils immobiliers, analyses du marché bordelais, guides pratiques pour vendre ou acheter à Bordeaux et Métropole. Articles d\'experts locaux.',
        ]);
    }

    public function show(string $slug): void
    {
        try {
            $articleModel = new Article();
            $article = $articleModel->findBySlug($slug);
        } catch (\Throwable $e) {
            error_log('Blog show error: ' . $e->getMessage());
            $article = null;
        }

        if ($article === null) {
            http_response_code(404);
            echo 'Article introuvable';
            return;
        }

        // Track page view
        try {
            $articleModel->incrementPageViews((int) $article['id']);
        } catch (\Throwable $e) {
            error_log('Page view tracking error: ' . $e->getMessage());
        }

        View::render('blog/show', ['article' => $article]);
    }
}
