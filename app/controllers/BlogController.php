<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;

final class BlogController
{
    /**
     * Category SEO configuration: clean URL slug -> metadata + filter keywords.
     */
    private const CATEGORIES = [
        'marche-immobilier' => [
            'silo_pattern' => '%march%',
            'keyword_patterns' => ['marche immobilier', 'prix immobilier', 'tendance', 'evolution prix', 'marche bordelais'],
            'page_title' => 'Marche immobilier Bordeaux 2026 — Tendances, prix et analyses',
            'meta_description' => 'Suivez l\'evolution du marche immobilier a Bordeaux et sa metropole : prix au m2, tendances par quartier, analyses detaillees du marche bordelais actualisees.',
            'h1' => 'Marche immobilier Bordeaux',
            'eyebrow' => 'Analyses &amp; tendances du marche',
            'intro' => 'Le marche immobilier bordelais reste l\'un des plus dynamiques du Sud-Ouest. Retrouvez ici nos analyses approfondies : evolution des prix au m2 par quartier, volumes de transactions, impact du DPE sur les valorisations, et perspectives pour les mois a venir. Nos donnees sont issues des bases notariales et actualisees regulierement pour vous offrir une vision fiable du marche.',
        ],
        'vendre-son-bien' => [
            'silo_pattern' => '%vendr%',
            'keyword_patterns' => ['vendre', 'vente', 'estimation', 'prix de vente', 'mandat'],
            'page_title' => 'Vendre son bien a Bordeaux — Guides et conseils de vente',
            'meta_description' => 'Tous nos conseils pour vendre votre maison ou appartement a Bordeaux au meilleur prix : estimation, mise en valeur, strategie de vente, negociation.',
            'h1' => 'Vendre son bien a Bordeaux',
            'eyebrow' => 'Guides de vente immobiliere',
            'intro' => 'Vendre un bien immobilier a Bordeaux demande une preparation rigoureuse. Decouvrez nos guides complets pour estimer correctement votre bien, preparer votre dossier de vente, fixer le bon prix et negocier efficacement avec les acheteurs. Des conseils adaptes au marche bordelais pour maximiser votre plus-value.',
        ],
        'conseils-astuces' => [
            'silo_pattern' => '%conseil%',
            'keyword_patterns' => ['conseil', 'astuce', 'guide', 'erreur', 'comment'],
            'page_title' => 'Conseils immobiliers Bordeaux — Astuces et bonnes pratiques',
            'meta_description' => 'Conseils pratiques et astuces immobilieres pour reussir votre projet a Bordeaux : erreurs a eviter, bonnes pratiques, check-lists et guides pas a pas.',
            'h1' => 'Conseils et astuces immobilieres',
            'eyebrow' => 'Conseils pratiques',
            'intro' => 'Que vous soyez vendeur ou acheteur, nos experts partagent leurs meilleurs conseils pour reussir votre projet immobilier a Bordeaux. Des astuces concretes, les erreurs a eviter et des guides pas a pas pour prendre les bonnes decisions a chaque etape.',
        ],
        'aspect-juridique' => [
            'silo_pattern' => '%juri%',
            'keyword_patterns' => ['juridique', 'loi', 'reglementation', 'dpe', 'diagnostic', 'notaire', 'fiscalite'],
            'page_title' => 'Aspects juridiques immobilier Bordeaux — Reglementation et obligations',
            'meta_description' => 'Decryptage des aspects juridiques de l\'immobilier a Bordeaux : DPE, diagnostics obligatoires, fiscalite, reglementation et obligations legales des vendeurs.',
            'h1' => 'Aspects juridiques de l\'immobilier',
            'eyebrow' => 'Reglementation &amp; droit immobilier',
            'intro' => 'La reglementation immobiliere evolue constamment. Retrouvez nos decryptages sur les diagnostics obligatoires (DPE, amiante, plomb), la fiscalite des plus-values, les obligations du vendeur et les dernieres evolutions legislatives qui impactent le marche immobilier bordelais.',
        ],
        'aspects-juridiques' => [
            'silo_pattern' => '%juri%',
            'keyword_patterns' => ['juridique', 'loi', 'reglementation', 'dpe', 'diagnostic', 'notaire', 'fiscalite'],
            'page_title' => 'Aspects juridiques immobilier Bordeaux — Reglementation et obligations',
            'meta_description' => 'Decryptage des aspects juridiques de l\'immobilier a Bordeaux : DPE, diagnostics obligatoires, fiscalite, reglementation et obligations legales des vendeurs.',
            'h1' => 'Aspects juridiques de l\'immobilier',
            'eyebrow' => 'Reglementation &amp; droit immobilier',
            'intro' => 'La reglementation immobiliere evolue constamment. Retrouvez nos decryptages sur les diagnostics obligatoires (DPE, amiante, plomb), la fiscalite des plus-values, les obligations du vendeur et les dernieres evolutions legislatives qui impactent le marche immobilier bordelais.',
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
            'meta_description' => 'Decouvrez nos articles sur l\'immobilier a Bordeaux : analyses du marche, conseils de vente, guides pratiques et actualites par quartier.',
        ]);
    }

    public function category(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $slug = basename($path);

        if (!isset(self::CATEGORIES[$slug])) {
            http_response_code(404);
            echo 'Categorie introuvable';
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
