<?php $page_title = 'Résultat de votre estimation - Avis de Valeur Indicatif'; ?>

<!-- ============================================ -->
<!-- RÉSULTAT ESTIMATION - FOURCHETTE 3 PRIX -->
<!-- ============================================ -->
<section class="estimation-result">
  <div class="container">

    <!-- EN-TÊTE RÉSULTAT -->
    <div class="section-heading result-page-heading">
      <p class="eyebrow"><i class="fas fa-chart-bar"></i> Estimation indicative obtenue</p>
      <h1>Votre fourchette de prix à <?= e((string) $estimate['city']) ?></h1>
      <p class="muted result-page-subtitle">
        Voici une estimation statistique basée sur les données du marché pour votre
        <strong><?= e((string) $estimate['property_type']) ?></strong> de
        <strong><?= number_format((float) $estimate['surface'], 0, ',', ' ') ?> m²</strong>.
      </p>
    </div>

    <!-- FOURCHETTE 3 PRIX -->
    <div class="result-layout">
      <article class="card result-summary">
        <div class="kpi-grid">
          <div class="kpi-box kpi-low">
            <p class="kpi-label"><i class="fas fa-arrow-down"></i> Estimation basse</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_low'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_low'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
          <div class="kpi-box kpi-mid">
            <p class="kpi-label"><i class="fas fa-bullseye"></i> Estimation moyenne</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_mid'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_mid'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
          <div class="kpi-box kpi-high">
            <p class="kpi-label"><i class="fas fa-arrow-up"></i> Estimation haute</p>
            <p class="kpi-value"><?= number_format((float) $estimate['estimated_high'], 0, ',', ' ') ?> &euro;</p>
            <p class="kpi-detail"><?= number_format((float) $estimate['per_sqm_high'], 0, ',', ' ') ?> &euro;/m²</p>
          </div>
        </div>

        <!-- AVERTISSEMENT STATISTIQUE -->
        <div class="result-warning">
          <p>
            <i class="fas fa-info-circle"></i>
            <strong>Estimation indicative :</strong> Ces chiffres sont basés sur des <strong>données statistiques</strong> du marché immobilier.
            Ils donnent une indication, mais ne remplacent pas un Avis de Valeur professionnel.
          </p>
        </div>
      </article>
    </div>

  </div>
</section>

<!-- ============================================ -->
<!-- CTA: ESTIMATION PLUS PRÉCISE -->
<!-- ============================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="result-cta-layout">

      <!-- COLONNE GAUCHE: POURQUOI ALLER PLUS LOIN -->
      <div class="result-cta-info">
        <p class="eyebrow">
          <i class="fas fa-user-tie"></i> Aller plus loin
        </p>
        <h2>Complétez avec un avis de valeur</h2>
        <p class="result-cta-description">
          L'estimation que vous venez de recevoir est basée sur des <strong>statistiques</strong> — comme tous les outils en ligne.
          C'est une bonne première indication, mais pour fixer un <strong>prix de mise en vente réaliste</strong>, l'idéal est de la compléter par un <strong>avis de valeur</strong> réalisé par un conseiller immobilier.
        </p>

        <div class="result-benefits-list">
          <h3>Ce qu'apporte un avis de valeur :</h3>
          <ul>
            <li>
              <i class="fas fa-certificate"></i>
              <span>Réalisé par un <strong>conseiller immobilier</strong> connaissant votre quartier</span>
            </li>
            <li>
              <i class="fas fa-eye"></i>
              <span><strong>Visite physique</strong> de votre bien (état, travaux, luminosité, vue...)</span>
            </li>
            <li>
              <i class="fas fa-file-alt"></i>
              <span>Prend en compte l'état, les travaux, la situation, l'environnement et la <strong>demande sur le secteur</strong></span>
            </li>
            <li>
              <i class="fas fa-bullseye"></i>
              <span><strong>Base de travail</strong> pour fixer un prix de mise en vente réaliste</span>
            </li>
          </ul>
        </div>

        <div class="result-tip">
          <p>
            <i class="fas fa-lightbulb"></i>
            <strong>Le saviez-vous ?</strong> Un avis de valeur est rédigé par un professionnel de l'immobilier après visite du bien.
            Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent.
          </p>
        </div>
      </div>

      <!-- COLONNE DROITE: FORMULAIRE CONTACT -->
      <article class="card result-form-card" id="lead-form">
        <div class="form-header">
          <h3>
            <i class="fas fa-handshake"></i>
            Demander un avis de valeur
          </h3>
          <p class="muted">Un conseiller immobilier vous recontacte pour organiser une visite et vous remettre un avis de valeur complet.</p>
        </div>

        <form action="/lead" method="post" class="form-grid form-lead">
          <?php if (!empty($leadErrors ?? [])): ?>
            <div class="form-error-banner">
              <?php foreach (($leadErrors ?? []) as $error): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?= e((string) $error) ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- CHAMPS CACHÉS -->
          <input type="hidden" name="ville" value="<?= e((string) $estimate['city']) ?>">
          <input type="hidden" name="estimation" value="<?= e((string) $estimate['estimated_mid']) ?>">
          <input type="hidden" name="estimation_id" value="<?= (int) ($estimationId ?? 0) ?>">

          <label for="nom">
            <span><i class="fas fa-user"></i> Nom complet *</span>
            <input type="text" id="nom" name="nom" placeholder="Jean Dupont" value="<?= e((string) ($leadOld['nom'] ?? '')) ?>" required>
          </label>

          <label for="email">
            <span><i class="fas fa-envelope"></i> Email *</span>
            <input type="email" id="email" name="email" placeholder="jean@example.com" value="<?= e((string) ($leadOld['email'] ?? '')) ?>" required>
          </label>

          <label for="telephone">
            <span><i class="fas fa-phone"></i> Téléphone *</span>
            <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" value="<?= e((string) ($leadOld['telephone'] ?? '')) ?>" required>
          </label>

          <div class="form-row">
            <label for="urgence">
              <span>Délai souhaité *</span>
              <select id="urgence" name="urgence" required>
                <option value="">-- Sélectionner --</option>
                <option value="rapide" <?= (($leadOld['urgence'] ?? '') === 'rapide') ? 'selected' : '' ?>>Rapide (&lt; 3 mois)</option>
                <option value="moyen" <?= (($leadOld['urgence'] ?? '') === 'moyen') ? 'selected' : '' ?>>Moyen (3-6 mois)</option>
                <option value="long" <?= (($leadOld['urgence'] ?? '') === 'long') ? 'selected' : '' ?>>Pas pressé (6+ mois)</option>
              </select>
            </label>

            <label for="motivation">
              <span>Raison *</span>
              <select id="motivation" name="motivation" required>
                <option value="">-- Sélectionner --</option>
                <option value="vente" <?= (($leadOld['motivation'] ?? '') === 'vente') ? 'selected' : '' ?>>Vente</option>
                <option value="succession" <?= (($leadOld['motivation'] ?? '') === 'succession') ? 'selected' : '' ?>>Succession</option>
                <option value="divorce" <?= (($leadOld['motivation'] ?? '') === 'divorce') ? 'selected' : '' ?>>Séparation</option>
                <option value="investissement" <?= (($leadOld['motivation'] ?? '') === 'investissement') ? 'selected' : '' ?>>Investissement</option>
                <option value="autre" <?= (($leadOld['motivation'] ?? '') === 'autre') ? 'selected' : '' ?>>Autre</option>
              </select>
            </label>
          </div>

          <label for="notes">
            <span><i class="fas fa-comment-dots"></i> Informations complémentaires</span>
            <textarea id="notes" name="notes" rows="3" placeholder="Travaux réalisés, particularités du bien, disponibilités pour la visite…"><?= e((string) ($leadOld['notes'] ?? '')) ?></textarea>
          </label>

          <button type="submit" class="btn btn-primary result-submit-btn">
            <i class="fas fa-certificate"></i> Demander mon avis de valeur
          </button>

          <p class="result-form-legal">
            <i class="fas fa-lock"></i> Vos données sont confidentielles. <a href="/mentions-legales">En savoir plus</a>
          </p>
        </form>
      </article>

    </div>
  </div>
</section>

<?php require __DIR__ . '/../partials/trust-block.php'; ?>

<!-- ============================================ -->
<!-- REFAIRE UNE ESTIMATION -->
<!-- ============================================ -->
<section class="section">
  <div class="container result-restart">
    <p class="muted">Les résultats ne correspondent pas ? Modifiez vos critères.</p>
    <a href="/estimation#form-estimation" class="btn btn-ghost">
      <i class="fas fa-redo"></i> Refaire une estimation
    </a>
  </div>
</section>
