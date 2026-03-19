<?php

declare(strict_types=1);

use App\Controllers\EstimationController;
use App\Controllers\PageController;

$router->get('/', [EstimationController::class, 'index']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/contact', [PageController::class, 'contact']);
