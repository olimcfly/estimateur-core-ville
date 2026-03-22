<div class="lead-confirmation">
  <section class="lead-success-banner">
    <div class="lead-success-icon">✓</div>
    <h2>Merci, votre demande a bien été enregistrée !</h2>
    <p>Votre référence : <strong>#<?= e((string) $leadId) ?></strong></p>
  </section>

  <section class="card lead-thankyou-card">
    <h3>Et maintenant ?</h3>
    <div class="lead-steps">
      <div class="lead-step">
        <div class="lead-step__icon"><i class="fas fa-phone-alt"></i></div>
        <div>
          <p class="lead-step__title">Un conseiller vous recontacte</p>
          <p class="lead-step__desc">Nous avons bien reçu votre demande. Un conseiller immobilier vous contactera sous <strong>48 heures</strong> pour organiser un rendez-vous.</p>
        </div>
      </div>
      <div class="lead-step">
        <div class="lead-step__icon"><i class="fas fa-home"></i></div>
        <div>
          <p class="lead-step__title">Visite de votre bien</p>
          <p class="lead-step__desc">Le conseiller se déplace chez vous pour évaluer votre bien en prenant en compte ses caractéristiques réelles.</p>
        </div>
      </div>
      <div class="lead-step">
        <div class="lead-step__icon"><i class="fas fa-file-alt"></i></div>
        <div>
          <p class="lead-step__title">Remise de votre avis de valeur</p>
          <p class="lead-step__desc">Vous recevez un avis de valeur détaillé, basé sur le marché local et l'état de votre bien.</p>
        </div>
      </div>
    </div>

    <div class="lead-thankyou-summary">
      <p><strong>Récapitulatif de votre demande</strong></p>
      <p><?= e((string) $lead['nom']) ?> &mdash; <?= e((string) $lead['ville']) ?> &mdash; <?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</p>
    </div>

    <div class="lead-actions">
      <a href="/" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
      <a href="/estimation" class="btn btn-ghost"><i class="fas fa-redo"></i> Nouvelle estimation</a>
    </div>
  </section>
</div>
