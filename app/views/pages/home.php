<?php
  $cityName = trim((string) ($city_name ?? site('city', '')));
  if ($cityName === '') {
      $cityName = 'Bordeaux';
  }

  $areaLabel = trim((string) ($area_label ?? ''));
  if ($areaLabel === '') {
      $areaLabel = 'Bordeaux et Métropole';
  }

  $page_title = 'Estimation immobilière Bordeaux Métropole | Maison & Appartement';
  $meta_description = 'Découvrez combien vaut votre maison à Bordeaux et dans la métropole. Estimation gratuite, sans engagement, résultat immédiat basé sur les ventes réelles locales.';
?>

<!-- ============================================ -->
<!-- HERO CAPTURE BORDEAUX -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <p class="eyebrow eyebrow--hero">
        <i class="fas fa-house"></i> 🏠 Page de capture — Bordeaux & Métropole
      </p>

      <h1>Votre maison vaut combien aujourd’hui à Bordeaux ou dans la métropole ?</h1>

      <p class="lead hero-lead">
        Obtenez en quelques clics une estimation basée sur les ventes réelles autour de votre bien :
        Bordeaux, Mérignac, Pessac, Talence, Bègles, Bruges et les communes voisines.
      </p>

      <ul class="trust-list">
        <li><i class="fas fa-check-circle"></i> ✅ Gratuit</li>
        <li><i class="fas fa-check-circle"></i> ✅ Sans engagement</li>
        <li><i class="fas fa-check-circle"></i> ✅ Résultat immédiat</li>
      </ul>

      <div class="hero-actions">
        <a href="#form-estimation" class="btn btn-primary btn-hero-primary">
          <i class="fas fa-bolt"></i> 👉 Lancer mon estimation gratuite
        </a>
      </div>
    </div>

    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2>
          <i class="fas fa-calculator"></i> Estimation gratuite immédiate
        </h2>
        <p class="muted">Renseignez 3 informations pour recevoir votre fourchette de prix.</p>
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
            placeholder="Ex: 85"
            required
          >
        </label>

        <label for="ville">
          <span><i class="fas fa-map-marker-alt"></i> Commune</span>
          <input
            type="text"
            id="ville"
            name="ville"
            placeholder="Bordeaux, Mérignac, Pessac..."
            required
            autocomplete="off"
          >
        </label>
        <input type="hidden" name="pieces" value="3">

        <button type="submit" class="btn btn-primary btn-full btn-pulse">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <p class="form-footer">
          <i class="fas fa-lock"></i> Données confidentielles • Sans engagement
        </p>
      </form>

      <div class="mini-estimator-result" data-mini-estimator-result hidden aria-live="polite">
        <p class="mini-estimator-result__label">Votre fourchette indicative</p>
        <p class="mini-estimator-result__price" data-mini-estimator-price></p>
        <p class="mini-estimator-result__meta" data-mini-estimator-meta></p>
        <a href="/estimation#form-estimation" class="btn btn-primary btn-full">Recevoir un avis de valeur détaillé</a>
      </div>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- LE VRAI PROBLÈME -->
<!-- ============================================ -->
<section class="section section-alt section-premium-light" id="enjeu-prix">
  <div class="container">
    <div class="section-heading">
      <h2>Le vrai problème aujourd’hui</h2>
    </div>

    <div class="card note-card">
      <p>
        À Bordeaux et dans la métropole, le marché n’est plus aussi simple qu’avant.
        D’un quartier à l’autre, d’une rue à l’autre, et parfois même d’un type de bien à l’autre,
        les écarts peuvent être importants.
      </p>
      <p>
        Beaucoup de propriétaires pensent connaître la valeur de leur bien… mais se basent sur des comparaisons imprécises,
        des annonces trop optimistes, ou des prix qui ne reflètent plus vraiment le marché actuel.
      </p>
    </div>

    <div class="comparison-grid">
      <article class="card comparison-card">
        <h3>Résultat :</h3>
        <ul class="comparison-list">
          <li><i class="fas fa-xmark" style="color: var(--danger);"></i> ❌ un prix trop haut qui bloque les visites</li>
          <li><i class="fas fa-xmark" style="color: var(--danger);"></i> ❌ un prix trop bas qui fait perdre de l’argent</li>
          <li><i class="fas fa-xmark" style="color: var(--danger);"></i> ❌ des semaines, parfois des mois, perdus inutilement</li>
        </ul>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CE QUE VOUS OBTENEZ -->
<!-- ============================================ -->
<section class="section section-premium-neutral" id="benefices">
  <div class="container">
    <div class="section-heading">
      <h2>Ce que vous obtenez</h2>
      <p class="lead">Une estimation claire, locale et cohérente avec votre secteur.</p>
    </div>

    <div class="card note-card">
      <p>
        Votre estimation s’appuie sur l’analyse du marché autour de votre bien,
        pour vous donner une base sérieuse avant de vendre.
      </p>
      <ul class="comparison-list">
        <li><i class="fas fa-check" style="color: var(--success);"></i> ✔️ une estimation plus réaliste de la valeur de votre bien</li>
        <li><i class="fas fa-check" style="color: var(--success);"></i> ✔️ une vision plus claire du marché bordelais actuel</li>
        <li><i class="fas fa-check" style="color: var(--success);"></i> ✔️ un meilleur point de départ pour vendre au bon prix</li>
      </ul>
      <p>Le tout, sans engagement et sans pression commerciale.</p>
    </div>

    <p style="text-align:center; margin-top: 1rem;">
      <a href="#form-estimation" class="btn btn-primary"><i class="fas fa-bolt"></i> 👉 Obtenir mon estimation gratuite</a>
    </p>
  </div>
</section>

<!-- ============================================ -->
<!-- COMMENT ÇA MARCHE -->
<!-- ============================================ -->
<section class="section section-alt section-premium-contrast" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <h2>Comment ça marche</h2>
      <p class="lead">Simple, rapide, local.</p>
    </div>

    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">1️⃣</div>
        <h3>Vous renseignez les informations de votre bien</h3>
        <p>En quelques minutes seulement.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">2️⃣</div>
        <h3>Le marché local est analysé</h3>
        <p>À partir des ventes réelles et des éléments de comparaison autour de chez vous.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">3️⃣</div>
        <h3>Vous découvrez votre estimation</h3>
        <p>Immédiatement, avec une base plus fiable pour prendre vos décisions.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- SECTEURS PRIS EN COMPTE -->
<!-- ============================================ -->
<section class="section section-premium-light" id="secteurs">
  <div class="container">
    <div class="section-heading">
      <h2>Secteurs pris en compte</h2>
    </div>

    <div class="card note-card">
      <p><strong>Bordeaux :</strong> Centre, Chartrons, Caudéran, Bastide, Saint-Michel, Nansouty, Bacalan…</p>
      <p><strong>Métropole :</strong> Mérignac, Pessac, Talence, Bègles, Bruges…</p>
    </div>

    <p style="text-align:center; margin-top: 1rem;">
      <a href="#form-estimation" class="btn btn-primary"><i class="fas fa-bolt"></i> 👉 Je découvre mon estimation</a>
    </p>
  </div>
</section>

<!-- ============================================ -->
<!-- POURQUOI MAINTENANT -->
<!-- ============================================ -->
<section class="section section-alt section-premium-contrast" id="pourquoi-maintenant">
  <div class="container">
    <div class="section-heading">
      <h2>Pourquoi faire cette estimation maintenant ?</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3>Vendre au bon prix</h3>
        <p>Évitez les erreurs fréquentes liées à une mauvaise lecture du marché local.</p>
      </article>
      <article class="card faq-card">
        <h3>Gagner du temps</h3>
        <p>Un bien bien positionné attire plus facilement les bons acheteurs.</p>
      </article>
      <article class="card faq-card">
        <h3>Décider avec plus de sérénité</h3>
        <p>Même si vous ne vendez pas tout de suite, vous savez enfin où vous en êtes.</p>
      </article>
    </div>

    <p style="text-align:center; margin-top: 1rem;">
      <a href="#form-estimation" class="btn btn-primary"><i class="fas fa-bolt"></i> 👉 Accéder à mon estimation</a>
    </p>
  </div>
</section>

<!-- ============================================ -->
<!-- CETTE ESTIMATION EST UTILE SI -->
<!-- ============================================ -->
<section class="section section-premium-cta" id="utile-si">
  <div class="container">
    <div class="card cta-final-card">
      <h2 style="margin-bottom: 1.5rem;">Cette estimation est utile si…</h2>

      <div class="faq-grid">
        <article class="card faq-card">
          <h3>Vous pensez vendre prochainement</h3>
          <p>Commencez par vérifier la vraie valeur de votre bien avant de prendre une décision.</p>
        </article>
        <article class="card faq-card">
          <h3>Votre bien est déjà en vente mais ça bloque</h3>
          <p>Le problème vient parfois simplement du positionnement prix.</p>
        </article>
        <article class="card faq-card">
          <h3>Vous êtes juste curieux</h3>
          <p>Vous voulez savoir ce que vaut votre maison aujourd’hui, sans engagement.</p>
        </article>
      </div>

      <a href="#form-estimation" class="btn btn-primary btn-pulse" style="display: inline-flex; margin-top: 1.5rem;">
        <i class="fas fa-bolt"></i> Lancer mon estimation gratuite
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
      "name": "Cette estimation est-elle gratuite ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, elle est gratuite, sans engagement, avec un résultat immédiat."
      }
    },
    {
      "@type": "Question",
      "name": "Sur quelles communes l'estimation s'applique-t-elle ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "L'outil couvre Bordeaux, Mérignac, Pessac, Talence, Bègles, Bruges et les communes voisines."
      }
    },
    {
      "@type": "Question",
      "name": "Pourquoi faire une estimation maintenant ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Pour éviter un mauvais positionnement prix, gagner du temps et décider plus sereinement."
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
      submitBtn.textContent = loading ? 'Calcul en cours...' : 'Obtenir mon estimation gratuite';
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
