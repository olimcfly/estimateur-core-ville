<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;

final class AIService
{
    public function generateArticle(string $persona, string $awarenessLevel, string $topic): array
    {
        $trendInsights = $this->fetchPerplexityTrends($topic, $persona);

        $prompt = "Tu es un copywriter immobilier expert Bordeaux. Rédige un article SEO en HTML strict avec les balises <h2>, <h3>, <p>, <ul>, <li>.\n"
            . "Persona cible: {$persona}. Niveau de conscience: {$awarenessLevel}. Sujet: {$topic}.\n"
            . "Intègre ce contexte marché:\n{$trendInsights}\n"
            . "Structure attendue: introduction engageante, 3 sections H2, sous-sections H3, FAQ, puis CTA estimation.\n"
            . "Réponds uniquement en JSON avec clés: title, meta_title, meta_description, content_html.";

        $fallback = $this->fallbackArticle($persona, $awarenessLevel, $topic, $trendInsights);

        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '') {
            return $fallback;
        }

        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.6,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => 'Tu écris un contenu premium pour un agent immobilier local.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return $fallback;
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'article_generation');

        $content = $response['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode((string) $content, true);

        if (!is_array($decoded) || !isset($decoded['title'], $decoded['content_html'])) {
            return $fallback;
        }

        return [
            'title' => (string) $decoded['title'],
            'meta_title' => (string) ($decoded['meta_title'] ?? $decoded['title']),
            'meta_description' => (string) ($decoded['meta_description'] ?? 'Découvrez notre analyse du marché immobilier à Bordeaux.'),
            'content' => (string) $decoded['content_html'],
        ];
    }

    private function fetchPerplexityTrends(string $topic, string $persona): string
    {
        $apiKey = (string) Config::get('perplexity.api_key', '');
        if ($apiKey === '') {
            return 'Tendance locale stable, demande soutenue sur Bordeaux intra-rocade, forte sensibilité au prix juste.';
        }

        $endpoint = (string) Config::get('perplexity.endpoint');
        $model = (string) Config::get('perplexity.model');

        $prompt = sprintf(
            'Résume en 6 points les tendances immobilières vendeurs à Bordeaux pour le sujet "%s" et le persona "%s".',
            $topic,
            $persona
        );

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.2,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return 'Tendance locale stable, demande soutenue sur Bordeaux intra-rocade, forte sensibilité au prix juste.';
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('perplexity', $model, $inputTokens, $outputTokens, $cost, 'market_research');

        $content = $response['choices'][0]['message']['content'] ?? '';
        return trim((string) $content) !== '' ? (string) $content : 'Tendance locale stable avec délais de vente variables selon quartier.';
    }

    private function postJson(string $endpoint, array $payload, array $headers): ?array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 25,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function estimateCost(string $model, int $inputTokens, int $outputTokens): float
    {
        $rates = [
            'gpt-4o'       => [0.0025, 0.0100],
            'gpt-4o-mini'  => [0.00015, 0.0006],
            'gpt-4.1'      => [0.002, 0.008],
            'gpt-4.1-mini' => [0.0004, 0.0016],
            'sonar'        => [0.001, 0.001],
            'sonar-pro'    => [0.003, 0.015],
        ];

        [$inRate, $outRate] = $rates[$model] ?? [0.001, 0.002];

        return round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
    }

    private function fallbackArticle(string $persona, string $awarenessLevel, string $topic, string $trendInsights): array
    {
        $title = $topic;
        return [
            'title' => $title,
            'meta_title' => $title . ' | Blog Immobilier Bordeaux',
            'meta_description' => 'Conseils concrets pour vendre votre bien à Bordeaux selon votre situation.',
            'content' => '<p>Vous souhaitez vendre dans les meilleures conditions ? Voici un guide pragmatique orienté résultats.</p>'
                . '<h2>Ce que dit le marché local</h2><p>' . nl2br(e($trendInsights)) . '</p>'
                . '<h2>Plan d\'action pour ' . e($persona) . '</h2><p>Commencez par une estimation précise puis préparez un plan de mise en vente adapté.</p>'
                . '<h2>FAQ vendeur</h2><h3>Quand publier l\'annonce ?</h3><p>Dès que le prix est validé avec les données de marché.</p>'
                . '<h3>Comment accélérer la vente ?</h3><p>Soignez la présentation du bien et ciblez les bons acquéreurs.</p>'
                . '<p><strong>CTA :</strong> Demandez votre estimation immobilière personnalisée dès maintenant.</p>',
        ];
    }
}
