<?php

use App\Controllers\EstimationController;
use App\Controllers\HomeController;

return [
    'GET' => [
        '/' => [HomeController::class, 'index'],
        '/estimation' => [EstimationController::class, 'form'],
    ],
    'POST' => [
        '/estimation' => [EstimationController::class, 'calculate'],
    ],
];
