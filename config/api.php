<?php

return [

    /*
    |--------------------------------------------------------------------------
    | APIs Immobilières
    |--------------------------------------------------------------------------
    */

    'dvf' => [
        'base_url'  => 'https://api.gouv.fr/dvf',
        'key'       => $_ENV['DVF_API_KEY'] ?? '',
        'timeout'   => 15,
        'cache_ttl' => 86400, // 24h
    ],

    'ban' => [
        // Base Adresse Nationale (géocodage)
        'base_url'  => 'https://api-adresse.data.gouv.fr',
        'timeout'   => 10,
        'cache_ttl' => 604800, // 7 jours
    ],

    'insee' => [
        'base_url'  => 'https://api.insee.fr/series/BDM/V1',
        'key'       => $_ENV['INSEE_API_KEY'] ?? '',
        'timeout'   => 15,
        'cache_ttl' => 86400,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'requests_per_minute' => 60,
        'requests_per_day'    => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry
    |--------------------------------------------------------------------------
    */
    'retry' => [
        'attempts' => 3,
        'delay'    => 1000, // millisecondes
    ],

];
