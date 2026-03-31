<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nom de l'application
    |--------------------------------------------------------------------------
    */
    'name'        => $_ENV['APP_NAME']    ?? 'Estimateur Immobilier',
    'url'         => $_ENV['APP_URL']     ?? 'http://localhost',
    'env'         => $_ENV['APP_ENV']     ?? 'production',
    'debug'       => $_ENV['APP_DEBUG']   ?? false,
    'timezone'    => 'Europe/Paris',
    'locale'      => 'fr_FR',
    'charset'     => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Sécurité
    |--------------------------------------------------------------------------
    */
    'secret_key'  => $_ENV['APP_KEY'] ?? throw new \RuntimeException(
        'La variable d\'environnement APP_KEY est requise.'
    ),
    'token_ttl'   => 3600, // secondes

    /*
    |--------------------------------------------------------------------------
    | Chemins
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'root'    => dirname(__DIR__),
        'public'  => dirname(__DIR__) . '/public',
        'storage' => dirname(__DIR__) . '/storage',
        'cache'   => dirname(__DIR__) . '/storage/cache',
        'logs'    => dirname(__DIR__) . '/logs',
        'views'   => dirname(__DIR__) . '/src/Views',
    ],

];
