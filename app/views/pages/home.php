<?php
$cityName = trim((string) ($city_name ?? site('city', 'votre ville')));
$areaLabel = trim((string) ($area_label ?? ($cityName . ' et alentours')));
?>
<section class="premium-hero premium-hero--home">
  <div class="container premium-hero__grid">
    <div class="premium-hero__content">
      <p class="premium-kicker">Spécial propriétaires vendeurs · <?= e($areaLabel) ?></p>
      <h1>Combien vaut votre bien aujourd’hui à <?= e($cityName) ?> ?</h1>
      <p class="premium-lead">Obtenez une estimation basée sur les ventes réelles autour de chez vous. Gratuit, rapide, sans engagement.</p>
      <div class="premium-actions">
        <a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien</a>
        <a class="btn btn-secondary" href="/contact">Parler à un conseiller</a>
      </div>
      <ul class="premium-inline-trust">
        <li>✔ Résultat en quelques minutes</li>
        <li>✔ Données du marché local</li>
        <li>✔ Sans inscription obligatoire</li>
      </ul>
    </div>
    <aside class="premium-card premium-card--emphasis">
      <h2>Pourquoi cette estimation change votre vente</h2>
      <p>Un bien mal positionné perd du temps et de la valeur. Notre approche vous aide à éviter les deux erreurs les plus coûteuses : afficher trop haut ou brader trop bas.</p>
      <div class="premium-mini-list">
        <p><strong>Objectif :</strong> attirer des acheteurs qualifiés rapidement.</p>
        <p><strong>Méthode :</strong> estimation + lecture du marché local + plan vendeur.</p>
      </div>
    </aside>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <header class="section-head">
      <h2>Le marché immobilier a changé.</h2>
      <p>Et aujourd’hui, une mauvaise estimation peut vous faire perdre des milliers d’euros.</p>
      <ul class="premium-bullet-list">
        <li>Certains biens restent en vente trop longtemps</li>
        <li>D’autres sont vendus trop bas</li>
        <li>Les écarts de prix entre quartiers sont énormes</li>
      </ul>
    </header>
    <div class="premium-grid-2">
      <article class="premium-card">
        <h3>Notre solution</h3>
        <p>Notre outil vous donne une estimation cohérente avec votre marché local.</p>
        <ul class="premium-bullet-list">
          <li>Analyse des ventes récentes</li>
          <li>Prise en compte de votre secteur</li>
          <li>Vision claire du prix actuel</li>
        </ul>
      </article>
      <article class="premium-card">
        <h3>Process en 3 étapes</h3>
        <ol class="premium-bullet-list">
          <li>Vous renseignez votre bien</li>
          <li>Nous analysons les données du marché</li>
          <li>Vous obtenez une estimation claire</li>
        </ol>
      </article>
    </div>
  </div>
</section>

<section class="section">
  <div class="container premium-grid-2">
    <article class="premium-card">
      <h2>Marché local : ce qui compte vraiment pour un vendeur</h2>
      <ul class="premium-bullet-list">
        <li>Écart de prix entre annonces et ventes réelles</li>
        <li>Délai moyen de vente selon secteur</li>
        <li>Niveau de négociation des acheteurs</li>
        <li>Impact de l’état du bien et du DPE</li>
      </ul>
    </article>
    <article class="premium-card">
      <h2>Bénéfices</h2>
      <ul class="premium-bullet-list">
        <li>Vendre au bon prix</li>
        <li>Éviter les erreurs classiques</li>
        <li>Gagner du temps</li>
        <li>Comprendre le marché</li>
      </ul>
    </article>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <header class="section-head">
      <h2>Questions fréquentes des propriétaires vendeurs</h2>
    </header>
    <div class="premium-grid-2">
      <article class="premium-card"><h3>L’estimation est-elle gratuite ?</h3><p>Oui, la première fourchette est gratuite et sans engagement.</p></article>
      <article class="premium-card"><h3>Dois-je laisser mes coordonnées ?</h3><p>Vous pouvez obtenir une estimation initiale sans parcours complexe.</p></article>
      <article class="premium-card"><h3>Est-ce adapté à mon quartier ?</h3><p>Oui, l’approche est pensée pour une lecture locale du marché.</p></article>
      <article class="premium-card"><h3>Et après l’estimation ?</h3><p>Vous pouvez demander un accompagnement personnalisé pour préparer votre vente.</p></article>
    </div>
    <div class="premium-final-cta">
      <h3>Découvrez la valeur de votre bien maintenant</h3>
      <a class="btn btn-gold" href="/estimation#form-estimation">👉 Estimation gratuite en 30 secondes</a>
    </div>
  </div>
</section>
