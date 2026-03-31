<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $pageTitle = "Accueil - Estimateur Immobilier";
        $view = __DIR__ . '/../../views/home/index.php';
        require __DIR__ . '/../../views/layouts/main.php';
    }
}
