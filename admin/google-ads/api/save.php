<?php
/**
 * Google Ads Campaign — Save / Toggle / Quality Score Endpoint
 * Actions: save_campaign, toggle_status, update_quality_score
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
    if (!$body || empty($body['action'])) {
        throw new \InvalidArgumentException('Missing action parameter');
    }

    $pdo = Database::connection();
    $siteConfig = getSiteConfig();
    $websiteId = (int) ($body['website_id'] ?? $siteConfig['website_id'] ?? 1);

    switch ($body['action']) {
        case 'save_campaign':
            echo json_encode(handleSaveCampaign($pdo, $body, $websiteId));
            break;
        case 'toggle_status':
            echo json_encode(handleToggleStatus($pdo, $body, $websiteId));
            break;
        case 'update_quality_score':
            echo json_encode(handleUpdateQualityScore($pdo, $body, $websiteId));
            break;
        default:
            throw new \InvalidArgumentException('Unknown action: ' . $body['action']);
    }
} catch (\Throwable $e) {
    http_response_code(200);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

/* ================================================================
   ACTION: save_campaign
   ================================================================ */
function handleSaveCampaign(\PDO $pdo, array $body, int $websiteId): array
{
    $id            = (int) ($body['id'] ?? 0);
    $campaignType  = trim($body['campaign_type'] ?? '');
    $campaignLabel = trim($body['campaign_label'] ?? '');
    $advisorName   = trim($body['advisor_name'] ?? $body['name'] ?? '');
    $ville         = trim($body['ville'] ?? '');
    $domain        = trim($body['domain'] ?? '');
    $budgetDaily   = (float) ($body['budget_daily'] ?? $body['budget'] ?? 0);
    $ads           = $body['ads'] ?? null;
    $keywords      = $body['keywords'] ?? [];
    $landingHtml   = $body['landing_html'] ?? '';
    $landingPath   = trim($body['landing_path'] ?? '');
    $status        = trim($body['status'] ?? 'draft');

    if (!$campaignType || !$campaignLabel || !$ville) {
        throw new \InvalidArgumentException('Missing required fields: campaign_type, campaign_label, ville');
    }

    // Compute slug
    $campaignSlug = slugifyCampaign($campaignLabel);

    // Compute landing path from campaign type
    if ($landingPath === '') {
        $villeSlug = slugifyCampaign($ville);
        switch ($campaignType) {
            case 'estimation':
                $landingPath = '/lp/estimation-' . $villeSlug;
                break;
            case 'vendre':
                $landingPath = '/lp/vendre-maison-' . $villeSlug;
                break;
            case 'avis':
                $landingPath = '/lp/avis-valeur-gratuit';
                break;
        }
    }

    // Compute URL finale
    $urlFinale = $body['url_finale'] ?? '';
    if ($urlFinale === '' && $domain) {
        $urlFinale = 'https://' . $domain . $landingPath
            . '?utm_source=google&utm_medium=cpc&utm_campaign=' . $campaignSlug;
    }

    // Tracking template
    $trackingTemplate = $body['tracking_template'] ?? '{lpurl}&utm_term={keyword}&utm_content={creative}&gclid={gclid}';

    // Encode JSON fields
    $adsJson     = $ads !== null ? json_encode($ads, JSON_UNESCAPED_UNICODE) : null;
    $keywordsJson = !empty($keywords) ? json_encode($keywords, JSON_UNESCAPED_UNICODE) : null;

    if ($id > 0) {
        // UPDATE
        $stmt = $pdo->prepare('
            UPDATE google_ads_campaigns SET
                campaign_type = ?,
                campaign_label = ?,
                campaign_slug = ?,
                advisor_name = ?,
                ville = ?,
                domain = ?,
                budget_daily = ?,
                ads_json = ?,
                keywords_json = ?,
                landing_html = ?,
                landing_path = ?,
                url_finale = ?,
                tracking_template = ?,
                status = ?
            WHERE id = ? AND website_id = ?
        ');
        $stmt->execute([
            $campaignType, $campaignLabel, $campaignSlug,
            $advisorName, $ville, $domain, $budgetDaily,
            $adsJson, $keywordsJson,
            $landingHtml, $landingPath,
            $urlFinale, $trackingTemplate,
            $status,
            $id, $websiteId
        ]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException('Campaign not found or access denied');
        }

        return ['ok' => true, 'id' => $id];
    }

    // INSERT
    $stmt = $pdo->prepare('
        INSERT INTO google_ads_campaigns
            (website_id, campaign_type, campaign_label, campaign_slug,
             advisor_name, ville, domain, budget_daily,
             ads_json, keywords_json, landing_html, landing_path,
             url_finale, tracking_template, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $websiteId,
        $campaignType, $campaignLabel, $campaignSlug,
        $advisorName, $ville, $domain, $budgetDaily,
        $adsJson, $keywordsJson,
        $landingHtml, $landingPath,
        $urlFinale, $trackingTemplate,
        $status
    ]);

    return ['ok' => true, 'id' => (int) $pdo->lastInsertId()];
}

/* ================================================================
   ACTION: toggle_status
   ================================================================ */
function handleToggleStatus(\PDO $pdo, array $body, int $websiteId): array
{
    $id     = (int) ($body['id'] ?? 0);
    $status = trim($body['status'] ?? '');

    $allowed = ['active', 'paused', 'draft', 'archived'];
    if (!in_array($status, $allowed, true)) {
        throw new \InvalidArgumentException('Invalid status. Allowed: ' . implode(', ', $allowed));
    }

    if ($id <= 0) {
        throw new \InvalidArgumentException('Missing campaign id');
    }

    $stmt = $pdo->prepare('UPDATE google_ads_campaigns SET status = ? WHERE id = ? AND website_id = ?');
    $stmt->execute([$status, $id, $websiteId]);

    if ($stmt->rowCount() === 0) {
        throw new \RuntimeException('Campaign not found or access denied');
    }

    return ['ok' => true];
}

/* ================================================================
   ACTION: update_quality_score
   ================================================================ */
function handleUpdateQualityScore(\PDO $pdo, array $body, int $websiteId): array
{
    $id    = (int) ($body['id'] ?? 0);
    $score = (int) ($body['score'] ?? 0);

    if ($id <= 0) {
        throw new \InvalidArgumentException('Missing campaign id');
    }

    // Clamp 1-10
    $score = max(1, min(10, $score));

    $stmt = $pdo->prepare('UPDATE google_ads_campaigns SET quality_score = ? WHERE id = ? AND website_id = ?');
    $stmt->execute([$score, $id, $websiteId]);

    if ($stmt->rowCount() === 0) {
        throw new \RuntimeException('Campaign not found or access denied');
    }

    return ['ok' => true];
}

/* ================================================================
   Slugify helper
   ================================================================ */
function slugifyCampaign(string $str): string
{
    if (function_exists('transliterator_transliterate')) {
        $str = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $str);
    } else {
        $str = strtolower($str);
        $str = strtr($str, [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ô' => 'o', 'ö' => 'o',
            'î' => 'i', 'ï' => 'i',
            'ç' => 'c',
        ]);
    }
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}
