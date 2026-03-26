<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminModule
{
    /**
     * Default modules definition.
     * Each module has: slug, name, description, icon, default_active, superuser_only
     */
    private static function defaultModules(): array
    {
        return [
            [
                'slug' => 'leads',
                'name' => 'Gestion des Leads',
                'description' => 'Gestion des prospects, scoring, funnel de vente',
                'icon' => 'fa-users',
                'category' => 'principal',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'partenaires',
                'name' => 'Partenaires',
                'description' => 'Gestion des partenaires et commissions',
                'icon' => 'fa-handshake',
                'category' => 'principal',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'blog',
                'name' => 'Articles Blog',
                'description' => 'Rédaction et publication d\'articles de blog',
                'icon' => 'fa-pen-fancy',
                'category' => 'contenu',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'actualites',
                'name' => 'Actualités',
                'description' => 'Gestion des actualités immobilières',
                'icon' => 'fa-newspaper',
                'category' => 'contenu',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'images',
                'name' => 'Images IA',
                'description' => 'Génération d\'images par intelligence artificielle',
                'icon' => 'fa-image',
                'category' => 'contenu',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'mailbox',
                'name' => 'Boîte Email',
                'description' => 'Boîte email intégrée — recevoir et envoyer des emails depuis l\'admin',
                'icon' => 'fa-envelope',
                'category' => 'communication',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'emails',
                'name' => 'Templates Email',
                'description' => 'Gestion des modèles d\'emails et séquences',
                'icon' => 'fa-envelope-open-text',
                'category' => 'communication',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'sequences',
                'name' => 'Séquences Email',
                'description' => 'Automatisation des séquences d\'emails',
                'icon' => 'fa-paper-plane',
                'category' => 'communication',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'notifications_email',
                'name' => 'Notifications Email',
                'description' => 'Envoi d\'emails de notification (leads, alertes)',
                'icon' => 'fa-bell',
                'category' => 'notifications',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'notifications_internes',
                'name' => 'Notifications Internes',
                'description' => 'Notifications visibles uniquement dans le panneau admin (non visibles en frontend)',
                'icon' => 'fa-inbox',
                'category' => 'notifications',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'notifications_banner',
                'name' => 'Bannières de Notification',
                'description' => 'Affichage des bandeaux toast et notifications navigateur en temps réel dans l\'admin',
                'icon' => 'fa-comment-dots',
                'category' => 'notifications',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'api_management',
                'name' => 'Gestion API',
                'description' => 'Configuration et test des clés API',
                'icon' => 'fa-key',
                'category' => 'outils',
                'default_active' => true,
                'superuser_only' => true,
            ],
            [
                'slug' => 'database',
                'name' => 'Base de données',
                'description' => 'Administration de la base de données',
                'icon' => 'fa-database',
                'category' => 'outils',
                'default_active' => true,
                'superuser_only' => true,
            ],
            [
                'slug' => 'diagnostic',
                'name' => 'Diagnostic',
                'description' => 'Diagnostic système et vérification de la configuration',
                'icon' => 'fa-stethoscope',
                'category' => 'outils',
                'default_active' => true,
                'superuser_only' => true,
            ],
            [
                'slug' => 'smtp',
                'name' => 'Configuration SMTP',
                'description' => 'Configuration et test du serveur SMTP',
                'icon' => 'fa-envelope',
                'category' => 'outils',
                'default_active' => true,
                'superuser_only' => true,
            ],
            [
                'slug' => 'achats',
                'name' => 'Achats',
                'description' => 'Suivi des achats et investissements',
                'icon' => 'fa-shopping-cart',
                'category' => 'principal',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'financement',
                'name' => 'Demandes de Financement',
                'description' => 'Demandes de financement des visiteurs — partenaire 2L Courtage',
                'icon' => 'fa-credit-card',
                'category' => 'principal',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'social_images',
                'name' => 'Images Réseaux Sociaux',
                'description' => 'Génération d\'images pour les réseaux sociaux',
                'icon' => 'fa-share-alt',
                'category' => 'contenu',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'google_ads',
                'name' => 'Google Ads',
                'description' => 'Landing pages et guide Google Ads',
                'icon' => 'fa-ad',
                'category' => 'marketing',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'gads_campaigns',
                'name' => 'Campagnes Google Ads',
                'description' => 'Créer, gérer et exporter des campagnes Google Ads avec génération IA',
                'icon' => 'fa-bullhorn',
                'category' => 'marketing',
                'default_active' => true,
                'superuser_only' => false,
            ],
            [
                'slug' => 'user_management',
                'name' => 'Gestion Utilisateurs',
                'description' => 'Gestion des comptes administrateurs et des rôles',
                'icon' => 'fa-user-shield',
                'category' => 'systeme',
                'default_active' => true,
                'superuser_only' => true,
            ],
        ];
    }

    public static function createTable(): void
    {
        Database::connection()->exec("
            CREATE TABLE IF NOT EXISTS admin_modules (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(100) NOT NULL UNIQUE,
                name VARCHAR(180) NOT NULL,
                description TEXT NOT NULL DEFAULT '',
                icon VARCHAR(80) NOT NULL DEFAULT 'fa-puzzle-piece',
                category VARCHAR(60) NOT NULL DEFAULT 'general',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                superuser_only TINYINT(1) NOT NULL DEFAULT 0,
                sort_order INT UNSIGNED NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_module_active (is_active),
                INDEX idx_module_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public static function seedDefaults(): void
    {
        $pdo = Database::connection();
        $defaults = self::defaultModules();

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO admin_modules (slug, name, description, icon, category, is_active, superuser_only, sort_order)
            VALUES (:slug, :name, :description, :icon, :category, :is_active, :superuser_only, :sort_order)
        ");

        foreach ($defaults as $i => $mod) {
            $stmt->execute([
                'slug' => $mod['slug'],
                'name' => $mod['name'],
                'description' => $mod['description'],
                'icon' => $mod['icon'],
                'category' => $mod['category'],
                'is_active' => (int) $mod['default_active'],
                'superuser_only' => (int) $mod['superuser_only'],
                'sort_order' => $i * 10,
            ]);
        }
    }

    public static function ensureTable(): void
    {
        try {
            $pdo = Database::connection();
            $tables = $pdo->query("SHOW TABLES LIKE 'admin_modules'")->fetchAll();
            if (empty($tables)) {
                self::createTable();
            }
            self::seedDefaults();
        } catch (\PDOException $e) {
            self::createTable();
            self::seedDefaults();
        }
    }

    public static function findAll(): array
    {
        self::ensureTable();
        $stmt = Database::connection()->query('SELECT * FROM admin_modules ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findBySlug(string $slug): ?array
    {
        self::ensureTable();
        $stmt = Database::connection()->prepare('SELECT * FROM admin_modules WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    /**
     * Check if a module is active.
     */
    public static function isActive(string $slug): bool
    {
        $mod = self::findBySlug($slug);
        if ($mod === null) {
            return true; // Unknown modules default to active
        }
        return (bool) $mod['is_active'];
    }

    /**
     * Check if current user has access to a module.
     * Superuser-only modules are hidden from admin role.
     * Per-user module permissions are checked for non-superuser users.
     */
    public static function hasAccess(string $slug, string $userRole = ''): bool
    {
        if ($userRole === '') {
            $userRole = AdminUser::currentRole();
        }

        $mod = self::findBySlug($slug);
        if ($mod === null) {
            return true;
        }

        if (!(bool) $mod['is_active']) {
            return false;
        }

        if ((bool) $mod['superuser_only'] && $userRole !== AdminUser::ROLE_SUPERUSER) {
            return false;
        }

        // Superusers always have full access
        if ($userRole === AdminUser::ROLE_SUPERUSER) {
            return true;
        }

        // Check per-user module permissions
        $userId = (int) ($_SESSION['admin_user_id'] ?? 0);
        if ($userId > 0) {
            return AdminUser::hasModuleAccess($userId, $slug);
        }

        return true;
    }

    public static function toggle(string $slug, bool $active): bool
    {
        self::ensureTable();
        $stmt = Database::connection()->prepare(
            'UPDATE admin_modules SET is_active = :active WHERE slug = :slug'
        );
        return $stmt->execute(['active' => (int) $active, 'slug' => $slug]);
    }

    public static function updateModule(string $slug, array $data): bool
    {
        $sets = [];
        $params = ['slug' => $slug];

        if (isset($data['is_active'])) {
            $sets[] = 'is_active = :is_active';
            $params['is_active'] = (int) $data['is_active'];
        }
        if (isset($data['superuser_only'])) {
            $sets[] = 'superuser_only = :superuser_only';
            $params['superuser_only'] = (int) $data['superuser_only'];
        }

        if (empty($sets)) {
            return false;
        }

        $sql = 'UPDATE admin_modules SET ' . implode(', ', $sets) . ' WHERE slug = :slug';
        $stmt = Database::connection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get all active modules accessible to a given role, grouped by category.
     */
    public static function getAccessibleModules(string $userRole): array
    {
        $all = self::findAll();
        $accessible = [];

        foreach ($all as $mod) {
            if (!(bool) $mod['is_active']) {
                continue;
            }
            if ((bool) $mod['superuser_only'] && $userRole !== AdminUser::ROLE_SUPERUSER) {
                continue;
            }
            $cat = $mod['category'] ?: 'general';
            $accessible[$cat][] = $mod;
        }

        return $accessible;
    }

    public static function getCategoryLabels(): array
    {
        return [
            'principal' => 'Principal',
            'contenu' => 'Contenu',
            'communication' => 'Communication',
            'notifications' => 'Notifications',
            'marketing' => 'Marketing',
            'outils' => 'Outils',
            'systeme' => 'Système',
            'general' => 'Général',
        ];
    }
}
