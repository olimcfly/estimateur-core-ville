<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\ActualiteAiConfig;
use App\Models\Actualite;
use App\Models\AdminNotification;
use App\Models\GmbPublication;
use App\Models\RssArticle;
use App\Services\ActualiteService;
use App\Services\GmbService;

final class AdminActualiteController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $actualites = [];
        $cronLogs = [];
        $rssStats = [];
        $aiConfig = [];
        $dbError = null;

        // Load actualites data
        try {
            $model = new Actualite();
            $actualites = $model->findAll();
            $cronLogs = $model->getCronLogs(10);
        } catch (\Throwable $e) {
            error_log('Actualites table error: ' . $e->getMessage());
            $dbError = 'Erreur base de données : la table "actualites" est peut-être absente. Exécutez "php database/migrate.php".';
        }

        // Load AI config (independent of actualites)
        try {
            $configModel = new ActualiteAiConfig();
            $aiConfig = $configModel->getAll();
        } catch (\Throwable $e) {
            error_log('ActualiteAiConfig error: ' . $e->getMessage());
            if ($dbError === null) {
                $dbError = 'Erreur base de données : la table "actualite_ai_config" est peut-être absente. Exécutez "php database/migrate.php".';
            }
        }

        // Load RSS stats (independent of above)
        try {
            $service = new ActualiteService();
            $collected = $service->collectRssArticles();
            $rssStats = [
                'total_candidates' => $collected['total_count'],
                'filtered_ready' => $collected['filtered_count'],
                'top_articles' => array_slice($collected['articles'], 0, 5),
            ];
        } catch (\Throwable $e) {
            error_log('RSS stats error: ' . $e->getMessage());
            if ($dbError === null) {
                $dbError = 'Erreur base de données : une table RSS est peut-être absente. Exécutez "php database/migrate.php".';
            }
        }

        View::renderAdmin('admin/actualites/index', [
            'actualites' => $actualites,
            'cronLogs' => $cronLogs,
            'rssStats' => $rssStats,
            'aiConfig' => $aiConfig,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => $dbError ?? (string) ($_GET['error'] ?? ''),
            'page_title' => 'Actualités - Admin',
            'admin_page_title' => 'Actualités',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Actualités',
        ]);
    }

    public function create(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/actualites/form', [
            'actualite' => null,
            'errors' => [],
            'action' => '/admin/actualites/store',
            'submitLabel' => 'Créer l\'actualité',
            'page_title' => 'Nouvelle actualité - Admin',
            'admin_page_title' => 'Nouvelle actualité',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Nouvelle actualité',
        ]);
    }

    public function store(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();

        try {
            $data = $this->validatedPayload($_POST);
            $id = $model->create($data);

            // Auto-generate GMB publication if enabled
            try {
                $gmbService = new GmbService();
                $gmbService->autoGenerateFromActualite($id);
            } catch (\Throwable $gmbError) {
                error_log('[actualites] GMB auto-generate error: ' . $gmbError->getMessage());
            }

            // Mark RSS articles as used if provided
            $rssArticleIds = $_POST['rss_article_ids'] ?? '';
            if ($rssArticleIds !== '') {
                $ids = array_filter(array_map('intval', explode(',', (string) $rssArticleIds)));
                $articleModel = new RssArticle();
                foreach ($ids as $rssId) {
                    $articleModel->markAsUsedForActualite($rssId, $id);
                }
            }

            // Auto-generate GMB publication
            $this->tryAutoGenerateGmbActualite($id, $data['title']);

            $this->redirect('/admin/actualites?message=' . urlencode('Actualité créée avec succès.'));
        } catch (\Throwable $e) {
            View::renderAdmin('admin/actualites/form', [
                'actualite' => $_POST,
                'errors' => [$e->getMessage()],
                'action' => '/admin/actualites/store',
                'submitLabel' => 'Créer l\'actualité',
                'page_title' => 'Nouvelle actualité - Admin',
                'admin_page_title' => 'Nouvelle actualité',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Nouvelle actualité',
            ]);
        }
    }

    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $actualite = $model->findById((int) $id);

        if ($actualite === null) {
            $this->redirect('/admin/actualites?error=' . urlencode('Actualité introuvable.'));
            return;
        }

        View::renderAdmin('admin/actualites/form', [
            'actualite' => $actualite,
            'errors' => [],
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => (string) ($_GET['error'] ?? ''),
            'action' => '/admin/actualites/update/' . (int) $id,
            'submitLabel' => 'Mettre à jour',
            'page_title' => 'Modifier actualité - Admin',
            'admin_page_title' => 'Modifier actualité',
            'admin_page' => 'actualites',
            'breadcrumb' => 'Modifier actualité',
        ]);
    }

    public function update(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();

        try {
            $data = $this->validatedPayload($_POST);
            $model->update((int) $id, $data);

            // Auto-generate GMB publication
            $this->tryAutoGenerateGmbActualite((int) $id, $data['title']);

            $this->redirect('/admin/actualites?message=' . urlencode('Actualité mise à jour.'));
        } catch (\Throwable $e) {
            $actualite = $_POST;
            $actualite['id'] = (int) $id;

            View::renderAdmin('admin/actualites/form', [
                'actualite' => $actualite,
                'errors' => [$e->getMessage()],
                'action' => '/admin/actualites/update/' . (int) $id,
                'submitLabel' => 'Mettre à jour',
                'page_title' => 'Modifier actualité - Admin',
                'admin_page_title' => 'Modifier actualité',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Modifier actualité',
            ]);
        }
    }

    public function delete(string $id): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $model->delete((int) $id);
        $this->redirect('/admin/actualites?message=' . urlencode('Actualité supprimée.'));
    }

    /**
     * Search Perplexity for news ideas (legacy).
     */
    public function search(): void
    {
        AuthController::requireAuth();

        $query = trim((string) ($_POST['query'] ?? ''));
        $service = new ActualiteService();

        try {
            $results = $service->searchNews($query !== '' ? $query : null);

            View::renderAdmin('admin/actualites/search_results', [
                'query' => $results['query'],
                'results' => $results['results'],
                'source' => $results['source'],
                'page_title' => 'Résultats de recherche - Admin',
                'admin_page_title' => 'Recherche actualités',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Recherche actualités',
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/admin/actualites?error=' . urlencode('Erreur recherche: ' . $e->getMessage()));
        }
    }

    /**
     * Generate from Perplexity pipeline (legacy).
     */
    public function generate(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $service = new ActualiteService();

        try {
            $customQuery = trim((string) ($_POST['query'] ?? ''));
            $query = $customQuery !== '' ? $customQuery : null;
            $result = $service->runAutomatedPipeline($query);

            if (!($result['success'] ?? false)) {
                $model->logCron(
                    $result['query'] ?? ($query ?? 'auto'),
                    0,
                    null,
                    'error',
                    $result['error'] ?? 'Erreur inconnue'
                );
                $this->redirect('/admin/actualites?error=' . urlencode($result['error'] ?? 'Erreur génération.'));
                return;
            }

            $article = $result['article'];

            $model->logCron(
                $result['query'] ?? ($query ?? 'auto'),
                $result['ideas_count'] ?? 0,
                null,
                'success'
            );

            View::renderAdmin('admin/actualites/form', [
                'actualite' => [
                    'title' => $article['title'],
                    'slug' => $this->slugify($article['title']),
                    'content' => $article['content'],
                    'excerpt' => $article['excerpt'],
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'image_url' => $article['image_url'] ?? '',
                    'image_prompt' => $article['image_prompt'] ?? '',
                    'source_query' => $article['source_query'] ?? '',
                    'source_results' => $result['source_results'] ?? '',
                    'generated_by' => 'ai',
                    'status' => 'draft',
                ],
                'errors' => [],
                'action' => '/admin/actualites/store',
                'submitLabel' => 'Publier l\'actualité',
                'page_title' => 'Article généré - Admin',
                'admin_page_title' => 'Article généré par IA',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Article généré par IA',
            ]);
        } catch (\Throwable $e) {
            $model->logCron(
                $customQuery !== '' ? $customQuery : 'auto',
                0,
                null,
                'error',
                $e->getMessage()
            );
            $this->redirect('/admin/actualites?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Generate actualité from RSS pipeline (NEW).
     */
    public function generateFromRss(): void
    {
        AuthController::requireAuth();

        $model = new Actualite();
        $service = new ActualiteService();

        try {
            $result = $service->runRssPipeline();

            if (!($result['success'] ?? false)) {
                $model->logCron(
                    'rss-pipeline',
                    0,
                    null,
                    'error',
                    $result['error'] ?? 'Erreur inconnue'
                );
                $this->redirect('/admin/actualites?error=' . urlencode($result['error'] ?? 'Erreur génération RSS.'));
                return;
            }

            $article = $result['article'];
            $rssArticleIds = $result['rss_article_ids'] ?? [];

            $model->logCron(
                'rss-pipeline',
                $result['ideas_count'] ?? 0,
                null,
                'success'
            );

            View::renderAdmin('admin/actualites/form', [
                'actualite' => [
                    'title' => $article['title'],
                    'slug' => $this->slugify($article['title']),
                    'content' => $article['content'],
                    'excerpt' => $article['excerpt'],
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'image_url' => $article['image_url'] ?? '',
                    'image_prompt' => $article['image_prompt'] ?? '',
                    'source_query' => 'rss-pipeline',
                    'source_results' => $result['source_results'] ?? '',
                    'rss_article_ids' => implode(',', $rssArticleIds),
                    'generated_by' => 'ai',
                    'status' => 'draft',
                ],
                'errors' => [],
                'action' => '/admin/actualites/store',
                'submitLabel' => 'Publier l\'actualité',
                'page_title' => 'Actualité générée depuis RSS - Admin',
                'admin_page_title' => 'Actualité générée depuis RSS',
                'admin_page' => 'actualites',
                'breadcrumb' => 'Actualité générée depuis RSS',
            ]);
        } catch (\Throwable $e) {
            $model->logCron('rss-pipeline', 0, null, 'error', $e->getMessage());
            $this->redirect('/admin/actualites?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Save AI configuration for actualité generation.
     */
    public function saveAiConfig(): void
    {
        AuthController::requireAuth();

        try {
            $configModel = new ActualiteAiConfig();
            $configModel->saveAll([
                'zone_priority' => trim((string) ($_POST['zone_priority'] ?? 'local_first')),
                'exclude_agencies' => isset($_POST['exclude_agencies']) ? '1' : '0',
                'exclude_keywords' => trim((string) ($_POST['exclude_keywords'] ?? '')),
                'require_keywords' => trim((string) ($_POST['require_keywords'] ?? '')),
                'max_article_age_days' => trim((string) ($_POST['max_article_age_days'] ?? '7')),
                'min_relevance_score' => trim((string) ($_POST['min_relevance_score'] ?? '6')),
                'article_tone' => trim((string) ($_POST['article_tone'] ?? 'journalistique')),
                'article_length' => trim((string) ($_POST['article_length'] ?? '800-1200')),
                'seo_focus' => trim((string) ($_POST['seo_focus'] ?? '')),
                'local_angle' => trim((string) ($_POST['local_angle'] ?? '')),
                'cta_style' => trim((string) ($_POST['cta_style'] ?? 'soft')),
                'source_citation' => isset($_POST['source_citation']) ? '1' : '0',
                'auto_publish' => isset($_POST['auto_publish']) ? '1' : '0',
                'generation_model' => trim((string) ($_POST['generation_model'] ?? 'anthropic')),
            ]);

            $this->redirect('/admin/actualites?message=' . urlencode('Configuration IA sauvegardée.'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/actualites?error=' . urlencode('Erreur : ' . $e->getMessage()));
        }
    }

    private function validatedPayload(array $input): array
    {
        $title = trim((string) ($input['title'] ?? ''));
        if (mb_strlen($title) < 5) {
            throw new \InvalidArgumentException('Le titre doit faire au moins 5 caractères.');
        }

        $slug = trim((string) ($input['slug'] ?? ''));
        if ($slug === '') {
            $slug = $title;
        }

        $content = trim((string) ($input['content'] ?? ''));
        if ($content === '') {
            throw new \InvalidArgumentException('Le contenu ne peut pas être vide.');
        }

        $status = trim((string) ($input['status'] ?? 'draft'));
        if (!in_array($status, ['draft', 'published'], true)) {
            throw new \InvalidArgumentException('Statut invalide.');
        }

        return [
            'title' => $title,
            'slug' => $this->slugify($slug),
            'content' => $content,
            'excerpt' => trim((string) ($input['excerpt'] ?? '')),
            'meta_title' => trim((string) ($input['meta_title'] ?? $title)),
            'meta_description' => trim((string) ($input['meta_description'] ?? '')),
            'image_url' => trim((string) ($input['image_url'] ?? '')) ?: null,
            'status' => $status,
            'generated_by' => trim((string) ($input['generated_by'] ?? 'manual')),
            'source_query' => trim((string) ($input['source_query'] ?? '')) ?: null,
            'source_results' => trim((string) ($input['source_results'] ?? '')) ?: null,
        ];
    }

    private function slugify(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');

        return $text !== '' ? $text : 'actualite';
    }

    /**
     * Try to auto-generate a GMB publication for an actualité.
     * Never blocks the main save flow — all errors are caught and logged.
     */
    private function tryAutoGenerateGmbActualite(int $id, string $title): void
    {
        try {
            $gmbModel = new GmbPublication();

            if ($gmbModel->getSetting('auto_generate', '0') !== '1') {
                return;
            }

            if ($gmbModel->getByActualite($id) !== null) {
                return;
            }

            $actualiteModel = new Actualite();
            $actualite = $actualiteModel->findById($id);

            if ($actualite === null) {
                return;
            }

            $gmbId = GmbService::generateFromActualite($actualite);

            AdminNotification::create(
                'Publication GMB créée automatiquement',
                "Publication GMB créée automatiquement pour l'actualité : {$title}",
                AdminNotification::TYPE_SUCCESS,
                '/admin/gmb/edit/' . $gmbId,
                'all'
            );

            error_log("[gmb-auto] Publication GMB #{$gmbId} créée pour l'actualité #{$id} : {$title}");
        } catch (\Throwable $e) {
            error_log("[gmb-auto] Erreur génération GMB pour l'actualité #{$id} : " . $e->getMessage());

            try {
                AdminNotification::create(
                    'Erreur génération GMB automatique',
                    "Impossible de créer la publication GMB pour l'actualité : {$title}. Erreur : " . $e->getMessage(),
                    AdminNotification::TYPE_WARNING,
                    null,
                    'all'
                );
            } catch (\Throwable $notifError) {
                error_log('[gmb-auto] Erreur notification : ' . $notifError->getMessage());
            }
        }
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
