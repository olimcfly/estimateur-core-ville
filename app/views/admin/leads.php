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
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
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
    font-size: 0.85rem;
  }

  .leads-admin-table thead { background: #f8fafc; }

  .leads-admin-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--admin-muted, #6b6459);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
  }

  .leads-admin-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: var(--admin-text, #1a1410);
  }

  .leads-admin-table tbody tr:hover { background: #f8fafc; }

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

  .leads-inline-select {
    padding: 0.25rem 0.4rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 4px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--admin-text, #1a1410);
    background: #fff;
    cursor: pointer;
    transition: border-color 0.15s;
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
          <div class="table-wrapper" style="overflow-x: auto;">
            <table class="leads-admin-table">
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
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

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
