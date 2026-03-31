<?php

$defaultConnection = $_ENV['DB_CONNECTION'] ?? 'saas_main';

$baseOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$makeConnection = static function (string $prefix, string $fallbackDbName) use ($baseOptions): array {
    $host = $_ENV["{$prefix}_DB_HOST"] ?? $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV["{$prefix}_DB_PORT"] ?? $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV["{$prefix}_DB_NAME"] ?? $fallbackDbName;
    $username = $_ENV["{$prefix}_DB_USER"] ?? $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV["{$prefix}_DB_PASS"] ?? $_ENV['DB_PASS'] ?? '';

    return [
        'driver'    => 'mysql',
        'host'      => $host,
        'port'      => $port,
        'database'  => $database,
        'username'  => $username,
        'password'  => $password,
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options'   => $baseOptions,
    ];
};

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Base de Données
    |--------------------------------------------------------------------------
    */
    'default' => $defaultConnection,

    'connections' => [
        'saas_main'        => $makeConnection('SAAS_MAIN', 'estimateur_immo'),
        'saas_estimations' => $makeConnection('SAAS_ESTIMATIONS', 'estimateur_immo_estimations'),
        'ville_test_paris' => $makeConnection('VILLE_TEST_PARIS', 'ville_test_paris'),
        'ville_demo_lyon'  => $makeConnection('VILLE_DEMO_LYON', 'ville_demo_lyon'),
        'immo_test'        => $makeConnection('IMMO_TEST', 'immo_test'),
        'immo_demo'        => $makeConnection('IMMO_DEMO', 'immo_demo'),
        'client_test'      => $makeConnection('CLIENT_TEST', 'client_test'),
        'client_demo'      => $makeConnection('CLIENT_DEMO', 'client_demo'),

        // Compatibilité historique (single DB)
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['DB_NAME'] ?? 'estimateur_immo',
            'username'  => $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => $baseOptions,
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
