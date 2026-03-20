<?php $page_title = 'Estimation Immobilière Bordeaux - Évaluez Votre Bien'; ?>

<!-- ============================================ -->
<!-- HERO + FORMULAIRE -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      <p class="eyebrow"><i class="fas fa-calculator"></i> Estimation immobilière à Bordeaux</p>
      <h1>Estimez la valeur de votre bien immobilier à Bordeaux</h1>
      <p class="lead">Remplissez le formulaire ci-contre pour obtenir une fourchette de prix basée sur les données réelles du marché bordelais. Résultat immédiat, 100% gratuit.</p>

      <?php if (!empty($errors)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
          <?php foreach ($errors as $error): ?>
            <p style="margin: 0; color: #dc2626; font-size: 0.9rem;">
              <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- FORMULAIRE D'ESTIMATION -->
    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2><i class="fas fa-home"></i> Votre bien</h2>
        <p class="muted">Remplissez les caractéristiques pour lancer l'estimation.</p>
      </div>

      <form action="/estimation" method="post" class="form-grid">
        <!-- VILLE -->
        <div class="form-row">
          <label for="ville">
            <span><i class="fas fa-map-marker-alt"></i> Ville</span>
            <input
              type="text"
              id="ville"
              name="ville"
              placeholder="Bordeaux, Talence, Mérignac..."
              required
              autocomplete="off"
            >
          </label>
        </div>

        <!-- TYPE & SURFACE -->
        <div class="form-row">
          <label for="type">
            <span><i class="fas fa-building"></i> Type de bien</span>
            <select id="type" name="type" required>
              <option value="">-- Sélectionner --</option>
              <option value="appartement">Appartement</option>
              <option value="maison">Maison</option>
              <option value="studio">Studio</option>
              <option value="loft">Loft</option>
              <option value="maison de ville">Maison de ville</option>
            </select>
          </label>

          <label for="surface">
            <span><i class="fas fa-ruler-combined"></i> Surface (m²)</span>
            <input
              type="number"
              id="surface"
              name="surface"
              min="5"
              max="10000"
              step="1"
              placeholder="85"
              required
            >
          </label>
        </div>

        <!-- PIÈCES -->
        <div class="form-row">
          <label for="pieces">
            <span><i class="fas fa-door-open"></i> Nombre de pièces</span>
            <input
              type="number"
              id="pieces"
              name="pieces"
              min="1"
              max="50"
              placeholder="3"
              required
            >
          </label>
        </div>

        <!-- BOUTON -->
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 1rem; padding: 1rem;">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <p class="form-footer" style="text-align: center; margin: 1rem 0 0; font-size: 0.8rem; color: var(--muted);">
          <i class="fas fa-check-circle"></i> 100% gratuit •
          <i class="fas fa-clock"></i> Résultat en 1 min •
          <i class="fas fa-lock"></i> Données sécurisées
        </p>
      </form>
    </aside>
  </div>
</section>

<!-- ============================================ -->
<!-- PROCESSUS -->
<!-- ============================================ -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">Notre méthode</p>
      <h2>3 étapes simples pour estimer votre bien</h2>
    </div>
    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">01</div>
        <h3>Renseignez votre bien</h3>
        <p>Ville, type, surface et nombre de pièces suffisent pour lancer l'estimation.</p>
      </article>
      <article class="card step-card">
        <div class="step-number">02</div>
        <h3>Analyse des données</h3>
        <p>Notre moteur analyse les transactions récentes dans votre secteur.</p>
      </article>
      <article class="card step-card">
        <div class="step-number">03</div>
        <h3>Recevez l'estimation</h3>
        <p>Une fourchette de prix détaillée avec prix au m² et analyse du marché.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ -->
<!-- ============================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow"><i class="fas fa-question-circle"></i> Questions fréquentes</p>
      <h2>FAQ Estimation</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation est-elle gratuite ?</h3>
        <p>Oui, 100% gratuite et sans engagement. Aucun frais caché.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> En combien de temps j'obtiens mon résultat ?</h3>
        <p>Le résultat est immédiat après validation du formulaire, en moins de 60 secondes.</p>
      </article>
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je être accompagné ensuite ?</h3>
        <p>Oui. Après l'estimation, vous pouvez laisser vos coordonnées pour un accompagnement personnalisé gratuit.</p>
      </article>
    </div>
  </div>
</section>
