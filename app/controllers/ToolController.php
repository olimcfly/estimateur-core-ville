<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\View;

final class ToolController
{
    public function calculatrice(): void
    {
        $city = trim((string) Config::get('city.name', ''));
        $cityLabel = $city !== '' ? $city : 'locale';
        View::render('tools/calculatrice', [
            'page_title' => 'Calculatrice Immobilière ' . $cityLabel . ' - Estimation Rapide',
        ]);
    }
}
