<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;
use App\Models\GmbPublication;

final class GmbService
{
    private GmbPublication $model;

    public function __construct()
    {
        $this->model = new GmbPublication();
    }

    // ──────────────────────────────────────────────
    // Generate from Article
    // ──────────────────────────────────────────────

    /**
     * Generate a GMB publication from a blog article.
     *
     * @param array $article Article data (title, content, slug, focus_keyword, meta_description, article_type)
     * @return array|null Created publication data, or null on failure
     */
    public function generateFromArticle(array $article): ?array
    {
        // Determine post type based on article content
        $postType = $this->detectPostType($article);
        $ctaType = $this->detectCtaType($postType);
        $ctaUrl = $this->buildCtaUrl($this->detectCtaPath($postType, $article), $postType);

        // Generate content via AI
        $generated = $this->generateContent($article, $postType);
        if ($generated === null) {
            return null;
        }

        // Find next available slot
        $scheduledAt = $this->model->getNextAvailableSlot();

        $pubId = $this->model->create([
            'article_id'   => $article['id'] ?? null,
            'post_type'    => $postType,
            'title'        => $generated['title'] ?? null,
            'content'      => $generated['content'],
            'cta_type'     => $ctaType,
            'cta_url'      => $ctaUrl,
            'image_path'   => $article['og_image'] ?? null,
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        return $this->model->findById($pubId);
    }

    // ──────────────────────────────────────────────
    // Generate from Actualite
    // ──────────────────────────────────────────────

    /**
     * Generate a GMB publication from a news item.
     *
     * @param array $actualite Actualite data (title, content, slug, excerpt)
     * @return array|null Created publication data, or null on failure
     */
    public function generateFromActualite(array $actualite): ?array
    {
        $postType = 'update';
        $ctaUrl = $this->buildCtaUrl('/actualites/' . ($actualite['slug'] ?? ''), $postType);

        $generated = $this->generateContent([
            'title'            => $actualite['title'] ?? '',
            'content'          => $actualite['content'] ?? '',
            'focus_keyword'    => '',
            'meta_description' => $actualite['meta_description'] ?? $actualite['excerpt'] ?? '',
            'article_type'     => 'actualite',
        ], $postType);

        if ($generated === null) {
            return null;
        }

        $scheduledAt = $this->model->getNextAvailableSlot();

        $pubId = $this->model->create([
            'actualite_id' => $actualite['id'] ?? null,
            'post_type'    => $postType,
            'title'        => null,
            'content'      => $generated['content'],
            'cta_type'     => 'learn_more',
            'cta_url'      => $ctaUrl,
            'image_path'   => $actualite['image_url'] ?? null,
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        return $this->model->findById($pubId);
    }

    // ──────────────────────────────────────────────
    // Generate from free subject (manual)
    // ──────────────────────────────────────────────

    /**
     * Generate a GMB publication from a free-form subject.
     *
     * @param string $subject The topic/theme
     * @param string $postType The GMB post type
     * @return array|null Generated content {content, title}
     */
    public function generateManual(string $subject, string $postType = 'update'): ?array
    {
        return $this->generateContent([
            'title'            => $subject,
            'content'          => '',
            'focus_keyword'    => '',
            'meta_description' => '',
            'article_type'     => '',
        ], $postType);
    }

    // ──────────────────────────────────────────────
    // Notification Email
    // ──────────────────────────────────────────────

    /**
     * Send notification email for a scheduled publication.
     */
    public function sendNotificationEmail(array $publication): bool
    {
        $email = $this->model->getSetting('notification_email', '');
        if ($email === '' || $email === null) {
            $email = (string) Config::get('mail.from', 'contact@estimation-immobilier-bordeaux.fr');
        }

        $gmbUrl = $this->model->getSetting('gmb_profile_url', '');
        $siteUrl = (string) Config::get('app.url', 'https://estimation-immobilier-bordeaux.fr');
        $markPublishedUrl = $siteUrl . '/admin/gmb/mark-published/' . $publication['id'];

        $subject = "Publication GMB à poster - " . mb_substr($publication['title'] ?? $publication['content'], 0, 50);

        $htmlBody = $this->buildNotificationHtml($publication, $gmbUrl, $markPublishedUrl);

        $sent = Mailer::send($email, $subject, $htmlBody);

        if ($sent) {
            $this->model->markAsNotified((int) $publication['id']);
        }

        return $sent;
    }

    // ──────────────────────────────────────────────
    // Scheduling
    // ──────────────────────────────────────────────

    /**
     * Schedule a publication to a specific or next available date.
     */
    public function schedulePublication(int $publicationId, ?string $preferredDate = null): string
    {
        if ($preferredDate !== null) {
            // Check if the day already has 2+ publications
            $dateOnly = substr($preferredDate, 0, 10);
            $existing = $this->model->getScheduledForDate($dateOnly);
            if (count($existing) >= 2) {
                // Fall back to next available
                $preferredDate = null;
            }
        }

        $scheduledAt = $preferredDate ?? $this->model->getNextAvailableSlot();

        $pub = $this->model->findById($publicationId);
        if ($pub === null) {
            return $scheduledAt;
        }

        $this->model->update($publicationId, array_merge($pub, [
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]));

        return $scheduledAt;
    }

    /**
     * Expire old unposted publications and return count.
     */
    public function checkExpiredPosts(): int
    {
        return $this->model->expireOldScheduled();
    }

    // ──────────────────────────────────────────────
    // UTM Builder
    // ──────────────────────────────────────────────

    public function buildCtaUrl(string $path, string $postType = 'update'): string
    {
        $baseUrl = rtrim((string) Config::get('app.url', 'https://estimation-immobilier-bordeaux.fr'), '/');
        $url = $baseUrl . '/' . ltrim($path, '/');

        $params = http_build_query([
            'utm_source'   => 'google_business',
            'utm_medium'   => 'organic',
            'utm_campaign' => 'gmb_post',
            'utm_content'  => $postType,
        ]);

        return $url . (str_contains($url, '?') ? '&' : '?') . $params;
    }

    // ──────────────────────────────────────────────
    // Private — AI Generation
    // ──────────────────────────────────────────────

    private function generateContent(array $article, string $postType): ?array
    {
        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '') {
            return $this->fallbackContent($article, $postType);
        }

        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $systemPrompt = "Tu es un expert en marketing local et Google Business Profile pour une agence d'estimation immobilière à Bordeaux. "
            . "Génère une publication Google My Business à partir du contenu fourni.\n\n"
            . "RÈGLES STRICTES :\n"
            . "- Maximum 300 caractères (idéal 150-250)\n"
            . "- Le mot-clé principal doit apparaître dans la première phrase\n"
            . "- Inclure une mention géographique (Bordeaux, quartier)\n"
            . "- Ton professionnel mais accessible\n"
            . "- Pas de hashtags (inutiles sur GMB)\n"
            . "- Pas de numéro de téléphone dans le texte\n"
            . "- Pas d'URL dans le texte (le CTA s'en charge)\n"
            . "- Terminer par une phrase d'appel à l'action\n"
            . "- Maximum 1-2 émojis\n"
            . "- TYPE DE POST : {$postType}\n\n"
            . "Réponds UNIQUEMENT en JSON : {\"content\": \"...\", \"title\": \"...\"}\n"
            . "Le title est requis UNIQUEMENT si le type est event, offer ou product (max 58 caractères). Sinon, title = null.";

        $focusKeyword = $article['focus_keyword'] ?? '';
        $userPrompt = "ARTICLE SOURCE :\n"
            . "Titre : " . ($article['title'] ?? '') . "\n"
            . "Mot-clé principal : {$focusKeyword}\n"
            . "Description : " . ($article['meta_description'] ?? '') . "\n"
            . "Extrait du contenu : " . mb_substr(strip_tags($article['content'] ?? ''), 0, 500);

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.7,
            'max_tokens' => 300,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return $this->fallbackContent($article, $postType);
        }

        // Log usage
        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'gmb_generation');

        $content = $response['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode((string) $content, true);

        if (!is_array($decoded) || !isset($decoded['content'])) {
            return $this->fallbackContent($article, $postType);
        }

        return [
            'content' => mb_substr((string) $decoded['content'], 0, 1500),
            'title'   => isset($decoded['title']) ? mb_substr((string) $decoded['title'], 0, 58) : null,
        ];
    }

    private function fallbackContent(array $article, string $postType): array
    {
        $title = $article['title'] ?? 'Estimation immobilière Bordeaux';
        $keyword = $article['focus_keyword'] ?? 'estimation immobilière';

        $templates = [
            'update' => "{$keyword} à Bordeaux : découvrez notre dernier article sur {$title}. Nos experts vous accompagnent dans votre projet immobilier bordelais.",
            'offer'  => "Profitez d'une estimation gratuite de votre bien à Bordeaux. Nos experts analysent le marché local pour vous donner une valorisation précise et fiable.",
            'event'  => "Événement immobilier à Bordeaux : {$title}. Venez rencontrer nos experts pour une estimation personnalisée de votre bien.",
            'product' => "{$title} à Bordeaux. Faites estimer votre bien par nos experts du marché immobilier bordelais.",
        ];

        $content = $templates[$postType] ?? $templates['update'];

        return [
            'content' => mb_substr($content, 0, 300),
            'title'   => in_array($postType, ['event', 'offer', 'product'], true)
                ? mb_substr($title, 0, 58)
                : null,
        ];
    }

    // ──────────────────────────────────────────────
    // Private — Post type detection
    // ──────────────────────────────────────────────

    private function detectPostType(array $article): string
    {
        $type = mb_strtolower($article['article_type'] ?? '');
        $title = mb_strtolower($article['title'] ?? '');
        $keyword = mb_strtolower($article['focus_keyword'] ?? '');
        $combined = $type . ' ' . $title . ' ' . $keyword;

        if (preg_match('/estimation|avis.?valeur|estimer/', $combined)) {
            return 'offer';
        }
        if (preg_match('/evenement|événement|portes?.?ouvertes|salon|journée/', $combined)) {
            return 'event';
        }
        if (preg_match('/vendre|vente|achat|acheter|mandat|prix.?de.?vente/', $combined)) {
            return 'product';
        }

        return 'update';
    }

    private function detectCtaType(string $postType): string
    {
        return match ($postType) {
            'offer'   => 'get_offer',
            'event'   => 'book',
            'product' => 'learn_more',
            default   => 'learn_more',
        };
    }

    private function detectCtaPath(string $postType, array $article): string
    {
        return match ($postType) {
            'offer'  => '/lp/estimation-bordeaux',
            'event'  => '/lp/estimation-bordeaux',
            default  => '/blog/' . ($article['slug'] ?? ''),
        };
    }

    // ──────────────────────────────────────────────
    // Private — Notification HTML
    // ──────────────────────────────────────────────

    private function buildNotificationHtml(array $pub, string $gmbUrl, string $markPublishedUrl): string
    {
        $postTypeLabels = [
            'update'  => 'Nouveauté',
            'event'   => 'Événement',
            'offer'   => 'Offre',
            'product' => 'Produit/Service',
        ];
        $postTypeColors = [
            'update'  => '#3B82F6',
            'event'   => '#8B5CF6',
            'offer'   => '#10B981',
            'product' => '#F59E0B',
        ];

        $typeLabel = $postTypeLabels[$pub['post_type']] ?? 'Publication';
        $typeColor = $postTypeColors[$pub['post_type']] ?? '#6B7280';
        $title = htmlspecialchars($pub['title'] ?? '', ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($pub['content'] ?? '', ENT_QUOTES, 'UTF-8');
        $articleTitle = htmlspecialchars($pub['article_title'] ?? $pub['actualite_title'] ?? '', ENT_QUOTES, 'UTF-8');

        $titleBlock = $title !== '' ? "<p style=\"font-size:16px;font-weight:bold;margin:0 0 8px 0;\">{$title}</p>" : '';
        $articleBlock = $articleTitle !== '' ? "<p style=\"font-size:13px;color:#6B7280;margin:12px 0 0 0;\">Article source : <strong>{$articleTitle}</strong></p>" : '';

        $eventBlock = '';
        if ($pub['post_type'] === 'event' && !empty($pub['event_start'])) {
            $eventBlock = '<p style="font-size:13px;color:#8B5CF6;margin:8px 0 0 0;">Dates : '
                . htmlspecialchars($pub['event_start'], ENT_QUOTES, 'UTF-8')
                . (!empty($pub['event_end']) ? ' → ' . htmlspecialchars($pub['event_end'], ENT_QUOTES, 'UTF-8') : '')
                . '</p>';
        }

        $offerBlock = '';
        if ($pub['post_type'] === 'offer' && !empty($pub['offer_code'])) {
            $offerBlock = '<p style="font-size:13px;color:#10B981;margin:8px 0 0 0;">Code promo : <strong>'
                . htmlspecialchars($pub['offer_code'], ENT_QUOTES, 'UTF-8') . '</strong></p>';
        }

        $gmbButton = $gmbUrl !== '' && $gmbUrl !== null
            ? "<a href=\"{$gmbUrl}\" style=\"display:inline-block;padding:12px 24px;background:#8B1538;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;margin-right:12px;\">Ouvrir ma fiche Google</a>"
            : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">

<!-- Header -->
<tr><td style="background:#8B1538;padding:20px 24px;text-align:center;">
    <p style="margin:0;color:#D4AF37;font-size:20px;font-weight:bold;">Estimation Immobilier Bordeaux</p>
    <p style="margin:4px 0 0;color:#ffffff;font-size:13px;">Publication Google My Business</p>
</td></tr>

<!-- Body -->
<tr><td style="padding:24px;">
    <p style="font-size:15px;color:#374151;margin:0 0 16px 0;">
        Bonjour, une publication Google My Business est prévue aujourd'hui.
    </p>

    <!-- Type badge -->
    <span style="display:inline-block;padding:4px 12px;background:{$typeColor};color:#fff;border-radius:12px;font-size:12px;font-weight:bold;margin-bottom:12px;">
        {$typeLabel}
    </span>

    <!-- Content block -->
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:12px 0;">
        {$titleBlock}
        <p style="font-size:14px;color:#1f2937;margin:0;line-height:1.6;white-space:pre-wrap;">{$content}</p>
        {$eventBlock}
        {$offerBlock}
        {$articleBlock}
    </div>

    <!-- CTA info -->
    <p style="font-size:13px;color:#6B7280;margin:12px 0;">
        CTA prévu : <strong>{$pub['cta_type']}</strong>
    </p>

    <!-- Buttons -->
    <div style="text-align:center;margin:24px 0 12px 0;">
        {$gmbButton}
        <a href="{$markPublishedUrl}" style="display:inline-block;padding:12px 24px;background:#10B981;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">
            Marquer comme publié
        </a>
    </div>
</td></tr>

<!-- Footer -->
<tr><td style="background:#f9fafb;padding:16px 24px;text-align:center;border-top:1px solid #e5e7eb;">
    <p style="margin:0;font-size:12px;color:#9CA3AF;">
        Ce rappel a été généré automatiquement par votre plateforme Estimation Bordeaux.
    </p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;
    }

    // ──────────────────────────────────────────────
    // Private — HTTP & Cost (same pattern as AIService)
    // ──────────────────────────────────────────────

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
        ];

        [$inRate, $outRate] = $rates[$model] ?? [0.001, 0.002];

        return round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
    }
}
