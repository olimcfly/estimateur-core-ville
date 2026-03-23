<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Models\GmbPublication;
use PDO;

/**
 * Admin controller for Google My Business publications management.
 *
 * Handles CRUD, AI generation, calendar view, preview and settings.
 */
final class AdminGmbController
{
    private const POST_TYPES = ['update', 'event', 'offer', 'product'];
    private const CTA_TYPES  = ['LEARN_MORE', 'BOOK', 'ORDER', 'SHOP', 'SIGN_UP', 'CALL'];
    private const STATUSES   = ['draft', 'scheduled', 'notified', 'published', 'expired'];

    private const MAX_CONTENT_LENGTH = 1500;
    private const MAX_TITLE_LENGTH   = 58;
    private const MAX_IMAGE_SIZE     = 5 * 1024 * 1024; // 5 MB
    private const MIN_IMAGE_WIDTH    = 400;
    private const MIN_IMAGE_HEIGHT   = 300;
    private const PER_PAGE           = 20;

    // ─── Pages ────────────────────────────────────────────────

    /**
     * List all GMB publications with filters, stats and calendar data.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        // Filters
        $status   = trim((string) ($_GET['status'] ?? ''));
        $postType = trim((string) ($_GET['post_type'] ?? ''));
        $month    = (int) ($_GET['month'] ?? date('n'));
        $year     = (int) ($_GET['year'] ?? date('Y'));
        $page     = max(1, (int) ($_GET['page'] ?? 1));

        $filters = [];
        if ($status !== '' && in_array($status, self::STATUSES, true)) {
            $filters['status'] = $status;
        }
        if ($postType !== '' && in_array($postType, self::POST_TYPES, true)) {
            $filters['post_type'] = $postType;
        }
        if ($month >= 1 && $month <= 12 && $year >= 2020) {
            $filters['month'] = $month;
            $filters['year']  = $year;
        }

        $total       = $model->count($filters);
        $offset      = ($page - 1) * self::PER_PAGE;
        $publications = $model->getAll(self::PER_PAGE, $offset, $filters);
        $stats       = $model->getStats();
        $calendar    = $model->getCalendarData($month, $year);
        $totalPages  = max(1, (int) ceil($total / self::PER_PAGE));

        View::renderAdmin('admin/gmb/index', [
            'page_title'       => 'Publications GMB - Admin',
            'admin_page_title' => 'Publications Google My Business',
            'admin_page'       => 'gmb',
            'publications'     => $publications,
            'stats'            => $stats,
            'calendar'         => $calendar,
            'current_status'   => $status,
            'current_type'     => $postType,
            'current_month'    => $month,
            'current_year'     => $year,
            'current_page'     => $page,
            'total_pages'      => $totalPages,
            'total'            => $total,
            'post_types'       => self::POST_TYPES,
            'cta_types'        => self::CTA_TYPES,
            'settings'         => $model->getAllSettings(),
            'message'          => (string) ($_GET['message'] ?? ''),
            'error'            => (string) ($_GET['error'] ?? ''),
        ]);
    }

    /**
     * Static guide page for GMB best practices.
     */
    public function guide(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/gmb/guide', [
            'page_title'       => 'Guide GMB - Admin',
            'admin_page_title' => 'Guide des bonnes pratiques GMB',
            'admin_page'       => 'gmb',
        ]);
    }

    /**
     * Create form for a new GMB publication.
     */
    public function create(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        View::renderAdmin('admin/gmb/form', [
            'page_title'       => 'Nouvelle publication GMB - Admin',
            'admin_page_title' => 'Nouvelle publication GMB',
            'admin_page'       => 'gmb',
            'publication'      => null,
            'post_types'       => self::POST_TYPES,
            'cta_types'        => self::CTA_TYPES,
            'settings'         => $model->getAllSettings(),
        ]);
    }

    /**
     * Edit form for an existing GMB publication.
     */
    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $model       = new GmbPublication();
        $publication = $model->findById((int) $id);

        if ($publication === null) {
            header('Location: /admin/gmb?error=' . urlencode('Publication introuvable.'));
            exit;
        }

        View::renderAdmin('admin/gmb/form', [
            'page_title'       => 'Modifier publication GMB - Admin',
            'admin_page_title' => 'Modifier la publication',
            'admin_page'       => 'gmb',
            'publication'      => $publication,
            'post_types'       => self::POST_TYPES,
            'cta_types'        => self::CTA_TYPES,
            'settings'         => $model->getAllSettings(),
        ]);
    }

    /**
     * Save (create or update) a GMB publication.
     */
    public function save(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $id    = (int) ($_POST['id'] ?? 0);

        // ── Validation ──────────────────────────────────────
        $errors = [];

        $content  = trim((string) ($_POST['content'] ?? ''));
        $title    = trim((string) ($_POST['title'] ?? ''));
        $postType = trim((string) ($_POST['post_type'] ?? ''));
        $ctaType  = trim((string) ($_POST['cta_type'] ?? ''));
        $ctaUrl   = trim((string) ($_POST['cta_url'] ?? ''));
        $status   = trim((string) ($_POST['status'] ?? 'draft'));
        $scheduledAt = trim((string) ($_POST['scheduled_at'] ?? ''));

        if ($content === '') {
            $errors[] = 'Le contenu est requis.';
        } elseif (mb_strlen($content) > self::MAX_CONTENT_LENGTH) {
            $errors[] = 'Le contenu ne doit pas dépasser ' . self::MAX_CONTENT_LENGTH . ' caractères.';
        }

        if (!in_array($postType, self::POST_TYPES, true)) {
            $errors[] = 'Le type de publication est invalide.';
        }

        if (in_array($postType, ['event', 'offer', 'product'], true) && $title === '') {
            $errors[] = 'Le titre est requis pour ce type de publication.';
        }
        if ($title !== '' && mb_strlen($title) > self::MAX_TITLE_LENGTH) {
            $errors[] = 'Le titre ne doit pas dépasser ' . self::MAX_TITLE_LENGTH . ' caractères.';
        }

        if ($ctaType !== '' && !in_array($ctaType, self::CTA_TYPES, true)) {
            $errors[] = 'Le type de CTA est invalide.';
        }
        if ($ctaType !== '' && $ctaUrl !== '' && filter_var($ctaUrl, FILTER_VALIDATE_URL) === false) {
            $errors[] = "L'URL du CTA n'est pas valide.";
        }

        // ── Image upload ────────────────────────────────────
        $imagePath = null;
        if ($id > 0) {
            $existing  = $model->findById($id);
            $imagePath = $existing['image_path'] ?? null;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmpFile = $_FILES['image']['tmp_name'];
            $mime    = mime_content_type($tmpFile);
            $size    = (int) $_FILES['image']['size'];

            if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
                $errors[] = "L'image doit être au format JPG ou PNG.";
            } elseif ($size > self::MAX_IMAGE_SIZE) {
                $errors[] = "L'image ne doit pas dépasser 5 Mo.";
            } else {
                $dims = getimagesize($tmpFile);
                if ($dims === false || $dims[0] < self::MIN_IMAGE_WIDTH || $dims[1] < self::MIN_IMAGE_HEIGHT) {
                    $errors[] = 'Les dimensions minimales sont ' . self::MIN_IMAGE_WIDTH . 'x' . self::MIN_IMAGE_HEIGHT . ' pixels.';
                }
            }

            if (empty($errors)) {
                $ext       = $mime === 'image/png' ? 'png' : 'jpg';
                $filename  = 'gmb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploadDir = rtrim((string) Config::get('paths.uploads', 'public/uploads'), '/') . '/gmb';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $dest = $uploadDir . '/' . $filename;
                if (move_uploaded_file($tmpFile, $dest)) {
                    $imagePath = '/uploads/gmb/' . $filename;
                } else {
                    $errors[] = "Erreur lors de l'upload de l'image.";
                }
            }
        }

        // ── Redirect on errors ──────────────────────────────
        if (!empty($errors)) {
            $errorMsg = implode(' ', $errors);
            $redirect = $id > 0 ? "/admin/gmb/edit/{$id}" : '/admin/gmb/create';
            header('Location: ' . $redirect . '?error=' . urlencode($errorMsg));
            exit;
        }

        // ── Save ────────────────────────────────────────────
        $data = [
            'post_type'    => $postType,
            'title'        => $title ?: null,
            'content'      => $content,
            'cta_type'     => $ctaType ?: null,
            'cta_url'      => $ctaUrl ?: null,
            'image_path'   => $imagePath,
            'event_start'  => $_POST['event_start'] ?? null,
            'event_end'    => $_POST['event_end'] ?? null,
            'offer_code'   => $_POST['offer_code'] ?? null,
            'offer_terms'  => $_POST['offer_terms'] ?? null,
            'status'       => in_array($status, self::STATUSES, true) ? $status : 'draft',
            'scheduled_at' => $scheduledAt !== '' ? $scheduledAt : null,
            'article_id'   => !empty($_POST['article_id']) ? (int) $_POST['article_id'] : null,
            'actualite_id' => !empty($_POST['actualite_id']) ? (int) $_POST['actualite_id'] : null,
        ];

        if ($id > 0 && $model->findById($id) !== null) {
            $model->update($id, $data);
            $message = 'Publication mise à jour avec succès.';
        } else {
            $id = $model->create($data);
            $message = 'Publication créée avec succès.';
        }

        header('Location: /admin/gmb?message=' . urlencode($message));
        exit;
    }

    /**
     * Delete a GMB publication.
     */
    public function delete(string $id): void
    {
        AuthController::requireAuth();

        $model       = new GmbPublication();
        $publication = $model->findById((int) $id);

        if ($publication === null) {
            header('Location: /admin/gmb?error=' . urlencode('Publication introuvable.'));
            exit;
        }

        $model->delete((int) $id);
        header('Location: /admin/gmb?message=' . urlencode('Publication supprimée.'));
        exit;
    }

    /**
     * Mark a publication as published (called from notification email link).
     */
    public function markPublished(string $id): void
    {
        AuthController::requireAuth();

        $model       = new GmbPublication();
        $publication = $model->findById((int) $id);

        if ($publication === null) {
            header('Location: /admin/gmb?error=' . urlencode('Publication introuvable.'));
            exit;
        }

        $model->markAsPublished((int) $id);
        header('Location: /admin/gmb?message=' . urlencode('Publication marquée comme publiée.'));
        exit;
    }

    // ─── API Endpoints ────────────────────────────────────────

    /**
     * Generate a GMB publication from an existing article.
     * POST JSON: { "article_id": 123 }
     */
    public function generateFromArticle(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
            if (!is_array($input)) {
                throw new \RuntimeException('Données invalides.');
            }

            $articleId = (int) ($input['article_id'] ?? 0);
            if ($articleId < 1) {
                throw new \RuntimeException('article_id requis.');
            }

            // Fetch article
            $pdo  = Database::connection();
            $stmt = $pdo->prepare('SELECT id, title, meta_description, content FROM articles WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $articleId]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$article) {
                throw new \RuntimeException('Article introuvable.');
            }

            // Generate via AI
            $generated = $this->aiGenerateFromContent(
                $article['title'],
                strip_tags((string) $article['content']),
                'article'
            );

            // Save
            $model = new GmbPublication();
            $pubId = $model->create([
                'article_id'   => $articleId,
                'post_type'    => $generated['post_type'] ?? 'update',
                'title'        => $generated['title'] ?? null,
                'content'      => $generated['content'],
                'cta_type'     => $generated['cta_type'] ?? 'LEARN_MORE',
                'cta_url'      => $generated['cta_url'] ?? null,
                'status'       => 'draft',
                'scheduled_at' => $model->getNextAvailableSlot(),
            ]);

            $publication = $model->findById($pubId);

            echo json_encode(['success' => true, 'data' => $publication], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    /**
     * Generate a GMB publication from a free-form subject/theme.
     * POST JSON: { "subject": "..." }
     */
    public function generateManual(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
            if (!is_array($input)) {
                throw new \RuntimeException('Données invalides.');
            }

            $subject = trim((string) ($input['subject'] ?? ''));
            if ($subject === '') {
                throw new \RuntimeException('Le sujet est requis.');
            }

            $generated = $this->aiGenerateFromContent($subject, $subject, 'manual');

            $model = new GmbPublication();
            $pubId = $model->create([
                'post_type'    => $generated['post_type'] ?? 'update',
                'title'        => $generated['title'] ?? null,
                'content'      => $generated['content'],
                'cta_type'     => $generated['cta_type'] ?? 'LEARN_MORE',
                'cta_url'      => $generated['cta_url'] ?? null,
                'status'       => 'draft',
                'scheduled_at' => $model->getNextAvailableSlot(),
            ]);

            $publication = $model->findById($pubId);

            echo json_encode(['success' => true, 'data' => $publication], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    /**
     * Return formatted HTML preview for a GMB publication.
     * POST JSON: { content, title, post_type, cta_type }
     */
    public function preview(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
            if (!is_array($input)) {
                throw new \RuntimeException('Données invalides.');
            }

            $content  = htmlspecialchars(trim((string) ($input['content'] ?? '')), ENT_QUOTES, 'UTF-8');
            $title    = htmlspecialchars(trim((string) ($input['title'] ?? '')), ENT_QUOTES, 'UTF-8');
            $postType = trim((string) ($input['post_type'] ?? 'update'));
            $ctaType  = trim((string) ($input['cta_type'] ?? ''));
            $imageUrl = htmlspecialchars(trim((string) ($input['image_url'] ?? '')), ENT_QUOTES, 'UTF-8');

            $typeLabels = [
                'update'  => 'Nouveauté',
                'event'   => 'Événement',
                'offer'   => 'Offre',
                'product' => 'Produit',
            ];
            $ctaLabels = [
                'LEARN_MORE' => 'En savoir plus',
                'BOOK'       => 'Réserver',
                'ORDER'      => 'Commander',
                'SHOP'       => 'Acheter',
                'SIGN_UP'    => "S'inscrire",
                'CALL'       => 'Appeler',
            ];

            $typeLabel = $typeLabels[$postType] ?? 'Publication';
            $ctaLabel  = $ctaLabels[$ctaType] ?? '';

            $siteName = htmlspecialchars((string) Config::get('site.name', 'Mon entreprise'), ENT_QUOTES, 'UTF-8');

            $imageHtml = $imageUrl !== ''
                ? '<div class="gmb-preview-image"><img src="' . $imageUrl . '" alt="Preview" style="width:100%;border-radius:8px 8px 0 0;"></div>'
                : '<div class="gmb-preview-image" style="background:#e0e0e0;height:200px;border-radius:8px 8px 0 0;display:flex;align-items:center;justify-content:center;color:#999;">Aucune image</div>';

            $ctaHtml = $ctaLabel !== ''
                ? '<div class="gmb-preview-cta" style="margin-top:12px;"><a style="display:inline-block;padding:8px 20px;background:#1a73e8;color:#fff;border-radius:4px;text-decoration:none;font-size:14px;">' . htmlspecialchars($ctaLabel, ENT_QUOTES, 'UTF-8') . '</a></div>'
                : '';

            $titleHtml = $title !== ''
                ? '<h4 style="margin:0 0 8px;font-size:16px;font-weight:600;">' . $title . '</h4>'
                : '';

            $html = <<<HTML
<div class="gmb-preview-card" style="max-width:400px;border:1px solid #dadce0;border-radius:8px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;overflow:hidden;">
    {$imageHtml}
    <div style="padding:16px;">
        <div style="display:flex;align-items:center;margin-bottom:12px;">
            <div style="width:40px;height:40px;border-radius:50%;background:#4285f4;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:18px;margin-right:12px;">{$siteName[0]}</div>
            <div>
                <div style="font-weight:600;font-size:14px;">{$siteName}</div>
                <div style="font-size:12px;color:#70757a;">{$typeLabel}</div>
            </div>
        </div>
        {$titleHtml}
        <p style="margin:0;font-size:14px;line-height:1.5;color:#3c4043;">{$content}</p>
        {$ctaHtml}
    </div>
</div>
HTML;

            echo json_encode(['success' => true, 'html' => $html], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    /**
     * Save GMB settings (notification email, posting days, hour, GMB URL, etc.).
     */
    public function saveSettings(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        $settingKeys = [
            'notification_email',
            'posting_days',
            'notification_hour',
            'gmb_url',
            'default_cta_type',
            'default_cta_url',
            'auto_schedule',
        ];

        foreach ($settingKeys as $key) {
            if (isset($_POST[$key])) {
                $model->saveSetting($key, trim((string) $_POST[$key]));
            }
        }

        header('Location: /admin/gmb?message=' . urlencode('Paramètres sauvegardés.'));
        exit;
    }

    /**
     * Return calendar data as JSON for a given month/year.
     */
    public function calendarData(string $month, string $year): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $m = max(1, min(12, (int) $month));
        $y = max(2020, (int) $year);

        $model    = new GmbPublication();
        $calendar = $model->getCalendarData($m, $y);

        echo json_encode(['success' => true, 'data' => $calendar, 'month' => $m, 'year' => $y], JSON_THROW_ON_ERROR);
        exit;
    }

    // ─── Private helpers ──────────────────────────────────────

    /**
     * Generate a GMB publication content using AI (Anthropic Claude).
     */
    private function aiGenerateFromContent(string $title, string $body, string $source): array
    {
        $apiKey = trim((string) Config::get('anthropic.api_key', ''));
        if ($apiKey === '') {
            // Fallback without AI
            return [
                'post_type' => 'update',
                'title'     => mb_substr($title, 0, self::MAX_TITLE_LENGTH),
                'content'   => mb_substr(strip_tags($body), 0, 300),
                'cta_type'  => 'LEARN_MORE',
                'cta_url'   => null,
            ];
        }

        $city = (string) Config::get('city.name', 'Bordeaux');
        $bodyExcerpt = mb_substr(strip_tags($body), 0, 2000);

        $prompt = <<<PROMPT
Tu es un expert en marketing local et Google My Business pour un professionnel immobilier à {$city}.
Génère une publication GMB à partir de ce contenu :

Titre source : {$title}
Contenu source : {$bodyExcerpt}
Type de source : {$source}

Réponds UNIQUEMENT en JSON strict :
{
  "post_type": "update",
  "title": "Titre court (max 58 car.)",
  "content": "Contenu engageant (max 1500 car.) avec emojis pertinents et appel à l'action",
  "cta_type": "LEARN_MORE",
  "cta_url": null
}

Règles :
- Le contenu doit être engageant, local, et inciter à l'action
- Maximum 1500 caractères pour le content
- Maximum 58 caractères pour le title
- Utilise des emojis avec parcimonie (2-3 max)
- Inclus un appel à l'action naturel dans le texte
- post_type parmi : update, event, offer, product
- cta_type parmi : LEARN_MORE, BOOK, ORDER, SHOP, SIGN_UP, CALL
PROMPT;

        $model = (string) Config::get('anthropic.model', 'claude-sonnet-4-20250514');

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model'      => $model,
                'max_tokens' => 1024,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ], JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            throw new \RuntimeException('Erreur API Anthropic (HTTP ' . $httpCode . ').');
        }

        $responseBody = json_decode((string) $response, true);

        // Log AI usage
        $inputTokens  = (int) ($responseBody['usage']['input_tokens'] ?? 0);
        $outputTokens = (int) ($responseBody['usage']['output_tokens'] ?? 0);
        $cost = round(($inputTokens / 1000) * 0.003 + ($outputTokens / 1000) * 0.015, 6);
        AdminSmtpApiController::logAiUsage('claude', $model, $inputTokens, $outputTokens, $cost, 'gmb_generation');

        $text = $responseBody['content'][0]['text'] ?? '';

        // Extract JSON from response (may be wrapped in markdown)
        if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
            $generated = json_decode($m[0], true);
        } else {
            $generated = json_decode($text, true);
        }

        if (!is_array($generated) || empty($generated['content'])) {
            throw new \RuntimeException('Réponse IA invalide.');
        }

        // Enforce limits
        $generated['content'] = mb_substr(trim($generated['content']), 0, self::MAX_CONTENT_LENGTH);
        if (!empty($generated['title'])) {
            $generated['title'] = mb_substr(trim($generated['title']), 0, self::MAX_TITLE_LENGTH);
        }

        return $generated;
    }
}
