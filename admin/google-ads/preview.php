<?php
/**
 * Google Ads Campaign — Landing Page Preview
 * Renders landing HTML directly (no admin chrome) for QA review.
 */

declare(strict_types=1);
defined('IMMO_ADMIN') or die();

use App\Core\Database;

$pdo = Database::connection();
$siteConfig = getSiteConfig();
$websiteId = (int) ($siteConfig['website_id'] ?? 1);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /admin/google-ads/');
    exit;
}

$stmt = $pdo->prepare('SELECT landing_html FROM google_ads_campaigns WHERE id = ? AND website_id = ?');
$stmt->execute([$id, $websiteId]);
$campaign = $stmt->fetch();

if (!$campaign || empty($campaign['landing_html'])) {
    header('Location: /admin/google-ads/');
    exit;
}

// Output the landing HTML directly — no admin sidebar/topbar
echo $campaign['landing_html'];
