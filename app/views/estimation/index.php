<?php
$cityName = (string) (
    \App\Core\Config::get('city.name', '') ?: site('city', 'votre ville')
);
$estimationContext = isset($estimationContext) && is_array($estimationContext) ? $estimationContext : [];
$quartiers = isset($estimationContext['quartiers']) && is_array($estimationContext['quartiers']) ? $estimationContext['quartiers'] : [];
?>
<section class="premium-hero premium-hero--estimation">
  <div class="container premium-hero__grid">
    <div class="premium-hero__content">
      <p class="premium-kicker">Page estimation · Conversion principale</p>
      <h1>Estimation immobilière à <?= e($cityName) ?> : rapide, claire, utile</h1>
      <p class="premium-lead">Remplissez le formulaire ci-contre pour obtenir votre fourchette de prix vendeuse et cadrer votre stratégie de mise en vente.</p>
      <ul class="premium-inline-trust">
        <li>Résultat en 60 secondes</li>
        <li>Sans engagement</li>
        <li>Conforme RGPD</li>
      </ul>
      <div class="premium-trust-box">
        <p><strong>Important :</strong> cette estimation est un point de départ fiable pour décider, ajuster et préparer votre vente dans de bonnes conditions.</p>
      </div>
    </div>

    <aside class="premium-card premium-card--form" id="form-estimation">
      <h2>Mon estimation gratuite</h2>
      <p class="premium-muted">3 informations suffisent pour obtenir votre fourchette.</p>

      <?php if (!empty($errors)): ?>
        <div class="premium-alert" role="alert">
          <?php foreach ($errors as $error): ?>
            <p><?= e((string) $error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="/estimation" method="post" class="premium-form">
        <label for="property_type">Type de bien</label>
        <select id="property_type" name="type_bien" required>
          <option value="">Sélectionner</option>
          <option value="appartement">Appartement</option>
          <option value="maison">Maison</option>
          <option value="studio">Studio</option>
          <option value="loft">Loft</option>
        </select>

        <label for="surface">Surface (m²)</label>
        <input id="surface" type="number" name="surface" min="10" max="600" step="1" required>

        <label for="ville">Ville</label>
        <input id="ville" type="text" name="ville" value="<?= e($cityName) ?>" required>

        <?php if ($quartiers !== []): ?>
          <label for="quartier">Quartier (optionnel)</label>
          <select id="quartier" name="quartier">
            <option value="">Sélectionner</option>
            <?php foreach ($quartiers as $quartier): ?>
              <option value="<?= e((string) $quartier) ?>"><?= e((string) $quartier) ?></option>
            <?php endforeach; ?>
          </select>
        <?php endif; ?>

        <input type="hidden" name="pieces" value="3">

        <button class="btn btn-gold" type="submit">Obtenir mon estimation</button>
      </form>

      <p class="premium-form-footer">Vos données restent confidentielles. Aucun engagement commercial imposé.</p>
    </aside>
  </div>
</section>

<section class="section section-alt">
  <div class="container premium-grid-3">
    <article class="premium-card"><h3>Positionner correctement</h3><p>Évitez la surestimation qui bloque les visites et la sous-estimation qui vous fait perdre de la valeur.</p></article>
    <article class="premium-card"><h3>Gagner du temps</h3><p>Un prix cohérent dès le départ attire plus vite les bons profils acquéreurs.</p></article>
    <article class="premium-card"><h3>Décider sereinement</h3><p>Vous avancez avec des repères clairs avant de signer un mandat ou de publier votre annonce.</p></article>
  </div>
</section>
