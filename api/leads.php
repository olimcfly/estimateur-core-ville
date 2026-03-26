<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$required = ['firstname', 'lastname', 'email', 'phone', 'ville'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => "Champ manquant: {$field}"]);
        exit;
    }
}

// Ici brancher votre logique d'insertion (DB, CRM, webhook...).

echo json_encode(['success' => true, 'message' => 'Lead enregistré']);
