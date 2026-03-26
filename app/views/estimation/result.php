<?php $page_title = 'Résultat de votre estimation - Avis de Valeur Indicatif'; ?>
<?php $accentColor = (string) ($siteConfig['color_accent'] ?? '#8B1538'); ?>
<?php $advisorName = (string) ($siteConfig['advisor_name'] ?? 'Notre conseiller'); ?>
<?php $cityName = (string) ($estimate['city'] ?? ($siteConfig['ville'] ?? 'votre ville')); ?>

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

        <div class="result-popup-action">
          <button type="button" class="btn btn-secondary" id="openLeadPopupButton">
            Affiner mon estimation
          </button>
        </div>
      </article>
    </div>

  </div>
</section>

<div
  id="leadPopupOverlay"
  class="lead-popup-overlay"
  aria-hidden="true"
  data-ville="<?= e($cityName) ?>"
  data-type-bien="<?= e((string) ($estimate['property_type'] ?? '')) ?>"
  data-surface="<?= e((string) ($estimate['surface'] ?? '')) ?>"
  data-pieces="<?= e((string) ($estimate['rooms'] ?? '')) ?>"
  data-estimation-moyenne="<?= e((string) ($estimate['estimated_mid'] ?? '')) ?>"
>
  <div class="lead-popup" role="dialog" aria-modal="true" aria-labelledby="leadPopupTitle">
    <button type="button" class="lead-popup-close" id="closeLeadPopupButton" aria-label="Fermer">&times;</button>
    <h3 id="leadPopupTitle">Recevez votre estimation précise à <?= e($cityName) ?></h3>

    <form id="leadPopupForm" novalidate>
      <label for="popup-prenom">Prénom *</label>
      <input type="text" id="popup-prenom" name="prenom" required>

      <label for="popup-email">Email *</label>
      <input type="email" id="popup-email" name="email" required>

      <label for="popup-telephone">Téléphone *</label>
      <input type="tel" id="popup-telephone" name="telephone" placeholder="06 12 34 56 78" required>
      <p class="popup-inline-error" id="popupTelephoneError" aria-live="polite"></p>

      <button type="submit" class="lead-popup-submit">Recevoir mon rapport complet</button>
      <p class="lead-popup-copy"><?= e($advisorName) ?> vous contactera sous 24h pour affiner cette estimation</p>
      <p class="popup-inline-success" id="popupSuccessMessage" aria-live="polite"></p>
      <p class="popup-inline-error" id="popupGlobalError" aria-live="polite"></p>
    </form>
  </div>
</div>

<style>
  .result-popup-action { margin-top: 1.5rem; text-align: center; }
  .result-popup-action .btn {
    border: 1px solid var(--line);
    background: #fff;
    color: var(--ink);
  }
  .lead-popup-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    z-index: 2000;
  }
  .lead-popup-overlay.is-open { display: flex; }
  .lead-popup {
    width: 100%;
    max-width: 400px;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    position: relative;
    box-shadow: 0 24px 56px rgba(0, 0, 0, 0.25);
  }
  .lead-popup h3 { margin: 0 0 1rem; font-size: 1.3rem; line-height: 1.3; }
  .lead-popup-close {
    position: absolute;
    top: 12px;
    right: 12px;
    border: none;
    background: transparent;
    font-size: 1.6rem;
    line-height: 1;
    cursor: pointer;
    color: #333;
  }
  #leadPopupForm { display: grid; gap: 0.7rem; }
  #leadPopupForm label { font-size: 0.9rem; font-weight: 600; }
  #leadPopupForm input {
    width: 100%;
    border: 1px solid #d8d8d8;
    border-radius: 10px;
    padding: 0.75rem 0.8rem;
    font-size: 1rem;
  }
  .lead-popup-submit {
    margin-top: 0.35rem;
    border: none;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    background: <?= e($accentColor) ?>;
    cursor: pointer;
  }
  .lead-popup-submit:disabled { opacity: 0.65; cursor: not-allowed; }
  .lead-popup-copy { margin: 0.25rem 0 0; font-size: 0.9rem; color: #565656; }
  .popup-inline-error { margin: 0; font-size: 0.85rem; color: #c62828; min-height: 1.1em; }
  .popup-inline-success { margin: 0; font-size: 0.85rem; color: #2e7d32; min-height: 1.1em; }
  @media (max-width: 480px) {
    .lead-popup { padding: 24px 18px; border-radius: 14px; }
  }
</style>

<script>
  (function () {
    var overlay = document.getElementById('leadPopupOverlay');
    var openButton = document.getElementById('openLeadPopupButton');
    var closeButton = document.getElementById('closeLeadPopupButton');
    var form = document.getElementById('leadPopupForm');
    if (!overlay || !openButton || !closeButton || !form) return;

    var telInput = document.getElementById('popup-telephone');
    var telError = document.getElementById('popupTelephoneError');
    var globalError = document.getElementById('popupGlobalError');
    var successMessage = document.getElementById('popupSuccessMessage');
    var submitButton = form.querySelector('.lead-popup-submit');
    var hasSubmitted = false;

    function openPopup() {
      overlay.classList.add('is-open');
      overlay.setAttribute('aria-hidden', 'false');
    }

    function closePopup() {
      overlay.classList.remove('is-open');
      overlay.setAttribute('aria-hidden', 'true');
    }

    setTimeout(function () {
      if (!hasSubmitted) {
        openPopup();
      }
    }, 4000);

    openButton.addEventListener('click', openPopup);
    closeButton.addEventListener('click', closePopup);

    overlay.addEventListener('click', function (event) {
      if (event.target === overlay) {
        closePopup();
      }
    });

    window.addEventListener('scroll', function () {
      if (hasSubmitted || overlay.classList.contains('is-open')) return;
      var scrollTop = window.scrollY || window.pageYOffset || 0;
      var documentHeight = document.documentElement.scrollHeight - window.innerHeight;
      if (documentHeight > 0 && (scrollTop / documentHeight) > 0.5) {
        openPopup();
      }
    }, { passive: true });

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      telError.textContent = '';
      globalError.textContent = '';
      successMessage.textContent = '';

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      var telephoneClean = (telInput.value || '').replace(/\s+/g, '');
      var phoneRegex = /^(0|\+33)[1-9][0-9]{8}$/;
      if (!phoneRegex.test(telephoneClean)) {
        telError.textContent = 'Numéro de téléphone invalide.';
        telInput.focus();
        return;
      }

      submitButton.disabled = true;

      fetch('/api/leads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          prenom: form.prenom.value.trim(),
          email: form.email.value.trim(),
          telephone: telephoneClean,
          ville: overlay.dataset.ville || '',
          type_bien: overlay.dataset.typeBien || '',
          surface: overlay.dataset.surface || '',
          pieces: overlay.dataset.pieces || '',
          estimation_moyenne: overlay.dataset.estimationMoyenne || '',
          source: 'estimation_popup'
        })
      })
      .then(function (response) { return response.json(); })
      .then(function (payload) {
        if (!payload || !payload.success) {
          throw new Error((payload && payload.error) ? payload.error : 'Envoi impossible.');
        }
        hasSubmitted = true;
        successMessage.textContent = 'Merci, votre demande a bien été envoyée.';
        setTimeout(closePopup, 1200);
      })
      .catch(function (error) {
        globalError.textContent = error.message || 'Une erreur est survenue.';
      })
      .finally(function () {
        submitButton.disabled = false;
      });
    });
  })();
</script>

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
