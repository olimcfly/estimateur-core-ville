<?php
$cityName = (string) ($city_name ?? 'votre ville');
$page_title = 'Estimation immobilière à ' . $cityName . ' | Propriétaires vendeurs';
$meta_description = 'Estimation immobilière à ' . $cityName . ' : prix local, dynamique du marché et accompagnement vendeur.';
?>
<section class="section">
  <div class="container">
    <h1>Estimation immobilière à <?= e($cityName) ?></h1>
    <p>Vous vendez à <?= e($cityName) ?> ? Obtenez une estimation claire, basée sur le marché local et vos objectifs de vente.</p>
    <div class="premium-grid-3">
      <article class="premium-card"><h3>Marché local</h3><p>Comprenez les niveaux de prix et les délais de vente dans votre secteur.</p></article>
      <article class="premium-card"><h3>Positionnement vendeur</h3><p>Fixez un prix de mise en vente cohérent pour attirer les bons acheteurs.</p></article>
      <article class="premium-card"><h3>Plan d'action</h3><p>Décidez si vous vendez maintenant, dans 3 mois ou dans 1 an.</p></article>
    </div>
    <p style="margin-top:1rem"><a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien à <?= e($cityName) ?></a></p>
  </div>
</section>
