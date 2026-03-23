<?php
$baseUrl = App\Core\Config::get('app.base_url', '');
$categoryPath = '/blog/' . rawurlencode((string) $category_slug);
$categoryUrl = $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . $categoryPath : $categoryPath;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => (string) $category['h1'],
    'description' => (string) $category['meta_description'],
    'url' => $categoryUrl,
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Estimation Immobilier Bordeaux',
        'url' => $baseUrl !== '' ? rtrim((string) $baseUrl, '/') : 'https://estimation-immobilier-bordeaux.fr',
    ],
    'breadcrumb' => [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Accueil',
                'item' => $baseUrl !== '' ? rtrim((string) $baseUrl, '/') : 'https://estimation-immobilier-bordeaux.fr',
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => ($baseUrl !== '' ? rtrim((string) $baseUrl, '/') : 'https://estimation-immobilier-bordeaux.fr') . '/blog',
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => (string) $category['h1'],
                'item' => $categoryUrl,
            ],
        ],
    ],
];
?>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<section class="section">
  <div class="container">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="/">Accueil</a> &rsaquo;
      <a href="/blog">Blog</a> &rsaquo;
      <span aria-current="page"><?= e((string) $category['h1']) ?></span>
    </nav>

    <p class="eyebrow">Blog immobilier Bordeaux</p>
    <h1><?= e((string) $category['h1']) ?></h1>
    <p class="lead"><?= e((string) $category['intro']) ?></p>

    <div class="blog-grid">
      <?php if (empty($articles)): ?>
        <article class="card">
          <h2>Aucun article dans cette catégorie pour le moment</h2>
          <p class="muted">Revenez prochainement pour lire nos derniers articles sur ce sujet.</p>
          <a class="btn btn-small" href="/blog">Voir tous les articles</a>
        </article>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <article class="card blog-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <?php if (!empty($article['published_at'])): ?>
              <time class="blog-date" datetime="<?= e((new DateTimeImmutable((string) $article['published_at']))->format('Y-m-d')) ?>">
                <?= e((new DateTimeImmutable((string) $article['published_at']))->format('d/m/Y')) ?>
              </time>
            <?php endif; ?>
            <p class="muted"><?= e((string) $article['meta_description']) ?></p>
            <?php if (!empty($article['reading_time_minutes']) && (int) $article['reading_time_minutes'] > 0): ?>
              <span class="reading-time"><?= (int) $article['reading_time_minutes'] ?> min de lecture</span>
            <?php endif; ?>
            <a class="btn btn-small" href="/blog/<?= e((string) $article['slug']) ?>">Lire l'article</a>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <section class="card cta-card">
      <h2>Vous voulez connaître la valeur réelle de votre bien ?</h2>
      <p class="muted">Obtenez une estimation précise en moins de 2 minutes.</p>
      <a class="btn" href="/estimation">Lancer mon estimation</a>
    </section>
  </div>
</section>
