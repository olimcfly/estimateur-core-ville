<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\AdminModule;
use App\Models\AdminUser;

final class AdminModuleController
{
    public function index(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        $modules = AdminModule::findAll();
        $categories = AdminModule::getCategoryLabels();

        // Group by category
        $grouped = [];
        foreach ($modules as $mod) {
            $cat = $mod['category'] ?: 'general';
            $grouped[$cat][] = $mod;
        }

        View::renderAdmin('admin/modules', [
            'page_title' => 'Gestion des Modules',
            'admin_page' => 'modules',
            'admin_page_title' => 'Modules',
            'breadcrumb' => 'Modules',
            'modules' => $modules,
            'grouped' => $grouped,
            'categories' => $categories,
        ]);
    }

    public function toggle(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $slug = trim((string) ($_POST['slug'] ?? ''));
        $active = filter_var($_POST['active'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

        if ($slug === '') {
            echo json_encode(['success' => false, 'error' => 'Module non specifie.']);
            return;
        }

        $mod = AdminModule::findBySlug($slug);
        if ($mod === null) {
            echo json_encode(['success' => false, 'error' => 'Module introuvable.']);
            return;
        }

        AdminModule::toggle($slug, $active);

        echo json_encode([
            'success' => true,
            'slug' => $slug,
            'active' => $active,
            'message' => $active ? "Module \"{$mod['name']}\" active." : "Module \"{$mod['name']}\" desactive.",
        ]);
    }

    public function update(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $slug = trim((string) ($_POST['slug'] ?? ''));

        if ($slug === '') {
            echo json_encode(['success' => false, 'error' => 'Module non specifie.']);
            return;
        }

        $data = [];
        if (isset($_POST['is_active'])) {
            $data['is_active'] = filter_var($_POST['is_active'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($_POST['superuser_only'])) {
            $data['superuser_only'] = filter_var($_POST['superuser_only'], FILTER_VALIDATE_BOOLEAN);
        }

        AdminModule::updateModule($slug, $data);

        echo json_encode(['success' => true, 'message' => 'Module mis a jour.']);
    }

    public function seedModules(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        AdminModule::ensureTable();
        AdminModule::seedDefaults();

        echo json_encode(['success' => true, 'message' => 'Modules reinitialises.']);
    }

    private static function requireSuperUser(): void
    {
        if (!AdminUser::isSuperUser()) {
            http_response_code(403);
            echo '<div style="text-align:center;padding:4rem;font-family:sans-serif;">';
            echo '<h1 style="color:#8B1538;">Acces refuse</h1>';
            echo '<p>Seul le super-utilisateur peut acceder a cette page.</p>';
            echo '<a href="/admin">Retour au tableau de bord</a>';
            echo '</div>';
            exit;
        }
    }
}
