<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\RssArticle;
use App\Models\RssSource;
use App\Services\RssFeedService;

final class AdminRssController
{
    /**
     * RSS dashboard: sources list + recent articles.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $sources = [];
        $articles = [];
        $generationLogs = [];
        $dbError = null;
        $filter = $_GET['filter'] ?? 'all';
        $sourceFilter = isset($_GET['source']) ? (int) $_GET['source'] : null;

        try {
            $sourceModel = new RssSource();
            $articleModel = new RssArticle();
            $sources = $sourceModel->findAll();

            $starred = $filter === 'starred' ? true : null;
            $used = $filter === 'used' ? true : ($filter === 'unused' ? false : null);

            $articles = $articleModel->findAll(50, 0, $sourceFilter, $starred, $used);
            $generationLogs = $articleModel->getGenerationLogs(10);
        } catch (\Throwable $e) {
            error_log('RSS index error: ' . $e->getMessage());
            $dbError = 'Tables RSS manquantes. Executez la migration : php database/migrate.php';
        }

        View::renderAdmin('admin/rss/index', [
            'sources' => $sources,
            'articles' => $articles,
            'generationLogs' => $generationLogs,
            'filter' => $filter,
            'sourceFilter' => $sourceFilter,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => $dbError ?? (string) ($_GET['error'] ?? ''),
            'page_title' => 'Flux RSS - Admin',
            'admin_page_title' => 'Veille RSS',
            'admin_page' => 'rss',
            'breadcrumb' => 'Flux RSS',
        ]);
    }

    /**
     * Manage RSS sources.
     */
    public function sources(): void
    {
        AuthController::requireAuth();

        $sourceModel = new RssSource();
        $sources = $sourceModel->findAll();

        View::renderAdmin('admin/rss/sources', [
            'sources' => $sources,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'page_title' => 'Sources RSS - Admin',
            'admin_page_title' => 'Sources RSS',
            'admin_page' => 'rss',
            'breadcrumb' => 'Sources RSS',
        ]);
    }

    /**
     * Add a new RSS source.
     */
    public function addSource(): void
    {
        AuthController::requireAuth();

        $name = trim((string) ($_POST['name'] ?? ''));
        $feedUrl = trim((string) ($_POST['feed_url'] ?? ''));
        $siteUrl = trim((string) ($_POST['site_url'] ?? ''));
        $category = trim((string) ($_POST['category'] ?? 'general'));
        $zone = trim((string) ($_POST['zone'] ?? 'national'));

        if ($name === '' || $feedUrl === '') {
            $this->redirect('/admin/rss/sources?error=' . urlencode('Nom et URL du flux sont obligatoires.'));
            return;
        }

        $sourceModel = new RssSource();

        try {
            $sourceModel->create([
                'name' => $name,
                'feed_url' => $feedUrl,
                'site_url' => $siteUrl !== '' ? $siteUrl : null,
                'category' => $category,
                'zone' => $zone,
            ]);
            $this->redirect('/admin/rss/sources?message=' . urlencode('Source ajoutee avec succes.'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/rss/sources?error=' . urlencode('Erreur : ' . $e->getMessage()));
        }
    }

    /**
     * Delete an RSS source.
     */
    public function deleteSource(string $id): void
    {
        AuthController::requireAuth();

        $sourceModel = new RssSource();
        $sourceModel->delete((int) $id);
        $this->redirect('/admin/rss/sources?message=' . urlencode('Source supprimee.'));
    }

    /**
     * Toggle source active/inactive.
     */
    public function toggleSource(string $id): void
    {
        AuthController::requireAuth();

        $sourceModel = new RssSource();
        $sourceModel->toggleActive((int) $id);
        $this->redirect('/admin/rss/sources?message=' . urlencode('Statut mis a jour.'));
    }

    /**
     * Fetch all active RSS feeds.
     */
    public function fetchAll(): void
    {
        AuthController::requireAuth();

        $service = new RssFeedService();
        $results = $service->fetchAllFeeds();

        $totalNew = 0;
        $errors = [];
        foreach ($results as $r) {
            $totalNew += $r['result']['new_articles'] ?? 0;
            if (!($r['result']['success'] ?? false)) {
                $errors[] = $r['source_name'] . ': ' . ($r['result']['error'] ?? 'Erreur inconnue');
            }
        }

        $msg = "{$totalNew} nouveaux articles recuperes depuis " . count($results) . " flux.";
        if (!empty($errors)) {
            $msg .= ' Erreurs : ' . implode(' | ', $errors);
        }

        $this->redirect('/admin/rss?message=' . urlencode($msg));
    }

    /**
     * Fetch a single RSS feed.
     */
    public function fetchOne(string $id): void
    {
        AuthController::requireAuth();

        $sourceModel = new RssSource();
        $source = $sourceModel->findById((int) $id);

        if ($source === null) {
            $this->redirect('/admin/rss/sources?error=' . urlencode('Source introuvable.'));
            return;
        }

        $service = new RssFeedService();
        $result = $service->fetchFeed($source);

        if ($result['success']) {
            $msg = "Flux \"{$source['name']}\" : {$result['new_articles']} nouveaux articles sur {$result['total_items']} trouves.";
            $this->redirect('/admin/rss/sources?message=' . urlencode($msg));
        } else {
            $this->redirect('/admin/rss/sources?error=' . urlencode("Erreur flux \"{$source['name']}\" : " . ($result['error'] ?? '')));
        }
    }

    /**
     * Toggle starred status on an article.
     */
    public function toggleStar(string $id): void
    {
        AuthController::requireAuth();

        $articleModel = new RssArticle();
        $articleModel->toggleStarred((int) $id);

        // Return JSON for AJAX calls
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Generate a blog article from selected RSS articles.
     */
    public function generate(): void
    {
        AuthController::requireAuth();

        $ids = $_POST['article_ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            $this->redirect('/admin/rss?error=' . urlencode('Selectionnez au moins un article RSS.'));
            return;
        }

        $ids = array_map('intval', $ids);
        $service = new RssFeedService();
        $result = $service->generateBlogArticle($ids);

        if (!$result['success']) {
            $this->redirect('/admin/rss?error=' . urlencode($result['error'] ?? 'Erreur generation.'));
            return;
        }

        $article = $result['article'];

        // Build source citation for the article
        $sourcesCitation = '';
        foreach ($result['rss_articles'] as $rssArt) {
            $sourcesCitation .= '<li><a href="' . htmlspecialchars($rssArt['link'], ENT_QUOTES, 'UTF-8')
                . '" target="_blank" rel="noopener">'
                . htmlspecialchars($rssArt['source_name'] ?? '', ENT_QUOTES, 'UTF-8')
                . ' : ' . htmlspecialchars($rssArt['title'], ENT_QUOTES, 'UTF-8')
                . '</a> (' . htmlspecialchars($rssArt['pub_date'] ?? '', ENT_QUOTES, 'UTF-8') . ')</li>';
        }

        View::renderAdmin('admin/rss/generated', [
            'article' => $article,
            'rss_articles' => $result['rss_articles'],
            'sources_citation' => $sourcesCitation,
            'page_title' => 'Article genere depuis RSS - Admin',
            'admin_page_title' => 'Article genere depuis RSS',
            'admin_page' => 'rss',
            'breadcrumb' => 'Article genere',
        ]);
    }

    /**
     * Seed default RSS sources.
     */
    public function seed(): void
    {
        AuthController::requireAuth();

        $sourceModel = new RssSource();
        $existing = $sourceModel->findAll();

        if (!empty($existing)) {
            $this->redirect('/admin/rss/sources?message=' . urlencode('Des sources existent deja. Ajoutez-les manuellement.'));
            return;
        }

        $feeds = $this->getDefaultFeeds();
        $count = 0;

        foreach ($feeds as $feed) {
            try {
                $sourceModel->create($feed);
                $count++;
            } catch (\Throwable $e) {
                error_log('RSS seed error: ' . $e->getMessage());
            }
        }

        $this->redirect('/admin/rss/sources?message=' . urlencode("{$count} sources RSS ajoutees avec succes."));
    }

    private function getDefaultFeeds(): array
    {
        return [
            // National - Medias economiques (source: atlasflux.saynete.net)
            ['name' => 'Le Figaro Immobilier', 'feed_url' => 'https://www.lefigaro.fr/rss/figaro_immobilier.xml', 'site_url' => 'https://www.lefigaro.fr', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'Le Monde Immobilier', 'feed_url' => 'https://www.lemonde.fr/immobilier/rss_full.xml', 'site_url' => 'https://www.lemonde.fr', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'Le Monde Logement', 'feed_url' => 'https://www.lemonde.fr/logement/rss_full.xml', 'site_url' => 'https://www.lemonde.fr', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'BFM Immobilier', 'feed_url' => 'https://www.bfmtv.com/rss/immobilier/', 'site_url' => 'https://www.bfmtv.com', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'France Info Immobilier', 'feed_url' => 'https://www.franceinfo.fr/economie/immobilier.rss', 'site_url' => 'https://www.franceinfo.fr', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'La Croix Immobilier', 'feed_url' => 'https://www.la-croix.com/feeds/rss/economie/immobilier.xml', 'site_url' => 'https://www.la-croix.com', 'category' => 'medias-economiques', 'zone' => 'national'],
            ['name' => 'AGEFI Immobilier', 'feed_url' => 'https://www.agefi.fr/theme/immobilier/.rss', 'site_url' => 'https://www.agefi.fr', 'category' => 'investissement', 'zone' => 'national'],

            // National - Portails & specialises immobiliers
            ['name' => 'SeLoger Edito', 'feed_url' => 'https://edito.seloger.com/rss.xml', 'site_url' => 'https://edito.seloger.com', 'category' => 'actualites-immo', 'zone' => 'national'],
            ['name' => 'Logic Immo Actualites', 'feed_url' => 'https://actualites.logic-immo.com/actus-rss.xml', 'site_url' => 'https://www.logic-immo.com', 'category' => 'actualites-immo', 'zone' => 'national'],
            ['name' => 'Journal de l\'Agence', 'feed_url' => 'https://www.journaldelagence.com/feed', 'site_url' => 'https://www.journaldelagence.com', 'category' => 'actualites-immo', 'zone' => 'national'],
            ['name' => 'Mon Immeuble', 'feed_url' => 'https://monimmeuble.com/feed', 'site_url' => 'https://monimmeuble.com', 'category' => 'actualites-immo', 'zone' => 'national'],

            // National - Investissement / Neuf / Defiscalisation
            ['name' => 'Medicis Immobilier Neuf', 'feed_url' => 'https://www.medicis-patrimoine.com/feed/', 'site_url' => 'https://www.medicis-patrimoine.com', 'category' => 'neuf-defiscalisation', 'zone' => 'national'],

            // National - Institutionnel / Logement / Construction
            ['name' => 'Les Notaires Immobilier', 'feed_url' => 'https://www.notaires.fr/fr/rss/thematique?id_thematique=4101', 'site_url' => 'https://www.notaires.fr', 'category' => 'institutionnel', 'zone' => 'national'],
            ['name' => 'UNIS Immobilier', 'feed_url' => 'https://www.unis-immo.fr/feed/', 'site_url' => 'https://www.unis-immo.fr', 'category' => 'institutionnel', 'zone' => 'national'],
            ['name' => 'BatInfo', 'feed_url' => 'https://batinfo.com/actualite/rss', 'site_url' => 'https://batinfo.com', 'category' => 'construction-urbanisme', 'zone' => 'national'],
            ['name' => 'Batirama', 'feed_url' => 'https://www.batirama.com/rss/2-l-info-actualites.html', 'site_url' => 'https://www.batirama.com', 'category' => 'construction-urbanisme', 'zone' => 'national'],

            // National - Agregateurs actualites immo
            ['name' => 'Google Actu Immobilier', 'feed_url' => 'https://news.google.com/rss/search?q=immobilier+france&hl=fr&gl=FR&ceid=FR:fr', 'site_url' => 'https://news.google.com', 'category' => 'actualites-immo', 'zone' => 'national'],

            // Presse locale — chargée depuis database/fixtures/{city_slug}/rss_sources.php
            ...($this->loadLocalRssSources()),
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');
        return $text !== '' ? $text : 'article';
    }

    /**
     * Load city-specific local RSS sources from database/fixtures/{city_slug}/rss_sources.php.
     * Falls back to database/fixtures/default/rss_sources.php if no city fixture exists.
     *
     * @return array<int, array<string, string>>
     */
    private function loadLocalRssSources(): array
    {
        $citySlug = (string) site('city_slug', 'default');
        $fixture  = base_path('database/fixtures/' . $citySlug . '/rss_sources.php');
        if (!is_file($fixture)) {
            $fixture = base_path('database/fixtures/default/rss_sources.php');
        }
        return is_file($fixture) ? (array) require $fixture : [];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
