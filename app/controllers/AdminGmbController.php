<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\GmbPublication;
use App\Services\GmbService;

final class AdminGmbController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $dbError = null;
        $publications = [];
        $stats = [];
        $calendarData = [];
        $settings = [];

        try {
            $filters = [];
            if (!empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            if (!empty($_GET['post_type'])) {
                $filters['post_type'] = $_GET['post_type'];
            }

            $publications = $model->getAll(50, 0, $filters);
            $stats = $model->getStats();
            $settings = $model->getAllSettings();

            // Calendar data for current month (or requested month)
            $month = (int) ($_GET['month'] ?? date('n'));
            $year = (int) ($_GET['year'] ?? date('Y'));
            $calendarData = $model->getCalendarData($month, $year);
        } catch (\Throwable $e) {
            error_log('GMB index error: ' . $e->getMessage());
            $dbError = 'Erreur base de donnees : les tables GMB sont peut-etre absentes. Executez "php database/migrate-gmb.php".';
        }

        $month = (int) ($_GET['month'] ?? date('n'));
        $year = (int) ($_GET['year'] ?? date('Y'));

        View::renderAdmin('admin/gmb/index', [
            'publications' => $publications,
            'stats' => $stats,
            'calendarData' => $calendarData,
            'settings' => $settings,
            'month' => $month,
            'year' => $year,
            'message' => (string) ($_GET['message'] ?? ''),
            'error' => $dbError ?? (string) ($_GET['error'] ?? ''),
            'page_title' => 'Google My Business - Admin',
            'admin_page_title' => 'Google My Business',
            'admin_page' => 'gmb',
            'breadcrumb' => 'Google My Business',
        ]);
    }

    public function create(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $settings = $model->getAllSettings();
        $nextSlot = $model->getNextAvailableSlot();

        View::renderAdmin('admin/gmb/form', [
            'publication' => null,
            'settings' => $settings,
            'nextSlot' => $nextSlot,
            'errors' => [],
            'action' => '/admin/gmb/store',
            'submitLabel' => 'Creer la publication',
            'page_title' => 'Nouvelle publication GMB - Admin',
            'admin_page_title' => 'Nouvelle publication GMB',
            'admin_page' => 'gmb',
            'breadcrumb' => 'Nouvelle publication',
        ]);
    }

    public function store(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        try {
            $data = $this->validatedPayload($_POST);
            $model->create($data);
            $this->redirect('/admin/gmb?message=' . urlencode('Publication GMB creee avec succes.'));
        } catch (\Throwable $e) {
            $settings = $model->getAllSettings();
            $nextSlot = $model->getNextAvailableSlot();

            View::renderAdmin('admin/gmb/form', [
                'publication' => $_POST,
                'settings' => $settings,
                'nextSlot' => $nextSlot,
                'errors' => [$e->getMessage()],
                'action' => '/admin/gmb/store',
                'submitLabel' => 'Creer la publication',
                'page_title' => 'Nouvelle publication GMB - Admin',
                'admin_page_title' => 'Nouvelle publication GMB',
                'admin_page' => 'gmb',
                'breadcrumb' => 'Nouvelle publication',
            ]);
        }
    }

    public function edit(string $id): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $publication = $model->findById((int) $id);

        if ($publication === null) {
            $this->redirect('/admin/gmb?error=' . urlencode('Publication introuvable.'));
            return;
        }

        $settings = $model->getAllSettings();
        $nextSlot = $model->getNextAvailableSlot();

        View::renderAdmin('admin/gmb/form', [
            'publication' => $publication,
            'settings' => $settings,
            'nextSlot' => $nextSlot,
            'errors' => [],
            'message' => (string) ($_GET['message'] ?? ''),
            'action' => '/admin/gmb/update/' . (int) $id,
            'submitLabel' => 'Mettre a jour',
            'page_title' => 'Modifier publication GMB - Admin',
            'admin_page_title' => 'Modifier publication GMB',
            'admin_page' => 'gmb',
            'breadcrumb' => 'Modifier publication',
        ]);
    }

    public function update(string $id): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        try {
            $data = $this->validatedPayload($_POST);
            $model->update((int) $id, $data);
            $this->redirect('/admin/gmb?message=' . urlencode('Publication GMB mise a jour.'));
        } catch (\Throwable $e) {
            $publication = $_POST;
            $publication['id'] = (int) $id;
            $settings = $model->getAllSettings();
            $nextSlot = $model->getNextAvailableSlot();

            View::renderAdmin('admin/gmb/form', [
                'publication' => $publication,
                'settings' => $settings,
                'nextSlot' => $nextSlot,
                'errors' => [$e->getMessage()],
                'action' => '/admin/gmb/update/' . (int) $id,
                'submitLabel' => 'Mettre a jour',
                'page_title' => 'Modifier publication GMB - Admin',
                'admin_page_title' => 'Modifier publication GMB',
                'admin_page' => 'gmb',
                'breadcrumb' => 'Modifier publication',
            ]);
        }
    }

    public function delete(string $id): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $model->delete((int) $id);
        $this->redirect('/admin/gmb?message=' . urlencode('Publication GMB supprimee.'));
    }

    public function markPublished(string $id): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $model->markAsPublished((int) $id);
        $this->redirect('/admin/gmb?message=' . urlencode('Publication marquee comme publiee.'));
    }

    public function preview(string $id): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();
        $publication = $model->findById((int) $id);

        if ($publication === null) {
            $this->redirect('/admin/gmb?error=' . urlencode('Publication introuvable.'));
            return;
        }

        View::renderAdmin('admin/gmb/preview', [
            'publication' => $publication,
            'page_title' => 'Preview GMB - Admin',
            'admin_page_title' => 'Preview publication GMB',
            'admin_page' => 'gmb',
            'breadcrumb' => 'Preview',
        ]);
    }

    public function guide(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/gmb/guide', [
            'page_title' => 'Guide GMB - Admin',
            'admin_page_title' => 'Guide Google My Business',
            'admin_page' => 'gmb',
            'breadcrumb' => 'Guide bonnes pratiques',
        ]);
    }

    public function generate(): void
    {
        AuthController::requireAuth();

        $service = new GmbService();
        $model = new GmbPublication();

        try {
            $result = $service->generateFromLatestContent();

            if (!$result['success']) {
                $this->redirect('/admin/gmb?error=' . urlencode($result['error'] ?? 'Aucun contenu disponible pour la generation.'));
                return;
            }

            $id = $model->create($result['publication']);
            $this->redirect('/admin/gmb/edit/' . $id . '?message=' . urlencode('Publication generee automatiquement. Verifiez et planifiez-la.'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/gmb?error=' . urlencode('Erreur generation : ' . $e->getMessage()));
        }
    }

    public function saveSettings(): void
    {
        AuthController::requireAuth();

        $model = new GmbPublication();

        try {
            $settingKeys = [
                'default_cta_type', 'default_cta_url', 'notification_email',
                'notification_hour', 'auto_generate', 'posting_days', 'gmb_profile_url',
            ];

            foreach ($settingKeys as $key) {
                if (isset($_POST[$key])) {
                    $model->saveSetting($key, trim((string) $_POST[$key]));
                }
            }

            $this->redirect('/admin/gmb?message=' . urlencode('Parametres GMB sauvegardes.'));
        } catch (\Throwable $e) {
            $this->redirect('/admin/gmb?error=' . urlencode('Erreur : ' . $e->getMessage()));
        }
    }

    private function validatedPayload(array $input): array
    {
        $content = trim((string) ($input['content'] ?? ''));
        if ($content === '') {
            throw new \InvalidArgumentException('Le contenu de la publication ne peut pas etre vide.');
        }
        if (mb_strlen($content) > 1500) {
            throw new \InvalidArgumentException('Le contenu ne doit pas depasser 1500 caracteres (actuellement ' . mb_strlen($content) . ').');
        }

        $postType = trim((string) ($input['post_type'] ?? 'update'));
        if (!in_array($postType, ['update', 'event', 'offer', 'product'], true)) {
            throw new \InvalidArgumentException('Type de publication invalide.');
        }

        $title = trim((string) ($input['title'] ?? ''));
        if (in_array($postType, ['event', 'offer', 'product'], true) && $title === '') {
            throw new \InvalidArgumentException('Le titre est requis pour les publications de type event, offre ou produit.');
        }
        if ($title !== '' && mb_strlen($title) > 58) {
            throw new \InvalidArgumentException('Le titre ne doit pas depasser 58 caracteres.');
        }

        $status = trim((string) ($input['status'] ?? 'draft'));
        if (!in_array($status, ['draft', 'scheduled', 'published'], true)) {
            throw new \InvalidArgumentException('Statut invalide.');
        }

        $ctaType = trim((string) ($input['cta_type'] ?? ''));
        $validCta = ['', 'book', 'order_online', 'buy', 'learn_more', 'sign_up', 'get_offer', 'call_now'];
        if (!in_array($ctaType, $validCta, true)) {
            throw new \InvalidArgumentException('Type de CTA invalide.');
        }

        return [
            'post_type'    => $postType,
            'title'        => $title ?: null,
            'content'      => $content,
            'cta_type'     => $ctaType ?: null,
            'cta_url'      => trim((string) ($input['cta_url'] ?? '')) ?: null,
            'image_path'   => trim((string) ($input['image_path'] ?? '')) ?: null,
            'event_start'  => trim((string) ($input['event_start'] ?? '')) ?: null,
            'event_end'    => trim((string) ($input['event_end'] ?? '')) ?: null,
            'offer_code'   => trim((string) ($input['offer_code'] ?? '')) ?: null,
            'offer_terms'  => trim((string) ($input['offer_terms'] ?? '')) ?: null,
            'article_id'   => !empty($input['article_id']) ? (int) $input['article_id'] : null,
            'actualite_id' => !empty($input['actualite_id']) ? (int) $input['actualite_id'] : null,
            'status'       => $status,
            'scheduled_at' => trim((string) ($input['scheduled_at'] ?? '')) ?: null,
        ];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
