<?php $city = (string) site('city', 'votre ville'); ?>
<section class="section">
  <div class="container">
    <h1>Blog immobilier vendeur · <?= e($city) ?></h1>
    <p>Conseils clairs pour vendre mieux : estimation, prix, stratégie de commercialisation et timing.</p>

    <?php if (empty($articles)): ?>
      <div class="premium-card">
        <p>Les prochains articles arrivent. En attendant, lancez votre estimation pour cadrer votre projet.</p>
        <a class="btn btn-secondary" href="/estimation">Démarrer l'estimation</a>
      </div>
    <?php else: ?>
      <div class="premium-grid-3">
        <?php foreach ($articles as $article): ?>
          <article class="premium-card">
            <h2><?= e((string) $article['title']) ?></h2>
            <p><?= e((string) $article['meta_description']) ?></p>
            <a class="btn btn-secondary" href="/blog/<?= e((string) $article['slug']) ?>">Lire</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
