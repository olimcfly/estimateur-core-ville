<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;
use App\Core\Database;
use App\Models\GmbPublication;

final class GmbService
{
    private GmbPublication $publication;

    public function __construct()
    {
        $this->publication = new GmbPublication();
    }

    // ──────────────────────────────────────────────
    // Generate from Article
    // ──────────────────────────────────────────────

    public function generateFromArticle(array $article): ?array
    {
        $postType = $this->determinePostType((string) ($article['article_type'] ?? ''));
        $ctaType = $this->determineCtaType($postType);
        $ctaPath = $this->determineCtaPath($postType, (string) ($article['slug'] ?? ''));
        $ctaUrl = $this->buildCtaUrl($ctaPath, 'gmb');

        $aiContent = $this->generateContent($article, $postType);
        if ($aiContent === null) {
            return null;
        }

        $scheduledAt = $this->publication->getNextAvailableSlot();

        $data = [
            'article_id'   => $article['id'] ?? null,
            'actualite_id' => null,
            'post_type'    => $postType,
            'title'        => $aiContent['title'] ?? null,
            'content'      => $aiContent['content'],
            'cta_type'     => $ctaType,
            'cta_url'      => $ctaUrl,
            'image_path'   => $article['image_path'] ?? null,
            'offer_code'   => $postType === 'offer' ? 'ESTIMATION GRATUITE' : null,
            'offer_terms'  => $postType === 'offer' ? 'Estimation gratuite et sans engagement à Bordeaux et alentours' : null,
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ];

        $id = $this->publication->create($data);

        return $this->publication->findById($id);
    }

    // ──────────────────────────────────────────────
    // Generate from Actualite
    // ──────────────────────────────────────────────

    public function generateFromActualite(array $actualite): ?array
    {
        $postType = 'update';
        $ctaType = 'learn_more';
        $ctaPath = '/actualites/' . ($actualite['slug'] ?? '');
        $ctaUrl = $this->buildCtaUrl($ctaPath, 'gmb');

        $aiContent = $this->generateContent($actualite, $postType);
        if ($aiContent === null) {
            return null;
        }

        $scheduledAt = $this->publication->getNextAvailableSlot();

        $data = [
            'article_id'   => null,
            'actualite_id' => $actualite['id'] ?? null,
            'post_type'    => $postType,
            'title'        => $aiContent['title'] ?? null,
            'content'      => $aiContent['content'],
            'cta_type'     => $ctaType,
            'cta_url'      => $ctaUrl,
            'image_path'   => $actualite['image_path'] ?? null,
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ];

        $id = $this->publication->create($data);

        return $this->publication->findById($id);
    }

    // ──────────────────────────────────────────────
    // UTM URL Builder
    // ──────────────────────────────────────────────

    public function buildCtaUrl(string $path, string $source = 'gmb'): string
    {
        $baseUrl = rtrim((string) Config::get('base_url', ''), '/');
        $path = '/' . ltrim($path, '/');

        $params = http_build_query([
            'utm_source'   => 'google_business',
            'utm_medium'   => 'organic',
            'utm_campaign' => 'gmb_post',
            'utm_content'  => $source,
        ]);

        return $baseUrl . $path . '?' . $params;
    }

    // ──────────────────────────────────────────────
    // Schedule Publication
    // ──────────────────────────────────────────────

    public function schedulePublication(int $websiteId, int $publicationId, ?string $preferredDate = null): bool
    {
        $publication = $this->publication->findById($publicationId);
        if ($publication === null) {
            return false;
        }

        if ($preferredDate !== null) {
            $existing = $this->publication->getScheduledForDate($preferredDate);
            if (count($existing) >= 2) {
                return false;
            }

            $notificationHour = (int) $this->publication->getSetting('notification_hour', '8');
            $scheduledAt = $preferredDate . ' ' . str_pad((string) $notificationHour, 2, '0', STR_PAD_LEFT) . ':00:00';
        } else {
            $scheduledAt = $this->publication->getNextAvailableSlot();
        }

        $publication['scheduled_at'] = $scheduledAt;
        $publication['status'] = 'scheduled';
        $this->publication->update($publicationId, $publication);

        return true;
    }

    // ──────────────────────────────────────────────
    // Notification Email
    // ──────────────────────────────────────────────

    public function sendNotificationEmail(array $publication): bool
    {
        $adminEmail = (string) Config::get('mail.admin_email', 'contact@estimation-immobilier-bordeaux.fr');
        $baseUrl = rtrim((string) Config::get('base_url', ''), '/');

        $title = htmlspecialchars((string) ($publication['title'] ?? 'Publication GMB'), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars((string) ($publication['content'] ?? ''), ENT_QUOTES, 'UTF-8');
        $postType = htmlspecialchars((string) ($publication['post_type'] ?? 'update'), ENT_QUOTES, 'UTF-8');
        $scheduledAt = (string) ($publication['scheduled_at'] ?? date('Y-m-d'));
        $publicationId = (int) ($publication['id'] ?? 0);

        $gmbUrl = htmlspecialchars((string) $this->publication->getSetting('gmb_url', 'https://business.google.com/'), ENT_QUOTES, 'UTF-8');
        $markPublishedUrl = htmlspecialchars($baseUrl . '/admin/gmb/mark-published/' . $publicationId, ENT_QUOTES, 'UTF-8');

        $imageHtml = '';
        if (!empty($publication['image_path'])) {
            $imageSrc = htmlspecialchars($baseUrl . '/' . ltrim((string) $publication['image_path'], '/'), ENT_QUOTES, 'UTF-8');
            $imageHtml = '<tr><td style="padding:0 40px 20px;"><img src="' . $imageSrc . '" alt="Image publication" style="max-width:100%;border-radius:8px;"></td></tr>';
        }

        $subject = "\xF0\x9F\x93\x8C Publication GMB à poster aujourd'hui - {$title}";

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

  <!-- Header -->
  <tr>
    <td style="background:#1a1410;padding:25px 40px;">
      <h1 style="margin:0;color:#ffffff;font-size:18px;">📌 Publication GMB à poster</h1>
      <p style="margin:5px 0 0;color:#D4AF37;font-size:14px;">Programmée le {$scheduledAt}</p>
    </td>
  </tr>

  <!-- Post type badge -->
  <tr>
    <td style="padding:25px 40px 0;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:#8B1538;color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:bold;">
            Type : {$postType}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Title -->
  <tr>
    <td style="padding:20px 40px 10px;">
      <h2 style="margin:0;color:#1a1410;font-size:20px;">{$title}</h2>
    </td>
  </tr>

  <!-- Content to copy -->
  <tr>
    <td style="padding:0 40px 20px;">
      <div style="background:#faf9f7;border:1px solid #e8dfd7;border-radius:8px;padding:20px;">
        <p style="margin:0 0 8px;color:#6b6459;font-size:12px;font-weight:bold;text-transform:uppercase;">Texte prêt à copier :</p>
        <p style="margin:0;color:#1a1410;line-height:1.7;font-size:15px;">{$content}</p>
      </div>
    </td>
  </tr>

  <!-- Image -->
  {$imageHtml}

  <!-- Action buttons -->
  <tr>
    <td style="padding:0 40px 30px;">
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td style="padding-right:10px;" width="50%">
            <a href="{$gmbUrl}" style="background:#4285F4;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;display:block;text-align:center;font-size:14px;font-weight:bold;">Ouvrir Google Business</a>
          </td>
          <td style="padding-left:10px;" width="50%">
            <a href="{$markPublishedUrl}" style="background:#16a34a;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;display:block;text-align:center;font-size:14px;font-weight:bold;">✅ Marquer comme publié</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#faf9f7;padding:20px 40px;text-align:center;border-top:1px solid #e8dfd7;">
      <p style="margin:0;font-size:12px;color:#6b6459;">
        Notification automatique &mdash; Estimation Immobilier Bordeaux
      </p>
    </td>
  </tr>

</table>
</td></tr></table>
</body>
</html>
HTML;

        $sent = Mailer::send($adminEmail, $subject, $html);

        if ($sent && $publicationId > 0) {
            $this->publication->markAsNotified($publicationId);
        }

        return $sent;
    }

    // ──────────────────────────────────────────────
    // Expire old posts
    // ──────────────────────────────────────────────

    public function checkExpiredPosts(int $websiteId): int
    {
        return $this->publication->expireOldScheduled();
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function determinePostType(string $articleType): string
    {
        $type = strtolower($articleType);

        if (str_contains($type, 'estimation') || str_contains($type, 'avis-valeur')) {
            return 'offer';
        }
        if (str_contains($type, 'evenement') || str_contains($type, 'portes-ouvertes')) {
            return 'event';
        }
        if (str_contains($type, 'vendre') || str_contains($type, 'achat')) {
            return 'product';
        }

        return 'update';
    }

    private function determineCtaType(string $postType): string
    {
        return match ($postType) {
            'offer'   => 'get_offer',
            'product' => 'learn_more',
            'event'   => 'book',
            default   => 'learn_more',
        };
    }

    private function determineCtaPath(string $postType, string $slug): string
    {
        return match ($postType) {
            'offer' => '/lp/estimation-bordeaux',
            default => '/blog/' . $slug,
        };
    }

    private function generateContent(array $source, string $postType): ?array
    {
        $title = (string) ($source['title'] ?? '');
        $content = (string) ($source['content'] ?? '');
        $focusKeyword = (string) ($source['focus_keyword'] ?? '');
        $metaDescription = (string) ($source['meta_description'] ?? '');

        $systemPrompt = "Tu es un expert en marketing local et Google Business Profile pour une agence d'estimation immobilière à Bordeaux. Génère une publication Google My Business à partir de cet article de blog.\n"
            . "RÈGLES STRICTES :\n"
            . "- Maximum 300 caractères (idéal 150-250)\n"
            . "- Le mot-clé principal doit apparaître dans la première phrase\n"
            . "- Inclure une mention géographique (Bordeaux, quartier)\n"
            . "- Ton professionnel mais accessible\n"
            . "- Pas de hashtags (inutiles sur GMB)\n"
            . "- Pas de numéro de téléphone dans le texte\n"
            . "- Pas d'URL dans le texte (le CTA s'en charge)\n"
            . "- Terminer par une phrase d'appel à l'action\n"
            . "- Ne pas utiliser d'émojis excessifs (1-2 max)\n"
            . "TYPE DE POST : {$postType}\n"
            . 'FORMAT : Retourne UNIQUEMENT un JSON : {"content": "...", "title": "..." (si event/offer/product, max 58 car)}';

        $userPrompt = "Titre de l'article : {$title}\n"
            . "Mot-clé principal : {$focusKeyword}\n"
            . "Meta description : {$metaDescription}\n"
            . "Contenu (extrait) : " . mb_substr(strip_tags($content), 0, 500);

        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '') {
            return $this->fallbackContent($title, $focusKeyword, $postType);
        }

        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $response = $this->postJson($endpoint, [
            'model'           => $model,
            'temperature'     => 0.7,
            'max_tokens'      => 300,
            'response_format' => ['type' => 'json_object'],
            'messages'        => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return $this->fallbackContent($title, $focusKeyword, $postType);
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'gmb_generation');

        $raw = $response['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode((string) $raw, true);

        if (!is_array($decoded) || !isset($decoded['content'])) {
            return $this->fallbackContent($title, $focusKeyword, $postType);
        }

        return [
            'content' => (string) $decoded['content'],
            'title'   => isset($decoded['title']) ? (string) $decoded['title'] : null,
        ];
    }

    private function fallbackContent(string $title, string $focusKeyword, string $postType): array
    {
        $keyword = $focusKeyword !== '' ? $focusKeyword : $title;

        $content = match ($postType) {
            'offer'   => "{$keyword} à Bordeaux : profitez d'une estimation gratuite et sans engagement. Nos experts analysent votre bien pour vous donner un prix juste. Demandez votre estimation dès maintenant.",
            'event'   => "{$keyword} à Bordeaux : participez à notre prochain événement immobilier. Rencontrez nos experts et obtenez des conseils personnalisés. Réservez votre place !",
            'product' => "{$keyword} à Bordeaux : découvrez nos solutions pour votre projet immobilier. Accompagnement personnalisé et expertise locale. En savoir plus sur notre approche.",
            default   => "{$keyword} à Bordeaux : retrouvez notre dernière analyse du marché immobilier local. Des conseils d'experts pour réussir votre projet. Consultez l'article complet.",
        };

        return [
            'content' => mb_substr($content, 0, 300),
            'title'   => in_array($postType, ['event', 'offer', 'product'], true) ? mb_substr($title, 0, 58) : null,
        ];
    }

    private function postJson(string $endpoint, array $payload, array $headers): ?array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT        => 25,
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
}
