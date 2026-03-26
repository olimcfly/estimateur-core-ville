<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Database;

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}


if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('site')) {
    function site(string $key, mixed $default = null): mixed
    {
        return Config::get("site.$key", $default);
    }
}

if (!function_exists('hex_to_rgb')) {
    function hex_to_rgb(string $hex): string
    {
        $value = ltrim(trim($hex), '#');

        if (strlen($value) === 3) {
            $value = preg_replace('/(.)/', '$1$1', $value) ?? $value;
        }

        if (!preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
            return '0, 0, 0';
        }

        return sprintf(
            '%d, %d, %d',
            (int) hexdec(substr($value, 0, 2)),
            (int) hexdec(substr($value, 2, 2)),
            (int) hexdec(substr($value, 4, 2))
        );
    }
}

if (!function_exists('getSiteConfig')) {
    function getSiteConfig(): array
    {
        $defaultColors = (array) Config::get('site.colors', []);
        $colors = $defaultColors;
        $googleSiteVerification = '';
        $tracking = [];

        $trackingKeys = [
            'gtm_id', 'ga4_measurement_id', 'google_ads_id', 'google_ads_conversion_label',
            'facebook_pixel_id', 'facebook_conversions_api_token',
            'microsoft_clarity_id', 'hotjar_id',
            'tiktok_pixel_id', 'linkedin_partner_id', 'pinterest_tag_id', 'snapchat_pixel_id',
            'custom_head_scripts', 'custom_body_scripts',
        ];

        try {
            $statement = Database::connection()->query("SELECT `key`, `value` FROM settings");
            $rows = $statement !== false ? $statement->fetchAll() : [];

            foreach ($rows as $row) {
                $key = (string) ($row['key'] ?? '');
                $val = trim((string) ($row['value'] ?? ''));

                if ($key === 'google_site_verification') {
                    $googleSiteVerification = $val;
                    continue;
                }

                if (in_array($key, $trackingKeys, true)) {
                    $tracking[$key] = $val;
                    continue;
                }

                if (str_starts_with($key, 'site.colors.')) {
                    $colorKey = str_replace('site.colors.', '', $key);
                    if ($colorKey !== '' && $val !== '') {
                        $colors[$colorKey] = $val;
                    }
                }
            }
        } catch (Throwable) {
            // Table/settings can be unavailable in some environments.
        }

        $rgbColors = [];

        foreach ($colors as $name => $hexColor) {
            if (is_string($hexColor)) {
                $rgbColors[$name] = hex_to_rgb($hexColor);
            }
        }

        return [
            'colors' => $colors,
            'rgb_colors' => $rgbColors,
            'google_site_verification' => $googleSiteVerification,
            'tracking' => $tracking,
        ];
    }
}

if (!function_exists('getSettingsMap')) {
    /**
     * Return settings as key/value map.
     * Supports both schemas: (`key`,`value`) and (`name`,`value`).
     *
     * @return array<string,string>
     */
    function getSettingsMap(): array
    {
        $settings = [];

        try {
            $pdo = Database::connection();

            try {
                $statement = $pdo->query('SELECT `key`, `value` FROM settings');
                $rows = $statement !== false ? $statement->fetchAll() : [];
                foreach ($rows as $row) {
                    $k = trim((string) ($row['key'] ?? ''));
                    if ($k !== '') {
                        $settings[$k] = trim((string) ($row['value'] ?? ''));
                    }
                }
            } catch (Throwable) {
                $statement = $pdo->query('SELECT `name`, `value` FROM settings');
                $rows = $statement !== false ? $statement->fetchAll() : [];
                foreach ($rows as $row) {
                    $k = trim((string) ($row['name'] ?? ''));
                    if ($k !== '') {
                        $settings[$k] = trim((string) ($row['value'] ?? ''));
                    }
                }
            }
        } catch (Throwable) {
            // Keep empty map if DB/settings is unavailable.
        }

        return $settings;
    }
}

if (!function_exists('getBrandingConfig')) {
    /**
     * Runtime public branding resolved by priority:
     * settings -> config/env -> neutral fallbacks.
     *
     * @return array{
     *   site_name:string,
     *   city_name:string,
     *   area_label:string,
     *   support_email:string,
     *   base_url:string
     * }
     */
    function getBrandingConfig(): array
    {
        $settings = getSettingsMap();

        $pick = static function (array $keys) use ($settings): string {
            foreach ($keys as $key) {
                $value = trim((string) ($settings[$key] ?? ''));
                if ($value !== '') {
                    return $value;
                }
            }
            return '';
        };

        $siteName = $pick(['site_name', 'brand_name', 'app_name']);
        if ($siteName === '') {
            $siteName = trim((string) Config::get('app_name', ''));
        }
        if ($siteName === '') {
            $siteName = 'Estimation Immobilière';
        }

        $cityName = $pick(['city_name']);
        if ($cityName === '') {
            $cityName = trim((string) Config::get('city.name', ''));
        }
        if ($cityName === '') {
            $cityName = 'votre ville';
        }

        $areaLabel = $pick(['area_label', 'city_area_label', 'city_region']);
        if ($areaLabel === '') {
            $areaLabel = trim((string) Config::get('city.region', ''));
        }
        if ($areaLabel === '') {
            $areaLabel = $cityName !== 'votre ville' ? $cityName : 'votre secteur';
        }

        $supportEmail = $pick(['support_email', 'contact_email', 'mail_from_address']);
        if ($supportEmail === '') {
            $supportEmail = trim((string) Config::get('mail.admin_email', ''));
        }
        if ($supportEmail === '') {
            $supportEmail = trim((string) Config::get('mail.from', ''));
        }
        if ($supportEmail === '') {
            $supportEmail = 'contact@example.test';
        }

        $baseUrl = $pick(['base_url', 'app_base_url']);
        if ($baseUrl === '') {
            $baseUrl = trim((string) Config::get('base_url', ''));
        }
        if ($baseUrl === '' && !empty($_SERVER['HTTP_HOST'])) {
            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            $scheme = $isHttps ? 'https' : 'http';
            $baseUrl = $scheme . '://' . (string) $_SERVER['HTTP_HOST'];
        }

        return [
            'site_name' => $siteName,
            'city_name' => $cityName,
            'area_label' => $areaLabel,
            'support_email' => $supportEmail,
            'base_url' => $baseUrl,
        ];
    }
}
