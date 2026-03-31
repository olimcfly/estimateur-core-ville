<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Estimateur Immobilier',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Europe/Paris',
    'locale' => $_ENV['APP_LOCALE'] ?? 'fr',
];
