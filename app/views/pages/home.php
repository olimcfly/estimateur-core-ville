<?php
$cityName = trim((string) ($city_name ?? site('city', 'votre ville')));
$areaLabel = trim((string) ($area_label ?? ($cityName . ' et alentours')));
?>
<section class="premium-hero premium-hero--home">
  <div class="container premium-hero__grid">
    <div class="premium-hero__content">
      <p class="premium-kicker">Spécial propriétaires vendeurs · <?= e($areaLabel) ?></p>
      <h1>Connaissez la valeur réelle de votre bien avant de vendre</h1>
      <p class="premium-lead">Obtenez une première estimation fiable en moins d’une minute, puis une stratégie claire pour vendre au bon prix et au bon moment.</p>
      <div class="premium-actions">
        <a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien</a>
        <a class="btn btn-secondary" href="/contact">Parler à un conseiller</a>
      </div>
      <ul class="premium-inline-trust">
        <li>Sans engagement</li>
        <li>Données locales</li>
        <li>Accompagnement humain</li>
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
      <h2>Un parcours simple pour vendre avec confiance</h2>
      <p>Chaque étape est pensée pour vous donner de la clarté et accélérer votre prise de décision.</p>
    </header>
    <div class="premium-grid-3">
      <article class="premium-card"><h3>1. Estimer</h3><p>Renseignez les caractéristiques clés de votre bien pour obtenir une fourchette instantanée.</p></article>
      <article class="premium-card"><h3>2. Positionner</h3><p>Comprenez les signaux du marché local et ajustez votre prix de mise en vente.</p></article>
      <article class="premium-card"><h3>3. Convertir</h3><p>Déployez une stratégie de vente crédible pour générer des visites sérieuses.</p></article>
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
      <h2>Vos avantages avec notre approche</h2>
      <ul class="premium-bullet-list">
        <li>Décision plus sereine dès le départ</li>
        <li>Moins d’allers-retours sur le prix</li>
        <li>Meilleure qualité des contacts acheteurs</li>
        <li>Processus plus fluide jusqu’à la vente</li>
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
      <a class="btn btn-gold" href="/estimation#form-estimation">Je lance mon estimation</a>
    </div>
  </div>
</section>
