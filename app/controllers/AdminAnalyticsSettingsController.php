<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;

final class AdminAnalyticsSettingsController
{
    /**
     * All analytics setting keys managed by this module.
     */
    private const KEYS = [
        'gtm_id',
        'ga4_measurement_id',
        'google_ads_id',
        'google_ads_conversion_label',
        'facebook_pixel_id',
        'facebook_conversions_api_token',
        'microsoft_clarity_id',
        'hotjar_id',
        'tiktok_pixel_id',
        'linkedin_partner_id',
        'pinterest_tag_id',
        'snapchat_pixel_id',
        'custom_head_scripts',
        'custom_body_scripts',
    ];

    public function index(): void
    {
        AuthController::requireAuth();

        $this->ensureSettingsTable();

        $settings = [];
        try {
            $pdo = Database::connection();
            $placeholders = implode(',', array_fill(0, count(self::KEYS), '?'));
            $stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ($placeholders)");
            $stmt->execute(self::KEYS);
            while ($row = $stmt->fetch()) {
                $settings[$row['key']] = (string) $row['value'];
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Fill missing keys with empty strings
        foreach (self::KEYS as $key) {
            if (!isset($settings[$key])) {
                $settings[$key] = '';
            }
        }

        View::renderAdmin('admin/analytics-settings', [
            'page_title' => 'Analytics & Tracking',
            'admin_page' => 'analytics-settings',
            'settings' => $settings,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'tab' => $_GET['tab'] ?? 'google',
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();

        $this->ensureSettingsTable();

        $tab = $_POST['_tab'] ?? 'google';

        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare(
                "INSERT INTO settings (`key`, `value`, updated_at) VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()"
            );

            foreach (self::KEYS as $key) {
                $value = trim((string) ($_POST[$key] ?? ''));
                $stmt->execute([$key, $value]);
            }
        } catch (\Throwable $e) {
            header('Location: /admin/analytics-settings?error=1&tab=' . urlencode($tab));
            exit;
        }

        header('Location: /admin/analytics-settings?success=1&tab=' . urlencode($tab));
        exit;
    }

    /**
     * AJAX endpoint: returns current tracking settings as JSON (for front-end verification).
     */
    public function status(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json');

        $this->ensureSettingsTable();

        $settings = [];
        try {
            $pdo = Database::connection();
            $placeholders = implode(',', array_fill(0, count(self::KEYS), '?'));
            $stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `key` IN ($placeholders)");
            $stmt->execute(self::KEYS);
            while ($row = $stmt->fetch()) {
                $settings[$row['key']] = (string) $row['value'];
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $active = [];
        $inactive = [];
        foreach (self::KEYS as $key) {
            if (!empty($settings[$key]) && !str_starts_with($key, 'custom_')) {
                $active[] = $key;
            } elseif (!str_starts_with($key, 'custom_')) {
                $inactive[] = $key;
            }
        }

        echo json_encode([
            'active_count' => count($active),
            'inactive_count' => count($inactive),
            'active' => $active,
            'inactive' => $inactive,
        ]);
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
