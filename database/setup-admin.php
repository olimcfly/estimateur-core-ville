<?php

/**
 * Setup script to create the admin_users table and seed the default admin account.
 *
 * Usage: php database/setup-admin.php [email]
 *
 * Environment variables (or edit values below):
 *   ADMIN_EMAIL - Admin email (default: admin@example.com)
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;
use App\Models\AdminUser;

// Allow email from CLI argument, env var, or default
$defaultAdminEmail = (string) ($_ENV['MAIL_ADMIN_EMAIL'] ?? $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? 'admin@example.com');
$email = $argv[1] ?? $_ENV['ADMIN_EMAIL'] ?? $defaultAdminEmail;

echo "=== Setup Admin ===\n\n";

// Test DB connection first
echo "Connexion à la base de données... ";
try {
    Database::connection();
    echo "OK\n";
} catch (\Throwable $e) {
    echo "ECHEC\n";
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "\nVérifiez votre fichier .env et la configuration de la base de données.\n";
    exit(1);
}

echo "Création de la table admin_users... ";
AdminUser::createTable();
echo "OK\n";

echo "Ajout de l'admin : {$email}... ";
AdminUser::seedDefaultAdmin($email);
echo "OK\n";

echo "\nSetup terminé !\n";
$baseUrl = rtrim((string) ($_ENV['APP_BASE_URL'] ?? ''), '/');
$loginUrl = $baseUrl !== '' ? $baseUrl . '/admin/login' : '/admin/login';
echo "Connectez-vous sur : {$loginUrl}\n";
echo "Un code de connexion sera envoyé à {$email}\n";
