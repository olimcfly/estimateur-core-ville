<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Database;

/**
 * Google Search Console API Service
 * Handles OAuth2 authentication and Search Analytics queries.
 */
final class GoogleSearchConsoleService
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const API_BASE = 'https://www.googleapis.com/webmasters/v3';
    private const SEARCHANALYTICS_URL = 'https://searchconsole.googleapis.com/webmasters/v3';
    private const SCOPES = 'https://www.googleapis.com/auth/webmasters.readonly';

    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = (string) Config::get('google_search_console.client_id');
        $this->clientSecret = (string) Config::get('google_search_console.client_secret');
        $this->redirectUri = rtrim((string) Config::get('base_url'), '/') . '/admin/seo-hub/callback';
    }

    /**
     * Generate the OAuth2 authorization URL.
     */
    public function getAuthUrl(int $websiteId): string
    {
        $state = base64_encode(json_encode(['website_id' => $websiteId, 'csrf' => $_SESSION['csrf_token'] ?? '']));

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => self::SCOPES,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ];

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access + refresh tokens.
     */
    public function exchangeCode(string $code): array
    {
        $response = $this->httpPost(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (!isset($response['access_token'])) {
            throw new \RuntimeException('GSC OAuth: impossible d\'obtenir le token. ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Refresh an expired access token.
     */
    public function refreshToken(string $refreshToken): array
    {
        $response = $this->httpPost(self::TOKEN_URL, [
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        if (!isset($response['access_token'])) {
            throw new \RuntimeException('GSC OAuth: impossible de rafraîchir le token.');
        }

        return $response;
    }

    /**
     * Store tokens in database for a website.
     */
    public function saveTokens(int $websiteId, string $siteUrl, array $tokenData): void
    {
        $this->ensureTable();
        $pdo = Database::connection();

        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'] ?? null;
        $expiresAt = date('Y-m-d H:i:s', time() + (int) ($tokenData['expires_in'] ?? 3600));

        $stmt = $pdo->prepare('SELECT id, refresh_token FROM gsc_connections WHERE website_id = :wid LIMIT 1');
        $stmt->execute(['wid' => $websiteId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $refresh = $refreshToken ?? $existing['refresh_token'];
            $stmt = $pdo->prepare(
                'UPDATE gsc_connections SET site_url = :site_url, access_token = :at, refresh_token = :rt, expires_at = :ea, updated_at = NOW() WHERE id = :id'
            );
            $stmt->execute([
                'site_url' => $siteUrl,
                'at' => $accessToken,
                'rt' => $refresh,
                'ea' => $expiresAt,
                'id' => $existing['id'],
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO gsc_connections (website_id, site_url, access_token, refresh_token, expires_at, created_at, updated_at) VALUES (:wid, :site_url, :at, :rt, :ea, NOW(), NOW())'
            );
            $stmt->execute([
                'wid' => $websiteId,
                'site_url' => $siteUrl,
                'at' => $accessToken,
                'rt' => $refreshToken,
                'ea' => $expiresAt,
            ]);
        }
    }

    /**
     * Get a valid access token for a website (auto-refresh if expired).
     */
    public function getValidToken(int $websiteId): ?array
    {
        $this->ensureTable();
        $pdo = Database::connection();

        $stmt = $pdo->prepare('SELECT * FROM gsc_connections WHERE website_id = :wid LIMIT 1');
        $stmt->execute(['wid' => $websiteId]);
        $conn = $stmt->fetch();

        if (!$conn) {
            return null;
        }

        if (strtotime($conn['expires_at']) <= time() + 60) {
            if (empty($conn['refresh_token'])) {
                return null;
            }

            $newTokens = $this->refreshToken($conn['refresh_token']);
            $this->saveTokens($websiteId, $conn['site_url'], array_merge($newTokens, ['refresh_token' => $conn['refresh_token']]));

            $conn['access_token'] = $newTokens['access_token'];
            $conn['expires_at'] = date('Y-m-d H:i:s', time() + (int) ($newTokens['expires_in'] ?? 3600));
        }

        return $conn;
    }

    /**
     * Disconnect GSC for a website.
     */
    public function disconnect(int $websiteId): void
    {
        $this->ensureTable();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM gsc_connections WHERE website_id = :wid');
        $stmt->execute(['wid' => $websiteId]);

        $stmt = $pdo->prepare('DELETE FROM gsc_keywords_cache WHERE website_id = :wid');
        $stmt->execute(['wid' => $websiteId]);
    }

    /**
     * List all verified sites in Google Search Console.
     */
    public function listSites(string $accessToken): array
    {
        $response = $this->httpGet(self::API_BASE . '/sites', $accessToken);
        return $response['siteEntry'] ?? [];
    }

    /**
     * Fetch search analytics data (keywords, clicks, impressions, CTR, position).
     */
    public function fetchSearchAnalytics(
        string $accessToken,
        string $siteUrl,
        string $startDate,
        string $endDate,
        array $dimensions = ['query'],
        int $rowLimit = 100,
        int $startRow = 0
    ): array {
        $encodedSiteUrl = urlencode($siteUrl);
        $url = self::SEARCHANALYTICS_URL . "/sites/{$encodedSiteUrl}/searchAnalytics/query";

        $body = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => $dimensions,
            'rowLimit' => $rowLimit,
            'startRow' => $startRow,
        ];

        $response = $this->httpPostJson($url, $body, $accessToken);
        return $response['rows'] ?? [];
    }

    /**
     * Fetch top keywords for the last N days.
     */
    public function fetchTopKeywords(int $websiteId, int $days = 28, int $limit = 100): array
    {
        $conn = $this->getValidToken($websiteId);
        if (!$conn) {
            return [];
        }

        $endDate = date('Y-m-d', strtotime('-1 day'));
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        return $this->fetchSearchAnalytics(
            $conn['access_token'],
            $conn['site_url'],
            $startDate,
            $endDate,
            ['query'],
            $limit
        );
    }

    /**
     * Fetch keywords grouped by page.
     */
    public function fetchKeywordsByPage(int $websiteId, int $days = 28, int $limit = 200): array
    {
        $conn = $this->getValidToken($websiteId);
        if (!$conn) {
            return [];
        }

        $endDate = date('Y-m-d', strtotime('-1 day'));
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        return $this->fetchSearchAnalytics(
            $conn['access_token'],
            $conn['site_url'],
            $startDate,
            $endDate,
            ['query', 'page'],
            $limit
        );
    }

    /**
     * Fetch and cache keywords in database.
     */
    public function refreshKeywordsCache(int $websiteId): int
    {
        $keywords = $this->fetchTopKeywords($websiteId, 28, 500);

        if (empty($keywords)) {
            return 0;
        }

        $this->ensureTable();
        $pdo = Database::connection();

        $stmt = $pdo->prepare('DELETE FROM gsc_keywords_cache WHERE website_id = :wid');
        $stmt->execute(['wid' => $websiteId]);

        $insertStmt = $pdo->prepare(
            'INSERT INTO gsc_keywords_cache (website_id, keyword, clicks, impressions, ctr, position, fetched_at)
             VALUES (:wid, :kw, :clicks, :impressions, :ctr, :pos, NOW())'
        );

        $count = 0;
        foreach ($keywords as $row) {
            $query = $row['keys'][0] ?? '';
            if ($query === '') {
                continue;
            }
            $insertStmt->execute([
                'wid' => $websiteId,
                'kw' => $query,
                'clicks' => (int) ($row['clicks'] ?? 0),
                'impressions' => (int) ($row['impressions'] ?? 0),
                'ctr' => round((float) ($row['ctr'] ?? 0), 4),
                'pos' => round((float) ($row['position'] ?? 0), 1),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Get cached keywords from database.
     */
    public function getCachedKeywords(int $websiteId, string $sort = 'clicks', string $order = 'DESC', int $limit = 100): array
    {
        $this->ensureTable();
        $pdo = Database::connection();

        $allowedSorts = ['clicks', 'impressions', 'ctr', 'position', 'keyword'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'clicks';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $stmt = $pdo->prepare(
            "SELECT * FROM gsc_keywords_cache WHERE website_id = :wid ORDER BY {$sort} {$order} LIMIT :lim"
        );
        $stmt->bindValue('wid', $websiteId, \PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get cache freshness info.
     */
    public function getCacheInfo(int $websiteId): ?array
    {
        $this->ensureTable();
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) as total, MAX(fetched_at) as last_fetched, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions FROM gsc_keywords_cache WHERE website_id = :wid'
        );
        $stmt->execute(['wid' => $websiteId]);
        $row = $stmt->fetch();

        if (!$row || (int) $row['total'] === 0) {
            return null;
        }

        return $row;
    }

    /**
     * Check if GSC is connected for a website.
     */
    public function isConnected(int $websiteId): bool
    {
        $this->ensureTable();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM gsc_connections WHERE website_id = :wid');
        $stmt->execute(['wid' => $websiteId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get connection info.
     */
    public function getConnection(int $websiteId): ?array
    {
        $this->ensureTable();
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, website_id, site_url, expires_at, created_at, updated_at FROM gsc_connections WHERE website_id = :wid LIMIT 1');
        $stmt->execute(['wid' => $websiteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Ensure required database tables exist.
     */
    private function ensureTable(): void
    {
        $pdo = Database::connection();

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS gsc_connections (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL,
                site_url VARCHAR(500) NOT NULL,
                access_token TEXT NOT NULL,
                refresh_token TEXT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_gsc_website (website_id),
                INDEX idx_gsc_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS gsc_keywords_cache (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL,
                keyword VARCHAR(500) NOT NULL,
                clicks INT UNSIGNED NOT NULL DEFAULT 0,
                impressions INT UNSIGNED NOT NULL DEFAULT 0,
                ctr DECIMAL(6,4) NOT NULL DEFAULT 0.0000,
                position DECIMAL(5,1) NOT NULL DEFAULT 0.0,
                fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_gsc_kw_website (website_id),
                INDEX idx_gsc_kw_clicks (website_id, clicks DESC),
                INDEX idx_gsc_kw_impressions (website_id, impressions DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * HTTP POST with form data.
     */
    private function httpPost(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('GSC API: erreur réseau.');
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("GSC API: réponse invalide (HTTP {$httpCode}).");
        }

        return $decoded;
    }

    /**
     * HTTP POST with JSON body and Bearer token.
     */
    private function httpPostJson(string $url, array $body, string $accessToken): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('GSC API: erreur réseau.');
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("GSC API: réponse invalide (HTTP {$httpCode}).");
        }

        if (isset($decoded['error'])) {
            throw new \RuntimeException('GSC API: ' . ($decoded['error']['message'] ?? json_encode($decoded['error'])));
        }

        return $decoded;
    }

    /**
     * HTTP GET with Bearer token.
     */
    private function httpGet(string $url, string $accessToken): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('GSC API: erreur réseau.');
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException("GSC API: réponse invalide (HTTP {$httpCode}).");
        }

        return $decoded;
    }
}
