<?php

declare(strict_types=1);

namespace App\Services;

final class LeadScoringService
{
    public function score(float $estimation, string $urgency, string $motivation): string
    {
        $budgetPoints = $estimation >= 450000 ? 3 : ($estimation >= 250000 ? 2 : 1);
        $urgencyPoints = match ($urgency) {
            'rapide' => 3,
            'moyen' => 2,
            default => 1,
        };
        $motivationPoints = match ($motivation) {
            'vente', 'divorce' => 3,
            'succession' => 2,
            default => 1,
        };

        $score = $budgetPoints + $urgencyPoints + $motivationPoints;

        if ($score >= 8) {
            return 'chaud';
        }

        if ($score >= 5) {
            return 'tiede';
        }

        return 'froid';
    }
}
