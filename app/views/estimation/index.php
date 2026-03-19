<section class="card">
  <h1>Estimateur immobilier</h1>
  <?php if (!empty($errors ?? [])): ?>
    <div class="alert">
      <?php foreach ($errors as $error): ?>
        <p><?= e($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="/estimation" method="post">
    <label>Ville
      <input type="text" name="ville" required>
    </label>

    <label>Type de bien
      <select name="type_bien" required>
        <option value="Appartement">Appartement</option>
        <option value="Maison">Maison</option>
      </select>
    </label>

    <label>Surface (m²)
      <input type="number" name="surface" min="5" step="0.1" required>
    </label>

    <label>Pièces
      <input type="number" name="pieces" min="1" required>
    </label>

    <button type="submit">Obtenir une estimation</button>
  </form>
</section>
