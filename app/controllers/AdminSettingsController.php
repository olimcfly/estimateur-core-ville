<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;

final class AdminSettingsController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $this->ensureSettingsTable();

        $googleSiteVerification = '';
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
            $stmt->execute(['google_site_verification']);
            $row = $stmt->fetch();
            if ($row) {
                $googleSiteVerification = (string) $row['value'];
            }
        } catch (\Throwable $e) {
            // ignore
        }

        View::renderAdmin('admin/settings', [
            'page_title' => 'Paramètres du site',
            'admin_page' => 'settings',
            'google_site_verification' => $googleSiteVerification,
            'success' => $_GET['success'] ?? null,
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();

        $this->ensureSettingsTable();

        $googleSiteVerification = trim((string) ($_POST['google_site_verification'] ?? ''));

        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare(
                "INSERT INTO settings (`key`, `value`, updated_at) VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()"
            );
            $stmt->execute(['google_site_verification', $googleSiteVerification]);
        } catch (\Throwable $e) {
            // ignore
        }

        header('Location: /admin/settings?success=1');
        exit;
    }

    private function ensureSettingsTable(): void
    {
        try {
            if (!Database::tableExists('settings')) {
                $sql = file_get_contents(dirname(__DIR__, 2) . '/database/migration_settings.sql');
                if ($sql !== false) {
                    Database::connection()->exec($sql);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
