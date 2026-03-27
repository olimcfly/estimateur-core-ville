<section class="section blog-page">
  <div class="container">
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="/">Accueil</a> &rsaquo;
      <span aria-current="page">Blog</span>
    </nav>

    <header class="blog-hero card">
      <span class="blog-badge">Éditorial immobilier</span>
      <p class="eyebrow">Blog immobilier <?= htmlspecialchars((string) site('city', ''), ENT_QUOTES, 'UTF-8') ?></p>
      <h1>Analyses locales, stratégies de vente et conseils d’experts</h1>
      <p class="lead">Notre rédaction partage une veille claire et actionable pour vous aider à vendre au bon prix, au bon moment et avec la bonne méthode.</p>
    </header>

    <section class="blog-filters" aria-labelledby="blog-filters-title">
      <div class="filters-head">
        <h2 id="blog-filters-title">Explorer nos thématiques</h2>
        <?php if (empty($articles)): ?>
          <p class="muted">La sélection éditoriale complète arrive progressivement. Ces rubriques seront alimentées en continu.</p>
        <?php endif; ?>
      </div>
      <nav class="category-nav" aria-label="Catégories du blog">
        <a href="/blog" class="category-link active">Tous les articles</a>
        <a href="/blog/marche-immobilier" class="category-link">Marché immobilier</a>
        <a href="/blog/vendre-son-bien" class="category-link">Vendre son bien</a>
        <a href="/blog/conseils-astuces" class="category-link">Conseils &amp; astuces</a>
        <a href="/blog/aspect-juridique" class="category-link">Aspect juridique</a>
      </nav>
    </section>

    <?php if (empty($articles)): ?>
      <section class="empty-editorial card" aria-labelledby="empty-editorial-title">
        <div class="empty-editorial-intro">
          <h2 id="empty-editorial-title">La rédaction prépare ses prochains contenus</h2>
          <p class="muted">Nous privilégions des articles complets et utiles plutôt qu’un flux superficiel. Vous retrouverez ici des décryptages précis sur le marché, la stratégie de prix et la vente immobilière locale.</p>
        </div>
        <div class="empty-editorial-grid">
          <article class="card substitute-card">
            <h3>Marché immobilier local</h3>
            <p class="muted">Tendances de prix, dynamiques de quartiers et signaux à surveiller avant de lancer une vente.</p>
          </article>
          <article class="card substitute-card">
            <h3>Préparer son bien à la vente</h3>
            <p class="muted">Check-list des points qui rassurent les acheteurs et valorisent votre bien dès les premières visites.</p>
          </article>
          <article class="card substitute-card">
            <h3>Estimer au bon prix</h3>
            <p class="muted">Méthodes d’estimation, marges de négociation et erreurs de positionnement à éviter.</p>
          </article>
          <article class="card substitute-card">
            <h3>Vendre sans erreurs</h3>
            <p class="muted">Documents clés, étapes juridiques et bonnes pratiques pour sécuriser la transaction.</p>
          </article>
          <article class="card substitute-card">
            <h3>Délais, négociation, stratégie</h3>
            <p class="muted">Comment arbitrer entre délai de vente, prix net vendeur et qualité des offres reçues.</p>
          </article>
          <article class="card substitute-card">
            <h3>Questions fréquentes vendeurs</h3>
            <p class="muted">Réponses concrètes aux interrogations les plus courantes avant la mise en vente.</p>
          </article>
        </div>
      </section>
    <?php else: ?>
      <div class="blog-grid" aria-live="polite">
        <?php foreach ($articles as $article): ?>
          <article class="card blog-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <?php if (!empty($article['published_at'])): ?>
              <?php $publishedDate = new DateTimeImmutable((string) $article['published_at']); ?>
              <time class="blog-date" datetime="<?= e($publishedDate->format('Y-m-d')) ?>">
                <?= e($publishedDate->format('d/m/Y')) ?>
              </time>
            <?php endif; ?>
            <p class="muted"><?= e((string) $article['meta_description']) ?></p>
            <div class="blog-card-footer">
              <?php if (!empty($article['reading_time_minutes']) && (int) $article['reading_time_minutes'] > 0): ?>
                <span class="reading-time"><?= (int) $article['reading_time_minutes'] ?> min de lecture</span>
              <?php endif; ?>
              <a class="btn btn-small" href="/blog/<?= e((string) $article['slug']) ?>">Lire l'article</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <section class="card cta-card cta-premium" aria-labelledby="blog-cta-title">
      <h2 id="blog-cta-title">Passez de l’information à l’action</h2>
      <p class="muted">Obtenez une estimation argumentée de votre bien et définissez une stratégie de vente adaptée à votre objectif.</p>
      <div class="cta-actions">
        <a class="btn" href="/estimation">Demander mon estimation</a>
        <a class="btn btn-secondary" href="/services">Découvrir nos services</a>
      </div>
    </section>
  </div>
</section>

<style>
.blog-page {
  padding-top: 1.25rem;
}
.blog-hero {
  margin-top: 1rem;
  padding: clamp(1.3rem, 2.4vw, 2rem);
  background: linear-gradient(145deg, rgba(var(--primary-rgb), 0.14), rgba(255, 255, 255, 0.98));
  border: 1px solid rgba(var(--primary-rgb), 0.2);
}
.blog-badge {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  background: rgba(var(--primary-rgb), 0.14);
  color: var(--primary);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  padding: 0.35rem 0.7rem;
  margin-bottom: 0.7rem;
}
.blog-hero .eyebrow {
  margin-bottom: 0.45rem;
}
.blog-hero h1 {
  margin-bottom: 0.8rem;
  max-width: 20ch;
}
.blog-hero .lead {
  max-width: 70ch;
  margin-bottom: 0;
}
.blog-filters {
  margin: 1.8rem 0 1.2rem;
}
.filters-head {
  display: flex;
  gap: 0.8rem;
  justify-content: space-between;
  align-items: baseline;
  flex-wrap: wrap;
}
.filters-head h2 {
  margin: 0;
  font-size: 1.1rem;
}
.filters-head .muted {
  margin: 0;
}
.category-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
  margin: 0.9rem 0 0;
}
.category-link {
  display: inline-block;
  padding: 0.55rem 1rem;
  border: 1px solid var(--border);
  border-radius: 999px;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--muted);
  transition: all 0.2s ease;
  background: #fff;
}
.category-link:hover {
  border-color: rgba(var(--primary-rgb), 0.4);
  color: var(--primary);
  transform: translateY(-1px);
}
.category-link.active {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
}
.blog-grid,
.empty-editorial-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1rem;
}
.empty-editorial {
  margin-top: 0.8rem;
  padding: clamp(1rem, 2vw, 1.6rem);
  background: linear-gradient(165deg, #fff, rgba(var(--primary-rgb), 0.06));
}
.empty-editorial-intro {
  margin-bottom: 1rem;
}
.empty-editorial-intro h2 {
  margin-bottom: 0.5rem;
}
.substitute-card {
  border: 1px solid rgba(var(--primary-rgb), 0.12);
  box-shadow: 0 16px 35px rgba(17, 24, 39, 0.06);
}
.substitute-card h3 {
  margin-bottom: 0.45rem;
}
.blog-card {
  border: 1px solid rgba(var(--primary-rgb), 0.14);
  box-shadow: 0 14px 30px rgba(16, 24, 40, 0.06);
  transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.blog-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 20px 34px rgba(16, 24, 40, 0.12);
}
.blog-date {
  display: inline-flex;
  margin: 0.2rem 0 0.6rem;
  font-size: 0.84rem;
  color: var(--muted);
}
.blog-card-footer {
  display: flex;
  gap: 0.8rem;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}
.reading-time {
  font-size: 0.84rem;
  color: var(--muted);
  font-weight: 600;
}
.cta-premium {
  margin-top: 2rem;
  padding: clamp(1.2rem, 2vw, 1.7rem);
  background: linear-gradient(160deg, rgba(var(--primary-rgb), 0.16), rgba(255, 255, 255, 0.98));
  border: 1px solid rgba(var(--primary-rgb), 0.2);
}
.cta-premium h2 {
  margin-bottom: 0.5rem;
}
.cta-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1rem;
}

@media (max-width: 640px) {
  .blog-hero {
    padding: 1.1rem;
  }
  .blog-hero h1 {
    max-width: 100%;
  }
  .filters-head h2 {
    font-size: 1rem;
  }
  .category-link {
    font-size: 0.84rem;
  }
}
</style>
