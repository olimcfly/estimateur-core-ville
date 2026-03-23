<section class="section">
  <div class="container">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="/">Accueil</a> &rsaquo;
      <span aria-current="page">Blog</span>
    </nav>

    <p class="eyebrow">Blog immobilier Bordeaux</p>
    <h1>Conseils pour vendre votre bien au meilleur prix</h1>
    <p class="lead">Découvrez nos analyses locales, guides pratiques et stratégies de vente adaptées à votre profil vendeur.</p>

    <nav class="category-nav" aria-label="Catégories du blog">
      <a href="/blog" class="category-link active">Tous les articles</a>
      <a href="/blog/marche-immobilier" class="category-link">Marché immobilier</a>
      <a href="/blog/vendre-son-bien" class="category-link">Vendre son bien</a>
      <a href="/blog/conseils-astuces" class="category-link">Conseils &amp; astuces</a>
      <a href="/blog/aspect-juridique" class="category-link">Aspect juridique</a>
    </nav>

    <div class="blog-grid">
      <?php if (empty($articles)): ?>
        <article class="card">
          <h2>Aucun article publié pour le moment</h2>
          <p class="muted">Revenez prochainement pour lire nos derniers conseils immobiliers.</p>
        </article>
      <?php else: ?>
        <?php foreach ($articles as $article): ?>
          <article class="card blog-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <?php if (!empty($article['published_at'])): ?>
              <time class="article-date" datetime="<?= e(date('Y-m-d', strtotime((string) $article['published_at']))) ?>">
                <i class="far fa-calendar-alt"></i>
                <?= e(date('d/m/Y', strtotime((string) $article['published_at']))) ?>
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

<style>
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
