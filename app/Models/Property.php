<?php

namespace App\Models;

class Property
{
    public function estimatePrice(string $typeBien, float $surface): float
    {
        $pricePerM2 = match ($typeBien) {
            'appartement' => 3200,
            'maison' => 2800,
            'terrain' => 850,
            default => 2500,
        };

        return $surface * $pricePerM2;
    }
}
