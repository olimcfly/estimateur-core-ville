<?php
  $tblExists = $tableExists ?? false;
  $flash = $_SESSION['leads_flash'] ?? null;
  unset($_SESSION['leads_flash']);
?>

<div class="container">
    <div style="margin-bottom: 1.5rem;">
      <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Leads</h1>
      <p style="color: #6b6459; font-size: 0.9rem; margin: 0;">Liste des leads enregistrés depuis le formulaire d'estimation.</p>
    </div>

    <?php if ($flash): ?>
      <div style="background: <?= $flash['type'] === 'success' ? '#f0fdf4' : '#fef2f2' ?>; border: 1px solid <?= $flash['type'] === 'success' ? '#86efac' : '#fca5a5' ?>; color: <?= $flash['type'] === 'success' ? '#166534' : '#991b1b' ?>; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($dbError ?? '')): ?>
      <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <?= e($dbError) ?>
      </div>
    <?php endif; ?>

    <?php if (!$tblExists): ?>
      <div style="background: linear-gradient(135deg, #fefce8, #fef9c3); border: 2px solid #facc15; border-radius: 0.75rem; padding: 2rem; margin-bottom: 2rem; text-align: center;">
        <h2 style="font-size: 1.3rem; font-weight: 700; margin: 0 0 0.75rem; color: #854d0e;">
          <i class="fas fa-database"></i> Table "leads" non detectee
        </h2>
        <p style="color: #713f12; margin: 0 0 1.25rem; font-size: 0.92rem;">
          La table <strong>leads</strong> n'existe pas encore dans votre base de donnees.<br>
          Cliquez sur le bouton ci-dessous pour la creer automatiquement et rendre cette page fonctionnelle.
        </p>
        <form method="POST" action="/admin/leads/create-table" style="display:inline;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" style="background: #16a34a; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-size: 0.95rem; font-weight: 600; cursor: pointer;" onclick="return confirm('Creer la table leads dans la base de donnees ?');">
            <i class="fas fa-magic"></i> Creer la table maintenant
          </button>
        </form>
      </div>
    <?php else: ?>

    <div class="card">
      <div class="table-wrapper">
        <table class="leads-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Ville</th>
              <th>Estimation</th>
              <th>Urgence</th>
              <th>Motivation</th>
              <th>Score</th>
              <th>Statut</th>
              <th>Créé le</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($leads ?? [])): ?>
              <tr>
                <td colspan="11" class="muted">Aucun lead pour le moment.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($leads as $lead): ?>
                <tr>
                  <td><?= e((string) $lead['id']) ?></td>
                  <td><?= e((string) $lead['nom']) ?></td>
                  <td><?= e((string) $lead['email']) ?></td>
                  <td><?= e((string) $lead['telephone']) ?></td>
                  <td><?= e((string) $lead['ville']) ?></td>
                  <td><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
                  <td><?= e((string) $lead['urgence']) ?></td>
                  <td><?= e((string) $lead['motivation']) ?></td>
                  <td><?= e((string) $lead['score']) ?></td>
                  <td><?= e((string) $lead['statut']) ?></td>
                  <td><?= e((string) $lead['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php endif; // end if (!$tblExists) ?>
</div>
