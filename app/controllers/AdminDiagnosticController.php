<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Services\Mailer;

final class AdminDiagnosticController
{
    public function index(): void
    {
        AuthController::requireAuth();

        // 1. Fichier .env
        $envFile = dirname(__DIR__, 2) . '/.env';
        $envExists = is_file($envFile);

        // 2. Configuration DB
        $dbConfig = [
            'host' => Config::get('db.host', '(non défini)'),
            'port' => Config::get('db.port', '(non défini)'),
            'name' => Config::get('db.name', '(non défini)'),
            'user' => Config::get('db.user', '(non défini)'),
        ];
        $dbPassDefined = Config::get('db.pass', '') !== '';

        // 3. Connexion DB
        $dbConnected = false;
        $dbVersion = '';
        $dbError = '';
        $pdo = null;
        try {
            $pdo = Database::connection();
            $dbConnected = true;
            $dbVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION) ?: '';
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        // 4. Tables
        $tables = [];
        if ($dbConnected && $pdo !== null) {
            try {
                $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 5. Admin users
        $adminTableOk = false;
        $adminCount = 0;
        $adminColumns = [];
        $loginCodeExists = false;
        $adminEmails = [];
        if ($dbConnected && $pdo !== null && in_array('admin_users', $tables, true)) {
            $adminTableOk = true;
            try {
                $adminColumns = $pdo->query('SHOW COLUMNS FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
                $loginCodeExists = in_array('login_code', $adminColumns, true);
                $rows = $pdo->query('SELECT email FROM admin_users')->fetchAll(\PDO::FETCH_COLUMN);
                $adminEmails = $rows ?: [];
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // 6. SMTP Configuration
        $smtpHost = (string) Config::get('mail.smtp_host');
        $smtpPort = (int) Config::get('mail.smtp_port', 587);
        $smtpUser = (string) Config::get('mail.smtp_user');
        $smtpPass = (string) Config::get('mail.smtp_pass');
        $smtpEncryption = (string) Config::get('mail.smtp_encryption', 'tls');
        $smtpFrom = (string) Config::get('mail.from', '');
        $smtpPassDefined = $smtpPass !== '';
        $smtpConfigured = $smtpHost !== '' && $smtpUser !== '' && $smtpPass !== '';

        // 7. Test SMTP connection
        $smtpConnected = false;
        $smtpError = '';
        $smtpDiagnostics = [];
        $smtpAdvice = '';
        if ($smtpConfigured) {
            try {
                if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->Port = $smtpPort;
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpUser;
                    $mail->Password = $smtpPass;
                    $mail->Timeout = 10;
                    $mail->SMTPDebug = 0;

                    if ($smtpPort === 465) {
                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                    } elseif ($smtpEncryption === 'tls' || $smtpPort === 587) {
                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    } else {
                        $mail->SMTPSecure = $smtpEncryption;
                    }
                    $mail->AuthType = '';

                    $mail->smtpConnect();
                    $mail->smtpClose();
                    $smtpConnected = true;
                } else {
                    $smtpError = 'PHPMailer non installé — Exécutez "composer install"';
                }
            } catch (\Throwable $e) {
                $smtpError = $e->getMessage();
                $smtpDiagnostics = Mailer::diagnose(['error_message' => $smtpError]);
            }
        }

        // 8. Collect issues
        $issues = [];
        if (!$envExists) {
            $issues[] = 'Le fichier .env est absent.';
        }
        if (!$dbPassDefined) {
            $issues[] = 'Le mot de passe de la base de données est vide.';
        }
        if (!$dbConnected) {
            $issues[] = 'Impossible de se connecter à la base de données.';
        }
        if ($dbConnected && !$adminTableOk) {
            $issues[] = 'La table admin_users est absente.';
        }
        if ($adminTableOk && !$loginCodeExists) {
            $issues[] = 'La colonne login_code est manquante dans admin_users.';
        }
        if ($smtpConfigured && !$smtpConnected) {
            $issues[] = 'La connexion SMTP a échoué.';
        }

        View::renderAdmin('admin/diagnostic', [
            'page_title' => 'Diagnostic - Admin',
            'admin_page_title' => 'Diagnostic',
            'admin_page' => 'diagnostic',
            'breadcrumb' => 'Diagnostic',
            'envExists' => $envExists,
            'dbConfig' => $dbConfig,
            'dbPassDefined' => $dbPassDefined,
            'dbConnected' => $dbConnected,
            'dbVersion' => $dbVersion,
            'dbError' => $dbError,
            'tables' => $tables,
            'adminTableOk' => $adminTableOk,
            'adminCount' => $adminCount,
            'adminColumns' => $adminColumns,
            'loginCodeExists' => $loginCodeExists,
            'adminEmails' => $adminEmails,
            'smtpHost' => $smtpHost,
            'smtpPort' => $smtpPort,
            'smtpUser' => $smtpUser,
            'smtpPassDefined' => $smtpPassDefined,
            'smtpEncryption' => $smtpEncryption,
            'smtpFrom' => $smtpFrom,
            'smtpConfigured' => $smtpConfigured,
            'smtpConnected' => $smtpConnected,
            'smtpError' => $smtpError,
            'smtpDiagnostics' => $smtpDiagnostics,
            'smtpAdvice' => $smtpAdvice,
            'issues' => $issues,
        ]);
    }
}
