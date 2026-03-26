<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;
use App\Models\RssArticle;
use App\Models\RssSource;

final class RssFeedService
{
    /**
     * Fetch and parse a single RSS feed, storing new articles.
     */
    public function fetchFeed(array $source): array
    {
        $feedUrl = $source['feed_url'];
        $sourceId = (int) $source['id'];
        $sourceModel = new RssSource();
        $articleModel = new RssArticle();

        $xml = $this->loadXml($feedUrl);
        if ($xml === null) {
            $error = 'Impossible de charger le flux RSS';
            $sourceModel->updateLastFetched($sourceId, $error);
            return ['success' => false, 'error' => $error, 'new_articles' => 0];
        }

        $items = $this->parseItems($xml);
        $newCount = 0;

        foreach ($items as $item) {
            $item['rss_source_id'] = $sourceId;
            $id = $articleModel->insertIfNew($item);
            if ($id !== null) {
                $newCount++;
            }
        }

        $sourceModel->updateLastFetched($sourceId);

        return ['success' => true, 'total_items' => count($items), 'new_articles' => $newCount];
    }

    /**
     * Fetch all active feeds.
     */
    public function fetchAllFeeds(): array
    {
        $sourceModel = new RssSource();
        $sources = $sourceModel->findActive();
        $results = [];

        foreach ($sources as $source) {
            $results[] = [
                'source_id' => $source['id'],
                'source_name' => $source['name'],
                'result' => $this->fetchFeed($source),
            ];
        }

        return $results;
    }

    /**
     * Build the Claude prompt for generating a blog article from one RSS source article.
     */
    public function buildSingleArticlePrompt(array $rssArticle): string
    {
        $location = $this->locationContext();
        $titre = $rssArticle['title'] ?? '';
        $nomSource = $rssArticle['source_name'] ?? '';
        $urlSource = $rssArticle['link'] ?? '';
        $dateSource = $rssArticle['pub_date'] ?? '';
        $resume = $rssArticle['description'] ?? '';
        $contenu = $rssArticle['content'] ?? '';

        return <<<PROMPT
Role : Tu es un expert en marche immobilier local ({$location['area']}) et en redaction web SEO. Tu aides un conseiller immobilier a publier des articles de blog originaux a partir d'articles d'actualite externes.

Contexte :
- Public cible : particuliers qui souhaitent acheter, vendre ou investir a {$location['area']}.
- Objectif : publier un article de blog pedagogique, oriente local, qui resume et commente l'actualite, en citant clairement la source.
- Le ton doit etre professionnel, clair, accessible, sans langage marketing agressif.

Donnees disponibles :
- Titre de l'article source : {$titre}
- Source (site / media) : {$nomSource}
- URL de la source : {$urlSource}
- Date de publication de la source : {$dateSource}
- Resume ou extrait du flux RSS : {$resume}
- Contenu plus detaille si disponible : {$contenu}

Tache :
1. Analyser le contenu fourni (resume + contenu detaille si present).
2. Rediger un article de blog ORIGINAL en francais pour le site d'un professionnel de l'immobilier a {$location['area']}.
3. Adapter systematiquement l'angle a {$location['area']} :
   - expliquer en quoi cette actualite impacte ou peut concerner les vendeurs, acheteurs et investisseurs locaux,
   - integrer, quand c'est pertinent, des remarques types : evolution des prix locale, attractivite des quartiers, dynamique du marche local, etc.
4. Ne pas copier le texte source : reformuler, synthetiser, commenter, ajouter de la valeur (conseils concrets).
5. Structure attendue :
   - Titre SEO accrocheur (H1)
   - Introduction courte (2-3 phrases)
   - 2 a 4 sous-titres (H2) avec du texte sous chaque H2
   - Un paragraphe de "conseils pratiques" pour le lecteur local
   - Un court paragraphe de conclusion oriente appel a l'action soft (prise de contact pour estimation, conseil, etc., sans pousser).
6. Citer clairement la source a la fin, dans un encadre texte, de cette forme :
   "Source : {$nomSource}, « {$titre} », publie le {$dateSource} - disponible sur : {$urlSource}."

Contraintes :
- Longueur totale : environ 700 a 1 200 mots.
- Pas de jargon technique non explique.
- Ne pas inventer de chiffres precis qui ne sont pas donnes dans le texte source.
- Ne pas faire de promesses exagerees (pas de "garanti", "certain", etc.).

Format de reponse : JSON avec les cles suivantes :
{
  "title": "Titre SEO H1",
  "meta_title": "Meta title pour SEO (max 60 caracteres)",
  "meta_description": "Meta description SEO (max 160 caracteres)",
  "excerpt": "Resume en 2-3 phrases",
  "content_html": "Le contenu complet en HTML (h2, h3, p, ul, li, strong, blockquote)"
}
PROMPT;
    }

    /**
     * Build the Claude prompt for generating a blog article from multiple RSS articles.
     */
    public function buildMultiArticlePrompt(array $rssArticles): string
    {
        $location = $this->locationContext();
        $sourcesList = '';
        foreach ($rssArticles as $i => $a) {
            $num = $i + 1;
            $sourcesList .= "{$num}. {$a['title']} - {$a['source_name']} - {$a['link']} - {$a['pub_date']}\n   Resume : {$a['description']}\n\n";
        }

        return <<<PROMPT
Role : Tu es un expert en marche immobilier local ({$location['area']}) et en redaction web SEO. Tu aides un conseiller immobilier a publier des articles de blog originaux a partir d'articles d'actualite externes.

Contexte :
- Public cible : particuliers qui souhaitent acheter, vendre ou investir a {$location['area']}.
- Objectif : publier un article de blog pedagogique, oriente local, qui fait une synthese de plusieurs sources d'actualite.
- Le ton doit etre professionnel, clair, accessible, sans langage marketing agressif.

Liste des articles sources :
{$sourcesList}

Tache :
1. Faire une synthese unique des points communs et des differences entre ces sources.
2. Rediger un article unique qui resume la tendance globale et la met en perspective pour {$location['area']}.
3. Adapter systematiquement l'angle a {$location['area']} :
   - expliquer en quoi cette actualite impacte ou peut concerner les vendeurs, acheteurs et investisseurs locaux,
   - integrer, quand c'est pertinent, des remarques types : evolution des prix locale, attractivite des quartiers, dynamique du marche local, etc.
4. Ne pas copier les textes sources : reformuler, synthetiser, commenter, ajouter de la valeur (conseils concrets).
5. Structure attendue :
   - Titre SEO accrocheur (H1)
   - Introduction courte (2-3 phrases)
   - 2 a 4 sous-titres (H2) avec du texte sous chaque H2
   - Un paragraphe de "conseils pratiques" pour le lecteur local
   - Un court paragraphe de conclusion oriente appel a l'action soft (prise de contact pour estimation, conseil, etc., sans pousser).
6. En fin d'article, ajouter un bloc "Sources" listant tous les liens.

Contraintes :
- Longueur totale : environ 700 a 1 200 mots.
- Pas de jargon technique non explique.
- Ne pas inventer de chiffres precis qui ne sont pas donnes dans les textes sources.
- Ne pas faire de promesses exagerees (pas de "garanti", "certain", etc.).

Format de reponse : JSON avec les cles suivantes :
{
  "title": "Titre SEO H1",
  "meta_title": "Meta title pour SEO (max 60 caracteres)",
  "meta_description": "Meta description SEO (max 160 caracteres)",
  "excerpt": "Resume en 2-3 phrases",
  "content_html": "Le contenu complet en HTML (h2, h3, p, ul, li, strong, blockquote)"
}
PROMPT;
    }

    /**
     * Generate a blog article from selected RSS articles using Claude/OpenAI.
     */
    public function generateBlogArticle(array $rssArticleIds): array
    {
        $articleModel = new RssArticle();
        $rssArticles = $articleModel->findByIds($rssArticleIds);

        if (empty($rssArticles)) {
            return ['success' => false, 'error' => 'Aucun article RSS selectionne.'];
        }

        $prompt = count($rssArticles) === 1
            ? $this->buildSingleArticlePrompt($rssArticles[0])
            : $this->buildMultiArticlePrompt($rssArticles);

        $apiKey = trim((string) Config::get('anthropic.api_key', ''));

        // Try Anthropic first, fallback to OpenAI
        if ($apiKey !== '') {
            $apiResult = $this->callAnthropic($apiKey, $prompt);
        } else {
            $apiKey = (string) Config::get('openai.api_key', '');
            if ($apiKey !== '') {
                $apiResult = $this->callOpenAI($apiKey, $prompt);
            } else {
                $articleModel->logGeneration($rssArticleIds, null, $prompt, 'error', 'Aucune cle API configuree (ANTHROPIC_API_KEY ou OPENAI_API_KEY)');
                return ['success' => false, 'error' => 'Aucune cle API configuree (ANTHROPIC_API_KEY ou OPENAI_API_KEY dans .env).'];
            }
        }

        if ($apiResult['data'] === null) {
            $errorMsg = $apiResult['error'] ?? 'Erreur inconnue';
            $articleModel->logGeneration($rssArticleIds, null, $prompt, 'error', $errorMsg);
            return ['success' => false, 'error' => 'Erreur lors de l\'appel a l\'API : ' . $errorMsg];
        }

        $result = $apiResult['data'];

        // Mark RSS articles as used
        foreach ($rssArticleIds as $id) {
            $articleModel->markAsUsed((int) $id);
        }

        $articleModel->logGeneration($rssArticleIds, null, $prompt, 'success');

        return [
            'success' => true,
            'article' => $result,
            'rss_articles' => $rssArticles,
            'prompt' => $prompt,
        ];
    }

    /**
     * @return array{data: array|null, error: string|null}
     */
    private function callAnthropic(string $apiKey, string $prompt): array
    {
        $endpoint = 'https://api.anthropic.com/v1/messages';
        $model = (string) Config::get('anthropic.model', 'claude-sonnet-4-20250514');
        $payload = [
            'model' => $model,
            'max_tokens' => 4096,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        $result = $this->postJson($endpoint, $payload, [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json',
        ]);

        if ($result['error'] !== null) {
            $error = 'Anthropic API : ' . $result['error'];
            if (str_contains($result['error'], '401') || str_contains($result['error'], 'invalid x-api-key')) {
                $error .= '. Verifiez votre cle API dans Administration > API.';
            }
            return ['data' => null, 'error' => $error];
        }

        $inputTokens = (int) ($result['response']['usage']['input_tokens'] ?? 0);
        $outputTokens = (int) ($result['response']['usage']['output_tokens'] ?? 0);
        $cost = round(($inputTokens / 1000) * 0.003 + ($outputTokens / 1000) * 0.015, 6);
        AdminSmtpApiController::logAiUsage('claude', $model, $inputTokens, $outputTokens, $cost, 'article_generation');

        $text = $result['response']['content'][0]['text'] ?? '';
        $parsed = $this->extractJson($text);
        if ($parsed === null) {
            error_log('callAnthropic: failed to extract JSON from response text: ' . mb_substr($text, 0, 300));
            return ['data' => null, 'error' => 'Reponse Anthropic invalide (JSON non extractible)'];
        }

        return ['data' => $parsed, 'error' => null];
    }

    /**
     * @return array{data: array|null, error: string|null}
     */
    private function callOpenAI(string $apiKey, string $prompt): array
    {
        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $result = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un expert immobilier et redacteur web SEO specialise en marche local.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if ($result['error'] !== null) {
            return ['data' => null, 'error' => 'OpenAI API : ' . $result['error']];
        }

        $inputTokens = (int) ($result['response']['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($result['response']['usage']['completion_tokens'] ?? 0);
        $inRate = str_contains($model, '4o-mini') ? 0.00015 : 0.0025;
        $outRate = str_contains($model, '4o-mini') ? 0.0006 : 0.0100;
        $cost = round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'article_generation');

        $text = $result['response']['choices'][0]['message']['content'] ?? '';
        $parsed = $this->extractJson($text);
        if ($parsed === null) {
            error_log('callOpenAI: failed to extract JSON from response text: ' . mb_substr($text, 0, 300));
            return ['data' => null, 'error' => 'Reponse OpenAI invalide (JSON non extractible)'];
        }

        return ['data' => $parsed, 'error' => null];
    }

    private function extractJson(string $text): ?array
    {
        $text = trim($text);
        // Try direct JSON
        if (preg_match('/\{[\s\S]*"content_html"[\s\S]*\}/u', $text, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded) && isset($decoded['title'], $decoded['content_html'])) {
                return $decoded;
            }
        }
        $decoded = json_decode($text, true);
        if (is_array($decoded) && isset($decoded['title'], $decoded['content_html'])) {
            return $decoded;
        }
        return null;
    }

    private function loadXml(string $url): ?\SimpleXMLElement
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => $this->rssUserAgent(),
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string((string) $response);
        libxml_clear_errors();

        return $xml instanceof \SimpleXMLElement ? $xml : null;
    }

    private function parseItems(\SimpleXMLElement $xml): array
    {
        $items = [];

        // RSS 2.0
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $items[] = $this->parseRssItem($item);
            }
            return $items;
        }

        // Atom
        $namespaces = $xml->getNamespaces(true);
        if (isset($xml->entry) || isset($namespaces[''])) {
            foreach ($xml->entry as $entry) {
                $items[] = $this->parseAtomEntry($entry, $namespaces);
            }
            // If default namespace
            if (empty($items) && isset($namespaces[''])) {
                $xml->registerXPathNamespace('atom', $namespaces['']);
                $entries = $xml->xpath('//atom:entry');
                if (is_array($entries)) {
                    foreach ($entries as $entry) {
                        $items[] = $this->parseAtomEntry($entry, $namespaces);
                    }
                }
            }
            return $items;
        }

        // RSS 1.0 (RDF)
        if (isset($xml->item)) {
            foreach ($xml->item as $item) {
                $items[] = $this->parseRssItem($item);
            }
        }

        return $items;
    }

    private function parseRssItem(\SimpleXMLElement $item): array
    {
        $guid = (string) ($item->guid ?? $item->link ?? '');
        $pubDate = (string) ($item->pubDate ?? '');
        $dateFormatted = null;
        if ($pubDate !== '') {
            $ts = strtotime($pubDate);
            $dateFormatted = $ts !== false ? date('Y-m-d H:i:s', $ts) : null;
        }

        // Try to get image from enclosure or media:content
        $imageUrl = null;
        if (isset($item->enclosure)) {
            $type = (string) $item->enclosure['type'];
            if (str_starts_with($type, 'image/')) {
                $imageUrl = (string) $item->enclosure['url'];
            }
        }
        $namespaces = $item->getNamespaces(true);
        if ($imageUrl === null && isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content)) {
                $imageUrl = (string) $media->content['url'];
            } elseif (isset($media->thumbnail)) {
                $imageUrl = (string) $media->thumbnail['url'];
            }
        }

        // Get content:encoded if available
        $content = null;
        if (isset($namespaces['content'])) {
            $contentNs = $item->children($namespaces['content']);
            if (isset($contentNs->encoded)) {
                $content = (string) $contentNs->encoded;
            }
        }

        return [
            'guid' => $guid !== '' ? $guid : (string) $item->link,
            'title' => (string) $item->title,
            'link' => (string) $item->link,
            'description' => (string) ($item->description ?? ''),
            'content' => $content,
            'author' => (string) ($item->author ?? $item->creator ?? ''),
            'pub_date' => $dateFormatted,
            'image_url' => $imageUrl,
        ];
    }

    /**
     * @return array{city:string,area:string}
     */
    private function locationContext(): array
    {
        $branding = function_exists('getBrandingConfig') ? \getBrandingConfig() : [];
        $city = trim((string) ($branding['city_name'] ?? (string) Config::get('city.name', '')));
        if ($city === '') {
            $city = 'votre ville';
        }

        $area = trim((string) ($branding['area_label'] ?? (string) Config::get('city.region', '')));
        if ($area === '') {
            $area = $city !== 'votre ville' ? $city : 'votre secteur';
        }

        return ['city' => $city, 'area' => $area];
    }

    private function rssUserAgent(): string
    {
        $branding = function_exists('getBrandingConfig') ? \getBrandingConfig() : [];
        $siteName = trim((string) ($branding['site_name'] ?? (string) Config::get('app_name', 'Estimation-Immobilier')));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($siteName)) ?: 'estimation-immobilier';
        return rtrim($slug, '-') . '-RSS/1.0';
    }

    private function parseAtomEntry(\SimpleXMLElement $entry, array $namespaces): array
    {
        $link = '';
        if (isset($entry->link)) {
            foreach ($entry->link as $l) {
                if ((string) $l['rel'] === 'alternate' || (string) $l['rel'] === '') {
                    $link = (string) $l['href'];
                    break;
                }
            }
            if ($link === '') {
                $link = (string) $entry->link['href'];
            }
        }

        $pubDate = (string) ($entry->published ?? $entry->updated ?? '');
        $dateFormatted = null;
        if ($pubDate !== '') {
            $ts = strtotime($pubDate);
            $dateFormatted = $ts !== false ? date('Y-m-d H:i:s', $ts) : null;
        }

        $content = (string) ($entry->content ?? '');
        $summary = (string) ($entry->summary ?? '');

        return [
            'guid' => (string) ($entry->id ?? $link),
            'title' => (string) $entry->title,
            'link' => $link,
            'description' => $summary !== '' ? $summary : mb_substr(strip_tags($content), 0, 500),
            'content' => $content !== '' ? $content : null,
            'author' => (string) ($entry->author->name ?? ''),
            'pub_date' => $dateFormatted,
            'image_url' => null,
        ];
    }

    /**
     * @return array{response: array|null, error: string|null}
     */
    private function postJson(string $endpoint, array $payload, array $headers): array
    {
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_TIMEOUT => 90,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            $error = 'Erreur cURL : ' . ($curlError !== '' ? $curlError : 'inconnue');
            error_log('postJson cURL error for ' . $endpoint . ': ' . $error);
            return ['response' => null, 'error' => $error];
        }

        if ($httpCode >= 400) {
            $body = mb_substr((string) $response, 0, 500);
            $errorDetail = "HTTP {$httpCode}";
            $decoded = json_decode((string) $response, true);
            if (is_array($decoded)) {
                // Anthropic / OpenAI error format: {"error": {"message": "..."}}
                if (isset($decoded['error']['message'])) {
                    $errorDetail .= ' - ' . $decoded['error']['message'];
                }
                // Generic format: {"message": "..."}
                elseif (isset($decoded['message'])) {
                    $errorDetail .= ' - ' . $decoded['message'];
                }
            }
            error_log('postJson HTTP error for ' . $endpoint . ': ' . $errorDetail . ' | Body: ' . $body);
            return ['response' => null, 'error' => $errorDetail];
        }

        $decoded = json_decode((string) $response, true);
        if (!is_array($decoded)) {
            error_log('postJson invalid JSON from ' . $endpoint . ': ' . mb_substr((string) $response, 0, 200));
            return ['response' => null, 'error' => 'Reponse API invalide (JSON non parsable)'];
        }

        return ['response' => $decoded, 'error' => null];
    }
}
