<?php $city = (string) site('city', 'votre ville'); ?>
<section class="section">
  <div class="container">
    <header class="section-head">
      <h1>Financement : préparez votre vente et votre prochain achat</h1>
      <p>À <?= e($city) ?>, le bon montage financier vous évite de vendre dans l’urgence et protège votre marge de négociation.</p>
    </header>

    <div class="premium-grid-3">
      <article class="premium-card"><h2>Capacité réelle</h2><p>Calculez votre budget net après vente pour éviter les mauvaises surprises.</p></article>
      <article class="premium-card"><h2>Timing achat-revente</h2><p>Synchronisez les étapes clés pour limiter la pression sur le prix de vente.</p></article>
      <article class="premium-card"><h2>Crédit relais</h2><p>Évaluez les avantages et limites selon votre profil et votre calendrier.</p></article>
    </div>

    <div class="premium-grid-2 premium-top-gap">
      <article class="premium-card">
        <h3>Cas d’usage fréquents</h3>
        <ul class="premium-bullet-list">
          <li>Vendre pour acheter plus grand</li>
          <li>Arbitrer un investissement locatif</li>
          <li>Réorganiser un patrimoine après succession</li>
          <li>Sécuriser une transition de vie</li>
        </ul>
      </article>
      <article class="premium-card">
        <h3>Passez à l’action</h3>
        <p>Commencez par estimer votre bien, puis échangez avec un conseiller pour valider votre scénario financier.</p>
        <div class="premium-actions">
          <a class="btn btn-gold" href="/estimation#form-estimation">Estimer mon bien</a>
          <a class="btn btn-secondary" href="/contact">Demander un échange</a>
        </div>
      </article>
    </div>
  </div>
</section>
