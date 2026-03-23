<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Validator;
use App\Core\View;
use App\Models\Article;
use App\Services\AIService;
use App\Services\SeoAnalyzerService;

final class AdminBlogController
{
    public function index(): void
    {
        AuthController::requireAuth();

        try {
            $this->ensureArticlesSchema();

            $articleModel = new Article();
            $articles = $articleModel->findAll();
            $silos = $articleModel->findAllSilos();
            $stats = $articleModel->getSeoStats();
        } catch (\Throwable $e) {
            error_log('[blog] index error: ' . $e->getMessage());
            View::renderAdmin('admin/blog/index', [
                'page_title' => 'Blog SEO - Admin',
                'admin_page_title' => 'Blog / CMS SEO',
                'admin_page' => 'blog',
                'articles' => [],
                'silos' => [],
                'stats' => [],
                'message' => '',
                'error' => 'Erreur de base de données : ' . $e->getMessage(),
            ]);
            return;
        }

        View::renderAdmin('admin/blog/index', [
            'page_title' => 'Blog SEO - Admin',
            'admin_page_title' => 'Blog / CMS SEO',
            'admin_page' => 'blog',
            'articles' => $articles,
            'silos' => $silos,
            'stats' => $stats,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    /**
     * Step 1: Article creation wizard - Questionnaire.
     */
    public function wizard(): void
    {
        AuthController::requireAuth();

        $this->ensureArticlesSchema();
        $articleModel = new Article();
        $silos = $articleModel->findAllSilos();

        View::renderAdmin('admin/blog/wizard', [
            'admin_page_title' => 'Nouvel article - Assistant SEO',
            'admin_page' => 'blog',
            'silos' => $silos,
        ]);
    }

    /**
     * Step 2: Process wizard and generate AI draft.
     */
    public function wizardGenerate(): void
    {
        AuthController::requireAuth();

        try {
            $persona = Validator::string($_POST, 'persona', 3, 100);
            $awarenessLevel = Validator::string($_POST, 'awareness_level', 3, 50);
            $topic = Validator::string($_POST, 'topic', 5, 180);
            $focusKeyword = trim((string) ($_POST['focus_keyword'] ?? ''));
            $secondaryKeywords = trim((string) ($_POST['secondary_keywords'] ?? ''));
            $targetAudience = trim((string) ($_POST['target_audience'] ?? ''));
            $articleGoal = trim((string) ($_POST['article_goal'] ?? ''));
            $articleType = trim((string) ($_POST['article_type'] ?? 'standalone'));
            $siloId = !empty($_POST['silo_id']) ? (int) $_POST['silo_id'] : null;

            $service = new AIService();
            $generated = $service->generateArticle($persona, $awarenessLevel, $topic);

            $articleData = [
                'title' => $generated['title'],
                'slug' => $this->slugify($generated['title']),
                'content' => $generated['content'],
                'meta_title' => $generated['meta_title'],
                'meta_description' => $generated['meta_description'],
                'persona' => $persona,
                'awareness_level' => $awarenessLevel,
                'focus_keyword' => $focusKeyword,
                'secondary_keywords' => $secondaryKeywords,
                'h1_tag' => $generated['title'],
                'target_audience' => $targetAudience,
                'article_goal' => $articleGoal,
                'article_type' => $articleType,
                'silo_id' => $siloId,
                'status' => 'draft',
            ];

            // Run SEO analysis on the generated content
            $seoService = new SeoAnalyzerService();
            $analysis = $seoService->analyze($articleData);

            $articleModel = new Article();
            $silos = $articleModel->findAllSilos();

            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Article généré par IA - Optimisez le SEO',
                'admin_page' => 'blog',
                'article' => $articleData,
                'analysis' => $analysis,
                'silos' => $silos,
                'errors' => [],
                'message' => 'Article généré par l\'IA. Modifiez le texte et optimisez le SEO avant de sauvegarder.',
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        } catch (\Throwable $throwable) {
            $this->redirect('/admin/blog?error=' . urlencode($throwable->getMessage()));
        }
    }

    public function create(): void
    {
        AuthController::requireAuth();

        $this->ensureArticlesSchema();
        $articleModel = new Article();
        $silos = $articleModel->findAllSilos();

        View::renderAdmin('admin/blog/form', [
            'admin_page_title' => 'Nouvel article',
            'admin_page' => 'blog',
            'article' => null,
            'analysis' => null,
            'silos' => $silos,
            'errors' => [],
            'action' => '/admin/blog/store',
            'submitLabel' => 'Créer l\'article',
        ]);
    }

    public function store(): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();

        try {
            $payload = $this->validatedPayload($_POST);

            // Run SEO analysis before saving
            $seoService = new SeoAnalyzerService();
            $analysis = $seoService->analyze($payload);
            $payload['seo_score'] = $analysis['seo_score'];
            $payload['semantic_score'] = $analysis['semantic_score'];
            $payload['keyword_density'] = $analysis['keyword_density'];
            $payload['keyword_count'] = $analysis['keyword_count'];
            $payload['word_count'] = $analysis['word_count'];
            $payload['internal_links_count'] = $analysis['content_stats']['links_count'] ?? 0;
            $payload['external_links_count'] = $analysis['content_stats']['links_count'] ?? 0;
            $payload['images_count'] = $analysis['content_stats']['images_count'] ?? 0;
            $payload['images_with_alt'] = $analysis['content_stats']['images_count'] ?? 0;
            $payload['reading_time_minutes'] = $analysis['content_stats']['reading_time'] ?? 0;
            $payload['seo_analysis_json'] = json_encode($analysis, JSON_UNESCAPED_UNICODE);

            $articleModel->create($payload);
            $this->redirect('/admin/blog?message=' . urlencode('Article créé avec succès. Score SEO: ' . $analysis['seo_score'] . '/100'));
        } catch (\Throwable $throwable) {
            $silos = $articleModel->findAllSilos();
            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Nouvel article',
                'admin_page' => 'blog',
                'article' => $_POST,
                'analysis' => null,
                'silos' => $silos,
                'errors' => [$throwable->getMessage()],
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        }
    }

    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $this->ensureArticlesSchema();
        $articleModel = new Article();
        $article = $articleModel->findById((int) $id);

        if ($article === null) {
            $this->redirect('/admin/blog?error=' . urlencode('Article introuvable.'));
            return;
        }

        // Run fresh SEO analysis
        $seoService = new SeoAnalyzerService();
        $analysis = $seoService->analyze($article);
        $silos = $articleModel->findAllSilos();

        View::renderAdmin('admin/blog/form', [
            'admin_page_title' => 'Modifier l\'article',
            'admin_page' => 'blog',
            'article' => $article,
            'analysis' => $analysis,
            'silos' => $silos,
            'revisions' => $articleModel->findRevisionsByArticleId((int) $id),
            'errors' => [],
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'action' => '/admin/blog/update/' . (int) $id,
            'submitLabel' => 'Mettre à jour',
        ]);
    }

    public function update(string $id): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();

        try {
            $payload = $this->validatedPayload($_POST);

            // Run SEO analysis
            $seoService = new SeoAnalyzerService();
            $analysis = $seoService->analyze($payload);
            $payload['seo_score'] = $analysis['seo_score'];
            $payload['semantic_score'] = $analysis['semantic_score'];
            $payload['keyword_density'] = $analysis['keyword_density'];
            $payload['keyword_count'] = $analysis['keyword_count'];
            $payload['word_count'] = $analysis['word_count'];
            $payload['internal_links_count'] = $analysis['content_stats']['links_count'] ?? 0;
            $payload['external_links_count'] = $analysis['content_stats']['links_count'] ?? 0;
            $payload['images_count'] = $analysis['content_stats']['images_count'] ?? 0;
            $payload['images_with_alt'] = $analysis['content_stats']['images_count'] ?? 0;
            $payload['reading_time_minutes'] = $analysis['content_stats']['reading_time'] ?? 0;
            $payload['seo_analysis_json'] = json_encode($analysis, JSON_UNESCAPED_UNICODE);

            $articleModel->update((int) $id, $payload);
            $this->redirect('/admin/blog/edit/' . (int) $id . '?message=' . urlencode('Article mis à jour. Score SEO: ' . $analysis['seo_score'] . '/100'));
        } catch (\Throwable $throwable) {
            $article = $_POST;
            $article['id'] = (int) $id;
            $silos = $articleModel->findAllSilos();

            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Modifier l\'article',
                'admin_page' => 'blog',
                'article' => $article,
                'analysis' => null,
                'silos' => $silos,
                'revisions' => $articleModel->findRevisionsByArticleId((int) $id),
                'errors' => [$throwable->getMessage()],
                'action' => '/admin/blog/update/' . (int) $id,
                'submitLabel' => 'Mettre à jour',
            ]);
        }
    }

    /**
     * AJAX: Real-time SEO analysis.
     */
    public function analyzeApi(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                throw new \InvalidArgumentException('Invalid JSON input');
            }

            $seoService = new SeoAnalyzerService();
            $analysis = $seoService->analyze($input);

            echo json_encode(['success' => true, 'data' => $analysis], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * AJAX: Check Google indexation & position for an article.
     */
    public function checkIndexing(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $articleId = (int) ($input['article_id'] ?? 0);

            if ($articleId <= 0) {
                throw new \InvalidArgumentException('ID article invalide');
            }

            $articleModel = new Article();
            $article = $articleModel->findById($articleId);

            if ($article === null) {
                throw new \InvalidArgumentException('Article introuvable');
            }

            $baseUrl = rtrim((string) ($_ENV['APP_BASE_URL'] ?? ''), '/');
            $pageUrl = $baseUrl . '/blog/' . $article['slug'];
            $focusKeyword = trim((string) ($article['focus_keyword'] ?? ''));

            // Check indexation via Google site: search
            $isIndexed = $this->checkGoogleIndexation($pageUrl);

            // Check position for focus keyword
            $position = null;
            if ($focusKeyword !== '' && $isIndexed) {
                $position = $this->checkGooglePosition($focusKeyword, $pageUrl);
            }

            // Save results to DB
            $articleModel->updateIndexingData($articleId, [
                'is_indexed' => $isIndexed,
                'position' => $position,
            ]);

            echo json_encode([
                'success' => true,
                'data' => [
                    'is_indexed' => $isIndexed,
                    'position' => $position,
                    'page_url' => $pageUrl,
                    'focus_keyword' => $focusKeyword,
                    'last_checked' => date('c'),
                ],
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    private function checkGoogleIndexation(string $url): bool
    {
        $query = 'site:' . $url;
        $googleUrl = 'https://www.google.com/search?q=' . urlencode($query) . '&num=1&hl=fr';

        $ch = curl_init($googleUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => ['Accept-Language: fr-FR,fr;q=0.9'],
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return false;
        }

        // If Google returns results containing our URL, the page is indexed
        $domain = parse_url($url, PHP_URL_HOST) ?? '';
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        return (str_contains((string) $response, $domain) && str_contains((string) $response, $path))
            || !str_contains((string) $response, 'did not match any documents');
    }

    private function checkGooglePosition(string $keyword, string $targetUrl): ?int
    {
        $googleUrl = 'https://www.google.com/search?q=' . urlencode($keyword) . '&num=30&hl=fr&gl=fr';

        $ch = curl_init($googleUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => ['Accept-Language: fr-FR,fr;q=0.9'],
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        $domain = parse_url($targetUrl, PHP_URL_HOST) ?? '';
        $path = parse_url($targetUrl, PHP_URL_PATH) ?? '';

        // Parse position from search results
        // Look for result links containing our domain + path
        $position = 0;
        if (preg_match_all('/<a[^>]+href="\/url\?q=([^"&]+)|<a[^>]+href="(https?:\/\/[^"]+)"/i', (string) $response, $matches)) {
            $urls = array_filter(array_merge($matches[1], $matches[2]));
            foreach ($urls as $resultUrl) {
                $resultUrl = urldecode($resultUrl);
                // Skip Google's own URLs
                if (str_contains($resultUrl, 'google.com') || str_contains($resultUrl, 'google.fr')) {
                    continue;
                }
                $position++;
                if (str_contains($resultUrl, $domain) && str_contains($resultUrl, $path)) {
                    return $position;
                }
                if ($position >= 30) {
                    break;
                }
            }
        }

        return null;
    }

    public function restoreRevision(string $id, string $revisionId): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();

        try {
            $articleModel->restoreRevision((int) $id, (int) $revisionId);
            $this->redirect('/admin/blog/edit/' . (int) $id . '?message=' . urlencode('Révision restaurée avec succès.'));
        } catch (\Throwable $throwable) {
            $this->redirect('/admin/blog/edit/' . (int) $id . '?error=' . urlencode($throwable->getMessage()));
        }
    }

    public function delete(string $id): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();
        $articleModel->delete((int) $id);
        $this->redirect('/admin/blog?message=' . urlencode('Article supprimé.'));
    }

    /**
     * Legacy generate (kept for backward compatibility).
     */
    public function generate(): void
    {
        AuthController::requireAuth();

        try {
            $persona = Validator::string($_POST, 'persona', 3, 100);
            $awarenessLevel = Validator::string($_POST, 'awareness_level', 3, 50);
            $topic = Validator::string($_POST, 'topic', 5, 180);

            $service = new AIService();
            $generated = $service->generateArticle($persona, $awarenessLevel, $topic);

            $articleData = [
                'title' => $generated['title'],
                'slug' => $this->slugify($generated['title']),
                'content' => $generated['content'],
                'meta_title' => $generated['meta_title'],
                'meta_description' => $generated['meta_description'],
                'persona' => $persona,
                'awareness_level' => $awarenessLevel,
                'status' => 'draft',
            ];

            $seoService = new SeoAnalyzerService();
            $analysis = $seoService->analyze($articleData);

            $articleModel = new Article();
            $silos = $articleModel->findAllSilos();

            View::renderAdmin('admin/blog/form', [
                'admin_page_title' => 'Article généré par IA',
                'admin_page' => 'blog',
                'article' => $articleData,
                'analysis' => $analysis,
                'silos' => $silos,
                'errors' => [],
                'action' => '/admin/blog/store',
                'submitLabel' => 'Créer l\'article',
            ]);
        } catch (\Throwable $throwable) {
            $this->redirect('/admin/blog?error=' . urlencode($throwable->getMessage()));
        }
    }

    // --- Silo management ---

    public function silos(): void
    {
        AuthController::requireAuth();

        $this->ensureArticlesSchema();
        $articleModel = new Article();
        $silos = $articleModel->findAllSilos();

        View::renderAdmin('admin/blog/silos', [
            'admin_page_title' => 'Silos SEO',
            'admin_page' => 'blog',
            'silos' => $silos,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
        ]);
    }

    public function createSilo(): void
    {
        AuthController::requireAuth();

        try {
            $name = Validator::string($_POST, 'name', 3, 255);
            $description = trim((string) ($_POST['description'] ?? ''));
            $color = trim((string) ($_POST['color'] ?? '#8B1538'));
            $city = trim((string) ($_POST['city'] ?? 'Bordeaux'));

            $articleModel = new Article();
            $articleModel->createSilo([
                'name' => $name,
                'description' => $description,
                'color' => $color,
                'city' => $city !== '' ? $city : 'Bordeaux',
            ]);

            $this->redirect('/admin/blog/silos?message=' . urlencode('Silo créé avec succès.'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/blog/silos?error=' . urlencode($e->getMessage()));
        }
    }

    public function deleteSilo(string $id): void
    {
        AuthController::requireAuth();

        $articleModel = new Article();
        $articleModel->deleteSilo((int) $id);
        $this->redirect('/admin/blog/silos?message=' . urlencode('Silo supprimé.'));
    }

    // --- SEO Guide ---

    public function seoGuide(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/blog/seo-guide', [
            'admin_page_title' => 'Guide SEO Local Immobilier',
            'admin_page' => 'blog',
        ]);
    }

    private function validatedPayload(array $input): array
    {
        $title = Validator::string($input, 'title', 5, 255);
        $slug = Validator::string($input, 'slug', 5, 255);
        $content = trim((string) ($input['content'] ?? ''));

        if ($content === '') {
            throw new \InvalidArgumentException('Champ invalide: content');
        }

        $metaTitle = Validator::string($input, 'meta_title', 5, 255);
        $metaDescription = Validator::string($input, 'meta_description', 20, 320);
        $persona = Validator::string($input, 'persona', 3, 100);
        $awarenessLevel = Validator::string($input, 'awareness_level', 3, 50);
        $status = Validator::string($input, 'status', 5, 20);

        if (!in_array($status, ['draft', 'published'], true)) {
            throw new \InvalidArgumentException('Statut invalide');
        }

        return [
            'title' => $title,
            'slug' => $this->slugify($slug),
            'content' => $content,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'persona' => $persona,
            'awareness_level' => $awarenessLevel,
            'focus_keyword' => trim((string) ($input['focus_keyword'] ?? '')),
            'secondary_keywords' => trim((string) ($input['secondary_keywords'] ?? '')),
            'h1_tag' => trim((string) ($input['h1_tag'] ?? $title)),
            'og_title' => trim((string) ($input['og_title'] ?? '')),
            'og_description' => trim((string) ($input['og_description'] ?? '')) ?: null,
            'og_image' => trim((string) ($input['og_image'] ?? '')) ?: null,
            'canonical_url' => trim((string) ($input['canonical_url'] ?? '')) ?: null,
            'faq_schema' => trim((string) ($input['faq_schema'] ?? '')) ?: null,
            'silo_id' => !empty($input['silo_id']) ? (int) $input['silo_id'] : null,
            'article_type' => in_array($input['article_type'] ?? '', ['pilier', 'satellite', 'standalone']) ? $input['article_type'] : 'standalone',
            'target_audience' => trim((string) ($input['target_audience'] ?? '')) ?: null,
            'article_goal' => trim((string) ($input['article_goal'] ?? '')) ?: null,
            'status' => $status,
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');

        return $text !== '' ? $text : 'article';
    }

    private function ensureArticlesSchema(): void
    {
        $pdo = Database::connection();

        if (!Database::tableExists('articles')) {
            $pdo->exec('CREATE TABLE articles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                meta_title VARCHAR(255) NOT NULL DEFAULT \'\',
                meta_description TEXT NOT NULL,
                persona VARCHAR(100) NOT NULL DEFAULT \'\',
                awareness_level VARCHAR(50) NOT NULL DEFAULT \'\',
                focus_keyword VARCHAR(255) NOT NULL DEFAULT \'\',
                secondary_keywords TEXT DEFAULT NULL,
                seo_score INT UNSIGNED NOT NULL DEFAULT 0,
                semantic_score INT UNSIGNED NOT NULL DEFAULT 0,
                keyword_density DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                keyword_count INT UNSIGNED NOT NULL DEFAULT 0,
                word_count INT UNSIGNED NOT NULL DEFAULT 0,
                h1_tag VARCHAR(255) NOT NULL DEFAULT \'\',
                og_title VARCHAR(255) NOT NULL DEFAULT \'\',
                og_description TEXT DEFAULT NULL,
                og_image VARCHAR(500) DEFAULT NULL,
                canonical_url VARCHAR(500) DEFAULT NULL,
                faq_schema LONGTEXT DEFAULT NULL,
                internal_links_count INT UNSIGNED NOT NULL DEFAULT 0,
                external_links_count INT UNSIGNED NOT NULL DEFAULT 0,
                images_count INT UNSIGNED NOT NULL DEFAULT 0,
                images_with_alt INT UNSIGNED NOT NULL DEFAULT 0,
                reading_time_minutes INT UNSIGNED NOT NULL DEFAULT 0,
                silo_id INT UNSIGNED DEFAULT NULL,
                article_type ENUM(\'pilier\', \'satellite\', \'standalone\') NOT NULL DEFAULT \'standalone\',
                target_audience TEXT DEFAULT NULL,
                article_goal TEXT DEFAULT NULL,
                seo_analysis_json LONGTEXT DEFAULT NULL,
                page_views INT UNSIGNED NOT NULL DEFAULT 0,
                is_indexed TINYINT(1) NOT NULL DEFAULT 0,
                google_position INT UNSIGNED DEFAULT NULL,
                indexing_checked_at DATETIME DEFAULT NULL,
                status ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\',
                published_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_articles_website_slug (website_id, slug),
                INDEX idx_website_id (website_id),
                INDEX idx_status_created_at (status, created_at),
                INDEX idx_focus_keyword (focus_keyword),
                INDEX idx_seo_score (seo_score),
                INDEX idx_silo_id (silo_id),
                INDEX idx_article_type (article_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        } else {
            $this->addMissingColumns($pdo, 'articles', [
                'website_id' => 'INT UNSIGNED NOT NULL DEFAULT 1',
                'title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'slug' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'content' => 'LONGTEXT NOT NULL',
                'meta_title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'meta_description' => 'TEXT NOT NULL',
                'persona' => 'VARCHAR(100) NOT NULL DEFAULT \'\'',
                'awareness_level' => 'VARCHAR(50) NOT NULL DEFAULT \'\'',
                'focus_keyword' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'secondary_keywords' => 'TEXT DEFAULT NULL',
                'seo_score' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'semantic_score' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'keyword_density' => 'DECIMAL(5,2) NOT NULL DEFAULT 0.00',
                'keyword_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'word_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'h1_tag' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'og_title' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
                'og_description' => 'TEXT DEFAULT NULL',
                'og_image' => 'VARCHAR(500) DEFAULT NULL',
                'canonical_url' => 'VARCHAR(500) DEFAULT NULL',
                'faq_schema' => 'LONGTEXT DEFAULT NULL',
                'internal_links_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'external_links_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'images_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'images_with_alt' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'reading_time_minutes' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'silo_id' => 'INT UNSIGNED DEFAULT NULL',
                'article_type' => 'ENUM(\'pilier\', \'satellite\', \'standalone\') NOT NULL DEFAULT \'standalone\'',
                'target_audience' => 'TEXT DEFAULT NULL',
                'article_goal' => 'TEXT DEFAULT NULL',
                'seo_analysis_json' => 'LONGTEXT DEFAULT NULL',
                'published_at' => 'DATETIME DEFAULT NULL',
                'page_views' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                'is_indexed' => 'TINYINT(1) NOT NULL DEFAULT 0',
                'google_position' => 'INT UNSIGNED DEFAULT NULL',
                'indexing_checked_at' => 'DATETIME DEFAULT NULL',
            ]);
        }

        if (!Database::tableExists('article_revisions')) {
            $pdo->exec('CREATE TABLE article_revisions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                article_id INT UNSIGNED NOT NULL,
                revision_number INT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                meta_title VARCHAR(255) NOT NULL DEFAULT \'\',
                meta_description TEXT NOT NULL,
                persona VARCHAR(100) NOT NULL DEFAULT \'\',
                awareness_level VARCHAR(50) NOT NULL DEFAULT \'\',
                status ENUM(\'draft\', \'published\') NOT NULL DEFAULT \'draft\',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_article_revision (article_id, revision_number),
                INDEX idx_article_created_at (article_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }

        if (!Database::tableExists('article_silos')) {
            $pdo->exec('CREATE TABLE article_silos (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                pillar_article_id INT UNSIGNED DEFAULT NULL,
                color VARCHAR(7) NOT NULL DEFAULT \'#8B1538\',
                city VARCHAR(100) NOT NULL DEFAULT \'Bordeaux\',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_silo_website (website_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        } else {
            $this->addMissingColumns($pdo, 'article_silos', [
                'city' => 'VARCHAR(100) NOT NULL DEFAULT \'Bordeaux\'',
            ]);
        }

        if (!Database::tableExists('article_keywords')) {
            $pdo->exec('CREATE TABLE article_keywords (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                article_id INT UNSIGNED NOT NULL,
                keyword VARCHAR(255) NOT NULL,
                keyword_type ENUM(\'focus\', \'secondary\', \'semantic\', \'lsi\') NOT NULL DEFAULT \'secondary\',
                search_volume INT UNSIGNED DEFAULT NULL,
                difficulty INT UNSIGNED DEFAULT NULL,
                position INT UNSIGNED DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_keyword_article (article_id),
                INDEX idx_keyword_type (keyword_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
        }
    }

    private function addMissingColumns(\PDO $pdo, string $table, array $expectedColumns): void
    {
        $stmt = $pdo->query('SHOW COLUMNS FROM ' . $table);
        $existingColumns = array_column($stmt->fetchAll(), 'Field');

        foreach ($expectedColumns as $column => $definition) {
            if (!in_array($column, $existingColumns, true)) {
                $pdo->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
                error_log(sprintf('[blog] Added missing column %s.%s', $table, $column));
            }
        }
    }

    /**
     * AJAX endpoint: AI suggestions for wizard form fields.
     */
    public function aiSuggest(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        $field = trim((string) ($_POST['field'] ?? ''));
        $persona = trim((string) ($_POST['persona'] ?? ''));
        $focusKeyword = trim((string) ($_POST['focus_keyword'] ?? ''));
        $topic = trim((string) ($_POST['topic'] ?? ''));
        $articleGoal = trim((string) ($_POST['article_goal_type'] ?? ''));

        $apiKey = (string) ($_ENV['OPENAI_API_KEY'] ?? '');
        if ($apiKey === '') {
            echo json_encode(['success' => false, 'error' => 'Cle OpenAI non configuree. Configurez-la dans Parametres API.']);
            return;
        }

        $prompt = match ($field) {
            'target_audience' => "Tu es un expert marketing immobilier a Bordeaux. Pour un article cible sur le persona \"{$persona}\" avec l'objectif \"{$articleGoal}\", suggere une description d'audience cible detaillee (age, situation, budget, localisation, motivations). Reponds en 2-3 phrases maximum, en francais.",
            'focus_keyword' => "Tu es un expert SEO immobilier a Bordeaux. Suggere 5 mots-cles focus SEO pertinents pour un article immobilier ciblant le persona \"{$persona}\". Formule : [Action] + [Type de bien] + [Specificite] + [Ville/Quartier]. Reponds avec une liste numerotee, un mot-cle par ligne, sans explication.",
            'secondary_keywords' => "Tu es un expert SEO immobilier a Bordeaux. Le mot-cle principal est \"{$focusKeyword}\". Suggere 8-10 mots-cles secondaires/semantiques complementaires pour le SEO. Reponds avec les mots-cles separes par des virgules, sans explication.",
            'topic' => "Tu es un redacteur immobilier expert a Bordeaux. Pour le persona \"{$persona}\" et le mot-cle \"{$focusKeyword}\", suggere 5 titres d'articles SEO accrocheurs et optimises. Reponds avec une liste numerotee, un titre par ligne, sans explication.",
            default => null,
        };

        if ($prompt === null) {
            echo json_encode(['success' => false, 'error' => 'Champ non supporte']);
            return;
        }

        $endpoint = (string) ($_ENV['OPENAI_ENDPOINT'] ?? 'https://api.openai.com/v1/chat/completions');
        $model = (string) ($_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini');

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'temperature' => 0.7,
                'max_tokens' => 300,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant specialise en immobilier et SEO a Bordeaux. Reponds de maniere concise et pratique.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ], JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            $decoded = $response ? json_decode($response, true) : null;
            $msg = $decoded['error']['message'] ?? 'Erreur API OpenAI (HTTP ' . $httpCode . ')';
            echo json_encode(['success' => false, 'error' => $msg]);
            return;
        }

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '';

        echo json_encode(['success' => true, 'suggestion' => trim($content)]);
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
