<?php
$cityName = (string) (\App\Core\Config::get('city.name', '') ?: 'votre ville');
$page_title = 'Estimation immobilière vendeur à ' . $cityName;
$meta_description = 'Formulaire d\'estimation immobilière ultra simple pour propriétaires vendeurs à ' . $cityName . '.';
?>
<section class="premium-hero">
  <div class="container premium-hero__grid">
    <div>
      <h1>Combien vaut votre bien à <?= e($cityName) ?> ?</h1>
      <p>Réponse immédiate, sans engagement. Conçu pour les propriétaires vendeurs.</p>
      <ul>
        <li>Formulaire en 60 secondes</li>
        <li>Lecture du marché local</li>
        <li>Données confidentielles</li>
      </ul>
    </div>
    <aside class="premium-card" id="form-estimation">
      <h2>Mon estimation gratuite</h2>
      <?php if (!empty($errors)): ?><p><?= e(implode(' ', array_map('strval', $errors))) ?></p><?php endif; ?>
      <form action="/estimation" method="post" class="premium-form">
        <label>Type de bien
          <select name="type_bien" required>
            <option value="">Sélectionner</option>
            <option value="appartement">Appartement</option>
            <option value="maison">Maison</option>
            <option value="loft">Loft</option>
          </select>
        </label>
        <label>Surface (m²)<input type="number" name="surface" min="10" max="600" required></label>
        <label>Ville<input type="text" name="ville" value="<?= e($cityName) ?>" required></label>
        <button class="btn btn-gold" type="submit">Obtenir mon estimation</button>
      </form>
      <p><small>Sans frais · Sans engagement · Conforme RGPD</small></p>
    </aside>
  </div>
</section>
