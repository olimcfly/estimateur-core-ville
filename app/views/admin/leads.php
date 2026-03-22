<?php
  $tblExists = $tableExists ?? false;
  $flash = $_SESSION['leads_flash'] ?? null;
  unset($_SESSION['leads_flash']);
  $allLeads = $leads ?? [];
  $totalLeads = count($allLeads);
  $hotLeads = 0;
  $newLeads = 0;
  $today = date('Y-m-d');
  foreach ($allLeads as $l) {
    if (($l['score'] ?? '') === 'chaud') $hotLeads++;
    if (isset($l['created_at']) && str_starts_with($l['created_at'], $today)) $newLeads++;
  }
?>

<style>
  .leads-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
    min-width: 0;
  }

  .leads-page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
  }

  .leads-page-header h1 i {
    color: var(--admin-primary, #8B1538);
  }

  .leads-page-header p {
    color: var(--admin-muted, #6b6459);
    font-size: 0.9rem;
    margin: 0.25rem 0 0;
  }

  .leads-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .leads-stat-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .leads-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .leads-stat-icon.total { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }
  .leads-stat-icon.hot { background: rgba(249,115,22,0.1); color: #f97316; }
  .leads-stat-icon.new { background: rgba(34,197,94,0.1); color: #22c55e; }

  .leads-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--admin-text, #1a1410); line-height: 1; }
  .leads-stat-label { font-size: 0.8rem; color: var(--admin-muted, #6b6459); margin-top: 4px; }

  .leads-table-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
    overflow: hidden;
    max-width: 100%;
  }

  .leads-table-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .leads-table-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--admin-text, #1a1410);
  }

  .leads-admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
  }

  .leads-admin-table thead { background: #f8fafc; }

  .leads-admin-table th {
    padding: 0.6rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted, #6b6459);
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
  }

  .leads-admin-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--admin-text, #1a1410);
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .leads-admin-table tbody tr:hover { background: #f8fafc; }

  .leads-badge-type {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.5rem;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 600;
  }

  .leads-badge-qualifie { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }
  .leads-badge-tendance { background: rgba(100,116,139,0.1); color: #475569; }

  .leads-actions-cell {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    white-space: nowrap;
  }

  .leads-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: #fff;
    color: var(--admin-muted, #6b6459);
    cursor: pointer;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.15s;
  }

  .leads-action-btn:hover { background: #f1f5f9; color: var(--admin-text, #1a1410); }
  .leads-action-btn.view:hover { color: #2563eb; border-color: #93c5fd; background: rgba(59,130,246,0.06); }
  .leads-action-btn.edit:hover { color: #d97706; border-color: #fcd34d; background: rgba(245,158,11,0.06); }
  .leads-action-btn.delete:hover { color: #dc2626; border-color: #fca5a5; background: rgba(239,68,68,0.06); }

  .leads-action-btn[title] { position: relative; }

  .leads-statut-select {
    font-size: 0.72rem;
    padding: 0.2rem 0.35rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 5px;
    background: #fff;
    color: var(--admin-text, #1a1410);
    cursor: pointer;
    max-width: 120px;
  }

  .leads-statut-select:hover { border-color: #93c5fd; }
  .leads-statut-select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }

  .leads-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
  }

  .leads-badge-chaud { background: rgba(249,115,22,0.1); color: #ea580c; }
  .leads-badge-tiede { background: rgba(245,158,11,0.1); color: #d97706; }
  .leads-badge-froid { background: rgba(100,116,139,0.1); color: #475569; }
  .leads-badge-nouveau { background: rgba(59,130,246,0.1); color: #2563eb; }
  .leads-badge-contacte { background: rgba(34,197,94,0.1); color: #16a34a; }
  .leads-badge-converti { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }

  .leads-empty-state {
    text-align: center;
    padding: 4rem 2rem;
  }

  .leads-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(139,21,56,0.08), rgba(212,175,55,0.08));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
  }

  .leads-empty-icon i {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--admin-primary, #8B1538), #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .leads-empty-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    margin: 0 0 0.5rem;
  }

  .leads-empty-desc {
    color: var(--admin-muted, #6b6459);
    font-size: 0.9rem;
    margin: 0 0 1.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.5;
  }

  .leads-empty-steps {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin-top: 2rem;
  }

  .leads-empty-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    max-width: 140px;
  }

  .leads-empty-step-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
  }

  .leads-empty-step-icon.step1 { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }
  .leads-empty-step-icon.step2 { background: rgba(59,130,246,0.1); color: #3b82f6; }
  .leads-empty-step-icon.step3 { background: rgba(34,197,94,0.1); color: #22c55e; }

  .leads-empty-step-text {
    font-size: 0.78rem;
    color: var(--admin-muted, #6b6459);
    text-align: center;
    line-height: 1.4;
  }

  .leads-empty-step-num {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--admin-primary, #8B1538);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .leads-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .leads-link-btn:hover { opacity: 0.9; }

  /* ── View Switcher ── */
  .leads-view-switcher {
    display: inline-flex;
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
  }

  .leads-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.9rem;
    border: none;
    background: transparent;
    color: var(--admin-muted, #6b6459);
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
  }

  .leads-view-btn:not(:last-child) {
    border-right: 1px solid var(--admin-border, #e8dfd7);
  }

  .leads-view-btn:hover {
    background: #f8fafc;
    color: var(--admin-text, #1a1410);
  }

  .leads-view-btn.active {
    background: var(--admin-primary, #8B1538);
    color: #fff;
  }

  /* ── Grille (Cards) View ── */
  .leads-grille-view {
    display: none;
    padding: 1.25rem;
  }

  .leads-grille-view.visible { display: block; }

  .leads-grille-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
  }

  .leads-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 4px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--admin-text, #1a1410);
    margin: 0;
    text-align: right;
  }

  .leads-card-footer {
    border-top: 1px solid var(--admin-border, #e8dfd7);
    padding-top: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--admin-muted, #6b6459);
  }

  /* ── Kanban View ── */
  .leads-kanban-view {
    display: none;
    padding: 1.25rem;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .leads-kanban-view.visible { display: block; }

  .leads-kanban-board {
    display: inline-flex;
    gap: 1rem;
  }

  .leads-kanban-col {
    width: 280px;
    min-width: 280px;
    flex-shrink: 0;
    background: #f8fafc;
    border-radius: var(--admin-radius, 12px);
    display: flex;
    flex-direction: column;
    max-height: 70vh;
  }

  .leads-inline-select:focus {
    outline: none;
    border-color: var(--admin-primary, #8B1538);
  }

  .leads-inline-select.saving {
    opacity: 0.6;
    pointer-events: none;
  }

  .leads-inline-select.saved {
    border-color: #22c55e;
    box-shadow: 0 0 0 1px rgba(34,197,94,0.3);
  }

  .leads-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #1e293b;
    color: #fff;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    animation: leadsSlideUp 0.3s ease;
  }

  .leads-toast.success { border-left: 4px solid #22c55e; }
  .leads-toast.error { border-left: 4px solid #ef4444; }

  @keyframes leadsSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  @media (max-width: 640px) {
    .leads-stats-grid { grid-template-columns: 1fr 1fr; }
    .leads-empty-steps { gap: 1rem; }
    .leads-grille-grid { grid-template-columns: 1fr; }
    .leads-kanban-col { width: 260px; min-width: 260px; }
    .leads-view-btn span.view-label { display: none; }
  }
</style>

<div class="container">

    <?php if ($flash): ?>
      <div style="background: <?= $flash['type'] === 'success' ? '#f0fdf4' : '#fef2f2' ?>; border: 1px solid <?= $flash['type'] === 'success' ? '#86efac' : '#fca5a5' ?>; color: <?= $flash['type'] === 'success' ? '#166534' : '#991b1b' ?>; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($dbError ?? '')): ?>
      <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
        <i class="fas fa-exclamation-triangle"></i> <?= e($dbError) ?>
      </div>
    <?php endif; ?>

    <?php if (!$tblExists): ?>
      <!-- TABLE NOT CREATED STATE -->
      <div class="leads-page-header">
        <div>
          <h1><i class="fas fa-users"></i> Leads</h1>
          <p>Liste des leads enregistrés depuis le formulaire d'estimation.</p>
        </div>
      </div>

      <div class="leads-table-card">
        <div class="leads-empty-state">
          <div class="leads-empty-icon">
            <i class="fas fa-database"></i>
          </div>
          <h2 class="leads-empty-title">Table "leads" non détectée</h2>
          <p class="leads-empty-desc">
            La table <strong>leads</strong> n'existe pas encore dans votre base de données.
            Créez-la automatiquement pour commencer à collecter vos prospects.
          </p>
          <form method="POST" action="/admin/leads/create-table" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="leads-link-btn" style="background: #16a34a;" onclick="return confirm('Créer la table leads dans la base de données ?');">
              <i class="fas fa-magic"></i> Créer la table maintenant
            </button>
          </form>
        </div>
      </div>

    <?php else: ?>

      <!-- PAGE HEADER -->
      <div class="leads-page-header">
        <div>
          <h1><i class="fas fa-users"></i> Leads</h1>
          <p>Liste des leads enregistrés depuis le formulaire d'estimation.</p>
        </div>
        <?php if (!empty($allLeads)): ?>
        <div class="leads-view-switcher">
          <button class="leads-view-btn active" data-view="liste" title="Vue liste">
            <i class="fas fa-list"></i> <span class="view-label">Liste</span>
          </button>
          <button class="leads-view-btn" data-view="grille" title="Vue grille">
            <i class="fas fa-th-large"></i> <span class="view-label">Grille</span>
          </button>
          <button class="leads-view-btn" data-view="kanban" title="Vue kanban">
            <i class="fas fa-columns"></i> <span class="view-label">Kanban</span>
          </button>
        </div>
        <?php endif; ?>
      </div>

      <!-- STATS -->
      <div class="leads-stats-grid">
        <div class="leads-stat-card">
          <div class="leads-stat-icon total"><i class="fas fa-users"></i></div>
          <div>
            <div class="leads-stat-value"><?= $totalLeads ?></div>
            <div class="leads-stat-label">Total Leads</div>
          </div>
        </div>
        <div class="leads-stat-card">
          <div class="leads-stat-icon hot"><i class="fas fa-fire"></i></div>
          <div>
            <div class="leads-stat-value"><?= $hotLeads ?></div>
            <div class="leads-stat-label">Leads chauds</div>
          </div>
        </div>
        <div class="leads-stat-card">
          <div class="leads-stat-icon new"><i class="fas fa-clock"></i></div>
          <div>
            <div class="leads-stat-value"><?= $newLeads ?></div>
            <div class="leads-stat-label">Aujourd'hui</div>
          </div>
        </div>
      </div>

      <!-- TABLE -->
      <div class="leads-table-card">
        <div class="leads-table-header">
          <span class="leads-table-title"><i class="fas fa-list"></i> Liste des leads</span>
          <span style="font-size: 0.8rem; color: var(--admin-muted, #6b6459);"><?= $totalLeads ?> résultat<?= $totalLeads > 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($allLeads)): ?>
          <!-- EMPTY STATE -->
          <div class="leads-empty-state">
            <div class="leads-empty-icon">
              <i class="fas fa-user-plus"></i>
            </div>
            <h2 class="leads-empty-title">Aucun lead pour le moment</h2>
            <p class="leads-empty-desc">
              Les leads apparaitront ici automatiquement lorsque des visiteurs rempliront le formulaire d'estimation sur votre site.
            </p>

            <div class="leads-empty-steps">
              <div class="leads-empty-step">
                <span class="leads-empty-step-num">Étape 1</span>
                <div class="leads-empty-step-icon step1"><i class="fas fa-globe"></i></div>
                <span class="leads-empty-step-text">Un visiteur accède à votre site</span>
              </div>
              <div class="leads-empty-step">
                <span class="leads-empty-step-num">Étape 2</span>
                <div class="leads-empty-step-icon step2"><i class="fas fa-file-alt"></i></div>
                <span class="leads-empty-step-text">Il remplit le formulaire d'estimation</span>
              </div>
              <div class="leads-empty-step">
                <span class="leads-empty-step-num">Étape 3</span>
                <div class="leads-empty-step-icon step3"><i class="fas fa-check-circle"></i></div>
                <span class="leads-empty-step-text">Le lead apparait ici avec son score</span>
              </div>
            </div>

            <div style="margin-top: 2rem;">
              <a href="/" target="_blank" class="leads-link-btn">
                <i class="fas fa-external-link-alt"></i> Voir le formulaire
              </a>
            </div>
          </div>

        <?php else: ?>
          <?php
          $statutLabels = [
            'nouveau' => 'Nouveau',
            'contacte' => 'Contacté',
            'rdv_pris' => 'RDV pris',
            'visite_realisee' => 'Visite réalisée',
            'mandat_simple' => 'Mandat simple',
            'mandat_exclusif' => 'Mandat exclusif',
            'compromis_vente' => 'Compromis',
            'signe' => 'Signé',
            'co_signature_partenaire' => 'Co-signé',
            'assigne_autre' => 'Assigné autre',
          ];
          $csrfToken = htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="table-wrapper" style="overflow-x: auto;">
            <table class="leads-admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Type</th>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Téléphone</th>
                  <th>Adresse</th>
                  <th>Ville</th>
                  <th>Bien</th>
                  <th>Surface</th>
                  <th>Pièces</th>
                  <th>Estimation</th>
                  <th>Urgence</th>
                  <th>Motivation</th>
                  <th>Score</th>
                  <th>Statut</th>
                  <th>Créé le</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($allLeads as $lead): ?>
                  <?php
                    $leadId = (int) $lead['id'];
                    $scoreClass = 'leads-badge-froid';
                    $score = strtolower($lead['score'] ?? '');
                    if ($score === 'chaud') $scoreClass = 'leads-badge-chaud';
                    elseif ($score === 'tiede' || $score === 'tiède') $scoreClass = 'leads-badge-tiede';

                    $statutKey = strtolower($lead['statut'] ?? 'nouveau');
                    $statutClass = 'leads-badge-nouveau';
                    if (in_array($statutKey, ['contacte', 'rdv_pris', 'visite_realisee'], true)) $statutClass = 'leads-badge-contacte';
                    elseif (in_array($statutKey, ['mandat_simple', 'mandat_exclusif', 'compromis_vente', 'signe', 'co_signature_partenaire'], true)) $statutClass = 'leads-badge-converti';

                    $typeClass = ($lead['lead_type'] ?? '') === 'qualifie' ? 'leads-badge-qualifie' : 'leads-badge-tendance';
                    $typeLabel = ($lead['lead_type'] ?? '') === 'qualifie' ? 'Qualifié' : 'Tendance';
                  ?>
                  <tr>
                    <td style="font-weight: 600; color: var(--admin-muted, #6b6459);">#<?= e((string) $lead['id']) ?></td>
                    <td style="font-weight: 500;"><?= e((string) $lead['nom']) ?></td>
                    <td><?= e((string) $lead['email']) ?></td>
                    <td><?= e((string) $lead['telephone']) ?></td>
                    <td><?= e((string) $lead['ville']) ?></td>
                    <td style="font-weight: 600;"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
                    <td><?= e((string) $lead['urgence']) ?></td>
                    <td><?= e((string) $lead['motivation']) ?></td>
                    <td>
                      <select class="leads-inline-select" data-lead-id="<?= (int) $lead['id'] ?>" data-field="score">
                        <option value="chaud" <?= $score === 'chaud' ? 'selected' : '' ?>>chaud</option>
                        <option value="tiede" <?= $score === 'tiede' || $score === 'tiède' ? 'selected' : '' ?>>tiede</option>
                        <option value="froid" <?= $score === 'froid' ? 'selected' : '' ?>>froid</option>
                      </select>
                    </td>
                    <td>
                      <select class="leads-inline-select" data-lead-id="<?= (int) $lead['id'] ?>" data-field="statut">
                        <?php
                          $allStatuts = [
                            'nouveau' => 'Nouveau',
                            'contacte' => 'Contacté',
                            'rdv_pris' => 'RDV Pris',
                            'visite_realisee' => 'Visite Réalisée',
                            'mandat_simple' => 'Mandat Simple',
                            'mandat_exclusif' => 'Mandat Exclusif',
                            'compromis_vente' => 'Compromis',
                            'signe' => 'Signé',
                            'co_signature_partenaire' => 'Co-signature',
                            'assigne_autre' => 'Assigné',
                          ];
                          foreach ($allStatuts as $sKey => $sLabel): ?>
                            <option value="<?= $sKey ?>" <?= $statut === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
                          <?php endforeach; ?>
                      </select>
                    </td>
                    <td style="white-space: nowrap; color: var(--admin-muted, #6b6459); font-size: 0.8rem;"><?= e((string) $lead['created_at']) ?></td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($allLeads as $lead): ?>
                    <?php
                      $scoreClass = 'leads-badge-froid';
                      $score = strtolower($lead['score'] ?? '');
                      if ($score === 'chaud') $scoreClass = 'leads-badge-chaud';
                      elseif ($score === 'tiede' || $score === 'tiède') $scoreClass = 'leads-badge-tiede';

                      $statutClass = 'leads-badge-nouveau';
                      $statut = strtolower($lead['statut'] ?? '');
                      if ($statut === 'contacte' || $statut === 'contacté') $statutClass = 'leads-badge-contacte';
                      elseif ($statut === 'converti') $statutClass = 'leads-badge-converti';
                    ?>
                    <tr>
                      <td style="font-weight: 600; color: var(--admin-muted, #6b6459);">#<?= e((string) $lead['id']) ?></td>
                      <td style="font-weight: 500;"><?= e((string) $lead['nom']) ?></td>
                      <td><?= e((string) $lead['email']) ?></td>
                      <td><?= e((string) $lead['telephone']) ?></td>
                      <td><?= e((string) $lead['ville']) ?></td>
                      <td style="font-weight: 600;"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
                      <td><?= e((string) $lead['urgence']) ?></td>
                      <td><?= e((string) $lead['motivation']) ?></td>
                      <td><span class="leads-badge <?= $scoreClass ?>"><?= e((string) $lead['score']) ?></span></td>
                      <td><span class="leads-badge <?= $statutClass ?>"><?= e((string) $lead['statut']) ?></span></td>
                      <td style="white-space: nowrap; color: var(--admin-muted, #6b6459); font-size: 0.8rem;"><?= e((string) $lead['created_at']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- GRILLE VIEW (cards) -->
          <div class="leads-grille-view">
            <div class="leads-grille-grid">
              <?php foreach ($allLeads as $lead):
                $scoreClass = 'leads-badge-froid';
                $sc = strtolower($lead['score'] ?? '');
                if ($sc === 'chaud') $scoreClass = 'leads-badge-chaud';
                elseif ($sc === 'tiede' || $sc === 'tiède') $scoreClass = 'leads-badge-tiede';

                $statutClass = 'leads-badge-nouveau';
                $st = strtolower($lead['statut'] ?? '');
                if ($st === 'contacte' || $st === 'contacté') $statutClass = 'leads-badge-contacte';
                elseif ($st === 'converti') $statutClass = 'leads-badge-converti';
              ?>
                <div class="leads-card">
                  <div class="leads-card-top">
                    <span class="leads-card-name"><?= e((string) $lead['nom']) ?></span>
                    <span class="leads-card-id">#<?= e((string) $lead['id']) ?></span>
                  </div>
                  <div class="leads-card-badges">
                    <span class="leads-badge <?= $scoreClass ?>"><?= e((string) $lead['score']) ?></span>
                    <span class="leads-badge <?= $statutClass ?>"><?= e((string) $lead['statut']) ?></span>
                  </div>
                  <dl class="leads-card-info">
                    <dt>Ville</dt><dd><?= e((string) $lead['ville']) ?></dd>
                    <dt>Estimation</dt><dd style="font-weight:600;"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</dd>
                    <dt>Urgence</dt><dd><?= e((string) $lead['urgence']) ?></dd>
                    <dt>Motivation</dt><dd><?= e((string) $lead['motivation']) ?></dd>
                  </dl>
                  <div class="leads-card-footer">
                    <span><i class="fas fa-envelope"></i> <?= e((string) $lead['email']) ?></span>
                    <span><?= e((string) $lead['created_at']) ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- KANBAN VIEW (pipeline columns) -->
          <div class="leads-kanban-view">
            <?php
              $kanbanStatuts = [
                'nouveau' => 'Nouveau',
                'contacte' => 'Contacté',
                'rdv_pris' => 'RDV pris',
                'visite_realisee' => 'Visite réalisée',
                'mandat_simple' => 'Mandat simple',
                'mandat_exclusif' => 'Mandat exclusif',
                'compromis_vente' => 'Compromis',
                'signe' => 'Signé',
              ];
              $kanbanData = [];
              foreach ($kanbanStatuts as $key => $label) {
                $kanbanData[$key] = [];
              }
              foreach ($allLeads as $lead) {
                $s = $lead['statut'] ?? 'nouveau';
                if (!isset($kanbanData[$s])) {
                  $kanbanData[$s] = [];
                }
                $kanbanData[$s][] = $lead;
              }
            ?>
            <div class="leads-kanban-board">
              <?php foreach ($kanbanStatuts as $key => $label): ?>
                <div class="leads-kanban-col">
                  <div class="leads-kanban-col-header">
                    <span><?= $label ?></span>
                    <span class="leads-kanban-count"><?= count($kanbanData[$key]) ?></span>
                  </div>
                  <div class="leads-kanban-cards">
                    <?php foreach ($kanbanData[$key] as $lead):
                      $scoreClass = 'leads-badge-froid';
                      $sc = strtolower($lead['score'] ?? '');
                      if ($sc === 'chaud') $scoreClass = 'leads-badge-chaud';
                      elseif ($sc === 'tiede' || $sc === 'tiède') $scoreClass = 'leads-badge-tiede';
                    ?>
                      <div class="leads-kanban-card">
                        <div class="leads-kanban-card-name"><?= e((string) $lead['nom']) ?></div>
                        <div class="leads-kanban-card-detail">
                          <?= e((string) $lead['ville']) ?> · <?= e((string) $lead['type_bien']) ?>
                        </div>
                        <div class="leads-kanban-card-bottom">
                          <span class="leads-kanban-card-est"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</span>
                          <span class="leads-badge <?= $scoreClass ?>" style="font-size:0.68rem;"><?= e((string) $lead['score']) ?></span>
                        </div>
                      </div>
                    <?php endforeach; ?>
                    <?php if (empty($kanbanData[$key])): ?>
                      <div style="text-align:center; padding:1.5rem 0.5rem; color:var(--admin-muted,#6b6459); font-size:0.78rem;">
                        <i class="fas fa-inbox" style="font-size:1.2rem; opacity:0.4; display:block; margin-bottom:0.4rem;"></i>
                        Aucun lead
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

        <?php endif; ?>
      </div>

      <!-- View Switcher JS -->
      <script>
      (function() {
        var saved = localStorage.getItem('leads_view') || 'liste';
        var btns = document.querySelectorAll('.leads-view-btn');
        if (!btns.length) return;

        function switchView(view) {
          var liste = document.querySelector('.leads-liste-view');
          var grille = document.querySelector('.leads-grille-view');
          var kanban = document.querySelector('.leads-kanban-view');
          var title = document.querySelector('.leads-table-title');
          if (!liste) return;

          liste.classList.add('hidden');
          if (grille) grille.classList.remove('visible');
          if (kanban) kanban.classList.remove('visible');

          btns.forEach(function(b) { b.classList.remove('active'); });
          var activeBtn = document.querySelector('.leads-view-btn[data-view="' + view + '"]');
          if (activeBtn) activeBtn.classList.add('active');

          if (view === 'liste') {
            liste.classList.remove('hidden');
            if (title) title.innerHTML = '<i class="fas fa-list"></i> Liste des leads';
          } else if (view === 'grille') {
            if (grille) grille.classList.add('visible');
            if (title) title.innerHTML = '<i class="fas fa-th-large"></i> Grille des leads';
          } else if (view === 'kanban') {
            if (kanban) kanban.classList.add('visible');
            if (title) title.innerHTML = '<i class="fas fa-columns"></i> Pipeline des leads';
          }

          localStorage.setItem('leads_view', view);
        }

        btns.forEach(function(btn) {
          btn.addEventListener('click', function() {
            switchView(this.getAttribute('data-view'));
          });
        });

        // Restore saved view on load
        if (['liste', 'grille', 'kanban'].indexOf(saved) !== -1) {
          switchView(saved);
        }
      })();
      </script>

    <?php endif; ?>
</div>

<!-- Toast notification -->
<div class="leads-toast" id="leadsToast"></div>

<script>
(function() {
  var csrfToken = <?= json_encode(\App\Controllers\AuthController::generateCsrfToken(), JSON_HEX_TAG | JSON_HEX_AMP) ?>;

  function showToast(message, type) {
    var toast = document.getElementById('leadsToast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'leads-toast ' + type;
    toast.style.display = 'block';
    setTimeout(function() { toast.style.display = 'none'; }, 2500);
  }

  function quickUpdate(leadId, field, value, selectEl) {
    selectEl.classList.add('saving');
    var body = 'csrf_token=' + encodeURIComponent(csrfToken)
      + '&id=' + encodeURIComponent(leadId)
      + '&field=' + encodeURIComponent(field)
      + '&value=' + encodeURIComponent(value);

    fetch('/admin/leads/update-inline', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: body
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      selectEl.classList.remove('saving');
      if (data.success) {
        selectEl.classList.add('saved');
        showToast('Lead #' + leadId + ' mis \u00e0 jour', 'success');
        setTimeout(function() { selectEl.classList.remove('saved'); }, 1500);
      } else {
        showToast(data.error || 'Erreur de mise \u00e0 jour', 'error');
      }
    })
    .catch(function() {
      selectEl.classList.remove('saving');
      showToast('Erreur r\u00e9seau', 'error');
    });
  }

  document.querySelectorAll('.leads-inline-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      quickUpdate(this.dataset.leadId, this.dataset.field, this.value, this);
    });
  });
})();
</script>
