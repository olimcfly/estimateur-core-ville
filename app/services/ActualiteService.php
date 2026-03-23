<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\AdminSmtpApiController;
use App\Core\Config;
use App\Models\ActualiteAiConfig;
use App\Models\RssArticle;

final class ActualiteService
{
    private const SEARCH_TOPICS = [
        'marché immobilier Bordeaux actualité prix',
        'immobilier Bordeaux Métropole tendances',
        'vente immobilière Gironde nouveautés',
        'prix immobilier quartiers Bordeaux évolution',
        'investissement immobilier Bordeaux CUB',
        'immobilier neuf Bordeaux programmes',
        'taux crédit immobilier impact Bordeaux',
        'urbanisme Bordeaux projets aménagement',
    ];

    // ─── RSS-based pipeline (NEW) ────────────────────────────────────────

    /**
     * Step 1: Collect RSS articles and filter them using AI config rules.
     * Returns filtered & scored articles ready for AI selection.
     */
    public function collectRssArticles(): array
    {
        $config = new ActualiteAiConfig();
        $aiConfig = $config->getAll();

        $maxAge = (int) ($aiConfig['max_article_age_days'] ?? 7);
        $zonePriority = $aiConfig['zone_priority'] ?? 'local_first';

        $articleModel = new RssArticle();
        $candidates = $articleModel->findForActualite($maxAge, $zonePriority, 50);

        if (empty($candidates)) {
            return ['articles' => [], 'filtered_count' => 0, 'total_count' => 0];
        }

        // Apply keyword filtering
        $excludeKeywords = $config->getExcludeKeywords();
        $requireKeywords = $config->getRequireKeywords();
        $excludeAgencies = ($aiConfig['exclude_agencies'] ?? '1') === '1';

        $filtered = [];
        foreach ($candidates as $article) {
            $text = mb_strtolower(($article['title'] ?? '') . ' ' . ($article['description'] ?? ''));

            // Exclude agency/promotional content
            if ($excludeAgencies && $this->isAgencyContent($text)) {
                continue;
            }

            // Exclude by keywords
            $excluded = false;
            foreach ($excludeKeywords as $kw) {
                if ($kw !== '' && mb_strpos($text, mb_strtolower($kw)) !== false) {
                    $excluded = true;
                    break;
                }
            }
            if ($excluded) {
                continue;
            }

            // Score relevance based on required keywords and zone
            $score = 0;
            foreach ($requireKeywords as $kw) {
                if ($kw !== '' && mb_strpos($text, mb_strtolower($kw)) !== false) {
                    $score += 2;
                }
            }

            // Bonus for local sources
            if (($article['source_zone'] ?? '') === 'Bordeaux/Nouvelle-Aquitaine') {
                $score += 3;
            }

            // Bonus for journalistic sources (not portals)
            if (in_array($article['source_category'] ?? '', ['presse-locale', 'medias-economiques', 'institutionnel'], true)) {
                $score += 1;
            }

            $article['relevance_score'] = $score;
            $filtered[] = $article;
        }

        // Sort by relevance score descending, then by date
        usort($filtered, static function (array $a, array $b): int {
            $scoreDiff = ($b['relevance_score'] ?? 0) - ($a['relevance_score'] ?? 0);
            if ($scoreDiff !== 0) {
                return $scoreDiff;
            }
            return strcmp((string) ($b['pub_date'] ?? ''), (string) ($a['pub_date'] ?? ''));
        });

        // Apply minimum relevance score
        $minScore = (int) ($aiConfig['min_relevance_score'] ?? 0);
        if ($minScore > 0) {
            $filtered = array_filter($filtered, static fn(array $a) => ($a['relevance_score'] ?? 0) >= $minScore);
            $filtered = array_values($filtered);
        }

        return [
            'articles' => array_slice($filtered, 0, 15),
            'filtered_count' => count($filtered),
            'total_count' => count($candidates),
        ];
    }

    /**
     * Step 2: Use AI to select the best article(s) from RSS candidates
     * and generate an actualité article.
     */
    public function generateFromRssArticles(array $rssArticles): array
    {
        if (empty($rssArticles)) {
            return ['success' => false, 'error' => 'Aucun article RSS disponible.'];
        }

        $config = new ActualiteAiConfig();
        $aiConfig = $config->getAll();

        $prompt = $this->buildActualitePrompt($rssArticles, $aiConfig);

        $preferredModel = $aiConfig['generation_model'] ?? 'anthropic';
        $apiResult = $this->callGenerationApi($preferredModel, $prompt);

        if ($apiResult['data'] === null) {
            return ['success' => false, 'error' => $apiResult['error'] ?? 'Erreur API'];
        }

        $result = $apiResult['data'];
        $usedIds = array_map(static fn(array $a) => (int) $a['id'], $rssArticles);

        return [
            'success' => true,
            'article' => $result,
            'rss_article_ids' => $usedIds,
            'rss_articles' => $rssArticles,
            'prompt' => $prompt,
        ];
    }

    /**
     * Full RSS-based pipeline: collect → filter → AI select → generate → image.
     */
    public function runRssPipeline(): array
    {
        // Step 1: Collect and filter RSS articles
        $collected = $this->collectRssArticles();
        $articles = $collected['articles'];

        if (empty($articles)) {
            return [
                'success' => false,
                'error' => 'Aucun article RSS pertinent trouvé. Vérifiez vos flux RSS et la configuration IA.',
                'total_count' => $collected['total_count'],
                'filtered_count' => 0,
            ];
        }

        // Take top 5 for generation
        $topArticles = array_slice($articles, 0, 5);

        // Step 2: Generate article
        $result = $this->generateFromRssArticles($topArticles);

        if (!$result['success']) {
            return $result;
        }

        // Step 3: Generate image
        $imageUrl = null;
        $imagePrompt = $result['article']['image_prompt'] ?? '';
        if ($imagePrompt !== '') {
            $imageUrl = $this->generateImage($imagePrompt);
        }

        // Build source citation
        $sourcesCitation = '';
        foreach ($topArticles as $rssArt) {
            $sourcesCitation .= '<li><a href="' . htmlspecialchars($rssArt['link'] ?? '', ENT_QUOTES, 'UTF-8')
                . '" target="_blank" rel="noopener">'
                . htmlspecialchars($rssArt['source_name'] ?? '', ENT_QUOTES, 'UTF-8')
                . ' : ' . htmlspecialchars($rssArt['title'] ?? '', ENT_QUOTES, 'UTF-8')
                . '</a></li>';
        }

        $rssArticleIds = $result['rss_article_ids'] ?? [];

        return [
            'success' => true,
            'query' => 'rss-pipeline',
            'ideas_count' => count($topArticles),
            'source_results' => json_encode(array_map(static fn(array $a) => [
                'id' => $a['id'],
                'title' => $a['title'],
                'source' => $a['source_name'] ?? '',
                'zone' => $a['source_zone'] ?? '',
                'score' => $a['relevance_score'] ?? 0,
            ], $topArticles), JSON_UNESCAPED_UNICODE),
            'rss_article_ids' => $rssArticleIds,
            'article' => [
                'title' => $result['article']['title'] ?? '',
                'meta_title' => $result['article']['meta_title'] ?? '',
                'meta_description' => $result['article']['meta_description'] ?? '',
                'excerpt' => $result['article']['excerpt'] ?? '',
                'content' => ($result['article']['content_html'] ?? $result['article']['content'] ?? '')
                    . "\n<div class=\"sources-block\"><h3>Sources</h3><ul>" . $sourcesCitation . "</ul></div>",
                'image_url' => $imageUrl,
                'image_prompt' => $imagePrompt,
                'source_query' => 'rss-pipeline',
            ],
        ];
    }

    // ─── Perplexity-based pipeline (existing) ────────────────────────────

    /**
     * Search Perplexity for real estate news around Bordeaux.
     */
    public function searchNews(?string $customQuery = null): array
    {
        $query = $customQuery ?? self::SEARCH_TOPICS[array_rand(self::SEARCH_TOPICS)];

        $apiKey = (string) Config::get('perplexity.api_key', '');
        if ($apiKey === '') {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        $prompt = sprintf(
            "Recherche les actualités immobilières récentes à Bordeaux et ses alentours (Gironde, Nouvelle-Aquitaine) sur le thème : \"%s\".\n"
            . "Retourne exactement 5 idées d'articles sous forme JSON avec les clés : title, summary, angle (l'angle éditorial unique).\n"
            . "Concentre-toi sur les données les plus récentes (dernière semaine/mois).\n"
            . "Réponds UNIQUEMENT en JSON valide : {\"articles\": [...]}",
            $query
        );

        $endpoint = (string) Config::get('perplexity.endpoint', 'https://api.perplexity.ai/chat/completions');
        $model = (string) Config::get('perplexity.model', 'sonar-pro');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un expert en veille immobilière sur Bordeaux et sa métropole. Tu fournis des actualités factuelles et récentes.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $inRate = str_contains($model, 'pro') ? 0.003 : 0.001;
        $outRate = str_contains($model, 'pro') ? 0.015 : 0.001;
        $cost = round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
        AdminSmtpApiController::logAiUsage('perplexity', $model, $inputTokens, $outputTokens, $cost, 'market_research');

        $content = $response['choices'][0]['message']['content'] ?? '';
        $content = trim((string) $content);

        if (preg_match('/\{[\s\S]*"articles"[\s\S]*\}/u', $content, $matches)) {
            $content = $matches[0];
        }

        $decoded = json_decode($content, true);
        $articles = $decoded['articles'] ?? [];

        if (empty($articles)) {
            return [
                'query' => $query,
                'results' => $this->fallbackNewsResults($query),
                'source' => 'fallback',
            ];
        }

        return [
            'query' => $query,
            'results' => $articles,
            'source' => 'perplexity',
        ];
    }

    /**
     * Select the best article idea and generate a full article using OpenAI.
     */
    public function generateArticleFromIdeas(array $ideas, string $query): array
    {
        $apiKey = (string) Config::get('openai.api_key', '');
        if ($apiKey === '' || empty($ideas)) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Bordeaux']);
        }

        $ideasText = '';
        foreach ($ideas as $i => $idea) {
            $ideasText .= ($i + 1) . ". Titre: " . ($idea['title'] ?? 'Sans titre')
                . " | Résumé: " . ($idea['summary'] ?? '')
                . " | Angle: " . ($idea['angle'] ?? '') . "\n";
        }

        $prompt = "Tu es un rédacteur expert en immobilier à Bordeaux. Voici 5 idées d'articles d'actualité immobilière :\n\n"
            . $ideasText . "\n"
            . "1. Choisis la MEILLEURE idée (la plus intéressante, actuelle et utile pour des propriétaires/vendeurs bordelais).\n"
            . "2. Rédige un article complet en HTML (balises h2, h3, p, ul, li, strong) de 800-1200 mots.\n"
            . "3. L'article doit être factuel, informatif, avec des données chiffrées quand possible.\n"
            . "4. Inclus un CTA vers l'estimation immobilière à la fin.\n\n"
            . "Réponds en JSON avec les clés : title, meta_title, meta_description, excerpt (2 phrases), content_html, image_prompt (prompt pour générer une image illustrative en anglais).";

        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un journaliste immobilier spécialisé sur Bordeaux et la Gironde. Tu rédiges des articles professionnels et engageants.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Bordeaux']);
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'article_generation');

        $content = $response['choices'][0]['message']['content'] ?? '';
        $decoded = json_decode((string) $content, true);

        if (!is_array($decoded) || !isset($decoded['title'], $decoded['content_html'])) {
            return $this->fallbackArticle($ideas[0] ?? ['title' => 'Actualité immobilière Bordeaux']);
        }

        return [
            'title' => (string) $decoded['title'],
            'meta_title' => (string) ($decoded['meta_title'] ?? $decoded['title']),
            'meta_description' => (string) ($decoded['meta_description'] ?? ''),
            'excerpt' => (string) ($decoded['excerpt'] ?? ''),
            'content' => (string) $decoded['content_html'],
            'image_prompt' => (string) ($decoded['image_prompt'] ?? ''),
        ];
    }

    /**
     * Generate an image for the article using OpenAI.
     */
    public function generateImage(string $imagePrompt): ?string
    {
        if (trim($imagePrompt) === '') {
            $imagePrompt = 'Professional real estate photography of beautiful Bordeaux architecture, stone buildings, sunny day, editorial style';
        }

        $imageService = new ImageGeneratorService();
        $result = $imageService->generate($imagePrompt, '1536x1024', 'medium');

        if (($result['success'] ?? false) === true) {
            return $result['url'] ?? null;
        }

        return null;
    }

    /**
     * Full automated pipeline (Perplexity): search → select → write → image → save.
     */
    public function runAutomatedPipeline(?string $customQuery = null): array
    {
        $searchResults = $this->searchNews($customQuery);
        $ideas = $searchResults['results'];
        $query = $searchResults['query'];

        if (empty($ideas)) {
            return ['success' => false, 'error' => 'Aucun résultat trouvé.', 'query' => $query];
        }

        $article = $this->generateArticleFromIdeas($ideas, $query);

        $imageUrl = null;
        $imagePrompt = $article['image_prompt'] ?? '';
        if ($imagePrompt !== '') {
            $imageUrl = $this->generateImage($imagePrompt);
        }

        return [
            'success' => true,
            'query' => $query,
            'ideas_count' => count($ideas),
            'source_results' => json_encode($ideas, JSON_UNESCAPED_UNICODE),
            'article' => [
                'title' => $article['title'],
                'meta_title' => $article['meta_title'],
                'meta_description' => $article['meta_description'],
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
                'image_url' => $imageUrl,
                'image_prompt' => $imagePrompt,
                'source_query' => $query,
            ],
        ];
    }

    // ─── Private helpers ─────────────────────────────────────────────────

    /**
     * Build the prompt for generating an actualité from RSS articles.
     * This is NOT a blog article — it's a news/actualité piece.
     */
    private function buildActualitePrompt(array $rssArticles, array $aiConfig): string
    {
        $tone = $aiConfig['article_tone'] ?? 'journalistique';
        $length = $aiConfig['article_length'] ?? '800-1200';
        $localAngle = $aiConfig['local_angle'] ?? "Adapter l'angle à Bordeaux et sa métropole.";
        $ctaStyle = $aiConfig['cta_style'] ?? 'soft';
        $seoKeywords = $aiConfig['seo_focus'] ?? 'estimation immobilière bordeaux';

        $sourcesList = '';
        foreach ($rssArticles as $i => $a) {
            $num = $i + 1;
            $zone = $a['source_zone'] ?? 'national';
            $sourcesList .= "{$num}. [{$zone}] {$a['title']} - {$a['source_name']} ({$a['pub_date']})\n"
                . "   Lien: {$a['link']}\n"
                . "   Résumé: " . mb_substr(strip_tags((string) ($a['description'] ?? '')), 0, 300) . "\n\n";
        }

        $ctaInstruction = match ($ctaStyle) {
            'none' => "Pas d'appel à l'action.",
            'direct' => "Termine par un appel à l'action direct vers l'estimation immobilière gratuite.",
            default => "Termine par un appel à l'action soft et naturel (suggestion de faire estimer son bien, sans pression).",
        };

        return <<<PROMPT
Rôle : Tu es un journaliste spécialisé en immobilier local à Bordeaux. Tu rédiges des ACTUALITÉS (pas des articles de blog) pour un site d'estimation immobilière.

IMPORTANT — DIFFÉRENCE ACTUALITÉ vs BLOG :
- Une ACTUALITÉ est factuelle, informative, basée sur des faits récents et des sources vérifiables.
- Un article de BLOG est plus éditorial, pédagogique, orienté conseil.
- Ici tu rédiges une ACTUALITÉ : ton {$tone}, factuel, sourcé.

Contexte :
- Public : propriétaires et vendeurs de biens immobiliers à Bordeaux et métropole.
- Objectif : informer des dernières actualités locales du marché immobilier.
- Mots-clés SEO à intégrer naturellement : {$seoKeywords}
- Angle local : {$localAngle}

Articles RSS sources (classés par pertinence locale) :
{$sourcesList}

Tâche :
1. Analyse les articles sources ci-dessus.
2. Sélectionne le sujet le plus pertinent et ACTUEL pour les propriétaires bordelais.
3. Privilégie les sujets LOCAUX (Bordeaux, Gironde, Nouvelle-Aquitaine) sur les sujets nationaux.
4. Rédige une actualité ORIGINALE de {$length} mots en HTML.
5. Structure :
   - Titre accrocheur (H1) orienté actualité locale
   - Chapô / introduction factuelle (2-3 phrases)
   - 2-4 sous-sections (H2) avec analyse et faits
   - Mention des impacts concrets pour les propriétaires bordelais
   - {$ctaInstruction}
6. NE COPIE PAS les sources : reformule, synthétise, ajoute de la valeur.
7. Cite les sources dans le texte quand pertinent (ex: "selon Sud Ouest", "d'après les notaires").

Contraintes :
- Ton : {$tone} — pas de marketing, pas de jargon non expliqué.
- Ne pas inventer de chiffres.
- Ne pas faire de promesses ("garanti", "certain").
- Les sources seront ajoutées automatiquement en bas de l'article, ne les liste pas.

Format de réponse JSON :
{
  "title": "Titre SEO H1 de l'actualité",
  "meta_title": "Meta title SEO (max 60 caractères)",
  "meta_description": "Meta description SEO (max 155 caractères)",
  "excerpt": "Résumé en 2-3 phrases pour la liste d'actualités",
  "content_html": "Le contenu complet en HTML (h2, h3, p, ul, li, strong, blockquote)",
  "image_prompt": "Prompt en anglais pour générer une image éditoriale illustrative",
  "selected_sources": "Indices des sources utilisées (ex: 1,3)"
}
PROMPT;
    }

    /**
     * Detect agency/promotional content by checking common patterns.
     */
    private function isAgencyContent(string $text): bool
    {
        $patterns = [
            'à vendre',
            'prix en baisse',
            'exclusivité',
            'coup de coeur',
            'coup de cœur',
            'visite virtuelle',
            'honoraires',
            'mandat exclusif',
            'frais d\'agence',
            'frais de notaire offerts',
            'offre exceptionnelle',
            'dernières opportunités',
            'investissez maintenant',
            'rendement locatif garanti',
            'livraison immédiate',
            'pinel',
            'défiscalisation',
        ];

        foreach ($patterns as $pattern) {
            if (mb_strpos($text, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Call the generation API (Anthropic preferred, fallback OpenAI).
     * @return array{data: array|null, error: string|null}
     */
    private function callGenerationApi(string $preferred, string $prompt): array
    {
        if ($preferred === 'anthropic') {
            $apiKey = trim((string) Config::get('anthropic.api_key', ''));
            if ($apiKey !== '') {
                return $this->callAnthropic($apiKey, $prompt);
            }
        }

        $apiKey = trim((string) Config::get('openai.api_key', ''));
        if ($apiKey !== '') {
            return $this->callOpenAI($apiKey, $prompt);
        }

        // Fallback to Anthropic if OpenAI not configured
        $apiKey = trim((string) Config::get('anthropic.api_key', ''));
        if ($apiKey !== '') {
            return $this->callAnthropic($apiKey, $prompt);
        }

        return ['data' => null, 'error' => 'Aucune clé API configurée (ANTHROPIC_API_KEY ou OPENAI_API_KEY).'];
    }

    /**
     * @return array{data: array|null, error: string|null}
     */
    private function callAnthropic(string $apiKey, string $prompt): array
    {
        $endpoint = 'https://api.anthropic.com/v1/messages';
        $model = (string) Config::get('anthropic.model', 'claude-sonnet-4-20250514');

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'max_tokens' => 4096,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return ['data' => null, 'error' => 'Erreur Anthropic API'];
        }

        $inputTokens = (int) ($response['usage']['input_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['output_tokens'] ?? 0);
        $cost = round(($inputTokens / 1000) * 0.003 + ($outputTokens / 1000) * 0.015, 6);
        AdminSmtpApiController::logAiUsage('claude', $model, $inputTokens, $outputTokens, $cost, 'actualite_generation');

        $text = $response['content'][0]['text'] ?? '';
        $parsed = $this->extractJson($text);
        if ($parsed === null) {
            error_log('ActualiteService::callAnthropic: JSON extraction failed: ' . mb_substr($text, 0, 300));
            return ['data' => null, 'error' => 'Réponse Anthropic invalide (JSON non extractible)'];
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

        $response = $this->postJson($endpoint, [
            'model' => $model,
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un journaliste immobilier spécialisé sur Bordeaux. Tu rédiges des actualités factuelles et sourcées.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ], [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);

        if (!is_array($response)) {
            return ['data' => null, 'error' => 'Erreur OpenAI API'];
        }

        $inputTokens = (int) ($response['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($response['usage']['completion_tokens'] ?? 0);
        $cost = $this->estimateCost($model, $inputTokens, $outputTokens);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'actualite_generation');

        $text = $response['choices'][0]['message']['content'] ?? '';
        $parsed = $this->extractJson($text);
        if ($parsed === null) {
            error_log('ActualiteService::callOpenAI: JSON extraction failed: ' . mb_substr($text, 0, 300));
            return ['data' => null, 'error' => 'Réponse OpenAI invalide (JSON non extractible)'];
        }

        return ['data' => $parsed, 'error' => null];
    }

    private function extractJson(string $text): ?array
    {
        $text = trim($text);
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

    private function estimateCost(string $model, int $inputTokens, int $outputTokens): float
    {
        $rates = [
            'gpt-4o' => [0.0025, 0.0100],
            'gpt-4o-mini' => [0.00015, 0.0006],
        ];
        [$inRate, $outRate] = $rates[$model] ?? [0.001, 0.002];
        return round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
    }

    private function fallbackNewsResults(string $query): array
    {
        return [
            ['title' => 'Évolution des prix immobiliers à Bordeaux ce mois', 'summary' => 'Les prix au m² continuent leur ajustement dans les quartiers centraux.', 'angle' => 'Analyse quartier par quartier'],
            ['title' => 'Nouveaux projets urbains en métropole bordelaise', 'summary' => 'Plusieurs projets d\'aménagement transforment le paysage immobilier.', 'angle' => 'Impact sur les valeurs immobilières'],
            ['title' => 'Taux de crédit : impact sur le marché bordelais', 'summary' => 'L\'évolution des taux influence les décisions d\'achat et de vente.', 'angle' => 'Opportunités pour vendeurs'],
            ['title' => 'Le marché locatif étudiant à Bordeaux', 'summary' => 'La demande locative étudiante reste forte dans certains quartiers.', 'angle' => 'Investissement locatif'],
            ['title' => 'Rénovation énergétique : les aides disponibles en Gironde', 'summary' => 'Les nouvelles réglementations impactent la valeur des biens.', 'angle' => 'Valorisation du patrimoine'],
        ];
    }

    private function fallbackArticle(array $idea): array
    {
        $title = $idea['title'] ?? 'Actualité immobilière Bordeaux';
        $summary = $idea['summary'] ?? 'Les dernières nouvelles du marché immobilier bordelais.';

        return [
            'title' => $title,
            'meta_title' => $title . ' | Actualités Immobilier Bordeaux',
            'meta_description' => $summary,
            'excerpt' => $summary,
            'content' => '<h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>'
                . '<p>' . htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<h2>Ce que cela signifie pour vous</h2>'
                . '<p>Le marché immobilier bordelais continue d\'évoluer. Que vous soyez propriétaire souhaitant vendre ou simplement curieux de la valeur de votre bien, il est important de rester informé des dernières tendances.</p>'
                . '<h2>Les quartiers à surveiller</h2>'
                . '<ul><li><strong>Chartrons</strong> : un quartier en constante valorisation</li>'
                . '<li><strong>Bastide</strong> : le renouveau de la rive droite</li>'
                . '<li><strong>Saint-Michel</strong> : authenticité et dynamisme</li>'
                . '<li><strong>Caudéran</strong> : le calme résidentiel prisé des familles</li></ul>'
                . '<h2>Estimez votre bien gratuitement</h2>'
                . '<p>Vous souhaitez connaître la valeur actuelle de votre bien immobilier à Bordeaux ? '
                . '<strong><a href="/estimation">Lancez votre estimation gratuite</a></strong> et obtenez un résultat en moins de 2 minutes.</p>',
            'image_prompt' => 'Professional editorial photo of Bordeaux city skyline with stone architecture and Garonne river, warm lighting, real estate magazine style',
        ];
    }

    private function postJson(string $endpoint, array $payload, array $headers): ?array
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
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            $body = is_string($response) ? mb_substr($response, 0, 500) : '';
            error_log("ActualiteService::postJson error {$httpCode} for {$endpoint}: {$body}");
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }
}
