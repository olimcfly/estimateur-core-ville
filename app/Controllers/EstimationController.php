<?php

namespace App\Controllers;

class EstimationController
{
    public function form(): void
    {
        $pageTitle = "Formulaire d'estimation";
        $view = __DIR__ . '/../../views/home/index.php';
        require __DIR__ . '/../../views/layouts/main.php';
    }

    public function calculate(): void
    {
        $ville = trim($_POST['ville'] ?? '');
        $typeBien = trim($_POST['type_bien'] ?? '');
        $surface = (float) ($_POST['surface'] ?? 0);

        if ($ville === '' || $typeBien === '' || $surface <= 0) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Données invalides. Veuillez renseigner ville, type de bien et surface.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $basePrice = match ($typeBien) {
            'appartement' => 3200,
            'maison' => 2800,
            'terrain' => 850,
            default => 2500,
        };

        $estimation = round($surface * $basePrice);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'ville' => $ville,
            'type_bien' => $typeBien,
            'surface' => $surface,
            'estimation' => $estimation,
        ], JSON_UNESCAPED_UNICODE);
    }
}
