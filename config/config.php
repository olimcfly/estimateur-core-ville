<?php

declare(strict_types=1);

$siteConfig = require __DIR__ . '/site.php';

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'Estimateur Immobilier',
    'base_url' => $_ENV['APP_BASE_URL'] ?? '',
    'website' => [
        'id' => (int) ($_ENV['WEBSITE_ID'] ?? 1),
    ],
    'db' => [
        'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'    => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name'    => $_ENV['DB_NAME'] ?? '',
        'user'    => $_ENV['DB_USER'] ?? 'root',
        'pass'    => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],
    'perplexity' => [
        'api_key'  => $_ENV['PERPLEXITY_API_KEY'] ?? '',
        'model'    => $_ENV['PERPLEXITY_MODEL'] ?? 'sonar-pro',
        'endpoint' => $_ENV['PERPLEXITY_ENDPOINT'] ?? 'https://api.perplexity.ai/chat/completions',
    ],
    'mail' => [
        'from'            => $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? '',
        'from_name'       => $_ENV['MAIL_FROM_NAME'] ?? '',
        'admin_email'     => $_ENV['MAIL_ADMIN_EMAIL'] ?? $_ENV['MAIL_FROM_ADDRESS'] ?? $_ENV['MAIL_FROM'] ?? '',
        'smtp_host'       => $_ENV['MAIL_HOST'] ?? $_ENV['MAIL_SMTP_HOST'] ?? '',
        'smtp_port'       => (int) ($_ENV['MAIL_SMTP_PORT'] ?? $_ENV['MAIL_PORT'] ?? 587),
        'smtp_user'       => $_ENV['MAIL_USERNAME'] ?? $_ENV['MAIL_SMTP_USER'] ?? '',
        'smtp_pass'       => $_ENV['MAIL_SMTP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '',
        'smtp_encryption' => $_ENV['MAIL_SMTP_ENCRYPTION'] ?? $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'imap_host'       => $_ENV['MAIL_IMAP_HOST'] ?? $_ENV['MAIL_HOST'] ?? '',
        'imap_port'       => (int) ($_ENV['MAIL_IMAP_PORT'] ?? 993),
        'imap_user'       => $_ENV['MAIL_IMAP_USER'] ?? $_ENV['MAIL_USERNAME'] ?? '',
        'imap_pass'       => $_ENV['MAIL_IMAP_PASS'] ?? $_ENV['MAIL_PASSWORD'] ?? '',
        'imap_encryption' => $_ENV['MAIL_IMAP_ENCRYPTION'] ?? 'ssl',
    ],
    'openai' => [
        'api_key'  => $_ENV['OPENAI_API_KEY'] ?? '',
        'model'    => $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini',
        'endpoint' => $_ENV['OPENAI_ENDPOINT'] ?? 'https://api.openai.com/v1/chat/completions',
    ],
    'anthropic' => [
        'api_key' => $_ENV['ANTHROPIC_API_KEY'] ?? '',
        'model'   => $_ENV['ANTHROPIC_MODEL'] ?? 'claude-sonnet-4-20250514',
    ],
    'google_search_console' => [
        'client_id'     => $_ENV['GSC_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GSC_CLIENT_SECRET'] ?? '',
    ],
    'google_maps' => [
        'api_key' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? '',
    ],
    'sms_partner' => [
        'api_key' => $_ENV['SMSPARTNER_API_KEY'] ?? '',
    ],
    'twilio' => [
        'account_sid'  => $_ENV['TWILIO_ACCOUNT_SID'] ?? '',
        'auth_token'   => $_ENV['TWILIO_AUTH_TOKEN'] ?? '',
        'phone_number' => $_ENV['TWILIO_PHONE_NUMBER'] ?? '',
    ],
    'city' => [
        'name'          => $_ENV['CITY_NAME'] ?? '',
        'region'        => $_ENV['CITY_REGION'] ?? '',
        'code_postal'   => $_ENV['CITY_CODE_POSTAL'] ?? '',
        'quartiers'     => array_values(array_filter(
            array_map('trim', explode(',', (string) ($_ENV['CITY_QUARTIERS'] ?? ''))),
            static fn (string $quartier): bool => $quartier !== ''
        )),
        'prix_m2_moyen' => (int) ($_ENV['CITY_PRIX_M2'] ?? 0),
        'colors' => [
            'primary'   => $_ENV['SITE_COLOR_PRIMARY'] ?? '#1f6f8b',
            'secondary' => $_ENV['SITE_COLOR_SECONDARY'] ?? '#FFFFFF',
            'accent'    => $_ENV['SITE_COLOR_ACCENT'] ?? '#22a06b',
        ],
    ],
    'site' => array_merge($siteConfig, [
        'colors' => [
            'bg'           => $_ENV['SITE_COLOR_BG'] ?? '#faf9f7',
            'surface'      => $_ENV['SITE_COLOR_SURFACE'] ?? '#ffffff',
            'text'         => $_ENV['SITE_COLOR_TEXT'] ?? '#1a1410',
            'muted'        => $_ENV['SITE_COLOR_MUTED'] ?? '#6b6459',
            'primary'      => $_ENV['SITE_COLOR_PRIMARY'] ?? '#1f6f8b',
            'primary_dark' => $_ENV['SITE_COLOR_PRIMARY_DARK'] ?? '#174f64',
            'accent'       => $_ENV['SITE_COLOR_ACCENT'] ?? '#22a06b',
            'accent_light' => $_ENV['SITE_COLOR_ACCENT_LIGHT'] ?? '#4bc48b',
            'border'       => $_ENV['SITE_COLOR_BORDER'] ?? '#e8dfd7',
            'success'      => $_ENV['SITE_COLOR_SUCCESS'] ?? '#22c55e',
            'warning'      => $_ENV['SITE_COLOR_WARNING'] ?? '#f97316',
            'danger'       => $_ENV['SITE_COLOR_DANGER'] ?? '#e24b4a',
            'info'         => $_ENV['SITE_COLOR_INFO'] ?? '#3b82f6',
            'neutral'      => $_ENV['SITE_COLOR_NEUTRAL'] ?? '#000000',
        ],
    ]),
    'maintenance' => [
        'enabled'       => filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'retry_after'   => (int) ($_ENV['MAINTENANCE_RETRY_AFTER'] ?? 3600),
        'allowed_paths' => [
            '/admin/login',
            '/admin/logout',
            '/admin/leads',
        ],
    ],
    'lead' => [
        'strict_ip' => filter_var($_ENV['LEAD_STRICT_IP'] ?? true, FILTER_VALIDATE_BOOLEAN),
    ],
];
