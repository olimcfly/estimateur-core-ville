<?php

declare(strict_types=1);

namespace App\Services;

final class EstimationService
{
    public function __construct(private readonly PerplexityService $perplexityService)
    {
    }

    public function estimate(string $city, string $propertyType, float $surface, int $rooms): array
    {
        $market = $this->perplexityService->fetchMarketRange($city, $propertyType);

        $low = round($market['low'] * $surface, 2);
        $mid = round(((($market['low'] + $market['mid'] + $market['high']) / 3) * $surface), 2);
        $high = round($market['high'] * $surface, 2);

        return [
            'city' => $city,
            'property_type' => $propertyType,
            'surface' => $surface,
            'rooms' => $rooms,
            'per_sqm_low' => $market['low'],
            'per_sqm_mid' => $market['mid'],
            'per_sqm_high' => $market['high'],
            'estimated_low' => $low,
            'estimated_mid' => $mid,
            'estimated_high' => $high,
        ];
    }
}
