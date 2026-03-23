<?php
/**
 * Google Ads Campaign — Claude API Generate Endpoint
 * Actions: generate_ads, generate_landing
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body || empty($body['action'])) {
        throw new \InvalidArgumentException('Missing action parameter');
    }

    $action = $body['action'];

    switch ($action) {
        case 'generate_ads':
            echo json_encode(handleGenerateAds($body));
            break;
        case 'generate_landing':
            echo json_encode(handleGenerateLanding($body));
            break;
        default:
            throw new \InvalidArgumentException('Unknown action: ' . $action);
    }
} catch (\Throwable $e) {
    http_response_code(200); // keep 200 so JS can parse
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

/* ================================================================
   ACTION: generate_ads
   ================================================================ */
function handleGenerateAds(array $body): array
{
    $name          = trim($body['name'] ?? '');
    $ville         = trim($body['ville'] ?? '');
    $domain        = trim($body['domain'] ?? '');
    $campaignType  = trim($body['campaign_type'] ?? '');
    $campaignLabel = trim($body['campaign_label'] ?? '');

    if (!$name || !$ville || !$domain || !$campaignType) {
        throw new \InvalidArgumentException('Missing required fields for generate_ads');
    }

    $prompt = <<<PROMPT
Tu es un expert Google Ads immobilier. Génère 2 variantes d'annonces Google Ads pour :
- Conseiller : {$name}
- Ville : {$ville}
- Domaine : {$domain}
- Type de campagne : {$campaignType}
- Libellé : {$campaignLabel}

Règles strictes :
- titre1, titre2, titre3 : maximum 30 caractères chacun
- desc1, desc2 : maximum 90 caractères chacun
- 2 variantes : varianteA et varianteB

Réponds UNIQUEMENT en JSON valide, sans markdown, sans explication :
{
  "varianteA": { "titre1": "", "titre2": "", "titre3": "", "desc1": "", "desc2": "" },
  "varianteB": { "titre1": "", "titre2": "", "titre3": "", "desc1": "", "desc2": "" }
}
PROMPT;

    $raw = callClaudeApi($prompt, 800);
    $ads = parseJsonResponse($raw);

    // Validate character limits
    foreach (['varianteA', 'varianteB'] as $variant) {
        if (!isset($ads[$variant])) {
            throw new \RuntimeException("Missing {$variant} in API response");
        }
        foreach (['titre1', 'titre2', 'titre3'] as $field) {
            if (mb_strlen($ads[$variant][$field] ?? '') > 30) {
                $ads[$variant][$field] = mb_substr($ads[$variant][$field], 0, 30);
            }
        }
        foreach (['desc1', 'desc2'] as $field) {
            if (mb_strlen($ads[$variant][$field] ?? '') > 90) {
                $ads[$variant][$field] = mb_substr($ads[$variant][$field], 0, 90);
            }
        }
    }

    return ['ok' => true, 'ads' => $ads];
}

/* ================================================================
   ACTION: generate_landing
   ================================================================ */
function handleGenerateLanding(array $body): array
{
    $name          = trim($body['name'] ?? '');
    $ville         = trim($body['ville'] ?? '');
    $domain        = trim($body['domain'] ?? '');
    $phone         = trim($body['phone'] ?? '');
    $tagline       = trim($body['tagline'] ?? '');
    $campaignType  = trim($body['campaign_type'] ?? '');
    $campaignLabel = trim($body['campaign_label'] ?? '');
    $primaryColor  = trim($body['primary_color'] ?? '#1a73e8');
    $reviewsCount  = trim($body['reviews_count'] ?? '150');
    $rating        = trim($body['rating'] ?? '4.9');
    $estimations   = trim($body['estimations'] ?? '5000');
    $yearsExp      = trim($body['years_exp'] ?? '15');
    $testimonial1  = trim($body['testimonial1'] ?? '');
    $testimonial2  = trim($body['testimonial2'] ?? '');
    $adTitre1      = trim($body['ad_titre1'] ?? '');

    if (!$name || !$ville || !$domain || !$campaignType) {
        throw new \InvalidArgumentException('Missing required fields for generate_landing');
    }

    $villeSlug = slugify($ville);
    $path      = "/lp/estimation-immobiliere-{$villeSlug}";
    $canonical = "https://{$domain}{$path}";

    $prompt = <<<PROMPT
Tu es un expert en landing pages immobilières optimisées pour Google Ads.
Génère une landing page HTML complète pour :

Informations :
- Conseiller : {$name}
- Ville : {$ville}
- Domaine : {$domain}
- Téléphone : {$phone}
- Accroche : {$tagline}
- Type : {$campaignType}
- Libellé campagne : {$campaignLabel}
- Couleur principale : {$primaryColor}
- Nombre d'avis : {$reviewsCount}
- Note moyenne : {$rating}
- Estimations réalisées : {$estimations}
- Années d'expérience : {$yearsExp}
- Témoignage 1 : {$testimonial1}
- Témoignage 2 : {$testimonial2}
- Titre annonce (H1 doit correspondre) : {$adTitre1}

Règles STRICTES :
1. Le H1 doit reprendre exactement le mot-clé de l'annonce pour la cohérence Quality Score
2. Formulaire au-dessus de la ligne de flottaison avec 3 champs : Nom, Email, Téléphone
3. Meta robots : noindex, nofollow
4. CSS 100% inline (aucun fichier externe, aucun CDN)
5. Aucun menu de navigation
6. Inclure un JSON-LD Schema.org LocalBusiness
7. Balise canonical : {$canonical}
8. Capture UTM via URLSearchParams → sessionStorage dans un script
9. Fonction JS handleSubmit qui pousse vers dataLayer : {{ event: 'form_submit', formType: '{$campaignType}' }}
10. Sections obligatoires dans cet ordre :
    - Hero avec formulaire
    - 3 bénéfices
    - Preuve sociale (chiffres)
    - Témoignages
    - Comment ça marche (3 étapes)
    - FAQ (5 questions/réponses)
    - CTA final
    - Footer minimal
11. La réponse commence par <!DOCTYPE html>, sans aucun markdown

Génère UNIQUEMENT le code HTML complet.
PROMPT;

    $html = callClaudeApi($prompt, 4096);

    // Strip any markdown fences that might wrap the HTML
    $html = preg_replace('/^```html?\s*/i', '', $html);
    $html = preg_replace('/\s*```\s*$/', '', $html);
    $html = trim($html);

    if (stripos($html, '<!DOCTYPE html') !== 0) {
        throw new \RuntimeException('Landing HTML does not start with <!DOCTYPE html>');
    }

    return ['ok' => true, 'html' => $html, 'path' => $path];
}

/* ================================================================
   Claude API helper
   ================================================================ */
function callClaudeApi(string $prompt, int $maxTokens): string
{
    $apiKey = trim((string) getenv('ANTHROPIC_API_KEY'));
    if ($apiKey === '') {
        // Fallback to a config file
        $configPath = dirname(__DIR__, 2) . '/config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
            $apiKey = defined('ANTHROPIC_API_KEY') ? trim((string) ANTHROPIC_API_KEY) : '';
        }
    }
    if ($apiKey === '') {
        throw new \RuntimeException('ANTHROPIC_API_KEY is not configured');
    }

    $payload = json_encode([
        'model'      => 'claude-sonnet-4-20250514',
        'max_tokens' => $maxTokens,
        'messages'   => [
            ['role' => 'user', 'content' => $prompt]
        ]
    ]);

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        throw new \RuntimeException('cURL error: ' . $curlErr);
    }
    if ($httpCode !== 200) {
        throw new \RuntimeException('Claude API returned HTTP ' . $httpCode);
    }

    $data = json_decode($response, true);
    if (!$data || empty($data['content'][0]['text'])) {
        throw new \RuntimeException('Invalid response from Claude API');
    }

    return $data['content'][0]['text'];
}

/* ================================================================
   Parse JSON from Claude response (strip markdown fences)
   ================================================================ */
function parseJsonResponse(string $raw): array
{
    // Strip ```json ... ``` fences
    $clean = preg_replace('/^```json?\s*/i', '', trim($raw));
    $clean = preg_replace('/\s*```\s*$/', '', $clean);

    $decoded = json_decode($clean, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException('Failed to parse JSON from API: ' . json_last_error_msg());
    }

    return $decoded;
}

/* ================================================================
   Slugify helper
   ================================================================ */
function slugify(string $str): string
{
    $str = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $str);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}
