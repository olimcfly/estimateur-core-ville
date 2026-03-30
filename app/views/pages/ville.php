<?php
$cityName = (string) ($city_name ?? 'votre ville');
$marketInsight = (string) ($market_insight ?? 'Le marché local reste sélectif : les biens bien positionnés se vendent plus vite que la moyenne.');
$localAreas = isset($local_areas) && is_array($local_areas) ? $local_areas : [];
?>
<section class="section">
  <div class="container">
    <header class="section-head">
      <h1>Estimation immobilière à <?= e($cityName) ?></h1>
      <p>Vous êtes propriétaire vendeur à <?= e($cityName) ?> ? Cette page locale vous donne les bases pour vendre dans de bonnes conditions.</p>
    </header>

    <div class="premium-grid-2">
      <article class="premium-card">
        <h2>Marché local à <?= e($cityName) ?></h2>
        <p><?= e($marketInsight) ?></p>
        <p>Notre objectif : vous aider à définir un prix de mise en vente crédible pour attirer des acheteurs qualifiés.</p>
      </article>
      <article class="premium-card">
        <h2>Arguments vendeurs</h2>
        <ul class="premium-bullet-list">
          <li>Positionnement prix cohérent dès le lancement</li>
          <li>Meilleure maîtrise des délais de vente</li>
          <li>Négociation plus favorable</li>
          <li>Décisions plus sereines à chaque étape</li>
        </ul>
      </article>
    </div>

    <?php if ($localAreas !== []): ?>
      <section class="premium-top-gap">
        <h2>Secteurs suivis à <?= e($cityName) ?></h2>
        <div class="premium-tags">
          <?php foreach ($localAreas as $area): ?>
            <span><?= e((string) $area) ?></span>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <div class="premium-final-cta premium-top-gap">
      <a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien à <?= e($cityName) ?></a>
    </div>
  </div>
</section>
