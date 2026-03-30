<?php
$cityName = trim((string) ($city_name ?? site('city', 'votre ville')));
$areaLabel = trim((string) ($area_label ?? ($cityName . ' et alentours')));
$page_title = 'Estimation immobilière ' . $areaLabel . ' | Vendeurs';
$meta_description = 'Estimation immobilière premium pour propriétaires vendeurs à ' . $areaLabel . '. Obtenez votre fourchette de prix et démarrez votre vente.';
?>
<section class="premium-hero">
  <div class="container premium-hero__grid">
    <div>
      <p>Cabinet immobilier premium · <?= e($areaLabel) ?></p>
      <h1>Vendez au bon prix dès le départ</h1>
      <p>Notre estimateur vous donne une première fourchette fiable en moins d'une minute, puis un plan d'action vendeur clair.</p>
      <a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien</a>
    </div>
    <aside class="premium-card">
      <h2>Pourquoi les vendeurs nous choisissent</h2>
      <ul>
        <li>Estimation immédiate et sans engagement</li>
        <li>Lecture locale du marché à <?= e($cityName) ?></li>
        <li>Accompagnement humain pour la mise en vente</li>
      </ul>
    </aside>
  </div>
</section>

<section class="section">
  <div class="container premium-grid-3">
    <article class="premium-card"><h3>1. Estimer</h3><p>3 informations essentielles pour cadrer votre prix de vente.</p></article>
    <article class="premium-card"><h3>2. Ajuster</h3><p>Analyse locale et stratégie selon votre délai de vente.</p></article>
    <article class="premium-card"><h3>3. Convertir</h3><p>Mise en marché optimisée pour générer des visites qualifiées.</p></article>
  </div>
</section>
