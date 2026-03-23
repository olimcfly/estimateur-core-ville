<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\View;
use App\Services\GoogleSearchConsoleService;

final class AdminSeoHubController
{
    /**
     * SEO Hub dashboard — shows cached keywords or connection prompt.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();

        $connected = $gsc->isConnected($websiteId);
        $connection = $connected ? $gsc->getConnection($websiteId) : null;
        $cacheInfo = $connected ? $gsc->getCacheInfo($websiteId) : null;

        $sort = $_GET['sort'] ?? 'clicks';
        $order = $_GET['order'] ?? 'DESC';
        $search = trim((string) ($_GET['q'] ?? ''));

        $keywords = [];
        if ($connected) {
            $keywords = $gsc->getCachedKeywords($websiteId, $sort, $order, 500);

            if ($search !== '') {
                $keywords = array_filter($keywords, function ($kw) use ($search) {
                    return stripos($kw['keyword'], $search) !== false;
                });
                $keywords = array_values($keywords);
            }
        }

        $hasCredentials = $this->hasGscCredentials();

        View::renderAdmin('admin/seo-hub/index', [
            'page_title' => 'SEO Hub - Google Search Console',
            'admin_page_title' => 'SEO Hub',
            'admin_page' => 'seo-hub',
            'connected' => $connected,
            'connection' => $connection,
            'cache_info' => $cacheInfo,
            'keywords' => $keywords,
            'sort' => $sort,
            'order' => $order,
            'search' => $search,
            'has_credentials' => $hasCredentials,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    /**
     * Redirect to Google OAuth2.
     */
    public function connect(): void
    {
        AuthController::requireAuth();

        if (!$this->hasGscCredentials()) {
            header('Location: /admin/seo-hub?error=' . urlencode('Configurez GSC_CLIENT_ID et GSC_CLIENT_SECRET dans votre fichier .env'));
            exit;
        }

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();
        $authUrl = $gsc->getAuthUrl($websiteId);

        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * OAuth2 callback — exchange code for tokens.
     */
    public function callback(): void
    {
        AuthController::requireAuth();

        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $errorParam = $_GET['error'] ?? '';

        if ($errorParam !== '') {
            header('Location: /admin/seo-hub?error=' . urlencode('Autorisation refusée : ' . $errorParam));
            exit;
        }

        if ($code === '') {
            header('Location: /admin/seo-hub?error=' . urlencode('Code d\'autorisation manquant.'));
            exit;
        }

        $stateData = json_decode(base64_decode($state), true);
        $websiteId = (int) ($stateData['website_id'] ?? Config::get('website.id', 1));

        $gsc = new GoogleSearchConsoleService();

        try {
            $tokens = $gsc->exchangeCode($code);

            // List sites to let user pick, or auto-select first
            $sites = $gsc->listSites($tokens['access_token']);

            if (empty($sites)) {
                header('Location: /admin/seo-hub?error=' . urlencode('Aucun site vérifié trouvé dans votre Google Search Console.'));
                exit;
            }

            // Store in session for site selection
            $_SESSION['gsc_tokens'] = $tokens;
            $_SESSION['gsc_sites'] = $sites;

            header('Location: /admin/seo-hub/select-site');
            exit;
        } catch (\Throwable $e) {
            error_log('[seo-hub] OAuth callback error: ' . $e->getMessage());
            header('Location: /admin/seo-hub?error=' . urlencode('Erreur OAuth : ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Site selection page after OAuth.
     */
    public function selectSite(): void
    {
        AuthController::requireAuth();

        $tokens = $_SESSION['gsc_tokens'] ?? null;
        $sites = $_SESSION['gsc_sites'] ?? [];

        if (!$tokens || empty($sites)) {
            header('Location: /admin/seo-hub?error=' . urlencode('Session expirée, reconnectez-vous.'));
            exit;
        }

        View::renderAdmin('admin/seo-hub/select-site', [
            'page_title' => 'Sélection du site - SEO Hub',
            'admin_page_title' => 'SEO Hub - Sélection du site',
            'admin_page' => 'seo-hub',
            'sites' => $sites,
        ]);
    }

    /**
     * Confirm site selection and save tokens.
     */
    public function confirmSite(): void
    {
        AuthController::requireAuth();

        $tokens = $_SESSION['gsc_tokens'] ?? null;
        $siteUrl = $_POST['site_url'] ?? '';

        if (!$tokens || $siteUrl === '') {
            header('Location: /admin/seo-hub?error=' . urlencode('Données manquantes.'));
            exit;
        }

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();

        try {
            $gsc->saveTokens($websiteId, $siteUrl, $tokens);
            unset($_SESSION['gsc_tokens'], $_SESSION['gsc_sites']);

            // Auto-fetch keywords
            $count = $gsc->refreshKeywordsCache($websiteId);

            header('Location: /admin/seo-hub?message=' . urlencode("Connecté avec succès ! {$count} mots-clés importés."));
            exit;
        } catch (\Throwable $e) {
            error_log('[seo-hub] confirmSite error: ' . $e->getMessage());
            header('Location: /admin/seo-hub?error=' . urlencode('Erreur : ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Refresh keywords cache.
     */
    public function refresh(): void
    {
        AuthController::requireAuth();

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();

        try {
            $count = $gsc->refreshKeywordsCache($websiteId);
            header('Location: /admin/seo-hub?message=' . urlencode("{$count} mots-clés mis à jour depuis Google Search Console."));
        } catch (\Throwable $e) {
            error_log('[seo-hub] refresh error: ' . $e->getMessage());
            header('Location: /admin/seo-hub?error=' . urlencode('Erreur lors du rafraîchissement : ' . $e->getMessage()));
        }
        exit;
    }

    /**
     * Disconnect GSC.
     */
    public function disconnect(): void
    {
        AuthController::requireAuth();

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();
        $gsc->disconnect($websiteId);

        header('Location: /admin/seo-hub?message=' . urlencode('Google Search Console déconnecté.'));
        exit;
    }

    /**
     * API endpoint: fetch keywords as JSON (for AJAX).
     */
    public function apiKeywords(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();

        $sort = $_GET['sort'] ?? 'clicks';
        $order = $_GET['order'] ?? 'DESC';
        $limit = min((int) ($_GET['limit'] ?? 100), 500);

        $keywords = $gsc->getCachedKeywords($websiteId, $sort, $order, $limit);
        $cacheInfo = $gsc->getCacheInfo($websiteId);

        echo json_encode([
            'success' => true,
            'keywords' => $keywords,
            'cache_info' => $cacheInfo,
        ]);
        exit;
    }

    /**
     * API endpoint: fetch live keyword-by-page data.
     */
    public function apiKeywordsByPage(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $websiteId = (int) Config::get('website.id', 1);
        $gsc = new GoogleSearchConsoleService();

        try {
            $data = $gsc->fetchKeywordsByPage($websiteId, 28, 200);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    private function hasGscCredentials(): bool
    {
        $clientId = (string) Config::get('google_search_console.client_id');
        $clientSecret = (string) Config::get('google_search_console.client_secret');
        return $clientId !== '' && $clientSecret !== '';
    }
}
