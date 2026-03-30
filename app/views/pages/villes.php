<?php
$cities = [
  ['slug' => 'toulon', 'name' => 'Toulon'],
  ['slug' => 'hyeres', 'name' => 'Hyères'],
  ['slug' => 'la-seyne-sur-mer', 'name' => 'La Seyne-sur-Mer'],
  ['slug' => 'sanary-sur-mer', 'name' => 'Sanary-sur-Mer'],
];
?>
<section class="section">
  <div class="container">
    <header class="section-head">
      <h1>Villes couvertes pour votre estimation immobilière</h1>
      <p>Chaque page locale vous aide à comprendre le marché et à vendre au bon prix dans votre secteur.</p>
    </header>

    <div class="premium-grid-2">
      <?php foreach ($cities as $city): ?>
        <article class="premium-card">
          <h2>Estimation à <?= e($city['name']) ?></h2>
          <p>Accédez à un contenu local dédié : repères marché, arguments vendeurs et CTA estimation.</p>
          <a class="btn btn-secondary" href="/ville/<?= e($city['slug']) ?>">Voir la page <?= e($city['name']) ?></a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
