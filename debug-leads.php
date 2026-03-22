<?php
/**
 * Debug / Diagnostic pour la page Leads (erreur 500)
 * Accès : /debug-leads.php
 * À SUPPRIMER après résolution du problème.
 */

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/app/core/bootstrap.php';

use App\Core\Config;
use App\Core\Database;

$checks = [];

// ── 1. PHP Version ──
$phpVersion = PHP_VERSION;
$checks[] = [
    'label' => 'Version PHP',
    'value' => $phpVersion,
    'ok' => version_compare($phpVersion, '8.0.0', '>='),
    'detail' => version_compare($phpVersion, '8.0.0', '>=') ? '' : 'PHP 8.0+ requis',
];

// ── 2. Extensions requises ──
foreach (['pdo', 'pdo_mysql', 'mbstring', 'json'] as $ext) {
    $loaded = extension_loaded($ext);
    $checks[] = [
        'label' => "Extension PHP : $ext",
        'value' => $loaded ? 'chargée' : 'MANQUANTE',
        'ok' => $loaded,
        'detail' => $loaded ? '' : "Installer/activer l'extension $ext",
    ];
}

// ── 3. Fichier .env ──
$envFile = __DIR__ . '/.env';
$envExists = is_file($envFile);
$checks[] = [
    'label' => 'Fichier .env',
    'value' => $envExists ? 'présent' : 'ABSENT',
    'ok' => $envExists,
    'detail' => $envExists ? '' : 'Créer le fichier .env à la racine du projet',
];

// ── 4. Config DB ──
$dbHost = Config::get('db.host', '');
$dbName = Config::get('db.name', '');
$dbUser = Config::get('db.user', '');
$checks[] = [
    'label' => 'Config DB (host)',
    'value' => $dbHost !== '' ? $dbHost : '(vide)',
    'ok' => $dbHost !== '',
    'detail' => '',
];
$checks[] = [
    'label' => 'Config DB (name)',
    'value' => $dbName !== '' ? $dbName : '(vide)',
    'ok' => $dbName !== '',
    'detail' => $dbName === '' ? 'DB_NAME manquant dans .env ou config' : '',
];
$checks[] = [
    'label' => 'Config DB (user)',
    'value' => $dbUser !== '' ? $dbUser : '(vide)',
    'ok' => $dbUser !== '',
    'detail' => '',
];

// ── 5. Connexion BDD ──
$dbConnected = false;
$dbError = '';
try {
    $pdo = Database::connection();
    $pdo->query('SELECT 1');
    $dbConnected = true;
} catch (\Throwable $e) {
    $dbError = $e->getMessage();
}
$checks[] = [
    'label' => 'Connexion base de données',
    'value' => $dbConnected ? 'OK' : 'ERREUR',
    'ok' => $dbConnected,
    'detail' => $dbError,
];

// ── 6. Table leads ──
$tableLeadsExists = false;
$tableLeadsError = '';
if ($dbConnected) {
    try {
        $tableLeadsExists = Database::tableExists('leads');
    } catch (\Throwable $e) {
        $tableLeadsError = $e->getMessage();
    }
}
$checks[] = [
    'label' => 'Table "leads"',
    'value' => $dbConnected ? ($tableLeadsExists ? 'existe' : 'N\'EXISTE PAS') : 'non vérifiable',
    'ok' => $tableLeadsExists,
    'detail' => $tableLeadsError ?: ($tableLeadsExists ? '' : 'Aller sur /admin/leads pour créer la table'),
];

// ── 7. Tables secondaires ──
foreach (['lead_notes', 'lead_activities'] as $tbl) {
    $exists = false;
    if ($dbConnected) {
        try {
            $exists = Database::tableExists($tbl);
        } catch (\Throwable $e) {
            // ignore
        }
    }
    $checks[] = [
        'label' => "Table \"$tbl\"",
        'value' => $dbConnected ? ($exists ? 'existe' : 'n\'existe pas') : 'non vérifiable',
        'ok' => $exists,
        'detail' => $exists ? '' : 'Optionnelle mais recommandée',
    ];
}

// ── 8. Colonnes table leads ──
if ($tableLeadsExists) {
    try {
        $stmt = $pdo->query('SHOW COLUMNS FROM leads');
        $columns = array_column($stmt->fetchAll(), 'Field');
        $required = ['id', 'website_id', 'lead_type', 'nom', 'email', 'telephone', 'ville', 'estimation', 'score', 'statut', 'created_at'];
        $missing = array_diff($required, $columns);
        $checks[] = [
            'label' => 'Colonnes requises (leads)',
            'value' => empty($missing) ? 'toutes présentes (' . count($columns) . ' colonnes)' : 'MANQUANTES: ' . implode(', ', $missing),
            'ok' => empty($missing),
            'detail' => empty($missing) ? '' : 'Colonnes manquantes dans la table leads',
        ];
    } catch (\Throwable $e) {
        $checks[] = [
            'label' => 'Colonnes requises (leads)',
            'value' => 'ERREUR',
            'ok' => false,
            'detail' => $e->getMessage(),
        ];
    }
}

// ── 9. Fichiers critiques ──
$criticalFiles = [
    'app/controllers/AdminLeadController.php' => 'Contrôleur admin leads',
    'app/controllers/EstimationController.php' => 'Contrôleur estimation/leads',
    'app/models/Lead.php' => 'Modèle Lead',
    'app/models/LeadNote.php' => 'Modèle LeadNote',
    'app/models/LeadActivity.php' => 'Modèle LeadActivity',
    'app/views/admin/leads.php' => 'Vue admin leads',
    'app/views/estimation/leads.php' => 'Vue estimation leads',
    'app/core/View.php' => 'Classe View',
    'app/core/Database.php' => 'Classe Database',
    'app/core/helpers.php' => 'Helpers (fonction e())',
];

foreach ($criticalFiles as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    $exists = is_file($path);
    $checks[] = [
        'label' => "Fichier : $desc",
        'value' => $exists ? 'présent' : 'MANQUANT',
        'ok' => $exists,
        'detail' => $exists ? $file : "Fichier $file introuvable",
    ];
}

// ── 10. Classes chargables ──
$classes = [
    'App\\Controllers\\AdminLeadController',
    'App\\Controllers\\EstimationController',
    'App\\Controllers\\AuthController',
    'App\\Models\\Lead',
    'App\\Models\\LeadNote',
    'App\\Models\\LeadActivity',
    'App\\Core\\View',
    'App\\Core\\Database',
];

foreach ($classes as $class) {
    $loadable = false;
    $loadError = '';
    try {
        $loadable = class_exists($class);
    } catch (\Throwable $e) {
        $loadError = $e->getMessage();
    }
    $shortName = substr($class, strrpos($class, '\\') + 1);
    $checks[] = [
        'label' => "Classe : $shortName",
        'value' => $loadable ? 'chargeable' : 'ERREUR',
        'ok' => $loadable,
        'detail' => $loadError,
    ];
}

// ── 11. Test requête Lead::findAllLeads() ──
if ($tableLeadsExists) {
    try {
        $leadModel = new \App\Models\Lead();
        $leads = $leadModel->findAllLeads();
        $checks[] = [
            'label' => 'Lead::findAllLeads()',
            'value' => count($leads) . ' leads trouvés',
            'ok' => true,
            'detail' => '',
        ];
    } catch (\Throwable $e) {
        $checks[] = [
            'label' => 'Lead::findAllLeads()',
            'value' => 'ERREUR',
            'ok' => false,
            'detail' => $e->getMessage(),
        ];
    }

    try {
        $leadModel = new \App\Models\Lead();
        $counts = $leadModel->countByStatut();
        $checks[] = [
            'label' => 'Lead::countByStatut()',
            'value' => 'OK (' . array_sum($counts) . ' leads)',
            'ok' => true,
            'detail' => '',
        ];
    } catch (\Throwable $e) {
        $checks[] = [
            'label' => 'Lead::countByStatut()',
            'value' => 'ERREUR',
            'ok' => false,
            'detail' => $e->getMessage(),
        ];
    }
}

// ── 12. Session ──
$checks[] = [
    'label' => 'Session PHP',
    'value' => session_status() === PHP_SESSION_ACTIVE ? 'active' : 'inactive',
    'ok' => session_status() === PHP_SESSION_ACTIVE,
    'detail' => '',
];

$checks[] = [
    'label' => 'Authentification admin',
    'value' => !empty($_SESSION['admin_logged_in']) ? 'connecté' : 'non connecté',
    'ok' => true, // info only
    'detail' => empty($_SESSION['admin_logged_in']) ? 'La page leads requiert une authentification' : '',
];

// ── Résumé ──
$totalChecks = count($checks);
$passed = count(array_filter($checks, fn($c) => $c['ok']));
$failed = $totalChecks - $passed;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Debug Leads - Diagnostic</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f1f5f9; color: #1e293b; padding: 2rem; }
  .container { max-width: 900px; margin: 0 auto; }
  h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
  .summary { display: flex; gap: 1rem; margin: 1rem 0 2rem; }
  .summary-card { padding: 1rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; }
  .summary-ok { background: #f0fdf4; color: #166534; border: 1px solid #86efac; }
  .summary-fail { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
  .summary-total { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  th { background: #f8fafc; text-align: left; padding: 0.6rem 1rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
  td { padding: 0.6rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
  tr:hover { background: #f8fafc; }
  .status { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; }
  .status-ok { background: #22c55e; }
  .status-fail { background: #ef4444; }
  .detail { color: #ef4444; font-size: 0.78rem; margin-top: 2px; }
  .warn { color: #b45309; background: #fffbeb; border: 1px solid #fde68a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; }
</style>
</head>
<body>
<div class="container">
  <h1>Diagnostic Page Leads</h1>
  <p style="color: #64748b; margin-bottom: 1rem;">Vérification de tous les composants nécessaires au fonctionnement de la page leads.</p>

  <div class="warn">
    Ce fichier est un outil de debug temporaire. Supprimez-le après résolution du problème (<code>rm debug-leads.php</code>).
  </div>

  <div class="summary">
    <div class="summary-card summary-total"><?= $totalChecks ?> vérifications</div>
    <div class="summary-card summary-ok"><?= $passed ?> OK</div>
    <?php if ($failed > 0): ?>
      <div class="summary-card summary-fail"><?= $failed ?> problème<?= $failed > 1 ? 's' : '' ?></div>
    <?php endif; ?>
  </div>

  <table>
    <thead>
      <tr>
        <th></th>
        <th>Vérification</th>
        <th>Résultat</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($checks as $check): ?>
      <tr>
        <td><span class="status <?= $check['ok'] ? 'status-ok' : 'status-fail' ?>"></span></td>
        <td><?= htmlspecialchars($check['label']) ?></td>
        <td>
          <?= htmlspecialchars($check['value']) ?>
          <?php if ($check['detail'] !== ''): ?>
            <div class="detail"><?= htmlspecialchars($check['detail']) ?></div>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p style="margin-top: 2rem; color: #64748b; font-size: 0.8rem;">
    Généré le <?= date('d/m/Y à H:i:s') ?> &mdash; PHP <?= PHP_VERSION ?>
  </p>
</div>
</body>
</html>
