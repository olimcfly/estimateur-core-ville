<?php
/**
 * Google Ads Campaign — Landing Page Export Endpoint
 * Exports landing HTML as a PHP file and updates .htaccess
 */

declare(strict_types=1);

// Bootstrap the application
require_once dirname(__DIR__, 3) . '/app/core/bootstrap.php';

use App\Core\Database;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $body = json_decode(file_get_contents('php://input'), true);
    $id = (int) ($body['id'] ?? 0);
    if ($id <= 0) {
        throw new \InvalidArgumentException('Missing campaign id');
    }

    $pdo = Database::connection();
    $siteConfig = getSiteConfig();
    $websiteId = (int) ($body['website_id'] ?? $siteConfig['website_id'] ?? 1);

    // 1. Load campaign
    $stmt = $pdo->prepare('SELECT * FROM google_ads_campaigns WHERE id = ? AND website_id = ?');
    $stmt->execute([$id, $websiteId]);
    $campaign = $stmt->fetch();

    if (!$campaign) {
        throw new \RuntimeException('Campaign not found or access denied');
    }

    // 2. Verify landing_html
    $landingHtml = $campaign['landing_html'] ?? '';
    if (trim($landingHtml) === '') {
        throw new \RuntimeException('Landing HTML is empty. Generate a landing page first.');
    }

    // 3. Resolve target directory
    $basePath = dirname(__DIR__, 3);
    $frontLpDir = $basePath . '/front/lp';

    if (!is_dir($frontLpDir)) {
        if (!mkdir($frontLpDir, 0755, true)) {
            throw new \RuntimeException('Could not create directory: /front/lp/');
        }
    }

    if (!is_writable($frontLpDir)) {
        throw new \RuntimeException('Directory not writable: /front/lp/');
    }

    // 4. Build PHP file content
    $slug = $campaign['campaign_slug'];
    if (!$slug) {
        $slug = slugifyExport($campaign['campaign_label'] ?: 'campaign-' . $id);
    }

    $label = addslashes($campaign['campaign_label'] ?? '');
    $ville = addslashes($campaign['ville'] ?? '');
    $date  = date('Y-m-d H:i:s');

    $phpContent = "<?php\n";
    $phpContent .= "/**\n";
    $phpContent .= " * Campaign: {$label}\n";
    $phpContent .= " * Ville: {$ville}\n";
    $phpContent .= " * Generated: {$date}\n";
    $phpContent .= " * ID: {$id}\n";
    $phpContent .= " * AUTO-GENERATED — do not edit manually\n";
    $phpContent .= " */\n";
    $phpContent .= "?>\n";
    $phpContent .= $landingHtml;

    // 5. Write file
    $filePath = $frontLpDir . '/' . $slug . '.php';
    $written = file_put_contents($filePath, $phpContent);
    if ($written === false) {
        throw new \RuntimeException('Failed to write file: /front/lp/' . $slug . '.php');
    }

    // 6. Update DB
    $landingPath = '/lp/' . $slug . '.php';
    $stmtUpdate = $pdo->prepare('
        UPDATE google_ads_campaigns
        SET landing_path = ?, landing_exported_at = NOW()
        WHERE id = ? AND website_id = ?
    ');
    $stmtUpdate->execute([$landingPath, $id, $websiteId]);

    // 7. Update .htaccess
    $htaccessPath = $basePath . '/.htaccess';
    updateHtaccess($htaccessPath, $slug);

    // 8. Build URL finale
    $domain = $campaign['domain'] ?? '';
    $urlFinale = $domain ? 'https://' . $domain . $landingPath : $landingPath;

    echo json_encode([
        'ok'   => true,
        'file' => '/front/lp/' . $slug . '.php',
        'url'  => $urlFinale
    ]);
} catch (\Throwable $e) {
    http_response_code(200);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

/* ================================================================
   .htaccess updater
   ================================================================ */
function updateHtaccess(string $htaccessPath, string $slug): void
{
    if (!file_exists($htaccessPath)) {
        return; // No .htaccess to update
    }

    if (!is_writable($htaccessPath)) {
        throw new \RuntimeException('.htaccess is not writable');
    }

    $content = file_get_contents($htaccessPath);
    $rule = "RewriteRule ^lp/{$slug}$ /front/lp/{$slug}.php [L]";

    // Check for existing rule (avoid duplicates)
    if (str_contains($content, "^lp/{$slug}$")) {
        return; // Already exists
    }

    // Insert before the generic catch-all (index.php fallback)
    $catchAll = 'RewriteRule ^ index.php [L,QSA]';
    $pos = strpos($content, $catchAll);

    if ($pos !== false) {
        $content = substr($content, 0, $pos) . $rule . "\n" . substr($content, $pos);
    } else {
        // Append at end if no catch-all found
        $content .= "\n" . $rule . "\n";
    }

    file_put_contents($htaccessPath, $content);
}

/* ================================================================
   Slugify helper
   ================================================================ */
function slugifyExport(string $str): string
{
    if (function_exists('transliterator_transliterate')) {
        $str = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $str);
    } else {
        $str = strtolower($str);
    }
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}
