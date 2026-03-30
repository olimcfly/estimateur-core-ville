<?php $city = (string) site('city', 'votre ville'); ?>
<section class="section">
  <div class="container">
    <header class="section-head">
      <h1>Blog immobilier vendeurs · <?= e($city) ?></h1>
      <p>Retrouvez nos analyses locales, conseils de positionnement prix et bonnes pratiques pour vendre avec plus de sérénité.</p>
    </header>

    <?php if (empty($articles)): ?>
      <div class="premium-card">
        <h2>La sélection éditoriale arrive</h2>
        <p>Nous préparons des contenus utiles pour les propriétaires vendeurs : marché local, stratégie de vente et points juridiques clés.</p>
        <a class="btn btn-gold" href="/estimation#form-estimation">Lancer mon estimation</a>
      </div>
    <?php else: ?>
      <div class="premium-grid-3">
        <?php foreach ($articles as $article): ?>
          <article class="premium-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <p><?= e((string) $article['meta_description']) ?></p>
            <a class="btn btn-secondary" href="/blog/<?= e((string) $article['slug']) ?>">Lire l'article</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="premium-final-cta premium-top-gap">
      <a class="btn btn-gold" href="/estimation#form-estimation">Passer de l'info à l'action</a>
    </div>
  </div>
</section>
