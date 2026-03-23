<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    private const CATEGORIES = [
        'marche-immobilier' => [
            'title' => 'Marché Immobilier Bordeaux 2026 — Prix, Tendances & Analyses',
            'meta_description' => 'Suivez l\'évolution du marché immobilier à Bordeaux et sa métropole : prix au m², tendances par quartier, volumes de transactions et analyses d\'experts locaux.',
            'h1' => 'Marché immobilier à Bordeaux',
            'intro' => 'Retrouvez nos analyses approfondies du marché immobilier bordelais : évolution des prix au m² par quartier, volumes de transactions, tendances du marché et perspectives. Nos données sont issues des transactions réelles enregistrées sur Bordeaux Métropole et mises à jour régulièrement pour vous offrir une vision fiable et actuelle du marché immobilier local.',
            'og_title' => 'Marché Immobilier Bordeaux — Analyses & Tendances',
        ],
        'vendre-son-bien' => [
            'title' => 'Vendre son bien à Bordeaux — Guides & Stratégies de Vente',
            'meta_description' => 'Conseils d\'experts pour vendre votre maison ou appartement à Bordeaux au meilleur prix : estimation, mise en valeur, négociation et accompagnement personnalisé.',
            'h1' => 'Vendre son bien à Bordeaux',
            'intro' => 'Vous envisagez de vendre votre bien immobilier à Bordeaux ou dans sa métropole ? Découvrez nos guides pratiques pour optimiser votre vente : de l\'estimation initiale à la signature chez le notaire, en passant par la mise en valeur de votre bien et les stratégies de négociation adaptées au marché bordelais.',
            'og_title' => 'Vendre son bien à Bordeaux — Guides & Conseils',
        ],
        'conseils-astuces' => [
            'title' => 'Conseils Immobiliers Bordeaux — Astuces & Bonnes Pratiques',
            'meta_description' => 'Astuces et conseils pratiques pour réussir votre projet immobilier à Bordeaux : erreurs à éviter, préparation, home staging et optimisation de valeur.',
            'h1' => 'Conseils & astuces immobiliers',
            'intro' => 'Profitez de l\'expertise de nos professionnels de l\'immobilier bordelais : conseils pratiques, erreurs courantes à éviter, bonnes pratiques de home staging et astuces pour maximiser la valeur de votre bien. Chaque article est conçu pour vous aider concrètement dans votre projet immobilier à Bordeaux.',
            'og_title' => 'Conseils Immobiliers Bordeaux — Astuces Pratiques',
        ],
        'aspects-juridiques' => [
            'title' => 'Aspects Juridiques Immobilier Bordeaux — DPE, Lois & Fiscalité',
            'meta_description' => 'Décryptage des aspects juridiques de l\'immobilier à Bordeaux : DPE, diagnostics obligatoires, fiscalité immobilière, loi Climat et réglementations locales.',
            'h1' => 'Aspects juridiques de l\'immobilier',
            'intro' => 'Naviguez sereinement dans le cadre juridique de l\'immobilier bordelais : diagnostics obligatoires (DPE, amiante, plomb), fiscalité des plus-values, loi Climat et Résilience, réglementations locales du PLU de Bordeaux Métropole. Nos articles décryptent chaque sujet pour vous permettre de prendre des décisions éclairées.',
            'og_title' => 'Aspects Juridiques Immobilier — Bordeaux',
        ],
    ];

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
            'page_title' => 'Blog Immobilier Bordeaux — Conseils, Analyses & Guides',
            'meta_description' => 'Découvrez nos articles sur l\'immobilier à Bordeaux : analyses du marché, conseils de vente, guides pratiques et actualités par quartier.',
        ]);
    }

    public function category(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $slug = basename($path);
        $categoryConfig = self::CATEGORIES[$slug] ?? null;

        if ($categoryConfig === null) {
            http_response_code(404);
            echo 'Catégorie introuvable';
            return;
        }

        try {
            $articleModel = new Article();
            $articles = $articleModel->findPublishedByCategory($slug);
        } catch (\Throwable $e) {
            error_log('Blog category error: ' . $e->getMessage());
            $articles = [];
        }

        View::render('blog/category', [
            'articles' => $articles,
            'category' => $categoryConfig,
            'category_slug' => $slug,
            'page_title' => $categoryConfig['title'],
            'meta_description' => $categoryConfig['meta_description'],
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

        View::render('blog/show', [
            'article' => $article,
            'page_title' => !empty($article['meta_title']) ? $article['meta_title'] : $article['title'],
            'meta_description' => $article['meta_description'],
        ]);
    }
}
