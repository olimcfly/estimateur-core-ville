<?php

declare(strict_types=1);

return [
    'city' => env('SITE_CITY', ''),
    'city_slug' => env('SITE_CITY_SLUG', ''),
    'zip' => env('SITE_ZIP', ''),
    'region' => env('SITE_REGION', ''),
    'country' => env('SITE_COUNTRY', 'France'),
    'domain' => env('SITE_DOMAIN', ''),
    'city_factor' => (float) env('SITE_CITY_FACTOR', '1.0'),
    'city_baseline_price' => (float) env('SITE_CITY_BASELINE_PRICE', '4200.0'),
];
