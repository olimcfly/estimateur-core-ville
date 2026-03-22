<?php

declare(strict_types=1);

namespace App\Services;

/**
 * SEO Analyzer Service - Rank Math-style SEO analysis
 * Calculates technical SEO score, semantic score, SERP preview, and golden ratio.
 */
final class SeoAnalyzerService
{
    // Golden ratio for keyword density: ideal is ~1.618% (range 1.0% - 2.5%)
    private const GOLDEN_RATIO = 1.618;
    private const GOLDEN_RATIO_MIN = 1.0;
    private const GOLDEN_RATIO_MAX = 2.5;

    // Scoring weights
    private const TECHNICAL_WEIGHTS = [
        'meta_title_length' => 8,
        'meta_title_keyword' => 10,
        'meta_description_length' => 8,
        'meta_description_keyword' => 8,
        'url_slug_keyword' => 7,
        'url_slug_length' => 5,
        'h1_present' => 8,
        'h1_keyword' => 8,
        'h2_structure' => 6,
        'h3_structure' => 4,
        'internal_links' => 7,
        'external_links' => 5,
        'images_present' => 5,
        'images_alt_tags' => 6,
        'content_length' => 8,
        'faq_section' => 5,
    ];

    private const SEMANTIC_WEIGHTS = [
        'keyword_density_golden' => 15,
        'keyword_in_intro' => 10,
        'keyword_in_conclusion' => 8,
        'keyword_in_headings' => 10,
        'secondary_keywords' => 12,
        'semantic_richness' => 10,
        'heading_distribution' => 8,
        'paragraph_length' => 7,
        'sentence_variety' => 5,
        'readability' => 8,
        'content_depth' => 7,
    ];

    /**
     * Full SEO analysis of an article.
     */
    public function analyze(array $article): array
    {
        $content = (string) ($article['content'] ?? '');
        $focusKeyword = mb_strtolower(trim((string) ($article['focus_keyword'] ?? '')));
        $metaTitle = (string) ($article['meta_title'] ?? '');
        $metaDescription = (string) ($article['meta_description'] ?? '');
        $slug = (string) ($article['slug'] ?? '');
        $h1 = (string) ($article['h1_tag'] ?? $article['title'] ?? '');
        $secondaryKeywords = $this->parseSecondaryKeywords((string) ($article['secondary_keywords'] ?? ''));

        $plainText = $this->stripHtml($content);
        $wordCount = $this->countWords($plainText);
        $keywordCount = $this->countKeywordOccurrences($plainText, $focusKeyword);
        $keywordDensity = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;

        $technicalChecks = $this->technicalChecks($content, $focusKeyword, $metaTitle, $metaDescription, $slug, $h1, $wordCount);
        $semanticChecks = $this->semanticChecks($content, $plainText, $focusKeyword, $secondaryKeywords, $keywordDensity, $wordCount);

        $technicalScore = $this->calculateScore($technicalChecks, self::TECHNICAL_WEIGHTS);
        $semanticScore = $this->calculateScore($semanticChecks, self::SEMANTIC_WEIGHTS);

        $serpPreview = $this->generateSerpPreview($metaTitle, $metaDescription, $slug, $article);
        $goldenRatio = $this->analyzeGoldenRatio($keywordDensity, $keywordCount, $wordCount, $focusKeyword);

        $contentStats = $this->contentStatistics($content, $plainText, $wordCount);

        return [
            'seo_score' => $technicalScore,
            'semantic_score' => $semanticScore,
            'technical_checks' => $technicalChecks,
            'semantic_checks' => $semanticChecks,
            'serp_preview' => $serpPreview,
            'golden_ratio' => $goldenRatio,
            'keyword_density' => round($keywordDensity, 2),
            'keyword_count' => $keywordCount,
            'word_count' => $wordCount,
            'content_stats' => $contentStats,
            'recommendations' => $this->generateRecommendations($technicalChecks, $semanticChecks, $goldenRatio),
        ];
    }

    /**
     * Technical SEO checks.
     */
    private function technicalChecks(string $content, string $keyword, string $metaTitle, string $metaDescription, string $slug, string $h1, int $wordCount): array
    {
        $checks = [];
        $lowerContent = mb_strtolower($content);

        // Meta title
        $metaTitleLen = mb_strlen($metaTitle);
        $checks['meta_title_length'] = [
            'pass' => $metaTitleLen >= 50 && $metaTitleLen <= 60,
            'value' => $metaTitleLen,
            'target' => '50-60 caractères',
            'label' => 'Longueur du titre SEO',
            'detail' => $metaTitleLen < 50 ? 'Trop court (' . $metaTitleLen . ' car.)' : ($metaTitleLen > 60 ? 'Trop long (' . $metaTitleLen . ' car.)' : 'Optimal (' . $metaTitleLen . ' car.)'),
        ];

        $checks['meta_title_keyword'] = [
            'pass' => $keyword !== '' && mb_strpos(mb_strtolower($metaTitle), $keyword) !== false,
            'label' => 'Mot-clé dans le titre SEO',
            'detail' => $keyword !== '' ? ($checks['meta_title_keyword']['pass'] ?? false ? 'Présent' : 'Absent') : 'Aucun mot-clé défini',
        ];
        $checks['meta_title_keyword']['pass'] = $keyword !== '' && mb_strpos(mb_strtolower($metaTitle), $keyword) !== false;
        $checks['meta_title_keyword']['detail'] = $checks['meta_title_keyword']['pass'] ? 'Présent' : ($keyword === '' ? 'Aucun mot-clé défini' : 'Absent');

        // Meta description
        $metaDescLen = mb_strlen($metaDescription);
        $checks['meta_description_length'] = [
            'pass' => $metaDescLen >= 150 && $metaDescLen <= 160,
            'value' => $metaDescLen,
            'target' => '150-160 caractères',
            'label' => 'Longueur de la meta description',
            'detail' => $metaDescLen < 150 ? 'Trop courte (' . $metaDescLen . ' car.)' : ($metaDescLen > 160 ? 'Trop longue (' . $metaDescLen . ' car.)' : 'Optimale (' . $metaDescLen . ' car.)'),
        ];

        $checks['meta_description_keyword'] = [
            'pass' => $keyword !== '' && mb_strpos(mb_strtolower($metaDescription), $keyword) !== false,
            'label' => 'Mot-clé dans la meta description',
            'detail' => $keyword !== '' && mb_strpos(mb_strtolower($metaDescription), $keyword) !== false ? 'Présent' : ($keyword === '' ? 'Aucun mot-clé défini' : 'Absent'),
        ];

        // URL Slug
        $slugWords = explode('-', $slug);
        $checks['url_slug_keyword'] = [
            'pass' => $keyword !== '' && mb_strpos(mb_strtolower($slug), str_replace(' ', '-', $keyword)) !== false,
            'label' => 'Mot-clé dans l\'URL',
            'detail' => $keyword !== '' && mb_strpos(mb_strtolower($slug), str_replace(' ', '-', $keyword)) !== false ? 'Présent' : 'Absent',
        ];

        $checks['url_slug_length'] = [
            'pass' => count($slugWords) >= 3 && count($slugWords) <= 7,
            'value' => count($slugWords),
            'target' => '3-7 mots',
            'label' => 'Longueur de l\'URL',
            'detail' => count($slugWords) . ' mots',
        ];

        // H1
        $checks['h1_present'] = [
            'pass' => $h1 !== '',
            'label' => 'Balise H1 présente',
            'detail' => $h1 !== '' ? 'Présente' : 'Absente',
        ];

        $checks['h1_keyword'] = [
            'pass' => $keyword !== '' && $h1 !== '' && mb_strpos(mb_strtolower($h1), $keyword) !== false,
            'label' => 'Mot-clé dans le H1',
            'detail' => ($keyword !== '' && $h1 !== '' && mb_strpos(mb_strtolower($h1), $keyword) !== false) ? 'Présent' : 'Absent',
        ];

        // Heading structure
        preg_match_all('/<h2[^>]*>/i', $content, $h2Matches);
        $h2Count = count($h2Matches[0]);
        $checks['h2_structure'] = [
            'pass' => $h2Count >= 3 && $h2Count <= 8,
            'value' => $h2Count,
            'target' => '3-8 sections H2',
            'label' => 'Structure H2',
            'detail' => $h2Count . ' sections H2',
        ];

        preg_match_all('/<h3[^>]*>/i', $content, $h3Matches);
        $h3Count = count($h3Matches[0]);
        $checks['h3_structure'] = [
            'pass' => $h3Count >= 2,
            'value' => $h3Count,
            'label' => 'Sous-sections H3',
            'detail' => $h3Count . ' sous-sections H3',
        ];

        // Links
        preg_match_all('/<a\s[^>]*href\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $linkMatches);
        $internalLinks = 0;
        $externalLinks = 0;
        foreach ($linkMatches[1] as $href) {
            if (preg_match('/^https?:\/\//i', $href) && !preg_match('/localhost|127\.0\.0\.1/i', $href)) {
                $externalLinks++;
            } else {
                $internalLinks++;
            }
        }

        $checks['internal_links'] = [
            'pass' => $internalLinks >= 3,
            'value' => $internalLinks,
            'target' => '3+ liens internes',
            'label' => 'Liens internes',
            'detail' => $internalLinks . ' liens internes',
        ];

        $checks['external_links'] = [
            'pass' => $externalLinks >= 2,
            'value' => $externalLinks,
            'target' => '2+ liens externes',
            'label' => 'Liens externes',
            'detail' => $externalLinks . ' liens externes',
        ];

        // Images
        preg_match_all('/<img\s[^>]*>/i', $content, $imgMatches);
        $totalImages = count($imgMatches[0]);
        $imagesWithAlt = 0;
        foreach ($imgMatches[0] as $imgTag) {
            if (preg_match('/alt\s*=\s*["\'][^"\']+["\']/i', $imgTag)) {
                $imagesWithAlt++;
            }
        }

        $checks['images_present'] = [
            'pass' => $totalImages >= 3,
            'value' => $totalImages,
            'target' => '3+ images',
            'label' => 'Images dans le contenu',
            'detail' => $totalImages . ' images',
        ];

        $checks['images_alt_tags'] = [
            'pass' => $totalImages > 0 && $imagesWithAlt === $totalImages,
            'value' => $imagesWithAlt . '/' . $totalImages,
            'label' => 'Balises ALT des images',
            'detail' => $totalImages > 0 ? $imagesWithAlt . '/' . $totalImages . ' avec ALT' : 'Aucune image',
        ];

        // Content length
        $checks['content_length'] = [
            'pass' => $wordCount >= 1500,
            'value' => $wordCount,
            'target' => '1500+ mots',
            'label' => 'Longueur du contenu',
            'detail' => $wordCount . ' mots' . ($wordCount < 1500 ? ' (min. 1500 recommandé)' : ''),
        ];

        // FAQ section
        $hasFaq = preg_match('/<(h2|h3)[^>]*>.*?(FAQ|foire aux questions|questions fréquentes)/i', $content) === 1;
        $checks['faq_section'] = [
            'pass' => $hasFaq,
            'label' => 'Section FAQ',
            'detail' => $hasFaq ? 'Présente' : 'Absente',
        ];

        return $checks;
    }

    /**
     * Semantic SEO checks.
     */
    private function semanticChecks(string $content, string $plainText, string $keyword, array $secondaryKeywords, float $keywordDensity, int $wordCount): array
    {
        $checks = [];
        $lowerText = mb_strtolower($plainText);

        // Golden ratio keyword density
        $goldenDiff = abs($keywordDensity - self::GOLDEN_RATIO);
        $checks['keyword_density_golden'] = [
            'pass' => $keywordDensity >= self::GOLDEN_RATIO_MIN && $keywordDensity <= self::GOLDEN_RATIO_MAX,
            'value' => round($keywordDensity, 2) . '%',
            'target' => self::GOLDEN_RATIO_MIN . '%-' . self::GOLDEN_RATIO_MAX . '% (idéal: ' . self::GOLDEN_RATIO . '%)',
            'label' => 'Densité mot-clé (Golden Ratio)',
            'detail' => round($keywordDensity, 2) . '% (cible: ' . self::GOLDEN_RATIO . '%)',
        ];

        // Keyword in introduction (first 300 words)
        $words = preg_split('/\s+/', $lowerText);
        $intro = implode(' ', array_slice($words ?: [], 0, 300));
        $checks['keyword_in_intro'] = [
            'pass' => $keyword !== '' && mb_strpos($intro, $keyword) !== false,
            'label' => 'Mot-clé dans l\'introduction',
            'detail' => ($keyword !== '' && mb_strpos($intro, $keyword) !== false) ? 'Présent dans les 300 premiers mots' : 'Absent de l\'introduction',
        ];

        // Keyword in conclusion (last 200 words)
        $conclusion = implode(' ', array_slice($words ?: [], -200));
        $checks['keyword_in_conclusion'] = [
            'pass' => $keyword !== '' && mb_strpos($conclusion, $keyword) !== false,
            'label' => 'Mot-clé dans la conclusion',
            'detail' => ($keyword !== '' && mb_strpos($conclusion, $keyword) !== false) ? 'Présent' : 'Absent',
        ];

        // Keyword in headings
        preg_match_all('/<h[2-4][^>]*>(.*?)<\/h[2-4]>/is', $content, $headingMatches);
        $headingsWithKeyword = 0;
        $totalHeadings = count($headingMatches[1]);
        foreach ($headingMatches[1] as $heading) {
            if ($keyword !== '' && mb_strpos(mb_strtolower(strip_tags($heading)), $keyword) !== false) {
                $headingsWithKeyword++;
            }
        }
        $checks['keyword_in_headings'] = [
            'pass' => $headingsWithKeyword >= 2,
            'value' => $headingsWithKeyword . '/' . $totalHeadings,
            'label' => 'Mot-clé dans les sous-titres',
            'detail' => $headingsWithKeyword . ' titres contiennent le mot-clé sur ' . $totalHeadings,
        ];

        // Secondary keywords presence
        $secondaryFound = 0;
        foreach ($secondaryKeywords as $sk) {
            if (mb_strpos($lowerText, mb_strtolower($sk)) !== false) {
                $secondaryFound++;
            }
        }
        $totalSecondary = count($secondaryKeywords);
        $checks['secondary_keywords'] = [
            'pass' => $totalSecondary > 0 && $secondaryFound >= (int) ($totalSecondary * 0.6),
            'value' => $secondaryFound . '/' . $totalSecondary,
            'label' => 'Mots-clés secondaires utilisés',
            'detail' => $totalSecondary > 0 ? $secondaryFound . '/' . $totalSecondary . ' présents' : 'Aucun mot-clé secondaire défini',
        ];

        // Semantic richness (unique words ratio)
        $uniqueWords = count(array_unique($words ?: []));
        $totalWords = count($words ?: []);
        $richness = $totalWords > 0 ? ($uniqueWords / $totalWords) * 100 : 0;
        $checks['semantic_richness'] = [
            'pass' => $richness >= 40,
            'value' => round($richness, 1) . '%',
            'label' => 'Richesse lexicale',
            'detail' => round($richness, 1) . '% de mots uniques',
        ];

        // Heading distribution
        preg_match_all('/<h2[^>]*>/i', $content, $h2s);
        $checks['heading_distribution'] = [
            'pass' => count($h2s[0]) >= 3 && count($h2s[0]) <= 8,
            'value' => count($h2s[0]),
            'label' => 'Distribution des titres',
            'detail' => count($h2s[0]) . ' sections principales',
        ];

        // Paragraph length (avoid walls of text)
        preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $paragraphs);
        $longParagraphs = 0;
        foreach ($paragraphs[1] as $p) {
            if ($this->countWords(strip_tags($p)) > 150) {
                $longParagraphs++;
            }
        }
        $checks['paragraph_length'] = [
            'pass' => $longParagraphs === 0,
            'value' => $longParagraphs,
            'label' => 'Longueur des paragraphes',
            'detail' => $longParagraphs === 0 ? 'Tous les paragraphes sont lisibles' : $longParagraphs . ' paragraphe(s) trop long(s)',
        ];

        // Sentence variety
        $sentences = preg_split('/[.!?]+/', $plainText);
        $sentenceLengths = array_map(function ($s) { return $this->countWords(trim($s)); }, $sentences ?: []);
        $sentenceLengths = array_filter($sentenceLengths, fn($l) => $l > 0);
        $avgSentenceLen = count($sentenceLengths) > 0 ? array_sum($sentenceLengths) / count($sentenceLengths) : 0;
        $checks['sentence_variety'] = [
            'pass' => $avgSentenceLen >= 10 && $avgSentenceLen <= 25,
            'value' => round($avgSentenceLen, 1),
            'label' => 'Variété des phrases',
            'detail' => round($avgSentenceLen, 1) . ' mots/phrase en moyenne',
        ];

        // Readability (Flesch-like for French)
        $checks['readability'] = [
            'pass' => $avgSentenceLen <= 20 && $wordCount >= 800,
            'label' => 'Lisibilité',
            'detail' => $avgSentenceLen <= 20 ? 'Bonne lisibilité' : 'Phrases trop longues en moyenne',
        ];

        // Content depth (uses various HTML elements)
        $hasLists = preg_match('/<(ul|ol)[^>]*>/i', $content) === 1;
        $hasTables = preg_match('/<table[^>]*>/i', $content) === 1;
        $hasBlockquote = preg_match('/<blockquote[^>]*>/i', $content) === 1;
        $hasStrong = preg_match('/<(strong|b)[^>]*>/i', $content) === 1;
        $depthScore = ($hasLists ? 1 : 0) + ($hasTables ? 1 : 0) + ($hasBlockquote ? 1 : 0) + ($hasStrong ? 1 : 0);
        $checks['content_depth'] = [
            'pass' => $depthScore >= 2,
            'value' => $depthScore . '/4',
            'label' => 'Profondeur du contenu',
            'detail' => $depthScore . '/4 types de formatage (listes, tableaux, citations, gras)',
        ];

        return $checks;
    }

    /**
     * Generate SERP preview data.
     */
    private function generateSerpPreview(string $metaTitle, string $metaDescription, string $slug, array $article): array
    {
        $siteUrl = 'votre-site.fr';
        $displayUrl = $siteUrl . '/blog/' . $slug;

        // Truncate as Google would
        $displayTitle = mb_strlen($metaTitle) > 60 ? mb_substr($metaTitle, 0, 57) . '...' : $metaTitle;
        $displayDesc = mb_strlen($metaDescription) > 160 ? mb_substr($metaDescription, 0, 157) . '...' : $metaDescription;

        return [
            'title' => $displayTitle,
            'title_length' => mb_strlen($metaTitle),
            'title_ok' => mb_strlen($metaTitle) >= 50 && mb_strlen($metaTitle) <= 60,
            'url' => $displayUrl,
            'description' => $displayDesc,
            'description_length' => mb_strlen($metaDescription),
            'description_ok' => mb_strlen($metaDescription) >= 150 && mb_strlen($metaDescription) <= 160,
            'date' => date('d M Y'),
            'breadcrumbs' => [$siteUrl, 'blog', $slug],
        ];
    }

    /**
     * Analyze keyword golden ratio.
     */
    private function analyzeGoldenRatio(float $density, int $count, int $wordCount, string $keyword): array
    {
        $idealCount = $wordCount > 0 ? (int) round($wordCount * self::GOLDEN_RATIO / 100) : 0;
        $deviation = abs($density - self::GOLDEN_RATIO);
        $deviationPercent = self::GOLDEN_RATIO > 0 ? ($deviation / self::GOLDEN_RATIO) * 100 : 100;

        if ($density < self::GOLDEN_RATIO_MIN) {
            $status = 'under';
            $message = 'Sous-optimisé : ajoutez ' . max(0, $idealCount - $count) . ' occurrences du mot-clé';
        } elseif ($density > self::GOLDEN_RATIO_MAX) {
            $status = 'over';
            $message = 'Sur-optimisé : retirez ' . max(0, $count - $idealCount) . ' occurrences (risque de bourrage)';
        } else {
            $status = 'optimal';
            $message = 'Densité optimale proche du Golden Ratio';
        }

        return [
            'keyword' => $keyword,
            'current_density' => round($density, 2),
            'ideal_density' => self::GOLDEN_RATIO,
            'current_count' => $count,
            'ideal_count' => $idealCount,
            'word_count' => $wordCount,
            'status' => $status,
            'message' => $message,
            'deviation_percent' => round($deviationPercent, 1),
            'range' => [
                'min' => self::GOLDEN_RATIO_MIN,
                'max' => self::GOLDEN_RATIO_MAX,
            ],
        ];
    }

    /**
     * Content statistics.
     */
    private function contentStatistics(string $content, string $plainText, int $wordCount): array
    {
        preg_match_all('/<h2[^>]*>/i', $content, $h2s);
        preg_match_all('/<h3[^>]*>/i', $content, $h3s);
        preg_match_all('/<img[^>]*>/i', $content, $imgs);
        preg_match_all('/<a\s[^>]*href/i', $content, $links);

        $readingTime = (int) max(1, ceil($wordCount / 200));

        return [
            'word_count' => $wordCount,
            'reading_time' => $readingTime,
            'h2_count' => count($h2s[0]),
            'h3_count' => count($h3s[0]),
            'images_count' => count($imgs[0]),
            'links_count' => count($links[0]),
            'paragraphs' => substr_count($content, '<p'),
            'characters' => mb_strlen($plainText),
        ];
    }

    /**
     * Generate actionable recommendations.
     */
    private function generateRecommendations(array $technicalChecks, array $semanticChecks, array $goldenRatio): array
    {
        $recommendations = [];
        $priority = 1;

        // Critical technical issues
        foreach ($technicalChecks as $key => $check) {
            if (!$check['pass']) {
                $rec = [
                    'priority' => $this->getCheckPriority($key),
                    'category' => 'technique',
                    'check' => $key,
                    'label' => $check['label'],
                    'detail' => $check['detail'],
                    'action' => $this->getRecommendationAction($key, $check),
                ];
                $recommendations[] = $rec;
            }
        }

        // Semantic issues
        foreach ($semanticChecks as $key => $check) {
            if (!$check['pass']) {
                $rec = [
                    'priority' => $this->getCheckPriority($key),
                    'category' => 'sémantique',
                    'check' => $key,
                    'label' => $check['label'],
                    'detail' => $check['detail'],
                    'action' => $this->getRecommendationAction($key, $check),
                ];
                $recommendations[] = $rec;
            }
        }

        // Golden ratio
        if ($goldenRatio['status'] !== 'optimal') {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'golden_ratio',
                'check' => 'keyword_density',
                'label' => 'Golden Ratio du mot-clé',
                'detail' => $goldenRatio['message'],
                'action' => $goldenRatio['message'],
            ];
        }

        // Sort by priority
        usort($recommendations, function ($a, $b) {
            $order = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
            return ($order[$a['priority']] ?? 4) <=> ($order[$b['priority']] ?? 4);
        });

        return $recommendations;
    }

    private function getCheckPriority(string $key): string
    {
        $critical = ['meta_title_keyword', 'h1_present', 'h1_keyword', 'content_length', 'keyword_density_golden'];
        $high = ['meta_title_length', 'meta_description_keyword', 'url_slug_keyword', 'keyword_in_intro', 'secondary_keywords'];
        $medium = ['meta_description_length', 'h2_structure', 'internal_links', 'keyword_in_headings', 'semantic_richness'];

        if (in_array($key, $critical)) return 'critical';
        if (in_array($key, $high)) return 'high';
        if (in_array($key, $medium)) return 'medium';
        return 'low';
    }

    private function getRecommendationAction(string $key, array $check): string
    {
        $actions = [
            'meta_title_length' => 'Ajustez le titre SEO entre 50 et 60 caractères.',
            'meta_title_keyword' => 'Intégrez votre mot-clé focus dans le titre SEO.',
            'meta_description_length' => 'Ajustez la meta description entre 150 et 160 caractères.',
            'meta_description_keyword' => 'Ajoutez le mot-clé focus dans la meta description.',
            'url_slug_keyword' => 'Incluez le mot-clé focus dans l\'URL (slug).',
            'url_slug_length' => 'L\'URL doit contenir entre 3 et 7 mots.',
            'h1_present' => 'Ajoutez un titre H1 à votre article.',
            'h1_keyword' => 'Intégrez le mot-clé focus dans le titre H1.',
            'h2_structure' => 'Structurez avec 3 à 8 sections H2.',
            'h3_structure' => 'Ajoutez au moins 2 sous-sections H3 pour détailler.',
            'internal_links' => 'Ajoutez au moins 3 liens internes vers d\'autres articles.',
            'external_links' => 'Ajoutez 2-3 liens externes vers des sources autoritaires.',
            'images_present' => 'Ajoutez au moins 3 images dans l\'article.',
            'images_alt_tags' => 'Ajoutez des balises ALT descriptives à toutes les images.',
            'content_length' => 'Étoffez le contenu pour atteindre au moins 1500 mots.',
            'faq_section' => 'Ajoutez une section FAQ avec 5-10 questions.',
            'keyword_density_golden' => 'Ajustez la densité du mot-clé vers le Golden Ratio (1.618%).',
            'keyword_in_intro' => 'Placez le mot-clé focus dans les 300 premiers mots.',
            'keyword_in_conclusion' => 'Mentionnez le mot-clé dans la conclusion.',
            'keyword_in_headings' => 'Utilisez des variantes du mot-clé dans au moins 2 sous-titres.',
            'secondary_keywords' => 'Intégrez davantage de mots-clés secondaires.',
            'semantic_richness' => 'Enrichissez le vocabulaire avec des synonymes et termes LSI.',
            'heading_distribution' => 'Répartissez mieux les sous-titres H2 (3 à 8 sections).',
            'paragraph_length' => 'Découpez les paragraphes de plus de 150 mots.',
            'sentence_variety' => 'Variez la longueur des phrases (10-25 mots en moyenne).',
            'readability' => 'Simplifiez les phrases pour améliorer la lisibilité.',
            'content_depth' => 'Ajoutez des listes, tableaux ou citations pour enrichir.',
        ];

        return $actions[$key] ?? $check['detail'];
    }

    private function stripHtml(string $html): string
    {
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }

    private function countWords(string $text): int
    {
        $text = trim($text);
        if ($text === '') return 0;
        $words = preg_split('/\s+/u', $text);
        return count(array_filter($words ?: [], fn($w) => mb_strlen($w) > 0));
    }

    private function countKeywordOccurrences(string $text, string $keyword): int
    {
        if ($keyword === '') return 0;
        return mb_substr_count(mb_strtolower($text), mb_strtolower($keyword));
    }

    private function parseSecondaryKeywords(string $raw): array
    {
        if (trim($raw) === '') return [];
        $keywords = preg_split('/[,;\n]+/', $raw);
        return array_filter(array_map('trim', $keywords ?: []), fn($k) => $k !== '');
    }

    private function calculateScore(array $checks, array $weights): int
    {
        $totalWeight = array_sum($weights);
        if ($totalWeight === 0) return 0;

        $earned = 0;
        foreach ($checks as $key => $check) {
            if (isset($weights[$key]) && $check['pass']) {
                $earned += $weights[$key];
            }
        }

        return (int) round(($earned / $totalWeight) * 100);
    }
}
