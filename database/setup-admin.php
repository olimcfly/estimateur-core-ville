<?php

/**
 * Setup script to create the admin_users table and seed the default admin account.
 *
 * Usage: php database/setup-admin.php
 *
 * Environment variables (or edit values below):
 *   ADMIN_EMAIL    - Admin email (default: contact@estimation-immobilier-bordeaux.fr)
 *   ADMIN_PASSWORD - Admin password (MUST be set before running)
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Models\AdminUser;

$email = $_ENV['ADMIN_EMAIL'] ?? 'contact@estimation-immobilier-bordeaux.fr';
$password = $_ENV['ADMIN_PASSWORD'] ?? '';

if ($password === '') {
    echo "ERROR: You must set the ADMIN_PASSWORD environment variable.\n";
    echo "Example: ADMIN_PASSWORD='YourSecurePassword123!' php database/setup-admin.php\n";
    exit(1);
}

echo "Creating admin_users table...\n";
AdminUser::createTable();
echo "Table created.\n";

echo "Seeding admin user: {$email}\n";
AdminUser::seedDefaultAdmin($email, $password);
echo "Admin user ready.\n";
echo "\nYou can now log in at: https://estimation-immobilier-bordeaux.fr/admin/login\n";
