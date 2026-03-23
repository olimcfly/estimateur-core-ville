<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    /**
     * Category SEO configuration: clean URL slug → metadata + filter keywords.
     */
    private const CATEGORIES = [
        'marche-immobilier' => [
            'silo_pattern' => '%march%',
            'keyword_patterns' => ['marché immobilier', 'prix immobilier', 'tendance', 'évolution prix', 'marché bordelais'],
            'page_title' => 'Marché immobilier Bordeaux 2026 — Tendances, prix et analyses',
            'meta_description' => 'Suivez l\'évolution du marché immobilier à Bordeaux et sa métropole : prix au m², tendances par quartier, analyses détaillées du marché bordelais actualisées.',
            'h1' => 'Marché immobilier Bordeaux',
            'eyebrow' => 'Analyses &amp; tendances du marché',
            'intro' => 'Le marché immobilier bordelais reste l\'un des plus dynamiques du Sud-Ouest. Retrouvez ici nos analyses approfondies : évolution des prix au m² par quartier, volumes de transactions, impact du DPE sur les valorisations, et perspectives pour les mois à venir. Nos données sont issues des bases notariales et actualisées régulièrement pour vous offrir une vision fiable du marché.',
        ],
        'vendre-son-bien' => [
            'silo_pattern' => '%vendr%',
            'keyword_patterns' => ['vendre', 'vente', 'estimation', 'prix de vente', 'mandat'],
            'page_title' => 'Vendre son bien à Bordeaux — Guides et conseils de vente',
            'meta_description' => 'Tous nos conseils pour vendre votre maison ou appartement à Bordeaux au meilleur prix : estimation, mise en valeur, stratégie de vente, négociation.',
            'h1' => 'Vendre son bien à Bordeaux',
            'eyebrow' => 'Guides de vente immobilière',
            'intro' => 'Vendre un bien immobilier à Bordeaux demande une préparation rigoureuse. Découvrez nos guides complets pour estimer correctement votre bien, préparer votre dossier de vente, fixer le bon prix et négocier efficacement avec les acheteurs. Des conseils adaptés au marché bordelais pour maximiser votre plus-value.',
        ],
        'conseils-astuces' => [
            'silo_pattern' => '%conseil%',
            'keyword_patterns' => ['conseil', 'astuce', 'guide', 'erreur', 'comment'],
            'page_title' => 'Conseils immobiliers Bordeaux — Astuces et bonnes pratiques',
            'meta_description' => 'Conseils pratiques et astuces immobilières pour réussir votre projet à Bordeaux : erreurs à éviter, bonnes pratiques, check-lists et guides pas à pas.',
            'h1' => 'Conseils et astuces immobilières',
            'eyebrow' => 'Conseils pratiques',
            'intro' => 'Que vous soyez vendeur ou acheteur, nos experts partagent leurs meilleurs conseils pour réussir votre projet immobilier à Bordeaux. Des astuces concrètes, les erreurs à éviter et des guides pas à pas pour prendre les bonnes décisions à chaque étape.',
        ],
        'aspect-juridique' => [
            'silo_pattern' => '%juri%',
            'keyword_patterns' => ['juridique', 'loi', 'réglementation', 'dpe', 'diagnostic', 'notaire', 'fiscalité'],
            'page_title' => 'Aspects juridiques immobilier Bordeaux — Réglementation et obligations',
            'meta_description' => 'Décryptage des aspects juridiques de l\'immobilier à Bordeaux : DPE, diagnostics obligatoires, fiscalité, réglementation et obligations légales des vendeurs.',
            'h1' => 'Aspects juridiques de l\'immobilier',
            'eyebrow' => 'Réglementation &amp; droit immobilier',
            'intro' => 'La réglementation immobilière évolue constamment. Retrouvez nos décryptages sur les diagnostics obligatoires (DPE, amiante, plomb), la fiscalité des plus-values, les obligations du vendeur et les dernières évolutions législatives qui impactent le marché immobilier bordelais.',
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
            'page_title' => 'Blog immobilier Bordeaux — Conseils, analyses et guides',
            'meta_description' => 'Découvrez nos articles sur l\'immobilier à Bordeaux : analyses du marché, conseils de vente, guides pratiques et décryptages juridiques pour votre projet immobilier.',
        ]);
    }

    public function category(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $slug = basename($path);

        if (!isset(self::CATEGORIES[$slug])) {
            http_response_code(404);
            echo 'Catégorie introuvable';
            return;
        }

        $cat = self::CATEGORIES[$slug];

        try {
            $articleModel = new Article();
            $articles = $articleModel->findPublishedByCategory(
                $cat['silo_pattern'],
                $cat['keyword_patterns'],
            );
        } catch (\Throwable $e) {
            error_log('Blog category error: ' . $e->getMessage());
            $articles = [];
        }

        View::render('blog/category', [
            'articles' => $articles,
            'category_slug' => $slug,
            'page_title' => $cat['page_title'],
            'meta_description' => $cat['meta_description'],
            'h1' => $cat['h1'],
            'eyebrow' => $cat['eyebrow'],
            'intro' => $cat['intro'],
            'categories' => self::CATEGORIES,
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

        $viewData = [
            'article' => $article,
            'page_title' => !empty($article['meta_title']) ? $article['meta_title'] : $article['title'],
            'meta_description' => $article['meta_description'] ?? '',
        ];

        // Pass article-specific OG data if available
        if (!empty($article['og_title'])) {
            $viewData['og_title'] = $article['og_title'];
        }
        if (!empty($article['og_description'])) {
            $viewData['og_description'] = $article['og_description'];
        }
        if (!empty($article['og_image'])) {
            $viewData['og_image'] = $article['og_image'];
        }

        View::render('blog/show', $viewData);
    }
}
