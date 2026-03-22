<div class="lead-confirmation">
  <section class="lead-success-banner">
    <div class="lead-success-icon">✓</div>
    <h2>Merci, votre demande a bien été enregistrée.</h2>
    <p>Référence lead: <strong>#<?= e((string) $leadId) ?></strong></p>
    <p>Score commercial: <strong class="lead-badge lead-badge--<?= e((string) $temperature) ?>"><?= e((string) $temperature) ?></strong></p>
  </section>

  <section class="card lead-detail-card">
    <h3>Fiche complète du lead</h3>
    <div class="lead-sheet-grid">
      <div class="lead-field">
        <span class="lead-field__label">Nom</span>
        <p class="lead-field__value"><?= e((string) $lead['nom']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Email</span>
        <p class="lead-field__value"><?= e((string) $lead['email']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Téléphone</span>
        <p class="lead-field__value"><?= e((string) $lead['telephone']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Adresse du bien</span>
        <p class="lead-field__value"><?= e((string) $lead['adresse']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Ville</span>
        <p class="lead-field__value"><?= e((string) $lead['ville']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Estimation moyenne</span>
        <p class="lead-field__value lead-field__value--highlight"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Urgence</span>
        <p class="lead-field__value"><?= e((string) $lead['urgence']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Motivation</span>
        <p class="lead-field__value"><?= e((string) $lead['motivation']) ?></p>
      </div>
      <div class="lead-field">
        <span class="lead-field__label">Statut</span>
        <p class="lead-field__value"><?= e((string) $lead['statut']) ?></p>
      </div>
    </div>

    <div class="lead-notes">
      <span class="lead-field__label">Notes</span>
      <p class="lead-field__value"><?= nl2br(e((string) ($lead['notes'] !== '' ? $lead['notes'] : 'Aucune note renseignée.'))) ?></p>
    </div>

    <div class="lead-actions">
      <a href="/estimation" class="btn btn-primary">Faire une nouvelle estimation</a>
    </div>
  </section>
</div>
