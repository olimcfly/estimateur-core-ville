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
        $settings = getSettingsMap();
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

        $siteCity = trim((string) Config::get('site.city', Config::get('city.name', '')));
        $siteRegion = trim((string) Config::get('site.region', Config::get('city.region', '')));
        $siteZip = trim((string) Config::get('site.zip', Config::get('city.code_postal', '')));
        $siteCitySlug = trim((string) Config::get('site.city_slug', ''));
        if ($siteCitySlug === '' && $siteCity !== '') {
            $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $siteCity);
            $normalized = is_string($normalized) ? strtolower($normalized) : strtolower($siteCity);
            $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '';
            $siteCitySlug = trim($normalized, '-');
        }
        if ($siteCitySlug === '') {
            $siteCitySlug = 'default';
        }

        $pickSetting = static function (array $keys, string $fallback = '') use ($settings): string {
            foreach ($keys as $key) {
                $value = trim((string) ($settings[$key] ?? ''));
                if ($value !== '') {
                    return $value;
                }
            }

            return $fallback;
        };

        $advisorName = $pickSetting(['advisor_name', 'site.advisor_name']);
        $advisorPhoto = $pickSetting(['advisor_photo', 'site.advisor_photo']);
        $advisorExperienceYears = $pickSetting(['advisor_experience_years', 'site.advisor_experience_years']);
        $advisorZone = $pickSetting(['advisor_zone', 'site.advisor_zone'], $siteRegion);
        $advisorTagline = $pickSetting(['advisor_tagline', 'site.advisor_tagline']);
        $accentColor = $pickSetting(['color_accent', 'site.color_accent'], (string) ($colors['accent'] ?? '#D4AF37'));

        $quartiers = Config::get('city.quartiers', []);
        if (!is_array($quartiers)) {
            $quartiers = [];
        }
        $quartiers = array_values(array_filter(array_map(
            static fn (mixed $quartier): string => trim((string) $quartier),
            $quartiers
        ), static fn (string $quartier): bool => $quartier !== ''));

        if ($quartiers === []) {
            $fixturePath = base_path('database/fixtures/' . $siteCitySlug . '/quartiers.php');
            if (!is_file($fixturePath)) {
                $fixturePath = base_path('database/fixtures/default/quartiers.php');
            }

            if (is_file($fixturePath)) {
                $fixtureData = require $fixturePath;
                if (is_array($fixtureData)) {
                    $quartiers = array_values(array_unique(array_filter(array_map(
                        static fn (mixed $row): string => is_array($row) ? trim((string) ($row['name'] ?? '')) : '',
                        $fixtureData
                    ), static fn (string $name): bool => $name !== '')));
                }
            }
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
            'config_id' => (int) Config::get('website.id', 0),
            'ville' => $siteCity,
            'city_slug' => $siteCitySlug,
            'zip' => $siteZip,
            'region' => $siteRegion,
            'quartiers' => $quartiers,
            'advisor_name' => $advisorName,
            'advisor_photo' => $advisorPhoto,
            'advisor_experience_years' => $advisorExperienceYears,
            'advisor_zone' => $advisorZone,
            'advisor_tagline' => $advisorTagline,
            'color_accent' => $accentColor,
            'testimonials' => [],
        ];
    }
}

if (!function_exists('getEstimationContext')) {
    /**
     * Build normalized context expected by estimation UI.
     *
     * @return array{
     *   config_id:int,
     *   city_slug:string,
     *   city_name:string,
     *   zones:array<int,string>,
     *   quartiers:array<int,string>,
     *   advisor_context:array{name:string,zone:string,tagline:string}
     * }
     */
    function getEstimationContext(): array
    {
        $siteConfig = getSiteConfig();
        $cityName = trim((string) ($siteConfig['ville'] ?? Config::get('city.name', '')));
        if ($cityName === '') {
            $cityName = 'votre ville';
        }
        $citySlug = trim((string) ($siteConfig['city_slug'] ?? 'default'));
        if ($citySlug === '') {
            $citySlug = 'default';
        }

        $quartiers = isset($siteConfig['quartiers']) && is_array($siteConfig['quartiers'])
            ? array_values(array_filter(array_map(
                static fn (mixed $value): string => trim((string) $value),
                $siteConfig['quartiers']
            ), static fn (string $value): bool => $value !== ''))
            : [];

        $zones = [];
        if ($cityName !== '') {
            $zones[] = $cityName;
        }
        $region = trim((string) ($siteConfig['region'] ?? ''));
        if ($region !== '') {
            $zones[] = $region;
        }
        if ($quartiers !== []) {
            $zones[] = 'Quartiers';
        }
        if ($zones === []) {
            $zones[] = 'Zone locale';
        }
        $zones = array_values(array_unique($zones));

        return [
            'config_id' => (int) ($siteConfig['config_id'] ?? Config::get('website.id', 0)),
            'city_slug' => $citySlug,
            'city_name' => $cityName,
            'zones' => $zones,
            'quartiers' => $quartiers,
            'advisor_context' => [
                'name' => trim((string) ($siteConfig['advisor_name'] ?? '')),
                'zone' => trim((string) ($siteConfig['advisor_zone'] ?? '')),
                'tagline' => trim((string) ($siteConfig['advisor_tagline'] ?? '')),
            ],
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

if (!function_exists('getContactPhone')) {
    /**
     * Resolve contact phone from settings/config and return display + tel href.
     *
     * @return array{display:string,href:string}
     */
    function getContactPhone(): array
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

        $display = $pick(['contact_phone', 'phone_number', 'phone', 'telephone', 'advisor_phone']);
        if ($display === '') {
            $display = trim((string) Config::get('twilio.phone_number', ''));
        }

        if ($display === '') {
            return ['display' => '', 'href' => ''];
        }

        $href = preg_replace('/[^0-9+]/', '', $display) ?? '';
        if ($href === '') {
            return ['display' => '', 'href' => ''];
        }

        return [
            'display' => $display,
            'href' => 'tel:' . $href,
        ];
    }
}

if (!function_exists('getSocialProofConfig')) {
    /**
     * Build normalized social-proof metrics for public pages.
     *
     * @return array{
     *   google_reviews_count:int,
     *   google_rating:string,
     *   avg_delay_hours:int,
     *   clients_supported:int,
     *   sales_count:int,
     *   sectors_covered:int,
     *   local_support_label:string,
     *   google_maps_url:string
     * }
     */
    function getSocialProofConfig(): array
    {
        $settings = getSettingsMap();
        $siteConfig = getSiteConfig();
        $cityName = trim((string) ($siteConfig['ville'] ?? Config::get('city.name', 'votre ville')));
        if ($cityName === '') {
            $cityName = 'votre ville';
        }

        $quartiers = is_array($siteConfig['quartiers'] ?? null) ? $siteConfig['quartiers'] : [];
        $quartiersCount = count($quartiers);

        $intSetting = static function (string $key, int $fallback = 0) use ($settings): int {
            $value = trim((string) ($settings[$key] ?? ''));
            if ($value === '' || !is_numeric($value)) {
                return $fallback;
            }
            return max(0, (int) $value);
        };
        $stringSetting = static function (string $key, string $fallback = '') use ($settings): string {
            $value = trim((string) ($settings[$key] ?? ''));
            return $value !== '' ? $value : $fallback;
        };

        return [
            'google_reviews_count' => $intSetting('social_proof_google_reviews_count', 0),
            'google_rating' => $stringSetting('social_proof_google_rating', ''),
            'avg_delay_hours' => $intSetting('social_proof_avg_delay_hours', 24),
            'clients_supported' => $intSetting('social_proof_clients_supported', 0),
            'sales_count' => $intSetting('social_proof_sales_count', 0),
            'sectors_covered' => $intSetting('social_proof_sectors_count', $quartiersCount),
            'local_support_label' => $stringSetting('social_proof_local_support_label', 'Accompagnement local à ' . $cityName),
            'google_maps_url' => $stringSetting('social_proof_google_maps_url', ''),
        ];
    }
}
