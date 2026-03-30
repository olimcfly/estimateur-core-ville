<?php
$page_title = 'Villes couvertes | Estimation immobilière locale';
$meta_description = 'Découvrez nos zones couvertes et lancez votre estimation immobilière locale.';
$cities = [
  ['slug' => 'toulon', 'name' => 'Toulon'],
  ['slug' => 'hyeres', 'name' => 'Hyères'],
  ['slug' => 'la-seyne-sur-mer', 'name' => 'La Seyne-sur-Mer'],
  ['slug' => 'sanary-sur-mer', 'name' => 'Sanary-sur-Mer'],
];
?>
<section class="section">
  <div class="container">
    <h1>Nos villes d'estimation</h1>
    <p>Chaque ville dispose d'une page locale optimisée SEO et conversion vendeurs.</p>
    <div class="premium-grid-2">
      <?php foreach ($cities as $city): ?>
        <article class="premium-card">
          <h2><?= e($city['name']) ?></h2>
          <p>Estimation immobilière à <?= e($city['name']) ?>, tendances locales et plan d'action vendeur.</p>
          <a class="btn btn-secondary" href="/ville/<?= e($city['slug']) ?>">Voir la page locale</a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
