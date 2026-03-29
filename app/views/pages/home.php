<?php
  $cityName = trim((string) ($city_name ?? site('city', '')));
  if ($cityName === '') {
      $cityName = 'votre ville';
  }

  $areaLabel = trim((string) ($area_label ?? ''));
  if ($areaLabel === '') {
      $areaLabel = $cityName . ' et Métropole';
  }

  $page_title = 'Estimation immobilière ' . $cityName . ' | Prix maison & appartement | ' . site('name', 'Estimateur Immobilier');
  $meta_description = 'Estimez votre bien à ' . $cityName . ' gratuitement. Données locales, expertise terrain, résultat rapide et sans engagement pour vendre au bon prix.';
?>

<!-- ============================================ -->
<!-- HERO PREMIUM + FORMULAIRE -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <p class="eyebrow eyebrow--hero">
        <i class="fas fa-location-dot"></i> Estimation immobilière locale à <?= htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8') ?>
      </p>

      <h1>Connaissez le juste prix de votre bien à <?= htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8') ?> avant de le mettre en vente</h1>

      <p class="lead hero-lead">
        Notre estimation immobilière croise les données réelles du marché et la lecture locale du terrain.
        Vous obtenez une fourchette claire pour vendre au bon prix : ni trop haut, ni trop bas.
      </p>

      <div class="hero-proofbar" aria-label="Preuves de confiance">
        <div class="hero-proofbar__item">
          <strong>+X</strong>
          <span>biens analysés localement</span>
        </div>
        <div class="hero-proofbar__item">
          <strong>2 min</strong>
          <span>pour une première estimation</span>
        </div>
        <div class="hero-proofbar__item">
          <strong>100%</strong>
          <span>gratuit et sans engagement</span>
        </div>
      </div>

      <ul class="trust-list">
        <li>
          <i class="fas fa-circle-check"></i>
          <strong>Données locales actualisées</strong> — prix immobilier par secteur
        </li>
        <li>
          <i class="fas fa-circle-check"></i>
          <strong>Confidentiel</strong> — vos informations restent privées
        </li>
        <li>
          <i class="fas fa-circle-check"></i>
          <strong>Accompagnement humain</strong> — si vous souhaitez affiner ensuite
        </li>
      </ul>

      <div class="testimonial-block">
        <p class="testimonial-label">
          <i class="fas fa-quote-left"></i> Retour propriétaire
        </p>
        <p class="testimonial-quote">
          "J'ai évité de surévaluer mon appartement. J'ai vendu plus vite, avec une stratégie de prix cohérente dès le départ."
        </p>
        <p class="testimonial-author">
          — Propriétaire vendeur • <?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="hero-actions">
        <a href="#form-estimation" class="btn btn-primary btn-hero-primary">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </a>
        <a href="#how-it-works" class="btn btn-ghost btn-hero-secondary">
          <i class="fas fa-info-circle"></i> Voir comment ça fonctionne
        </a>
      </div>
    </div>

    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2>
          <i class="fas fa-calculator"></i> Estimation gratuite immédiate
        </h2>
        <p class="muted">3 informations suffisent pour recevoir une fourchette de prix fiable.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid" id="home-mini-estimator" data-mini-estimator>
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

        <label for="ville">
          <span><i class="fas fa-map-marker-alt"></i> Localité</span>
          <input
            type="text"
            id="ville"
            name="ville"
            placeholder="<?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?>..."
            required
            autocomplete="off"
          >
        </label>
        <input type="hidden" name="pieces" value="3">

        <button type="submit" class="btn btn-primary btn-full btn-pulse">
          <i class="fas fa-bolt"></i> Recevoir ma fourchette de prix
        </button>

        <p class="form-footer">
          <i class="fas fa-lock"></i> Gratuit • Sans engagement • Données confidentielles
        </p>
      </form>

      <div class="mini-estimator-result" data-mini-estimator-result hidden aria-live="polite">
        <p class="mini-estimator-result__label">Votre fourchette indicative</p>
        <p class="mini-estimator-result__price" data-mini-estimator-price></p>
        <p class="mini-estimator-result__meta" data-mini-estimator-meta></p>
        <a href="/estimation#form-estimation" class="btn btn-primary btn-full">Recevoir un avis de valeur détaillé</a>
      </div>

      <div class="hero-benefits">
        <ul class="hero-benefits-list">
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Estimation locale</strong> — basée sur le marché de <?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?></span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Rapide</strong> — premier résultat en quelques instants</span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Fiable</strong> — données + expertise terrain</span>
          </li>
          <li>
            <i class="fas fa-check-circle"></i>
            <span><strong>Flexible</strong> — accompagnement humain en option</span>
          </li>
        </ul>

        <a href="#form-estimation" class="btn btn-primary btn-full btn-pulse">
          <i class="fas fa-bolt"></i> Lancer mon estimation
        </a>
      </div>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- BLOC PROBLÈME -->
<!-- ============================================ -->
<section class="section section-alt section-premium-light" id="enjeu-prix">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-triangle-exclamation"></i> Pourquoi c'est stratégique
      </p>
      <h2>Un mauvais prix au départ peut ralentir ou pénaliser votre vente</h2>
    </div>

    <div class="comparison-grid">
      <article class="card comparison-card">
        <h3 class="comparison-header">
          <i class="fas fa-arrow-up-right-dots" style="color: var(--warning);"></i>
          Prix affiché trop haut
        </h3>
        <p class="muted" style="margin-bottom: 1rem;">
          Le bien attire moins d'acheteurs, reçoit moins de visites et peut rester en ligne plus longtemps.
          Souvent, cela conduit à une baisse de prix tardive et à une négociation plus forte.
        </p>
      </article>

      <article class="card comparison-card comparison-primary">
        <h3 class="comparison-header">
          <i class="fas fa-arrow-down" style="color: var(--primary);"></i>
          Prix affiché trop bas
        </h3>
        <p class="muted" style="margin-bottom: 1rem;">
          Vous vendez potentiellement vite, mais vous laissez une partie de la valeur sur la table.
          En quelques jours, la perte financière peut dépasser largement ce que vous imaginiez.
        </p>
      </article>
    </div>

    <div class="card note-card">
      <p>
        <i class="fas fa-compass" style="color: var(--primary);"></i>
        <strong>Notre objectif :</strong> vous donner une base de prix réaliste à <?= htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8') ?>,
        pour démarrer votre projet de vente avec une stratégie claire et crédible.
      </p>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- BÉNÉFICES -->
<!-- ============================================ -->
<section class="section section-premium-neutral" id="benefices">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-gem"></i> Ce que vous y gagnez
      </p>
      <h2>Une estimation conçue pour décider vite, bien et sereinement</h2>
    </div>

    <div class="steps-grid">
      <article class="card step-card">
        <h3>Un prix cohérent avec votre micro-marché</h3>
        <p>Nous tenons compte des dynamiques de votre zone pour refléter la réalité du prix immobilier local.</p>
      </article>

      <article class="card step-card">
        <h3>Un vrai gain de temps</h3>
        <p>En quelques minutes, vous obtenez une base solide pour cadrer votre projet de vente.</p>
      </article>

      <article class="card step-card">
        <h3>Une meilleure marge de négociation</h3>
        <p>Un prix juste inspire confiance aux acheteurs et protège mieux vos intérêts.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- COMMENT ÇA MARCHE -->
<!-- ============================================ -->
<section class="section section-alt section-premium-contrast" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-bolt"></i> Simple et rapide
      </p>
      <h2>Comment estimer votre bien en 3 étapes</h2>
    </div>

    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">1</div>
        <h3>Décrivez votre bien</h3>
        <p>Type, surface et localité : nous allons à l'essentiel.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">2</div>
        <h3>Recevez votre fourchette</h3>
        <p>Vous obtenez immédiatement une estimation basse, médiane et haute.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">3</div>
        <h3>Affinez avec un expert local</h3>
        <p>Si besoin, un conseiller peut ajuster l'analyse selon l'état réel et les atouts de votre bien.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- DIFFÉRENCIATION + PREUVES -->
<!-- ============================================ -->
<section class="section section-premium-light" id="differenciation">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-shield-halved"></i> Fiabilité
      </p>
      <h2>Pourquoi notre estimation va plus loin qu'un simple simulateur générique</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-database"></i> Données de marché locales</h3>
        <p>Nous analysons des références ciblées sur <?= htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8') ?>, pas une moyenne nationale trop large.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-map"></i> Lecture quartier par quartier</h3>
        <p>Un même type de bien peut varier fortement selon la rue, l'environnement et la demande locale.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-user-tie"></i> Expertise humaine disponible</h3>
        <p>Vous pouvez compléter l'estimation en ligne avec un avis de valeur pour sécuriser votre prix de mise en vente.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-lock"></i> Confidentialité et transparence</h3>
        <p>Vos données sont protégées et utilisées uniquement pour votre estimation immobilière.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ -->
<!-- ============================================ -->
<section class="section section-alt section-premium-contrast" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-comments"></i> Questions fréquentes
      </p>
      <h2>Ce que les propriétaires nous demandent le plus</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation est-elle vraiment gratuite ?</h3>
        <p>Oui. Votre estimation est 100% gratuite, sans abonnement et sans engagement.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Combien de temps faut-il ?</h3>
        <p>Quelques minutes suffisent pour obtenir une première fourchette de prix.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Est-ce fiable pour vendre ?</h3>
        <p>Oui, c'est une base solide. Pour fixer un prix final très précis, un avis de valeur sur place reste recommandé.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Maison et appartement sont-ils pris en compte ?</h3>
        <p>Oui, notre outil couvre l'estimation maison et l'estimation appartement selon les caractéristiques renseignées.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Mes données sont-elles protégées ?</h3>
        <p>Oui. Les informations communiquées restent confidentielles et sécurisées.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL -->
<!-- ============================================ -->
<section class="section section-premium-cta">
  <div class="container">
    <div class="card cta-final-card">
      <p class="eyebrow" style="margin-bottom: 1rem;">
        <i class="fas fa-calculator"></i> Dernière étape
      </p>
      <h2 style="margin-bottom: 1rem; font-size: 2rem;">
        Obtenez votre estimation immobilière à <?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?> et fixez un prix de vente crédible
      </h2>
      <p class="lead" style="max-width: 600px; margin: 0 auto 2rem;">
        Rapide, locale, sans engagement : prenez une décision éclairée avant de mettre votre bien sur le marché.
      </p>
      <a href="#form-estimation" class="btn btn-primary btn-pulse" style="display: inline-flex; font-size: 1.1rem; padding: 1.2rem 2rem;">
        <i class="fas fa-bolt"></i> Estimer mon bien maintenant
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
      "name": "L'estimation immobilière est-elle vraiment gratuite ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui. Votre estimation est 100% gratuite, sans abonnement et sans engagement."
      }
    },
    {
      "@type": "Question",
      "name": "Combien de temps faut-il pour obtenir une estimation ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Quelques minutes suffisent pour obtenir une première fourchette de prix."
      }
    },
    {
      "@type": "Question",
      "name": "Cette estimation est-elle fiable pour vendre ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, c'est une base solide. Pour fixer un prix final très précis, un avis de valeur sur place reste recommandé."
      }
    },
    {
      "@type": "Question",
      "name": "Maison et appartement sont-ils pris en compte ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, notre outil couvre l'estimation maison et l'estimation appartement selon les caractéristiques renseignées."
      }
    },
    {
      "@type": "Question",
      "name": "Mes données sont-elles protégées ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui. Les informations communiquées restent confidentielles et sécurisées."
      }
    }
  ]
}
</script>

<script>
  (function() {
    const form = document.querySelector('[data-mini-estimator]');
    const resultBox = document.querySelector('[data-mini-estimator-result]');
    const priceEl = document.querySelector('[data-mini-estimator-price]');
    const metaEl = document.querySelector('[data-mini-estimator-meta]');
    if (!form || !resultBox || !priceEl || !metaEl) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const currencyFormatter = new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0
    });

    function setLoadingState(loading) {
      if (!submitBtn) return;
      submitBtn.disabled = loading;
      submitBtn.setAttribute('aria-busy', loading ? 'true' : 'false');
      submitBtn.textContent = loading ? 'Calcul en cours...' : 'Recevoir ma fourchette de prix';
    }

    form.addEventListener('submit', async function(event) {
      event.preventDefault();
      setLoadingState(true);
      resultBox.hidden = true;

      try {
        const payload = new FormData(form);
        const response = await fetch('/api/estimation', {
          method: 'POST',
          headers: {
            'Accept': 'application/json'
          },
          body: payload
        });
        const data = await response.json();
        if (!response.ok || !data || !data.success || !data.data) {
          throw new Error((data && data.error) ? data.error : 'Estimation indisponible');
        }

        const estimatedLow = Number(data.data.estimated_low || 0);
        const estimatedHigh = Number(data.data.estimated_high || 0);
        const perSqmMid = Number(data.data.per_sqm_mid || 0);
        priceEl.textContent = currencyFormatter.format(estimatedLow) + ' – ' + currencyFormatter.format(estimatedHigh);
        metaEl.textContent = 'Base de calcul : ' + currencyFormatter.format(perSqmMid) + ' / m² (indicatif).';
        resultBox.hidden = false;
      } catch (error) {
        priceEl.textContent = 'Impossible de calculer pour le moment.';
        metaEl.textContent = 'Vous pouvez continuer vers le formulaire complet pour obtenir un avis détaillé.';
        resultBox.hidden = false;
      } finally {
        setLoadingState(false);
      }
    });
  })();
</script>
