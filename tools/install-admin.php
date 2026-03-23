<?php
/**
 * Installateur Admin - Interface Web
 *
 * Copie les fichiers admin vers un autre site SANS écraser la config personnalisée.
 * Protégé par l'authentification admin.
 *
 * Usage: https://mon-site.fr/tools/install-admin.php
 */

declare(strict_types=1);

// Si appelé directement (pas via le routeur), bootstrap et vérifier l'auth
if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['admin_logged_in'])) {
    require_once __DIR__ . '/../app/core/bootstrap.php';
    \App\Controllers\AuthController::requireAuth();
}

// ============================================================
// Configuration
// ============================================================

$sourceDir = dirname(__DIR__);

// Fichiers protégés - ne seront JAMAIS écrasés
$protectedPaths = [
    'config/config.php',
    '.env',
    '.env.local',
    '.htaccess',
    'database/',
    'logs/',
    'vendor/',
    'public/assets/images/ai-generated/',
];

// Liste des fichiers à copier, organisés par catégorie
$filesToCopy = [
    'Module Google Ads (admin/)' => glob_recursive($sourceDir . '/admin'),
    'Contrôleurs Admin' => array_merge(
        glob($sourceDir . '/app/controllers/Admin*.php'),
        array_filter([
            $sourceDir . '/app/controllers/AuthController.php',
        ], 'is_file')
    ),
    'Vues Admin' => array_merge(
        glob_recursive($sourceDir . '/app/views/admin'),
        array_filter([
            $sourceDir . '/app/views/layouts/admin.php',
        ], 'is_file')
    ),
    'Modèles' => array_filter([
        $sourceDir . '/app/models/AdminModule.php',
        $sourceDir . '/app/models/AdminNotification.php',
        $sourceDir . '/app/models/AdminUser.php',
        $sourceDir . '/app/models/Lead.php',
        $sourceDir . '/app/models/LeadActivity.php',
        $sourceDir . '/app/models/LeadNote.php',
        $sourceDir . '/app/models/Article.php',
        $sourceDir . '/app/models/Achat.php',
        $sourceDir . '/app/models/Actualite.php',
        $sourceDir . '/app/models/Partenaire.php',
        $sourceDir . '/app/models/DesignTemplate.php',
        $sourceDir . '/app/models/NewsletterSubscriber.php',
        $sourceDir . '/app/models/RssSource.php',
        $sourceDir . '/app/models/RssArticle.php',
    ], 'is_file'),
    'Services' => array_filter([
        $sourceDir . '/app/services/AIService.php',
        $sourceDir . '/app/services/ActualiteService.php',
        $sourceDir . '/app/services/ImageGeneratorService.php',
        $sourceDir . '/app/services/LeadNotificationService.php',
        $sourceDir . '/app/services/LeadScoringService.php',
        $sourceDir . '/app/services/Mailer.php',
        $sourceDir . '/app/services/PerplexityService.php',
        $sourceDir . '/app/services/SeoAnalyzerService.php',
        $sourceDir . '/app/services/SmtpAuthClient.php',
        $sourceDir . '/app/services/SmtpLogger.php',
        $sourceDir . '/app/services/RssFeedService.php',
        $sourceDir . '/app/services/UtmTrackingService.php',
    ], 'is_file'),
    'Core & Routes' => array_filter([
        $sourceDir . '/app/core/bootstrap.php',
        $sourceDir . '/app/core/Config.php',
        $sourceDir . '/app/core/Database.php',
        $sourceDir . '/app/core/Router.php',
        $sourceDir . '/app/core/Validator.php',
        $sourceDir . '/app/core/View.php',
        $sourceDir . '/app/core/helpers.php',
        $sourceDir . '/routes/web.php',
    ], 'is_file'),
];

// ============================================================
// Fonctions
// ============================================================

function glob_recursive(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function isProtected(string $relPath, array $protectedPaths): bool
{
    foreach ($protectedPaths as $protected) {
        if (str_starts_with($relPath, $protected)) {
            return true;
        }
    }
    return false;
}

function generateBackup(string $sourceDir, array $filesToCopy, array $protectedPaths): string
{
    $zip = new ZipArchive();
    $tmpFile = tempnam(sys_get_temp_dir(), 'admin_backup_') . '.zip';

    if ($zip->open($tmpFile, ZipArchive::CREATE) !== true) {
        throw new RuntimeException('Impossible de créer le fichier ZIP');
    }

    $count = 0;
    foreach ($filesToCopy as $category => $files) {
        foreach ($files as $file) {
            $relPath = ltrim(str_replace($sourceDir, '', $file), '/');
            if (!isProtected($relPath, $protectedPaths)) {
                $zip->addFile($file, $relPath);
                $count++;
            }
        }
    }

    $zip->close();
    return $tmpFile;
}

// ============================================================
// Actions
// ============================================================

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Action : Télécharger le ZIP
if ($action === 'download') {
    try {
        $zipFile = generateBackup($sourceDir, $filesToCopy, $protectedPaths);
        $zipName = 'admin-backup-' . date('Y-m-d-His') . '.zip';

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        unlink($zipFile);
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

// Action : Installer vers un dossier local
$installResult = null;
if ($action === 'install' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $destDir = trim($_POST['destination'] ?? '');

    if (empty($destDir)) {
        $error = 'Veuillez spécifier un chemin de destination.';
    } elseif (!is_dir($destDir)) {
        $error = "Le dossier de destination n'existe pas : " . htmlspecialchars($destDir);
    } else {
        $installResult = ['copied' => 0, 'skipped' => 0, 'errors' => 0, 'details' => []];

        foreach ($filesToCopy as $category => $files) {
            foreach ($files as $file) {
                $relPath = ltrim(str_replace($sourceDir, '', $file), '/');

                if (isProtected($relPath, $protectedPaths)) {
                    $installResult['skipped']++;
                    $installResult['details'][] = ['status' => 'skip', 'file' => $relPath];
                    continue;
                }

                $destFile = $destDir . '/' . $relPath;
                $destDirPath = dirname($destFile);

                if (!is_dir($destDirPath)) {
                    @mkdir($destDirPath, 0755, true);
                }

                if (@copy($file, $destFile)) {
                    $installResult['copied']++;
                    $installResult['details'][] = ['status' => 'ok', 'file' => $relPath];
                } else {
                    $installResult['errors']++;
                    $installResult['details'][] = ['status' => 'error', 'file' => $relPath];
                }
            }
        }
    }
}

// ============================================================
// Préparer la liste des fichiers pour l'affichage
// ============================================================
$fileList = [];
$totalFiles = 0;
foreach ($filesToCopy as $category => $files) {
    $categoryFiles = [];
    foreach ($files as $file) {
        $relPath = ltrim(str_replace($sourceDir, '', $file), '/');
        $protected = isProtected($relPath, $protectedPaths);
        $categoryFiles[] = ['path' => $relPath, 'protected' => $protected];
        $totalFiles++;
    }
    $fileList[$category] = $categoryFiles;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installateur Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #e2e8f0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { font-size: 1.8rem; margin-bottom: 8px; color: #fff; }
        .subtitle { color: #94a3b8; margin-bottom: 30px; }

        .card { background: #1e293b; border-radius: 12px; padding: 24px; margin-bottom: 20px; border: 1px solid #334155; }
        .card h2 { font-size: 1.2rem; margin-bottom: 16px; color: #38bdf8; }

        .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 640px) { .actions { grid-template-columns: 1fr; } }

        .action-card { background: #1e293b; border-radius: 12px; padding: 24px; border: 1px solid #334155; }
        .action-card h3 { margin-bottom: 8px; color: #fff; }
        .action-card p { color: #94a3b8; font-size: 0.9rem; margin-bottom: 16px; }

        input[type="text"] { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #475569; background: #0f172a; color: #e2e8f0; font-size: 0.95rem; margin-bottom: 12px; }
        input[type="text"]:focus { outline: none; border-color: #38bdf8; }
        input[type="text"]::placeholder { color: #64748b; }

        .btn { display: inline-block; padding: 10px 24px; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-green { background: #059669; color: #fff; }
        .btn-green:hover { background: #047857; }

        .file-list { max-height: 300px; overflow-y: auto; }
        .category { margin-bottom: 16px; }
        .category-title { font-weight: 600; color: #38bdf8; margin-bottom: 6px; font-size: 0.9rem; cursor: pointer; }
        .category-title:hover { color: #7dd3fc; }
        .category-files { padding-left: 16px; display: none; }
        .category-files.open { display: block; }
        .file-item { font-size: 0.82rem; padding: 2px 0; font-family: 'Fira Code', monospace; }
        .file-ok { color: #4ade80; }
        .file-skip { color: #fbbf24; }
        .file-error { color: #f87171; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-blue { background: #1e3a5f; color: #38bdf8; }
        .badge-yellow { background: #422006; color: #fbbf24; }

        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; }
        .alert-success { background: #064e3b; border: 1px solid #059669; color: #6ee7b7; }
        .alert-error { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; }

        .stats { display: flex; gap: 20px; margin-top: 12px; flex-wrap: wrap; }
        .stat { text-align: center; }
        .stat-number { font-size: 1.8rem; font-weight: 700; }
        .stat-label { font-size: 0.8rem; color: #94a3b8; }
        .stat-green .stat-number { color: #4ade80; }
        .stat-yellow .stat-number { color: #fbbf24; }
        .stat-red .stat-number { color: #f87171; }

        .protected-list { margin-top: 12px; }
        .protected-item { font-size: 0.85rem; color: #fbbf24; padding: 2px 0; }
        .protected-item::before { content: '🔒 '; }

        .back-link { display: inline-block; margin-bottom: 20px; color: #94a3b8; text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin" class="back-link">&larr; Retour à l'admin</a>

        <h1>Installateur Admin</h1>
        <p class="subtitle">Copier les fichiers admin vers un autre site sans écraser la configuration personnalisée</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($installResult): ?>
            <div class="alert alert-success">
                Installation terminée !
                <div class="stats">
                    <div class="stat stat-green">
                        <div class="stat-number"><?= $installResult['copied'] ?></div>
                        <div class="stat-label">Copiés</div>
                    </div>
                    <div class="stat stat-yellow">
                        <div class="stat-number"><?= $installResult['skipped'] ?></div>
                        <div class="stat-label">Protégés</div>
                    </div>
                    <div class="stat stat-red">
                        <div class="stat-number"><?= $installResult['errors'] ?></div>
                        <div class="stat-label">Erreurs</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Détail de l'installation</h2>
                <div class="file-list">
                    <?php foreach ($installResult['details'] as $detail): ?>
                        <div class="file-item file-<?= $detail['status'] ?>">
                            [<?= strtoupper($detail['status']) ?>] <?= htmlspecialchars($detail['file']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="actions">
            <!-- Option 1 : Télécharger ZIP -->
            <div class="action-card">
                <h3>Télécharger le ZIP</h3>
                <p>Télécharger une archive ZIP des fichiers admin, puis la décompresser manuellement sur l'autre site.</p>
                <a href="?action=download" class="btn btn-primary">Télécharger le ZIP</a>
            </div>

            <!-- Option 2 : Installer directement -->
            <div class="action-card">
                <h3>Installer sur le serveur</h3>
                <p>Copier directement les fichiers vers un autre dossier sur ce serveur.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="install">
                    <input type="text" name="destination" placeholder="/var/www/autre-site"
                           value="<?= htmlspecialchars($_POST['destination'] ?? '') ?>">
                    <button type="submit" class="btn btn-green">Installer</button>
                </form>
            </div>
        </div>

        <!-- Fichiers protégés -->
        <div class="card">
            <h2>Fichiers protégés <span class="badge badge-yellow">Ne seront PAS écrasés</span></h2>
            <div class="protected-list">
                <?php foreach ($protectedPaths as $path): ?>
                    <div class="protected-item"><?= htmlspecialchars($path) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Liste des fichiers -->
        <div class="card">
            <h2>Fichiers inclus <span class="badge badge-blue"><?= $totalFiles ?> fichiers</span></h2>
            <div class="file-list">
                <?php foreach ($fileList as $category => $files): ?>
                    <div class="category">
                        <div class="category-title" onclick="this.nextElementSibling.classList.toggle('open')">
                            ▸ <?= htmlspecialchars($category) ?> (<?= count($files) ?> fichiers)
                        </div>
                        <div class="category-files">
                            <?php foreach ($files as $f): ?>
                                <div class="file-item <?= $f['protected'] ? 'file-skip' : 'file-ok' ?>">
                                    <?= $f['protected'] ? '🔒' : '✓' ?> <?= htmlspecialchars($f['path']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Rappels -->
        <div class="card">
            <h2>Rappels après installation</h2>
            <ol style="padding-left: 20px; color: #94a3b8; line-height: 2;">
                <li>Vérifier que <code style="color: #4ade80;">config/config.php</code> existe sur le site cible</li>
                <li>Vérifier que <code style="color: #4ade80;">.env</code> est configuré sur le site cible</li>
                <li>Lancer <code style="color: #4ade80;">composer install</code> si vendor/ n'existe pas</li>
                <li>Exécuter les migrations SQL si nécessaire</li>
            </ol>
        </div>
    </div>
</body>
</html>
