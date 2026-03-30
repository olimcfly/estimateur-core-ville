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
      <h1>Obtenez une estimation fiable de votre bien à <?= e($cityName) ?></h1>
      <p class="premium-lead">Basée sur les ventes réelles et les tendances actuelles du marché local.</p>
      <ul class="premium-inline-trust">
        <li>✔ Gratuit</li>
        <li>✔ Sans engagement</li>
        <li>✔ Données confidentielles</li>
        <li>✔ Résultat immédiat</li>
      </ul>
      <div class="premium-trust-box">
        <p><strong>Remplissez simplement les informations ci-dessous.</strong><br>Cela ne prend que quelques secondes.</p>
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
        <label for="adresse">Adresse du bien</label>
        <input id="adresse" type="text" name="adresse" placeholder="Ex : 12 rue des Lilas, <?= e($cityName) ?>">
        <small class="premium-help">Pour localiser précisément votre bien</small>

        <label for="property_type">Type de bien</label>
        <select id="property_type" name="type_bien" required>
          <option value="">Sélectionner</option>
          <option value="appartement">Appartement</option>
          <option value="maison">Maison</option>
          <option value="studio">Studio</option>
          <option value="loft">Loft</option>
        </select>
        <small class="premium-help">Maison, appartement…</small>

        <label for="surface">Surface (m²)</label>
        <input id="surface" type="number" name="surface" min="10" max="600" step="1" required>
        <small class="premium-help">Approximation suffisante</small>

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

        <label for="projet">Votre projet</label>
        <select id="projet" name="projet">
          <option value="">Sélectionner</option>
          <option value="vendre-rapidement">Vendre rapidement</option>
          <option value="vendre-6-mois">Vendre dans les 6 mois</option>
          <option value="vendre-1-an">Vendre dans l'année</option>
          <option value="simple-repere">Obtenir un repère de prix</option>
        </select>
        <small class="premium-help">Pour adapter l’estimation</small>

        <input type="hidden" name="pieces" value="3">

        <button class="btn btn-gold" type="submit">Obtenir mon estimation</button>
      </form>

      <p class="premium-form-footer">Vos données restent confidentielles. Aucun engagement commercial imposé.</p>
      <div class="premium-after-form">
        <p><strong>Votre estimation est prête.</strong><br>Souhaitez-vous aller plus loin avec une analyse personnalisée ?</p>
        <a class="btn btn-secondary" href="/contact">👉 Être recontacté</a>
      </div>
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
