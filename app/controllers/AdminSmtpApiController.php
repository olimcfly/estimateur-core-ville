<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;

/**
 * Unified administration module for SMTP, API keys, and AI services management.
 */
final class AdminSmtpApiController
{
    /**
     * Main page — renders the tabbed interface (SMTP/API, IA Services, IA Credits).
     */
    public function index(): void
    {
        AuthController::requireAuth();

        // Active tab from query string
        $tab = $_GET['tab'] ?? 'smtp-api';
        if (!in_array($tab, ['smtp-api', 'ia-services', 'ia-credits'], true)) {
            $tab = 'smtp-api';
        }

        // ── SMTP data ────────────────────────────────────────
        $overrides = Config::getSmtpOverrides();
        $hasOverrides = !empty($overrides);

        $smtpHost = (string) Config::get('mail.smtp_host');
        $smtpPort = (int) Config::get('mail.smtp_port', 587);
        $smtpUser = (string) Config::get('mail.smtp_user');
        $smtpPass = (string) Config::get('mail.smtp_pass');
        $smtpEnc = (string) Config::get('mail.smtp_encryption', 'tls');
        $mailFrom = (string) Config::get('mail.from', '');
        $mailFromName = (string) Config::get('mail.from_name', '');

        // ── API data ─────────────────────────────────────────
        $apis = $this->getApiDefinitions();

        // ── AI services catalog ──────────────────────────────
        $aiUsed = $this->getAiUsedServices();
        $aiRecommended = $this->getAiRecommendedServices();

        // ── AI credits / usage ───────────────────────────────
        $aiUsage = $this->getAiUsageStats();

        // ── Flash messages ───────────────────────────────────
        $flashSuccess = $_SESSION['smtp_flash_success'] ?? '';
        $flashError = $_SESSION['smtp_flash_error'] ?? '';
        unset($_SESSION['smtp_flash_success'], $_SESSION['smtp_flash_error']);

        View::renderAdmin('admin/smtp-api-management', [
            'page_title'       => 'Administration SMTP, API & IA',
            'admin_page_title' => 'SMTP, API & IA',
            'admin_page'       => 'smtp-api-management',
            'tab'              => $tab,
            'smtp_host'        => $smtpHost,
            'smtp_port'        => $smtpPort,
            'smtp_user'        => $smtpUser,
            'smtp_pass'        => $smtpPass,
            'smtp_enc'         => $smtpEnc,
            'mail_from'        => $mailFrom,
            'mail_from_name'   => $mailFromName,
            'has_overrides'    => $hasOverrides,
            'flash_success'    => $flashSuccess,
            'flash_error'      => $flashError,
            'apis'             => $apis,
            'ai_used'          => $aiUsed,
            'ai_recommended'   => $aiRecommended,
            'ai_usage'         => $aiUsage,
        ]);
    }

    /**
     * Log an AI API call (called internally by services).
     */
    public static function logAiUsage(string $provider, string $model, int $inputTokens, int $outputTokens, float $cost = 0.0, string $feature = ''): void
    {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO ai_usage_logs (provider, model, input_tokens, output_tokens, estimated_cost, feature, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [$provider, $model, $inputTokens, $outputTokens, $cost, $feature]
            );
        } catch (\Throwable $e) {
            // Silently fail — logging should never break the app
        }
    }

    /**
     * API endpoint: get AI usage stats as JSON.
     */
    public function apiUsageStats(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->getAiUsageStats(), JSON_UNESCAPED_UNICODE);
    }

    // ─── AI Services Used ────────────────────────────────────

    private function getAiUsedServices(): array
    {
        return [
            [
                'name' => 'OpenAI (GPT-4o / GPT-4o-mini)',
                'provider' => 'openai',
                'icon' => 'fa-brain',
                'color' => '#10a37f',
                'type' => 'payante',
                'configured' => ($_ENV['OPENAI_API_KEY'] ?? '') !== '',
                'usage' => 'Generation d\'articles, emails, descriptions SEO, suggestions IA',
                'models' => ['gpt-4o', 'gpt-4o-mini', 'dall-e-3'],
                'pricing' => 'GPT-4o-mini: ~$0.15/1M input, ~$0.60/1M output | GPT-4o: ~$2.50/1M input, ~$10/1M output',
                'free_tier' => false,
                'url' => 'https://platform.openai.com/',
            ],
            [
                'name' => 'Claude (Anthropic)',
                'provider' => 'claude',
                'icon' => 'fa-robot',
                'color' => '#d97706',
                'type' => 'payante',
                'configured' => ($_ENV['ANTHROPIC_API_KEY'] ?? '') !== '',
                'usage' => 'Assistant IA avance, analyse de contenu, generation de texte',
                'models' => ['claude-sonnet-4-20250514', 'claude-haiku-4-5-20251001'],
                'pricing' => 'Sonnet 4: ~$3/1M input, ~$15/1M output | Haiku 4.5: ~$0.80/1M input, ~$4/1M output',
                'free_tier' => false,
                'url' => 'https://console.anthropic.com/',
            ],
            [
                'name' => 'Perplexity AI',
                'provider' => 'perplexity',
                'icon' => 'fa-magnifying-glass',
                'color' => '#1fb8cd',
                'type' => 'payante',
                'configured' => ($_ENV['PERPLEXITY_API_KEY'] ?? '') !== '',
                'usage' => 'Recherche IA en temps reel, tendances marche immobilier, donnees actualisees',
                'models' => ['sonar-pro', 'sonar'],
                'pricing' => 'Sonar Pro: ~$3/1M input, ~$15/1M output | Sonar: ~$1/1M input',
                'free_tier' => false,
                'url' => 'https://docs.perplexity.ai/',
            ],
        ];
    }

    // ─── AI Recommended Services ─────────────────────────────

    private function getAiRecommendedServices(): array
    {
        return [
            [
                'name' => 'Google Gemini',
                'provider' => 'gemini',
                'icon' => 'fa-gem',
                'color' => '#4285f4',
                'type' => 'gratuite + payante',
                'configured' => false,
                'usage' => 'Generation de contenu, analyse d\'images, multimodal. Excellent rapport qualite/prix.',
                'models' => ['gemini-2.5-pro', 'gemini-2.5-flash'],
                'pricing' => 'Gratuit: 15 requetes/min | Pro: $1.25/1M input, $10/1M output | Flash: $0.15/1M input',
                'free_tier' => true,
                'url' => 'https://ai.google.dev/',
                'recommendation' => 'Ideal pour remplacer GPT-4o avec un tier gratuit genereux',
            ],
            [
                'name' => 'Mistral AI',
                'provider' => 'mistral',
                'icon' => 'fa-wind',
                'color' => '#ff7000',
                'type' => 'gratuite + payante',
                'configured' => false,
                'usage' => 'IA francaise, excellente pour le contenu en francais. Modeles open-source disponibles.',
                'models' => ['mistral-large', 'mistral-small', 'mistral-medium'],
                'pricing' => 'Small: $0.10/1M input | Medium: $0.40/1M | Large: $2/1M input, $6/1M output',
                'free_tier' => true,
                'url' => 'https://console.mistral.ai/',
                'recommendation' => 'Recommande pour le contenu immobilier en francais - IA francaise',
            ],
            [
                'name' => 'Groq',
                'provider' => 'groq',
                'icon' => 'fa-bolt',
                'color' => '#f55036',
                'type' => 'gratuite',
                'configured' => false,
                'usage' => 'Inference ultra-rapide. Ideal pour des taches simples et rapides (classification, extraction).',
                'models' => ['llama-3.3-70b', 'llama-3.1-8b', 'mixtral-8x7b'],
                'pricing' => 'Gratuit avec limites (30 req/min) | Plans payants disponibles',
                'free_tier' => true,
                'url' => 'https://console.groq.com/',
                'recommendation' => 'Parfait pour des reponses instantanees - gratuit et tres rapide',
            ],
            [
                'name' => 'Ollama (local)',
                'provider' => 'ollama',
                'icon' => 'fa-server',
                'color' => '#333333',
                'type' => 'gratuite',
                'configured' => false,
                'usage' => 'IA locale, aucun cout API. Confidentialite totale des donnees clients.',
                'models' => ['llama3', 'mistral', 'gemma2', 'phi3'],
                'pricing' => 'Entierement gratuit - fonctionne sur votre serveur',
                'free_tier' => true,
                'url' => 'https://ollama.ai/',
                'recommendation' => 'Zero cout, donnees 100% privees - ideal pour la confidentialite RGPD',
            ],
            [
                'name' => 'Cohere',
                'provider' => 'cohere',
                'icon' => 'fa-layer-group',
                'color' => '#39594d',
                'type' => 'gratuite + payante',
                'configured' => false,
                'usage' => 'Specialise en RAG et recherche semantique. Ideal pour rechercher dans vos articles/leads.',
                'models' => ['command-r-plus', 'command-r', 'embed-v3'],
                'pricing' => 'Gratuit: 1000 req/mois | Production: $1/1M input, $2/1M output',
                'free_tier' => true,
                'url' => 'https://dashboard.cohere.com/',
                'recommendation' => 'Excellent pour la recherche semantique dans vos contenus immobiliers',
            ],
            [
                'name' => 'DeepSeek',
                'provider' => 'deepseek',
                'icon' => 'fa-microscope',
                'color' => '#0066ff',
                'type' => 'payante',
                'configured' => false,
                'usage' => 'Modele tres performant a prix reduit. Compatible API OpenAI.',
                'models' => ['deepseek-chat', 'deepseek-reasoner'],
                'pricing' => 'Chat: $0.14/1M input, $0.28/1M output | Reasoner: $0.55/1M input',
                'free_tier' => false,
                'url' => 'https://platform.deepseek.com/',
                'recommendation' => 'Alternative economique a GPT-4o avec des performances comparables',
            ],
            [
                'name' => 'Hugging Face Inference',
                'provider' => 'huggingface',
                'icon' => 'fa-face-smile',
                'color' => '#ffcc00',
                'type' => 'gratuite + payante',
                'configured' => false,
                'usage' => 'Acces a des milliers de modeles open-source. Classification, NER, generation.',
                'models' => ['Inference API (milliers de modeles)'],
                'pricing' => 'Gratuit: limites par modele | Pro: $9/mois pour acces etendu',
                'free_tier' => true,
                'url' => 'https://huggingface.co/inference-api',
                'recommendation' => 'Ideal pour des taches specifiques (analyse de sentiment, extraction d\'entites)',
            ],
        ];
    }

    // ─── AI Usage Stats ──────────────────────────────────────

    private function getAiUsageStats(): array
    {
        $stats = [
            'providers' => [],
            'total_tokens' => 0,
            'total_cost' => 0.0,
            'total_calls' => 0,
            'daily' => [],
            'by_feature' => [],
        ];

        try {
            $db = Database::getInstance();

            // Check if table exists
            $tableExists = false;
            try {
                $db->query("SELECT 1 FROM ai_usage_logs LIMIT 1");
                $tableExists = true;
            } catch (\Throwable $e) {
                // Table doesn't exist yet
            }

            if (!$tableExists) {
                return $stats;
            }

            // Per-provider stats (last 30 days)
            $rows = $db->fetchAll(
                "SELECT provider, model,
                        COUNT(*) as calls,
                        SUM(input_tokens) as total_input,
                        SUM(output_tokens) as total_output,
                        SUM(input_tokens + output_tokens) as total_tokens,
                        SUM(estimated_cost) as total_cost,
                        MAX(created_at) as last_used
                 FROM ai_usage_logs
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY provider, model
                 ORDER BY total_tokens DESC"
            );

            foreach ($rows as $row) {
                $provider = $row['provider'];
                if (!isset($stats['providers'][$provider])) {
                    $stats['providers'][$provider] = [
                        'name' => $provider,
                        'models' => [],
                        'total_calls' => 0,
                        'total_tokens' => 0,
                        'total_cost' => 0.0,
                        'last_used' => null,
                    ];
                }
                $stats['providers'][$provider]['models'][] = [
                    'model' => $row['model'],
                    'calls' => (int) $row['calls'],
                    'input_tokens' => (int) $row['total_input'],
                    'output_tokens' => (int) $row['total_output'],
                    'total_tokens' => (int) $row['total_tokens'],
                    'cost' => (float) $row['total_cost'],
                    'last_used' => $row['last_used'],
                ];
                $stats['providers'][$provider]['total_calls'] += (int) $row['calls'];
                $stats['providers'][$provider]['total_tokens'] += (int) $row['total_tokens'];
                $stats['providers'][$provider]['total_cost'] += (float) $row['total_cost'];
                $stats['providers'][$provider]['last_used'] = $row['last_used'];

                $stats['total_tokens'] += (int) $row['total_tokens'];
                $stats['total_cost'] += (float) $row['total_cost'];
                $stats['total_calls'] += (int) $row['calls'];
            }

            // Daily usage (last 14 days)
            $daily = $db->fetchAll(
                "SELECT DATE(created_at) as day,
                        provider,
                        COUNT(*) as calls,
                        SUM(input_tokens + output_tokens) as tokens,
                        SUM(estimated_cost) as cost
                 FROM ai_usage_logs
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                 GROUP BY DATE(created_at), provider
                 ORDER BY day DESC"
            );
            $stats['daily'] = $daily;

            // By feature
            $features = $db->fetchAll(
                "SELECT feature,
                        COUNT(*) as calls,
                        SUM(input_tokens + output_tokens) as tokens,
                        SUM(estimated_cost) as cost
                 FROM ai_usage_logs
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                   AND feature != ''
                 GROUP BY feature
                 ORDER BY calls DESC
                 LIMIT 10"
            );
            $stats['by_feature'] = $features;

        } catch (\Throwable $e) {
            // Return empty stats on error
        }

        return $stats;
    }

    /**
     * Create the ai_usage_logs table.
     */
    public function createTable(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $db = Database::getInstance();
            $db->query("
                CREATE TABLE IF NOT EXISTS ai_usage_logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    provider VARCHAR(50) NOT NULL DEFAULT '',
                    model VARCHAR(100) NOT NULL DEFAULT '',
                    input_tokens INT NOT NULL DEFAULT 0,
                    output_tokens INT NOT NULL DEFAULT 0,
                    estimated_cost DECIMAL(10,6) NOT NULL DEFAULT 0,
                    feature VARCHAR(100) NOT NULL DEFAULT '',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_provider (provider),
                    INDEX idx_created_at (created_at),
                    INDEX idx_feature (feature)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo json_encode(['success' => true, 'message' => 'Table ai_usage_logs creee avec succes']);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Insert sample data for demonstration.
     */
    public function seedSampleData(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $db = Database::getInstance();

            // Check table exists
            try {
                $db->query("SELECT 1 FROM ai_usage_logs LIMIT 1");
            } catch (\Throwable $e) {
                echo json_encode(['success' => false, 'error' => 'Table ai_usage_logs inexistante. Creez-la d\'abord.']);
                return;
            }

            $samples = [
                ['openai', 'gpt-4o-mini', 850, 420, 0.0004, 'article_generation'],
                ['openai', 'gpt-4o-mini', 1200, 680, 0.0006, 'email_generation'],
                ['openai', 'gpt-4o', 2000, 1500, 0.0200, 'article_generation'],
                ['claude', 'claude-sonnet-4-20250514', 3200, 1800, 0.0366, 'article_generation'],
                ['claude', 'claude-sonnet-4-20250514', 1500, 900, 0.0180, 'seo_analysis'],
                ['perplexity', 'sonar-pro', 800, 2500, 0.0399, 'market_research'],
                ['openai', 'dall-e-3', 100, 0, 0.0400, 'image_generation'],
                ['openai', 'gpt-4o-mini', 600, 300, 0.0003, 'lead_scoring'],
                ['claude', 'claude-haiku-4-5-20251001', 500, 250, 0.0014, 'email_generation'],
                ['perplexity', 'sonar', 400, 1200, 0.0016, 'market_research'],
            ];

            $inserted = 0;
            foreach ($samples as $s) {
                // Random date within last 14 days
                $daysAgo = random_int(0, 13);
                $hoursAgo = random_int(0, 23);
                $date = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -{$hoursAgo} hours"));

                $db->query(
                    "INSERT INTO ai_usage_logs (provider, model, input_tokens, output_tokens, estimated_cost, feature, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $date]
                );
                $inserted++;
            }

            echo json_encode(['success' => true, 'message' => $inserted . ' echantillons inseres']);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // ─── API Definitions (reused from AdminApiController) ────

    private function getApiDefinitions(): array
    {
        return [
            'openai' => [
                'name' => 'OpenAI',
                'icon' => 'fa-brain',
                'color' => '#10a37f',
                'description' => 'GPT-4, DALL-E - Generation de contenu et images',
                'configured' => ($_ENV['OPENAI_API_KEY'] ?? '') !== '',
                'env_keys' => ['OPENAI_API_KEY', 'OPENAI_MODEL', 'OPENAI_ENDPOINT'],
                'pricing_info' => 'GPT-4o-mini: ~$0.15/1M input, ~$0.60/1M output',
                'category' => 'ia',
            ],
            'claude' => [
                'name' => 'Claude (Anthropic)',
                'icon' => 'fa-robot',
                'color' => '#d97706',
                'description' => 'Claude - Assistant IA avance',
                'configured' => ($_ENV['ANTHROPIC_API_KEY'] ?? '') !== '',
                'env_keys' => ['ANTHROPIC_API_KEY', 'ANTHROPIC_MODEL'],
                'pricing_info' => 'Claude Sonnet 4: ~$3/1M input, ~$15/1M output',
                'category' => 'ia',
            ],
            'perplexity' => [
                'name' => 'Perplexity',
                'icon' => 'fa-magnifying-glass',
                'color' => '#1fb8cd',
                'description' => 'Recherche IA - Tendances du marche immobilier',
                'configured' => ($_ENV['PERPLEXITY_API_KEY'] ?? '') !== '',
                'env_keys' => ['PERPLEXITY_API_KEY', 'PERPLEXITY_MODEL', 'PERPLEXITY_ENDPOINT'],
                'pricing_info' => 'Sonar Pro: ~$3/1M input, ~$15/1M output',
                'category' => 'ia',
            ],
            'google_maps' => [
                'name' => 'Google Maps Places',
                'icon' => 'fa-map-location-dot',
                'color' => '#4285f4',
                'description' => 'Geocodage et donnees de localisation immobiliere',
                'configured' => ($_ENV['GOOGLE_MAPS_API_KEY'] ?? '') !== '',
                'env_keys' => ['GOOGLE_MAPS_API_KEY'],
                'pricing_info' => 'Places API: $17/1000 requetes (credit $200/mois)',
                'category' => 'geo',
            ],
            'sms_partner' => [
                'name' => 'SMS Partner',
                'icon' => 'fa-comment-sms',
                'color' => '#e91e63',
                'description' => 'Envoi de SMS - Notifications leads',
                'configured' => ($_ENV['SMSPARTNER_API_KEY'] ?? '') !== '',
                'env_keys' => ['SMSPARTNER_API_KEY'],
                'pricing_info' => 'A partir de 0.049EUR/SMS (France)',
                'category' => 'comm',
            ],
            'twilio' => [
                'name' => 'Twilio',
                'icon' => 'fa-phone',
                'color' => '#f22f46',
                'description' => 'SMS et appels - Communication multi-canal',
                'configured' => ($_ENV['TWILIO_ACCOUNT_SID'] ?? '') !== '' && ($_ENV['TWILIO_AUTH_TOKEN'] ?? '') !== '',
                'env_keys' => ['TWILIO_ACCOUNT_SID', 'TWILIO_AUTH_TOKEN', 'TWILIO_PHONE_NUMBER'],
                'pricing_info' => 'SMS France: ~0.0725EUR/SMS',
                'category' => 'comm',
            ],
            'dvf' => [
                'name' => 'DVF (Valeurs Foncieres)',
                'icon' => 'fa-landmark',
                'color' => '#000091',
                'description' => 'Donnees publiques des transactions immobilieres',
                'configured' => true,
                'env_keys' => [],
                'pricing_info' => 'Gratuit - API publique gouvernementale',
                'category' => 'data',
            ],
        ];
    }
}
