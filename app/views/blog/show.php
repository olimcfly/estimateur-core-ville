<?php
$createdAt = null;
$publishedAt = null;
$displayDate = null;
if (!empty($article['published_at'])) {
    try {
        $pubDate = new DateTimeImmutable((string) $article['published_at']);
        $publishedAt = $pubDate->format(DATE_ATOM);
        $displayDate = $pubDate->format('d/m/Y');
    } catch (Exception) {}
}
if (!empty($article['created_at'])) {
    try {
        $createDate = new DateTimeImmutable((string) $article['created_at']);
        $createdAt = $createDate->format(DATE_ATOM);
        if ($displayDate === null) {
            $displayDate = $createDate->format('d/m/Y');
        }
    } catch (Exception) {}
}

$datePublished = $publishedAt ?? $createdAt;
$dateModified = $createdAt ?? $publishedAt;

$baseUrl = App\Core\Config::get('app.base_url', '');
$siteDomain = site('domain', '');
$siteBase = $baseUrl !== '' ? rtrim((string) $baseUrl, '/') : ($siteDomain !== '' ? 'https://' . $siteDomain : '');
$branding = function_exists('getBrandingConfig') ? getBrandingConfig() : [];
$brandName = trim((string) ($branding['site_name'] ?? ''));
if ($brandName === '') {
    $city = trim((string) site('city', ''));
    $brandName = $city !== '' ? ('Plateforme immobilière ' . $city) : 'Plateforme immobilière';
}
$articlePath = '/blog/' . rawurlencode((string) $article['slug']);
$articleUrl = $siteBase . $articlePath;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => (string) $article['title'],
    'description' => (string) $article['meta_description'],
    'datePublished' => $datePublished,
    'dateModified' => $dateModified,
    'author' => [
        '@type' => 'Organization',
        'name' => $brandName,
        'url' => $siteBase,
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $brandName,
        'url' => $siteBase,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $siteBase . '/favicon.svg',
        ],
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => $articleUrl,
    ],
    'url' => $articleUrl,
    'image' => !empty($article['og_image']) ? (string) $article['og_image'] : $siteBase . '/assets/images/og-estimation.png',
    'wordCount' => !empty($article['word_count']) ? (int) $article['word_count'] : null,
    'inLanguage' => 'fr-FR',
];

if (!empty($article['og_image'])) {
    $jsonLd['image'] = (string) $article['og_image'];
}

$jsonLd = array_filter($jsonLd, static fn (mixed $value): bool => $value !== null && $value !== '');

// Breadcrumb Schema
$breadcrumbLd = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => $siteBase . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $siteBase . '/blog'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => (string) $article['title'], 'item' => $articleUrl],
    ],
];

// FAQ Schema if available
$faqSchema = null;
if (!empty($article['faq_schema'])) {
    $faqData = json_decode((string) $article['faq_schema'], true);
    if (is_array($faqData) && !empty($faqData)) {
        $faqSchema = $faqData;
    }
}

$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script type="application/ld+json"><?= json_encode($jsonLd, $jsonFlags) ?></script>
<script type="application/ld+json"><?= json_encode($breadcrumbLd, $jsonFlags) ?></script>
<?php if ($faqSchema !== null): ?>
<script type="application/ld+json"><?= json_encode($faqSchema, $jsonFlags) ?></script>
<?php endif; ?>

<section class="section">
  <div class="container article-container">

    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="/">Accueil</a> <span class="breadcrumb-sep">&rsaquo;</span>
      <a href="/blog">Blog</a> <span class="breadcrumb-sep">&rsaquo;</span>
      <span aria-current="page"><?= e(mb_substr((string) $article['title'], 0, 60)) ?></span>
    </nav>

    <h1><?= e((string) $article['title']) ?></h1>

    <div class="article-meta">
      <?php if ($displayDate): ?>
        <time class="article-date" datetime="<?= e($datePublished ?? '') ?>">
          <i class="far fa-calendar-alt"></i> Publie le <?= e($displayDate) ?>
        </time>
      <?php endif; ?>
      <?php if (!empty($article['reading_time_minutes']) && (int) $article['reading_time_minutes'] > 0): ?>
        <span class="article-reading-time"><i class="far fa-clock"></i> <?= (int) $article['reading_time_minutes'] ?> min de lecture</span>
      <?php endif; ?>
    </div>

    <p class="muted"><?= e((string) $article['meta_description']) ?></p>

    <article class="card article-content">
      <?= (string) $article['content'] ?>
    </article>

    <section class="card cta-card">
      <h2>Besoin d'un prix de vente realiste et defendable ?</h2>
      <p class="muted">Profitez de notre simulateur pour obtenir une fourchette fiable adaptée à votre marché local.</p>
      <a href="/estimation" class="btn">Demander mon estimation</a>
    </section>
  </div>
</section>

<style>
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
  color: var(--muted);
}
.breadcrumb a {
  color: var(--primary);
  text-decoration: none;
}
.breadcrumb a:hover {
  text-decoration: underline;
}
.breadcrumb-sep {
  color: var(--muted);
  opacity: 0.5;
}
.article-meta {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin: 0.5rem 0 1rem;
  flex-wrap: wrap;
}
.article-date,
.article-reading-time {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9rem;
  color: var(--muted);
}
</style>
