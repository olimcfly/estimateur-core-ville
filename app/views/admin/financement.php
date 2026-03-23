<style>
  .admin-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .admin-page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .admin-page-header h1 i { color: var(--admin-primary); }

  .header-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: var(--admin-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-primary:hover { opacity: 0.9; }

  .btn-success {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.7rem 1.5rem;
    background: #22c55e;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-success:hover { opacity: 0.9; }

  .btn-export {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: #D4AF37;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .btn-export:hover { opacity: 0.9; }

  .btn-transmettre {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    border-radius: 5px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #3b82f6;
    background: #fff;
    color: #3b82f6;
    cursor: pointer;
    transition: all 0.15s;
  }

  .btn-transmettre:hover { background: #3b82f6; color: #fff; }

  .courtier-banner {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #93c5fd;
    border-radius: var(--admin-radius);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
    color: #1e40af;
  }

  .courtier-banner i { font-size: 1.2rem; }
  .courtier-banner strong { color: #1e3a8a; }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .stat-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .stat-icon.total { background: rgba(139,21,56,0.1); color: var(--admin-primary); }
  .stat-icon.nouvelle { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .stat-icon.transmise { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .stat-icon.acceptee { background: rgba(34,197,94,0.1); color: #22c55e; }
  .stat-icon.volume { background: rgba(212,175,55,0.1); color: #D4AF37; }

  .stat-info { min-width: 0; }
  .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); line-height: 1; }
  .stat-label { font-size: 0.8rem; color: var(--admin-muted); margin-top: 4px; }

  .filters-bar {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    align-items: center;
  }

  .filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.85rem;
    border: 1px solid var(--admin-border);
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    color: var(--admin-muted);
    background: var(--admin-surface);
    transition: all 0.15s;
  }

  .filter-btn:hover { border-color: var(--admin-primary); color: var(--admin-primary); }
  .filter-btn.active { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }

  .table-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    overflow: hidden;
  }

  .table-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .table-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--admin-text);
  }

  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }

  .admin-table thead { background: #f8fafc; }

  .admin-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    border-bottom: 1px solid var(--admin-border);
  }

  .admin-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--admin-text);
  }

  .admin-table tbody tr:hover { background: #f8fafc; }

  .badge-statut {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    background: rgba(100,116,139,0.1);
    color: #475569;
  }

  .badge-statut.nouvelle { background: rgba(245,158,11,0.1); color: #d97706; }
  .badge-statut.contactee { background: rgba(139,92,246,0.1); color: #7c3aed; }
  .badge-statut.en_cours { background: rgba(59,130,246,0.1); color: #2563eb; }
  .badge-statut.transmise_courtier { background: rgba(6,182,212,0.1); color: #0891b2; }
  .badge-statut.acceptee { background: rgba(34,197,94,0.1); color: #16a34a; }
  .badge-statut.refusee { background: rgba(239,68,68,0.1); color: #dc2626; }
  .badge-statut.annulee { background: rgba(100,116,139,0.1); color: #64748b; }

  .btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    border-radius: 5px;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid var(--admin-border);
    cursor: pointer;
    transition: all 0.15s;
  }

  .btn-edit { background: #fff; color: var(--admin-text); }
  .btn-edit:hover { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }
  .btn-delete { background: #fff; color: #ef4444; border-color: #fecaca; }
  .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

  .actions-cell { display: flex; gap: 0.4rem; }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted);
  }

  .create-table-banner {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: var(--admin-radius);
    padding: 2rem;
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .create-table-banner h2 {
    color: #92400e;
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
  }

  .create-table-banner p {
    color: #78350f;
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
  }

  .flash-message {
    padding: 0.85rem 1.25rem;
    border-radius: var(--admin-radius);
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    font-weight: 500;
  }

  .flash-success { background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
  .flash-error { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }

  @media (max-width: 640px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
  }
</style>

<?php
  $allDemandes = $demandes ?? [];
  $s = $stats ?? [];
  $tblExists = $tableExists ?? false;
  $flash = $_SESSION['financement_flash'] ?? null;
  unset($_SESSION['financement_flash']);
  $sLabels = $statutLabels ?? [];
  $fStatut = $filterStatut ?? null;
  $sCounts = $statutCounts ?? [];
  $tpLabels = $typeProjetLabels ?? [];
  $spLabels = $situationProLabels ?? [];
?>

<!-- FLASH MESSAGE -->
<?php if ($flash): ?>
  <div class="flash-message flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<!-- CREATE TABLE BANNER -->
<?php if (!$tblExists): ?>
  <div class="create-table-banner">
    <h2><i class="fas fa-database"></i> Table "demandes_financement" non detectee</h2>
    <p>La table <strong>demandes_financement</strong> n'existe pas encore dans votre base de donnees.<br>Cliquez sur le bouton ci-dessous pour la creer automatiquement.</p>
    <form method="POST" action="/admin/financement/create-table" style="display:inline;">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit" class="btn-success" onclick="return confirm('Creer la table demandes_financement ?');">
        <i class="fas fa-magic"></i> Creer la table maintenant
      </button>
    </form>
  </div>
<?php else: ?>

<!-- COURTIER BANNER -->
<div class="courtier-banner">
  <i class="fas fa-handshake"></i>
  <span>Partenaire courtier : <strong>2L Courtage</strong> &mdash; Exportez les demandes en CSV pour les transmettre facilement.</span>
</div>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-credit-card"></i> Demandes de Financement</h1>
  <div class="header-actions">
    <a href="/admin/financement/export-courtier" class="btn-export"><i class="fas fa-file-csv"></i> Export 2L Courtage</a>
    <a href="/admin/financement/edit" class="btn-primary"><i class="fas fa-plus"></i> Nouvelle Demande</a>
  </div>
</div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon total"><i class="fas fa-credit-card"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['total'] ?? 0) ?></div>
      <div class="stat-label">Total Demandes</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon nouvelle"><i class="fas fa-exclamation-circle"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['nouvelles'] ?? 0) ?></div>
      <div class="stat-label">Nouvelles</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon transmise"><i class="fas fa-share-square"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['transmises'] ?? 0) ?></div>
      <div class="stat-label">Transmises Courtier</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon acceptee"><i class="fas fa-check-double"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= (int)($s['acceptees'] ?? 0) ?></div>
      <div class="stat-label">Acceptees</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon volume"><i class="fas fa-euro-sign"></i></div>
    <div class="stat-info">
      <div class="stat-value"><?= number_format((float)($s['volume_total'] ?? 0), 0, ',', ' ') ?> &euro;</div>
      <div class="stat-label">Volume Total Prets</div>
    </div>
  </div>
</div>

<!-- FILTERS -->
<div class="filters-bar">
  <a href="/admin/financement" class="filter-btn <?= $fStatut === null ? 'active' : '' ?>">Tous</a>
  <?php foreach ($sLabels as $key => $label): ?>
    <a href="/admin/financement?statut=<?= $key ?>" class="filter-btn <?= $fStatut === $key ? 'active' : '' ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?><?php if (isset($sCounts[$key])): ?> <span style="opacity:0.7;">(<?= (int)$sCounts[$key] ?>)</span><?php endif; ?></a>
  <?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="table-card">
  <div class="table-card-header">
    <span class="table-card-title"><?= count($allDemandes) ?> demande<?= count($allDemandes) > 1 ? 's' : '' ?></span>
  </div>

  <?php if (empty($allDemandes)): ?>
    <div class="empty-state">
      <i class="fas fa-credit-card" style="font-size:2.5rem;margin-bottom:1rem;opacity:0.3;"></i>
      <p>Aucune demande de financement pour le moment.</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">Les demandes des visiteurs apparaitront ici automatiquement.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Demandeur</th>
            <th>Projet</th>
            <th>Montant Pret</th>
            <th>Apport</th>
            <th>Situation</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allDemandes as $d): ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars(trim(($d['prenom'] ?? '') . ' ' . ($d['nom'] ?? '')), ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($d['email'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$d['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if (!empty($d['telephone'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$d['telephone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($tpLabels[$d['type_projet'] ?? ''] ?? $d['type_projet'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($d['type_bien'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$d['type_bien'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if (!empty($d['ville'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= htmlspecialchars((string)$d['ville'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($d['montant_pret_souhaite'])): ?>
                  <strong><?= number_format((float)$d['montant_pret_souhaite'], 0, ',', ' ') ?> &euro;</strong>
                <?php else: ?>
                  <span style="color:var(--admin-muted);">-</span>
                <?php endif; ?>
                <?php if (!empty($d['duree_souhaitee_mois'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= (int)$d['duree_souhaitee_mois'] ?> mois</div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($d['apport_personnel'])): ?>
                  <?= number_format((float)$d['apport_personnel'], 0, ',', ' ') ?> &euro;
                <?php else: ?>
                  <span style="color:var(--admin-muted);">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($spLabels[$d['situation_pro'] ?? ''] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($d['revenus_mensuels'])): ?>
                  <div style="font-size:0.8rem;color:var(--admin-muted);"><?= number_format((float)$d['revenus_mensuels'], 0, ',', ' ') ?> &euro;/mois</div>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge-statut <?= $d['statut'] ?? 'nouvelle' ?>"><?= htmlspecialchars($sLabels[$d['statut'] ?? 'nouvelle'] ?? $d['statut'] ?? 'Nouvelle', ENT_QUOTES, 'UTF-8') ?></span>
                <?php if (!empty($d['date_transmission'])): ?>
                  <div style="font-size:0.75rem;color:var(--admin-muted);margin-top:2px;">Transmise le <?= date('d/m/Y', strtotime((string)$d['date_transmission'])) ?></div>
                <?php endif; ?>
              </td>
              <td style="white-space:nowrap;font-size:0.8rem;color:var(--admin-muted);"><?= date('d/m/Y', strtotime((string)$d['created_at'])) ?></td>
              <td>
                <div class="actions-cell">
                  <?php if (in_array($d['statut'] ?? '', ['nouvelle', 'contactee', 'en_cours'], true)): ?>
                    <form method="POST" action="/admin/financement/transmettre" style="display:inline;" onsubmit="return confirm('Marquer comme transmise a 2L Courtage ?');">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                      <button type="submit" class="btn-transmettre" title="Transmettre a 2L Courtage"><i class="fas fa-share"></i></button>
                    </form>
                  <?php endif; ?>
                  <a href="/admin/financement/edit?id=<?= (int)$d['id'] ?>" class="btn-edit"><i class="fas fa-pen"></i></a>
                  <form method="POST" action="/admin/financement/delete" style="display:inline;" onsubmit="return confirm('Supprimer cette demande ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php endif; ?>
