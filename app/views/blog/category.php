<?php
$baseUrl = App\Core\Config::get('app.base_url', '');
$categoryPath = '/blog/' . rawurlencode((string) $category_slug);
$categoryUrl = $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . $categoryPath : $categoryPath;

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => (string) $h1,
    'description' => (string) $meta_description,
    'url' => $categoryUrl,
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Estimation Immobilier ' . site('city', ''),
        'url' => $baseUrl !== '' ? rtrim((string) $baseUrl, '/') : (site('domain', '') !== '' ? 'https://' . site('domain', '') : ''),
    ],
    'breadcrumb' => [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Accueil',
                'item' => $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . '/' : '/',
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => $baseUrl !== '' ? rtrim((string) $baseUrl, '/') . '/blog' : '/blog',
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => (string) $h1,
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
      <a href="/">Accueil</a> <span class="breadcrumb-sep">&rsaquo;</span>
      <a href="/blog">Blog</a> <span class="breadcrumb-sep">&rsaquo;</span>
      <span aria-current="page"><?= e((string) $h1) ?></span>
    </nav>

    <p class="eyebrow"><?= $eyebrow ?></p>
    <h1><?= e((string) $h1) ?></h1>
    <p class="lead"><?= e((string) $intro) ?></p>

    <?php if (!empty($categories)): ?>
    <nav class="category-nav" aria-label="Categories du blog">
      <a href="/blog" class="category-link">Tous les articles</a>
      <?php foreach ($categories as $catSlug => $catData): ?>
        <a href="/blog/<?= e($catSlug) ?>" class="category-link<?= $catSlug === $category_slug ? ' active' : '' ?>"><?= e($catData['h1']) ?></a>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>

    <div class="blog-grid">
      <?php if (empty($articles)): ?>
        <article class="card">
          <h2>Aucun article dans cette categorie pour le moment</h2>
          <p class="muted">Revenez prochainement ou consultez <a href="/blog">tous nos articles</a>.</p>
        </article>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <article class="card blog-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <?php if (!empty($article['published_at'])): ?>
              <time class="article-date" datetime="<?= e(date('Y-m-d', strtotime((string) $article['published_at']))) ?>">
                <i class="far fa-calendar-alt"></i>
                <?= e(date('d/m/Y', strtotime((string) $article['published_at']))) ?>
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
      <h2>Vous voulez connaitre la valeur reelle de votre bien ?</h2>
      <p class="muted">Obtenez une estimation precise en moins de 2 minutes.</p>
      <a class="btn" href="/estimation">Lancer mon estimation</a>
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
.category-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin: 1.5rem 0 2rem;
}
.category-link {
  display: inline-block;
  padding: 0.5rem 1rem;
  border: 1px solid var(--border);
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.9rem;
  color: var(--muted);
  transition: all 0.2s ease;
}
.category-link:hover {
  border-color: var(--primary);
  color: var(--primary);
  background: rgba(var(--primary-rgb), 0.05);
}
.category-link.active {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.article-date {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  color: var(--muted);
  margin: 0.25rem 0 0.5rem;
}
</style>
