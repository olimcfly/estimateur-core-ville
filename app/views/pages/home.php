<?php
$cityName = (string) (\App\Core\Config::get('city.name', '') ?: 'votre ville');
$cityRegion = (string) (\App\Core\Config::get('city.region', '') ?: 'votre région');
$page_title = 'Estimation Immobilier ' . $cityName . ' et ' . $cityRegion . ' | Avis de Valeur Gratuit en 60s';
$meta_description = 'Obtenez une estimation immobilière gratuite à ' . $cityName . ' et ' . $cityRegion . ' en 60 secondes. Prix au m² local, tendances du marché. 100% gratuit, sans engagement.';
?>

<!-- ============================================ -->
<!-- HERO + FORMULAIRE SIMPLE -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <!-- COLONNE 1: HEADLINE -->
    <div>
      <p class="eyebrow">
        <i class="fas fa-chart-line"></i> Avis de valeur indicatif en ligne
      </p>

      <h1>Estimez la valeur de votre bien immobilier à <?= e($cityName) ?> et <?= e($cityRegion) ?></h1>

      <p class="lead">
        Obtenez une fourchette de prix indicative en quelques secondes.
        3 informations suffisent pour recevoir votre avis de valeur gratuit.
      </p>

      <ul class="trust-list">
        <li>
          <i class="fas fa-bolt"></i>
          <strong>3 champs</strong> — Résultat immédiat
        </li>
        <li>
          <i class="fas fa-hand-holding-usd"></i>
          <strong>100% gratuit</strong> — Sans engagement
        </li>
        <li>
          <i class="fas fa-shield-alt"></i>
          <strong>Données sécurisées</strong> — RGPD conforme
        </li>
      </ul>

      <!-- SOCIAL PROOF -->
      <div class="testimonial-block">
        <p class="testimonial-label">
          <i class="fas fa-quote-left"></i> Témoignage client
        </p>
        <p class="testimonial-quote">
          "L'avis de valeur était très proche de l'offre reçue. Recommandé pour avoir une estimation fiable avant de vendre à <?= e($cityName) ?> !"
        </p>
        <p class="testimonial-author">
          — Marie D.
        </p>
      </div>

      <!-- CTA BUTTONS -->
      <div class="hero-actions">
        <a href="/estimation" class="btn btn-primary">
          <i class="fas fa-bolt"></i> Estimer gratuitement
        </a>
        <a href="#how-it-works" class="btn btn-ghost">
          <i class="fas fa-info-circle"></i> Comment ça marche
        </a>
      </div>
    </div>

    <!-- COLONNE 2: CTA RAPIDE -->
    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2>
          <i class="fas fa-calculator"></i> Votre avis de valeur gratuit
        </h2>
        <p class="muted">Remplissez ces 3 informations pour obtenir une fourchette de prix.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid">
        <!-- CHAMP 1: TYPE DE BIEN -->
        <label for="property_type">
          <span><i class="fas fa-home"></i> Type de bien</span>
          <select id="property_type" name="type_bien" required>
            <option value="">-- Sélectionner --</option>
            <option value="appartement">Appartement</option>
            <option value="maison">Maison / Villa</option>
            <option value="studio">Studio</option>
            <option value="loft">Loft</option>
            <option value="maison de ville">Maison de ville</option>
          </select>
        </label>

        <!-- CHAMP 2: SUPERFICIE -->
        <label for="surface">
          <span><i class="fas fa-ruler-combined"></i> Superficie (m²)</span>
          <input
            type="number"
            id="surface"
            name="surface"
            min="10"
            max="500"
            step="1"
            placeholder="Ex: 75"
            required
          >
        </label>

        <!-- CHAMP 3: LOCALITÉ -->
        <label for="ville">
          <span><i class="fas fa-map-marker-alt"></i> Localité</span>
          <input
            type="text"
            id="ville"
            name="ville"
            placeholder="<?= e($cityName) ?>, secteur proche..."
            required
            autocomplete="off"
          >
        </label>

        <!-- BOUTON -->
        <button type="submit" class="btn btn-primary btn-full btn-pulse">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <p class="form-footer">
          <i class="fas fa-lock"></i> Aucune donnée personnelle requise
        </p>
      </form>

      <div class="hero-benefits">
        <ul class="hero-benefits-list">
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>100% gratuit</strong> — aucun frais caché</span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Résultat immédiat</strong> — en moins d'1 minute</span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Données réelles</strong> — transactions récentes sur <?= e($cityName) ?> et sa région</span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Sans engagement</strong> — aucune obligation</span>
          </li>
        </ul>

        <a href="/estimation" class="btn btn-primary btn-full btn-pulse">
          <i class="fas fa-bolt"></i> Lancer mon estimation gratuite
        </a>

        <p class="form-footer">
          <i class="fas fa-lock"></i> Données sécurisées & conformes RGPD
        </p>
      </div>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- COMPRENDRE L'AVIS DE VALEUR -->
<!-- ============================================ -->
<section class="section section-alt" id="avis-de-valeur">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-gavel"></i> Ce qu'il faut savoir
      </p>
      <h2>Estimation en ligne vs. Avis de valeur réalisé par un conseiller immobilier</h2>
    </div>

    <div class="comparison-grid">

      <!-- COLONNE GAUCHE: CE QUE NOUS PROPOSONS -->
      <article class="card comparison-card">
        <h3 class="comparison-header">
          <i class="fas fa-chart-bar" style="color: var(--accent);"></i>
          Notre estimation en ligne
        </h3>
        <p class="muted" style="margin-bottom: 1rem;">
          Notre outil analyse les <strong>données statistiques du marché</strong> (transactions récentes, prix au m² par quartier, tendances)
          pour vous donner une <strong>fourchette indicative</strong> de la valeur de votre bien.
        </p>
        <ul class="comparison-list">
          <li>
            <i class="fas fa-check" style="color: var(--success);"></i>
            <span>Résultat instantané et gratuit</span>
          </li>
          <li>
            <i class="fas fa-check" style="color: var(--success);"></i>
            <span>Basé sur les données statistiques du marché local</span>
          </li>
          <li>
            <i class="fas fa-info-circle" style="color: var(--warning);"></i>
            <span>Donne une <strong>indication</strong>, pas un prix de vente garanti</span>
          </li>
          <li>
            <i class="fas fa-info-circle" style="color: var(--warning);"></i>
            <span>Ne prend pas en compte l'état précis du bien, les travaux, la vue, la luminosité, etc.</span>
          </li>
        </ul>
      </article>

      <!-- COLONNE DROITE: AVIS DE VALEUR DU CONSEILLER -->
      <article class="card comparison-card comparison-primary">
        <h3 class="comparison-header">
          <i class="fas fa-user-tie" style="color: var(--primary);"></i>
          L'avis de valeur d'un conseiller immobilier
        </h3>
        <p class="muted" style="margin-bottom: 1rem;">
          Un <strong>avis de valeur</strong> est une estimation rédigée par un <strong>professionnel de l'immobilier</strong> qui connaît le marché local.
          Il s'appuie sur une visite du bien et sur des références de ventes récentes pour proposer un prix de mise en vente cohérent.
        </p>
        <ul class="comparison-list">
          <li>
            <i class="fas fa-certificate" style="color: var(--primary);"></i>
            <span>Réalisé par un <strong>conseiller immobilier</strong> connaissant votre quartier</span>
          </li>
          <li>
            <i class="fas fa-certificate" style="color: var(--primary);"></i>
            <span>Visite physique du bien et analyse détaillée</span>
          </li>
          <li>
            <i class="fas fa-certificate" style="color: var(--primary);"></i>
            <span>Prend en compte l'état, les travaux, la situation, l'environnement et la demande sur le secteur</span>
          </li>
          <li>
            <i class="fas fa-certificate" style="color: var(--primary);"></i>
            <span>Base de travail pour fixer un prix de mise en vente réaliste</span>
          </li>
        </ul>
      </article>

    </div>

    <!-- ENCART IMPORTANT -->
    <div class="card note-card">
      <p>
        <i class="fas fa-exclamation-triangle" style="color: var(--primary);"></i>
        <strong>Important :</strong> Tous les outils en ligne (y compris le nôtre) fournissent des <strong>estimations statistiques</strong> à partir de données de marché.
        Pour affiner le prix de vente de votre bien, l'idéal est de compléter cette première estimation par un <strong>avis de valeur</strong> réalisé par un conseiller immobilier
        qui se déplace chez vous et analyse votre bien dans le détail.
      </p>
    </div>

  </div>
</section>

<!-- ============================================ -->
<!-- 3 ÉTAPES -->
<!-- ============================================ -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-bolt"></i> Simple et rapide
      </p>
      <h2>Comment ça marche ?</h2>
    </div>

    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">1</div>
        <h3>Remplissez 3 champs</h3>
        <p>Type de bien, superficie et localité. C'est tout ce dont nous avons besoin.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">2</div>
        <h3>Recevez votre fourchette</h3>
        <p>Notre moteur calcule une estimation basse, moyenne et haute basée sur les données du marché.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">3</div>
        <h3>Allez plus loin</h3>
        <p>Pour une évaluation précise, demandez un avis de valeur à un conseiller immobilier.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ -->
<!-- ============================================ -->
<section class="section section-alt" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-comments"></i> Questions fréquentes
      </p>
      <h2>Vos questions, nos réponses</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Cette estimation est-elle fiable ?</h3>
        <p>Notre outil donne une <strong>indication statistique</strong> basée sur les données du marché. Pour fixer un prix de mise en vente précis, il est recommandé de demander un <strong>avis de valeur</strong> à un conseiller immobilier qui visitera votre bien.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Qu'est-ce qu'un avis de valeur ?</h3>
        <p>C'est un document rédigé par un <strong>professionnel de l'immobilier</strong> (conseiller ou agent immobilier) après visite du bien. Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Pourquoi les outils en ligne ne suffisent pas ?</h3>
        <p>Les outils en ligne utilisent des <strong>statistiques générales</strong> (prix au m², tendances, historique des ventes). Ils ne voient pas l'état réel du bien, les travaux, la luminosité, la vue ou le voisinage. Seul un professionnel qui se rend sur place peut intégrer ces critères dans un avis de valeur.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation en ligne est-elle gratuite ?</h3>
        <p>Oui, 100% gratuite et sans engagement. Vous obtenez une fourchette indicative en quelques secondes, sans donner vos coordonnées.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je obtenir un avis de valeur ensuite ?</h3>
        <p>Oui ! Après votre estimation en ligne, nous vous proposons de demander un avis de valeur réalisé par un conseiller immobilier pour une évaluation complète de votre bien.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> En quoi est-ce utile alors ?</h3>
        <p>Notre outil vous donne une <strong>première indication</strong> rapide et gratuite. C'est un bon point de départ avant de faire appel à un conseiller immobilier pour un avis de valeur complet.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="card cta-final-card">
      <p class="eyebrow" style="margin-bottom: 1rem;">
        <i class="fas fa-calculator"></i> Commencez maintenant
      </p>
      <h2 style="margin-bottom: 1rem; font-size: 2rem;">
        Obtenez votre fourchette de prix en 30 secondes
      </h2>
      <p class="lead" style="max-width: 600px; margin: 0 auto 2rem;">
        3 informations suffisent. Gratuit, sans engagement, sans inscription.
      </p>
      <a href="/estimation" class="btn btn-primary btn-pulse" style="display: inline-flex; font-size: 1.1rem; padding: 1.2rem 2rem;">
        <i class="fas fa-calculator"></i> Lancer mon estimation gratuite
      </a>
    </div>
  </div>
</section>

<!-- Schema.org FAQPage -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Cette estimation immobilière en ligne est-elle fiable ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Notre outil donne une indication statistique basée sur les données du marché. Pour fixer un prix de mise en vente précis, il est recommandé de demander un avis de valeur à un conseiller immobilier qui visitera votre bien."
      }
    },
    {
      "@type": "Question",
      "name": "Qu'est-ce qu'un avis de valeur immobilier ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "C'est un document rédigé par un professionnel de l'immobilier (conseiller ou agent immobilier) après visite du bien. Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent."
      }
    },
    {
      "@type": "Question",
      "name": "L'estimation en ligne est-elle gratuite ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, 100% gratuite et sans engagement. Vous obtenez une fourchette indicative en quelques secondes, sans donner vos coordonnées."
      }
    },
    {
      "@type": "Question",
      "name": "Pourquoi les outils d'estimation en ligne ne suffisent pas ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Les outils en ligne utilisent des statistiques générales (prix au m², tendances, historique des ventes). Ils ne voient pas l'état réel du bien, les travaux, la luminosité, la vue ou le voisinage. Seul un professionnel qui se rend sur place peut intégrer ces critères dans un avis de valeur."
      }
    },
    {
      "@type": "Question",
      "name": "Puis-je obtenir un avis de valeur après l'estimation en ligne ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui ! Après votre estimation en ligne, nous vous proposons de demander un avis de valeur réalisé par un conseiller immobilier pour une évaluation complète de votre bien."
      }
    }
  ]
}
</script>
