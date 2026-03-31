<?php

$defaultConnection = $_ENV['DB_CONNECTION'] ?? 'saas_main';

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Base de Données
    |--------------------------------------------------------------------------
    */
    'default' => $defaultConnection,

    'connections' => [

        'saas_main' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['SAAS_MAIN_DB_HOST']     ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['SAAS_MAIN_DB_PORT']     ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['SAAS_MAIN_DB_NAME']     ?? $_ENV['DB_NAME'] ?? 'estimateur_immo',
            'username'  => $_ENV['SAAS_MAIN_DB_USER']     ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['SAAS_MAIN_DB_PASS']     ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'saas_estimations' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['SAAS_ESTIMATIONS_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['SAAS_ESTIMATIONS_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['SAAS_ESTIMATIONS_DB_NAME'] ?? 'estimateur_immo_estimations',
            'username'  => $_ENV['SAAS_ESTIMATIONS_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['SAAS_ESTIMATIONS_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'ville_test_paris' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['VILLE_TEST_PARIS_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['VILLE_TEST_PARIS_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['VILLE_TEST_PARIS_DB_NAME'] ?? 'ville_test_paris',
            'username'  => $_ENV['VILLE_TEST_PARIS_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['VILLE_TEST_PARIS_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'ville_demo_lyon' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['VILLE_DEMO_LYON_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['VILLE_DEMO_LYON_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['VILLE_DEMO_LYON_DB_NAME'] ?? 'ville_demo_lyon',
            'username'  => $_ENV['VILLE_DEMO_LYON_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['VILLE_DEMO_LYON_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'immo_test' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['IMMO_TEST_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['IMMO_TEST_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['IMMO_TEST_DB_NAME'] ?? 'immo_test',
            'username'  => $_ENV['IMMO_TEST_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['IMMO_TEST_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'immo_demo' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['IMMO_DEMO_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['IMMO_DEMO_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['IMMO_DEMO_DB_NAME'] ?? 'immo_demo',
            'username'  => $_ENV['IMMO_DEMO_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['IMMO_DEMO_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'client_test' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['CLIENT_TEST_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['CLIENT_TEST_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['CLIENT_TEST_DB_NAME'] ?? 'client_test',
            'username'  => $_ENV['CLIENT_TEST_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['CLIENT_TEST_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'client_demo' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['CLIENT_DEMO_DB_HOST'] ?? $_ENV['SAAS_MAIN_DB_HOST'] ?? $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => $_ENV['CLIENT_DEMO_DB_PORT'] ?? $_ENV['SAAS_MAIN_DB_PORT'] ?? $_ENV['DB_PORT'] ?? '3306',
            'database'  => $_ENV['CLIENT_DEMO_DB_NAME'] ?? 'client_demo',
            'username'  => $_ENV['CLIENT_DEMO_DB_USER'] ?? $_ENV['SAAS_MAIN_DB_USER'] ?? $_ENV['DB_USER'] ?? 'root',
            'password'  => $_ENV['CLIENT_DEMO_DB_PASS'] ?? $_ENV['SAAS_MAIN_DB_PASS'] ?? $_ENV['DB_PASS'] ?? '',
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
