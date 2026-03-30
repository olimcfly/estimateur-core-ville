<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Base de Données
    |--------------------------------------------------------------------------
    */
    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',

    'connections' => [

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST']     ?? '127.0.0.1',
            'port'      => $_ENV['DB_PORT']     ?? '3306',
            'database'  => $_ENV['DB_NAME']     ?? 'estimateur_immo',
            'username'  => $_ENV['DB_USER']     ?? 'root',
            'password'  => $_ENV['DB_PASS']     ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Pool de connexions
    |--------------------------------------------------------------------------
    */
    'pool' => [
        'min_connections' => 1,
        'max_connections' => 10,
        'timeout'         => 30,
    ],

];
