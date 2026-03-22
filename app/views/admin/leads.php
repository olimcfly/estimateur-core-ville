<div class="container">
    <div style="margin-bottom: 1.5rem;">
      <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Leads</h1>
      <p style="color: #6b6459; font-size: 0.9rem; margin: 0;">Liste des leads enregistrés depuis le formulaire d'estimation.</p>
    </div>

    <?php if (!empty($flash ?? null)): ?>
      <div style="background: <?= ($flash['type'] ?? '') === 'success' ? '#f0fdf4' : '#fef2f2' ?>; border: 1px solid <?= ($flash['type'] ?? '') === 'success' ? '#86efac' : '#fca5a5' ?>; color: <?= ($flash['type'] ?? '') === 'success' ? '#166534' : '#991b1b' ?>; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <?= e($flash['message'] ?? '') ?>
      </div>
    <?php endif; ?>

    <?php if (!($dbDiag['connected'] ?? false)): ?>
      <!-- DB not connected -->
      <div class="card" style="border: 2px solid #ef4444; margin-bottom: 1.5rem;">
        <div style="padding: 1.5rem;">
          <h2 style="font-size: 1.1rem; font-weight: 700; color: #991b1b; margin: 0 0 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            <span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 50%;"></span>
            Connexion base de données impossible
          </h2>
          <p style="color: #6b7280; margin: 0 0 0.5rem;">
            Vérifiez les paramètres de connexion dans le fichier <code>.env</code> :
            <code>DB_HOST</code>, <code>DB_PORT</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code>.
          </p>
          <p style="color: #6b7280; margin: 0;">
            Assurez-vous que le serveur MySQL/MariaDB est démarré et accessible.
          </p>
        </div>
      </div>

    <?php elseif (!($allTablesOk ?? false)): ?>
      <!-- DB connected but tables/columns missing -->
      <div class="card" style="border: 2px solid #f59e0b; margin-bottom: 1.5rem;">
        <div style="padding: 1.5rem;">
          <h2 style="font-size: 1.1rem; font-weight: 700; color: #92400e; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span style="display: inline-block; width: 12px; height: 12px; background: #f59e0b; border-radius: 50%;"></span>
            Diagnostic des tables
          </h2>

          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <span style="display: inline-block; width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></span>
            <span style="color: #166534; font-size: 0.85rem; font-weight: 600;">Connexion à la base de données : OK</span>
          </div>

          <?php
            $tablesToCreate = [];
            foreach (($dbDiag['tables'] ?? []) as $tableName => $tableInfo):
          ?>
            <div style="background: <?= $tableInfo['exists'] && $tableInfo['columns_ok'] ? '#f0fdf4' : ($tableInfo['exists'] ? '#fffbeb' : '#fef2f2') ?>; border: 1px solid <?= $tableInfo['exists'] && $tableInfo['columns_ok'] ? '#86efac' : ($tableInfo['exists'] ? '#fcd34d' : '#fca5a5') ?>; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <?php if ($tableInfo['exists'] && $tableInfo['columns_ok']): ?>
                  <span style="display: inline-block; width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></span>
                  <strong style="color: #166534;"><?= e($tableName) ?></strong>
                  <span style="color: #166534; font-size: 0.8rem;">— OK</span>
                <?php elseif ($tableInfo['exists']): ?>
                  <span style="display: inline-block; width: 10px; height: 10px; background: #f59e0b; border-radius: 50%;"></span>
                  <strong style="color: #92400e;"><?= e($tableName) ?></strong>
                  <span style="color: #92400e; font-size: 0.8rem;">— Table existe, colonnes manquantes :
                    <code><?= e(implode(', ', $tableInfo['missing_columns'])) ?></code>
                  </span>
                  <?php $tablesToCreate[] = $tableName; ?>
                <?php else: ?>
                  <span style="display: inline-block; width: 10px; height: 10px; background: #ef4444; border-radius: 50%;"></span>
                  <strong style="color: #991b1b;"><?= e($tableName) ?></strong>
                  <span style="color: #991b1b; font-size: 0.8rem;">— Table manquante</span>
                  <?php $tablesToCreate[] = $tableName; ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (!empty($tablesToCreate)): ?>
            <form method="POST" action="/admin/leads/create-tables" style="margin-top: 1rem;">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
              <?php foreach ($tablesToCreate as $t): ?>
                <input type="hidden" name="tables[]" value="<?= e($t) ?>">
              <?php endforeach; ?>
              <button type="submit" style="background: #2563eb; color: #fff; border: none; padding: 0.6rem 1.5rem; border-radius: 0.375rem; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                Créer / Réparer les tables manquantes (<?= count($tablesToCreate) ?>)
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>

    <?php else: ?>
      <!-- All OK -->
      <div style="background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
        <span style="display: inline-block; width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></span>
        Base de données connectée — toutes les tables et colonnes sont OK.
      </div>
    <?php endif; ?>

    <?php if (!empty($dbError ?? '')): ?>
      <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <?= e($dbError) ?>
      </div>
    <?php endif; ?>

    <?php if ($allTablesOk ?? false): ?>
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
    <?php endif; ?>
</div>
