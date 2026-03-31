<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
$vendorAutoload = $rootPath . '/vendor/autoload.php';

if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

if (class_exists('Dotenv\\Dotenv') && file_exists($rootPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($rootPath)->safeLoad();
}

$config = require $rootPath . '/config/app.php';
$routes = require $rootPath . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$handler = $routes[$method][$uri] ?? null;

if ($handler === null) {
    http_response_code(404);
    echo '404 - Page non trouvée';
    exit;
}

[$controllerClass, $action] = $handler;
$controller = new $controllerClass();
$controller->$action();
