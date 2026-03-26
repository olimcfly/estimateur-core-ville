<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use PDO;

/**
 * Google Ads Campaign Manager — admin controller.
 *
 * Handles CRUD for campaigns, ad groups, keywords and ads.
 * AI generation is delegated to the generate API endpoint.
 */
final class AdminGoogleAdsCampaignController
{
    // ─── Pages ────────────────────────────────────────────────

    /**
     * Campaign list (index).
     */
    public function index(): void
    {
        AuthController::requireAuth();
        $this->ensureSchema();

        $pdo = Database::connection();

        $status = trim((string) ($_GET['status'] ?? ''));
        $where = '';
        $params = [];
        if ($status !== '' && in_array($status, ['draft', 'ready', 'exported', 'active', 'paused', 'archived'], true)) {
            $where = 'WHERE c.status = :status';
            $params['status'] = $status;
        }

        $campaigns = $pdo->prepare("
            SELECT c.*,
                   COUNT(DISTINCT ag.id) AS ad_group_count,
                   COUNT(DISTINCT a.id)  AS ad_count
            FROM gads_campaigns c
            LEFT JOIN gads_ad_groups ag ON ag.campaign_id = c.id
            LEFT JOIN gads_ads a ON a.ad_group_id = ag.id
            {$where}
            GROUP BY c.id
            ORDER BY c.updated_at DESC
        ");
        $campaigns->execute($params);
        $campaigns = $campaigns->fetchAll(PDO::FETCH_ASSOC);

        // Stats
        $stats = $pdo->query("
            SELECT
                COUNT(*) AS total,
                SUM(status = 'draft') AS drafts,
                SUM(status = 'ready') AS ready,
                SUM(status = 'exported') AS exported,
                SUM(status IN ('active','paused')) AS live
            FROM gads_campaigns
        ")->fetch(PDO::FETCH_ASSOC);

        View::renderAdmin('admin/gads-campaigns/index', [
            'page_title'       => 'Google Ads Campaigns - Admin',
            'admin_page_title' => 'Campagnes Google Ads',
            'admin_page'       => 'gads-campaigns',
            'campaigns'        => $campaigns,
            'stats'            => $stats ?: [],
            'current_status'   => $status,
            'message'          => (string) ($_GET['message'] ?? ''),
            'error'            => (string) ($_GET['error'] ?? ''),
        ]);
    }

    /**
     * Campaign creation wizard.
     */
    public function wizard(): void
    {
        AuthController::requireAuth();
        $this->ensureSchema();

        $campaignId = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $campaign = null;
        $adGroups = [];
        $keywords = [];
        $ads = [];

        if ($campaignId !== null) {
            $pdo = Database::connection();
            $campaign = $pdo->prepare('SELECT * FROM gads_campaigns WHERE id = :id LIMIT 1');
            $campaign->execute(['id' => $campaignId]);
            $campaign = $campaign->fetch(PDO::FETCH_ASSOC) ?: null;

            if ($campaign) {
                $adGroups = $pdo->prepare('SELECT * FROM gads_ad_groups WHERE campaign_id = :cid ORDER BY sort_order');
                $adGroups->execute(['cid' => $campaignId]);
                $adGroups = $adGroups->fetchAll(PDO::FETCH_ASSOC);

                $agIds = array_column($adGroups, 'id');
                if (!empty($agIds)) {
                    $placeholders = implode(',', array_fill(0, count($agIds), '?'));
                    $kwStmt = $pdo->prepare("SELECT * FROM gads_keywords WHERE ad_group_id IN ({$placeholders}) ORDER BY ad_group_id, id");
                    $kwStmt->execute($agIds);
                    $keywords = $kwStmt->fetchAll(PDO::FETCH_ASSOC);

                    $adStmt = $pdo->prepare("SELECT * FROM gads_ads WHERE ad_group_id IN ({$placeholders}) ORDER BY ad_group_id, id");
                    $adStmt->execute($agIds);
                    $ads = $adStmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        }

        $landingPages = $this->getAvailableLandingPages();

        View::renderAdmin('admin/gads-campaigns/wizard', [
            'page_title'       => $campaign ? 'Modifier campagne - Admin' : 'Nouvelle campagne Google Ads',
            'admin_page_title' => $campaign ? 'Modifier la campagne' : 'Nouvelle campagne',
            'admin_page'       => 'gads-campaigns',
            'campaign'         => $campaign,
            'ad_groups'        => $adGroups,
            'keywords'         => $keywords,
            'ads'              => $ads,
            'landing_pages'    => $landingPages,
            'has_anthropic'    => ((string) Config::get('anthropic.api_key', '')) !== '',
        ]);
    }

    /**
     * Preview a campaign's ads as they would appear on Google.
     */
    public function preview(): void
    {
        AuthController::requireAuth();
        $this->ensureSchema();

        $campaignId = (int) ($_GET['id'] ?? 0);
        if ($campaignId < 1) {
            header('Location: /admin/gads-campaigns');
            exit;
        }

        $pdo = Database::connection();
        $campaign = $pdo->prepare('SELECT * FROM gads_campaigns WHERE id = :id LIMIT 1');
        $campaign->execute(['id' => $campaignId]);
        $campaign = $campaign->fetch(PDO::FETCH_ASSOC);

        if (!$campaign) {
            header('Location: /admin/gads-campaigns?error=' . urlencode('Campagne introuvable.'));
            exit;
        }

        $adGroups = $pdo->prepare('SELECT * FROM gads_ad_groups WHERE campaign_id = :cid ORDER BY sort_order');
        $adGroups->execute(['cid' => $campaignId]);
        $adGroups = $adGroups->fetchAll(PDO::FETCH_ASSOC);

        $agIds = array_column($adGroups, 'id');
        $ads = [];
        $keywords = [];
        if (!empty($agIds)) {
            $ph = implode(',', array_fill(0, count($agIds), '?'));
            $adStmt = $pdo->prepare("SELECT * FROM gads_ads WHERE ad_group_id IN ({$ph}) ORDER BY ad_group_id, id");
            $adStmt->execute($agIds);
            $ads = $adStmt->fetchAll(PDO::FETCH_ASSOC);

            $kwStmt = $pdo->prepare("SELECT * FROM gads_keywords WHERE ad_group_id IN ({$ph}) ORDER BY ad_group_id, id");
            $kwStmt->execute($agIds);
            $keywords = $kwStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        View::renderAdmin('admin/gads-campaigns/preview', [
            'page_title'       => 'Aperçu campagne - ' . $campaign['name'],
            'admin_page_title' => 'Aperçu Google Ads',
            'admin_page'       => 'gads-campaigns',
            'campaign'         => $campaign,
            'ad_groups'        => $adGroups,
            'ads'              => $ads,
            'keywords'         => $keywords,
        ]);
    }

    // ─── API: Generate ad copy with AI ────────────────────────

    public function apiGenerate(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $apiKey = trim((string) Config::get('anthropic.api_key', ''));
            if ($apiKey === '') {
                throw new \RuntimeException('Clé API Anthropic non configurée.');
            }

            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
            if (!is_array($input)) {
                $input = [];
            }

            $keyword    = trim((string) ($input['keyword'] ?? ''));
            $landingUrl = trim((string) ($input['landing_url'] ?? ''));
            $tone       = trim((string) ($input['tone'] ?? 'professionnel et rassurant'));
            $city       = trim((string) ($input['city'] ?? Config::get('city.name')));

            if ($keyword === '') {
                throw new \RuntimeException('Mot-clé requis pour la génération.');
            }

            $prompt = <<<PROMPT
Tu es un expert Google Ads spécialisé en immobilier à {$city}.
Génère des annonces Responsive Search Ads pour le mot-clé principal : "{$keyword}".

URL de destination : {$landingUrl}
Ton : {$tone}

Réponds UNIQUEMENT en JSON strict avec cette structure :
{
  "headlines": ["titre1", "titre2", ... ],
  "descriptions": ["desc1", "desc2", ... ],
  "path1": "chemin1",
  "path2": "chemin2",
  "sitelinks": [
    {"text": "Lien 1", "description": "Description lien 1", "url": "/page1"},
    {"text": "Lien 2", "description": "Description lien 2", "url": "/page2"}
  ],
  "callouts": ["avantage1", "avantage2", ...],
  "negative_keywords": ["mot1", "mot2", ...],
  "additional_keywords": [
    {"keyword": "mot-clé", "match_type": "phrase"}
  ]
}

Règles :
- 15 headlines maximum, chacun ≤ 30 caractères
- 4 descriptions maximum, chacune ≤ 90 caractères
- path1 et path2 ≤ 15 caractères chacun, pas de slash
- 4 sitelinks maximum
- 6 callouts maximum, chacun ≤ 25 caractères
- Inclus des chiffres, des CTA et le nom de la ville
- Adapte au marché immobilier local
PROMPT;

            $model = (string) Config::get('anthropic.model', 'claude-sonnet-4-20250514');

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'anthropic-version: 2023-06-01',
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode([
                    'model'      => $model,
                    'max_tokens' => 2048,
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ], JSON_THROW_ON_ERROR),
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $httpCode >= 400) {
                throw new \RuntimeException('Erreur API Anthropic (HTTP ' . $httpCode . ').');
            }

            $body = json_decode((string) $response, true);

            $inputTokens = (int) ($body['usage']['input_tokens'] ?? 0);
            $outputTokens = (int) ($body['usage']['output_tokens'] ?? 0);
            $cost = round(($inputTokens / 1000) * 0.003 + ($outputTokens / 1000) * 0.015, 6);
            AdminSmtpApiController::logAiUsage('claude', $model, $inputTokens, $outputTokens, $cost, 'article_generation');

            $text = $body['content'][0]['text'] ?? '';

            // Extract JSON from response (may be wrapped in markdown)
            if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
                $generated = json_decode($m[0], true);
            } else {
                $generated = json_decode($text, true);
            }

            if (!is_array($generated) || empty($generated['headlines'])) {
                throw new \RuntimeException('Réponse IA invalide.');
            }

            // Enforce character limits
            $generated['headlines'] = array_map(
                fn(string $h) => mb_substr(trim($h), 0, 30),
                array_slice((array) $generated['headlines'], 0, 15)
            );
            $generated['descriptions'] = array_map(
                fn(string $d) => mb_substr(trim($d), 0, 90),
                array_slice((array) ($generated['descriptions'] ?? []), 0, 4)
            );
            $generated['path1'] = mb_substr(trim((string) ($generated['path1'] ?? '')), 0, 15);
            $generated['path2'] = mb_substr(trim((string) ($generated['path2'] ?? '')), 0, 15);

            echo json_encode(['success' => true, 'data' => $generated], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    // ─── API: Save campaign ───────────────────────────────────

    public function apiSave(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
            if (!is_array($input)) {
                throw new \RuntimeException('Données invalides.');
            }

            $this->ensureSchema();
            $pdo = Database::connection();
            $pdo->beginTransaction();

            $campaignId = isset($input['campaign_id']) && (int) $input['campaign_id'] > 0
                ? (int) $input['campaign_id']
                : null;

            // Validate campaign fields
            $name = trim((string) ($input['name'] ?? ''));
            if ($name === '') {
                throw new \RuntimeException('Le nom de la campagne est requis.');
            }

            $campaignData = [
                'name'             => $name,
                'campaign_type'    => in_array($input['campaign_type'] ?? '', ['search', 'display', 'performance_max'], true)
                    ? $input['campaign_type'] : 'search',
                'status'           => in_array($input['status'] ?? '', ['draft', 'ready', 'exported', 'active', 'paused', 'archived'], true)
                    ? $input['status'] : 'draft',
                'daily_budget'     => max(0, (float) ($input['daily_budget'] ?? 0)),
                'target_location'  => trim((string) ($input['target_location'] ?? Config::get('city.name'))),
                'target_radius_km' => max(1, min(500, (int) ($input['target_radius_km'] ?? 30))),
                'language'         => 'fr',
                'bid_strategy'     => in_array($input['bid_strategy'] ?? '', ['manual_cpc', 'maximize_clicks', 'maximize_conversions', 'target_cpa'], true)
                    ? $input['bid_strategy'] : 'maximize_clicks',
                'target_cpa'       => isset($input['target_cpa']) && (float) $input['target_cpa'] > 0
                    ? (float) $input['target_cpa'] : null,
                'start_date'       => $this->parseDate($input['start_date'] ?? null),
                'end_date'         => $this->parseDate($input['end_date'] ?? null),
                'notes'            => trim((string) ($input['notes'] ?? '')),
            ];

            if ($campaignId !== null) {
                // Update
                $sets = [];
                $params = ['id' => $campaignId];
                foreach ($campaignData as $col => $val) {
                    $sets[] = "{$col} = :{$col}";
                    $params[$col] = $val;
                }
                $pdo->prepare('UPDATE gads_campaigns SET ' . implode(', ', $sets) . ' WHERE id = :id')->execute($params);
            } else {
                // Insert
                $campaignData['created_by'] = (int) ($_SESSION['admin_user_id'] ?? 0) ?: null;
                $cols = implode(', ', array_keys($campaignData));
                $placeholders = ':' . implode(', :', array_keys($campaignData));
                $pdo->prepare("INSERT INTO gads_campaigns ({$cols}) VALUES ({$placeholders})")->execute($campaignData);
                $campaignId = (int) $pdo->lastInsertId();
            }

            // Delete existing ad groups (cascade deletes keywords + ads)
            $pdo->prepare('DELETE FROM gads_ad_groups WHERE campaign_id = :cid')->execute(['cid' => $campaignId]);

            // Re-insert ad groups
            $adGroups = (array) ($input['ad_groups'] ?? []);
            foreach ($adGroups as $sortIdx => $ag) {
                $agName = trim((string) ($ag['name'] ?? 'Groupe ' . ($sortIdx + 1)));
                $landingUrl = trim((string) ($ag['landing_url'] ?? ''));
                $cpcBid = isset($ag['cpc_bid']) && (float) $ag['cpc_bid'] > 0 ? (float) $ag['cpc_bid'] : null;

                $pdo->prepare("
                    INSERT INTO gads_ad_groups (campaign_id, name, landing_url, cpc_bid, sort_order)
                    VALUES (:cid, :name, :url, :bid, :sort)
                ")->execute([
                    'cid'  => $campaignId,
                    'name' => $agName,
                    'url'  => $landingUrl,
                    'bid'  => $cpcBid,
                    'sort' => $sortIdx,
                ]);
                $agId = (int) $pdo->lastInsertId();

                // Keywords
                $kwStmt = $pdo->prepare("
                    INSERT INTO gads_keywords (ad_group_id, keyword, match_type, is_negative, cpc_bid)
                    VALUES (:agid, :kw, :mt, :neg, :bid)
                ");
                foreach ((array) ($ag['keywords'] ?? []) as $kw) {
                    $kwText = trim((string) ($kw['keyword'] ?? ''));
                    if ($kwText === '') continue;
                    $kwStmt->execute([
                        'agid' => $agId,
                        'kw'   => $kwText,
                        'mt'   => in_array($kw['match_type'] ?? '', ['broad', 'phrase', 'exact'], true)
                            ? $kw['match_type'] : 'phrase',
                        'neg'  => (int) !empty($kw['is_negative']),
                        'bid'  => isset($kw['cpc_bid']) && (float) $kw['cpc_bid'] > 0
                            ? (float) $kw['cpc_bid'] : null,
                    ]);
                }

                // Ads
                $adStmt = $pdo->prepare("
                    INSERT INTO gads_ads (ad_group_id, ad_type, headlines, descriptions, final_url, path1, path2, sitelinks, callouts, ai_generated)
                    VALUES (:agid, :type, :headlines, :descriptions, :url, :p1, :p2, :sitelinks, :callouts, :ai)
                ");
                foreach ((array) ($ag['ads'] ?? []) as $ad) {
                    $headlines = (array) ($ad['headlines'] ?? []);
                    $descriptions = (array) ($ad['descriptions'] ?? []);
                    if (empty($headlines)) continue;

                    $adStmt->execute([
                        'agid'         => $agId,
                        'type'         => 'responsive_search',
                        'headlines'    => json_encode(array_values($headlines), JSON_UNESCAPED_UNICODE),
                        'descriptions' => json_encode(array_values($descriptions), JSON_UNESCAPED_UNICODE),
                        'url'          => trim((string) ($ad['final_url'] ?? $landingUrl)),
                        'p1'           => mb_substr(trim((string) ($ad['path1'] ?? '')), 0, 15),
                        'p2'           => mb_substr(trim((string) ($ad['path2'] ?? '')), 0, 15),
                        'sitelinks'    => !empty($ad['sitelinks']) ? json_encode($ad['sitelinks'], JSON_UNESCAPED_UNICODE) : null,
                        'callouts'     => !empty($ad['callouts']) ? json_encode($ad['callouts'], JSON_UNESCAPED_UNICODE) : null,
                        'ai'           => (int) !empty($ad['ai_generated']),
                    ]);
                }
            }

            $pdo->commit();

            echo json_encode([
                'success'     => true,
                'campaign_id' => $campaignId,
                'message'     => $campaignData['name'] !== '' ? "Campagne « {$campaignData['name']} » sauvegardée." : 'Campagne sauvegardée.',
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            if (Database::connection()->inTransaction()) {
                Database::connection()->rollBack();
            }
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    // ─── API: Export campaign as Google Ads Editor CSV ─────────

    public function apiExport(): void
    {
        AuthController::requireAuth();

        try {
            $this->ensureSchema();
            $campaignId = (int) ($_GET['id'] ?? 0);
            if ($campaignId < 1) {
                throw new \RuntimeException('ID campagne requis.');
            }

            $pdo = Database::connection();
            $campaign = $pdo->prepare('SELECT * FROM gads_campaigns WHERE id = :id LIMIT 1');
            $campaign->execute(['id' => $campaignId]);
            $campaign = $campaign->fetch(PDO::FETCH_ASSOC);
            if (!$campaign) {
                throw new \RuntimeException('Campagne introuvable.');
            }

            $adGroups = $pdo->prepare('SELECT * FROM gads_ad_groups WHERE campaign_id = :cid ORDER BY sort_order');
            $adGroups->execute(['cid' => $campaignId]);
            $adGroups = $adGroups->fetchAll(PDO::FETCH_ASSOC);

            $agIds = array_column($adGroups, 'id');
            $keywords = [];
            $ads = [];
            if (!empty($agIds)) {
                $ph = implode(',', array_fill(0, count($agIds), '?'));
                $kwStmt = $pdo->prepare("SELECT * FROM gads_keywords WHERE ad_group_id IN ({$ph})");
                $kwStmt->execute($agIds);
                $keywords = $kwStmt->fetchAll(PDO::FETCH_ASSOC);

                $adStmt = $pdo->prepare("SELECT * FROM gads_ads WHERE ad_group_id IN ({$ph})");
                $adStmt->execute($agIds);
                $ads = $adStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Index by ad_group_id
            $kwByAg = [];
            foreach ($keywords as $kw) {
                $kwByAg[(int) $kw['ad_group_id']][] = $kw;
            }
            $adsByAg = [];
            foreach ($ads as $ad) {
                $adsByAg[(int) $ad['ad_group_id']][] = $ad;
            }

            // Build CSV
            $filename = 'gads-' . preg_replace('/[^a-z0-9-]/i', '-', $campaign['name']) . '-' . date('Y-m-d') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Campaign', 'Campaign Type', 'Budget', 'Bid Strategy',
                'Ad Group', 'Ad Group CPC',
                'Keyword', 'Match Type', 'Negative',
                'Headline 1', 'Headline 2', 'Headline 3', 'Headline 4', 'Headline 5',
                'Description 1', 'Description 2',
                'Final URL', 'Path 1', 'Path 2',
            ], ';');

            foreach ($adGroups as $ag) {
                $agId = (int) $ag['id'];

                // Keywords rows
                foreach ($kwByAg[$agId] ?? [] as $kw) {
                    fputcsv($out, [
                        $campaign['name'], $campaign['campaign_type'], $campaign['daily_budget'], $campaign['bid_strategy'],
                        $ag['name'], $ag['cpc_bid'] ?? '',
                        $kw['keyword'], $kw['match_type'], $kw['is_negative'] ? 'Yes' : '',
                        '', '', '', '', '',
                        '', '',
                        '', '', '',
                    ], ';');
                }

                // Ad rows
                foreach ($adsByAg[$agId] ?? [] as $ad) {
                    $h = json_decode($ad['headlines'], true) ?: [];
                    $d = json_decode($ad['descriptions'], true) ?: [];
                    fputcsv($out, [
                        $campaign['name'], $campaign['campaign_type'], '', '',
                        $ag['name'], '',
                        '', '', '',
                        $h[0] ?? '', $h[1] ?? '', $h[2] ?? '', $h[3] ?? '', $h[4] ?? '',
                        $d[0] ?? '', $d[1] ?? '',
                        $ad['final_url'], $ad['path1'], $ad['path2'],
                    ], ';');
                }
            }

            fclose($out);

            // Mark as exported
            $pdo->prepare("UPDATE gads_campaigns SET status = 'exported' WHERE id = :id AND status = 'ready'")
                ->execute(['id' => $campaignId]);

            exit;
        } catch (\Throwable $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // ─── API: Delete campaign ─────────────────────────────────

    public function apiDelete(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $this->ensureSchema();
            $campaignId = (int) ($_POST['id'] ?? 0);
            if ($campaignId < 1) {
                throw new \RuntimeException('ID campagne requis.');
            }

            $pdo = Database::connection();
            $pdo->prepare('DELETE FROM gads_campaigns WHERE id = :id')->execute(['id' => $campaignId]);

            echo json_encode(['success' => true, 'message' => 'Campagne supprimée.'], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    // ─── API: Update status ───────────────────────────────────

    public function apiStatus(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $this->ensureSchema();
            $campaignId = (int) ($_POST['id'] ?? 0);
            $newStatus  = trim((string) ($_POST['status'] ?? ''));

            if ($campaignId < 1 || !in_array($newStatus, ['draft', 'ready', 'exported', 'active', 'paused', 'archived'], true)) {
                throw new \RuntimeException('Paramètres invalides.');
            }

            Database::connection()
                ->prepare('UPDATE gads_campaigns SET status = :status WHERE id = :id')
                ->execute(['status' => $newStatus, 'id' => $campaignId]);

            echo json_encode(['success' => true], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_THROW_ON_ERROR);
        }
    }

    // ─── Private helpers ──────────────────────────────────────

    private function ensureSchema(): void
    {
        $pdo = Database::connection();
        try {
            $pdo->query("SELECT 1 FROM gads_campaigns LIMIT 1");
        } catch (\Throwable) {
            $sql = file_get_contents(base_path('database/migration_gads_campaigns.sql'));
            if ($sql !== false) {
                $pdo->exec($sql);
            }
        }
    }

    private function getAvailableLandingPages(): array
    {
        return [
            ['url' => '/lp/estimation', 'label' => 'Estimation'],
            ['url' => '/lp/vendre-maison', 'label' => 'Vendre Maison'],
            ['url' => '/lp/avis-valeur-gratuit', 'label' => 'Avis de Valeur Gratuit'],
        ];
    }

    private function parseDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $ts = strtotime($value);
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }
}
