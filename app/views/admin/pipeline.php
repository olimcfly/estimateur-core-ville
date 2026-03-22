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

  .header-actions {
    display: flex;
    gap: 0.5rem;
  }

  .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid var(--admin-border);
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
    transition: all 0.15s;
  }

  .btn-action:hover {
    background: var(--admin-primary);
    color: #fff;
    border-color: var(--admin-primary);
  }

  /* Pipeline summary stats */
  .pipeline-summary {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }

  .pipeline-summary-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: var(--admin-radius);
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--admin-text);
  }

  .pipeline-summary-item .count {
    font-weight: 700;
    font-size: 1rem;
  }

  .pipeline-summary-item.hot .count { color: #ef4444; }
  .pipeline-summary-item.warm .count { color: #f59e0b; }
  .pipeline-summary-item.cold .count { color: #64748b; }

  /* Kanban board */
  .kanban-board {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: 60vh;
    align-items: flex-start;
  }

  .kanban-column {
    min-width: 260px;
    max-width: 280px;
    flex-shrink: 0;
    background: #f1f5f9;
    border-radius: var(--admin-radius);
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 200px);
  }

  .kanban-column-header {
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 3px solid;
    border-radius: var(--admin-radius) var(--admin-radius) 0 0;
    background: var(--admin-surface);
    position: sticky;
    top: 0;
    z-index: 2;
  }

  .kanban-column-title {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .kanban-column-count {
    background: rgba(0,0,0,0.08);
    color: var(--admin-text);
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
  }

  .kanban-column-body {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .kanban-card {
    background: var(--admin-surface);
    border: 1px solid var(--admin-border);
    border-radius: 6px;
    padding: 0.75rem;
    cursor: grab;
    transition: box-shadow 0.15s, transform 0.15s, opacity 0.2s;
  }

  .kanban-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transform: translateY(-1px);
  }

  .kanban-card.dragging {
    opacity: 0.4;
    transform: scale(0.95);
    cursor: grabbing;
  }

  .kanban-column-body.drag-over {
    background: rgba(139, 21, 56, 0.06);
    border: 2px dashed var(--admin-primary);
    border-radius: 6px;
  }

  .kanban-card.drag-preview {
    border: 2px dashed var(--admin-primary);
    background: rgba(139, 21, 56, 0.03);
    min-height: 60px;
  }

  .kanban-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.5rem;
  }

  .kanban-card-name {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--admin-text);
    line-height: 1.2;
  }

  .kanban-card-name.anonymous {
    color: var(--admin-muted);
    font-style: italic;
    font-weight: 400;
  }

  .kanban-card-score {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 600;
    flex-shrink: 0;
  }

  .kanban-card-score.chaud { background: rgba(239,68,68,0.1); color: #dc2626; }
  .kanban-card-score.tiede { background: rgba(245,158,11,0.1); color: #d97706; }
  .kanban-card-score.froid { background: rgba(100,116,139,0.1); color: #475569; }

  .kanban-card-details {
    font-size: 0.78rem;
    color: var(--admin-muted);
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
  }

  .kanban-card-details .detail-row {
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  .kanban-card-details .detail-row i {
    width: 14px;
    text-align: center;
    font-size: 0.7rem;
  }

  .kanban-card-estimation {
    font-weight: 700;
    color: var(--admin-text);
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .kanban-card-actions {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .kanban-move-select {
    flex: 1;
    padding: 0.3rem 0.4rem;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    font-size: 0.72rem;
    font-family: inherit;
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
  }

  .kanban-move-select:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .kanban-score-select {
    padding: 0.3rem 0.4rem;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    font-size: 0.72rem;
    font-family: inherit;
    color: var(--admin-text);
    background: #fff;
    cursor: pointer;
    width: 80px;
  }

  .kanban-score-select:focus {
    outline: none;
    border-color: var(--admin-primary);
  }

  .kanban-empty {
    text-align: center;
    padding: 1.5rem 0.5rem;
    color: var(--admin-muted);
    font-size: 0.8rem;
    font-style: italic;
  }

  /* Pipeline progress bar (horizontal overview) */
  .pipeline-progress {
    display: flex;
    gap: 2px;
    margin-bottom: 1.5rem;
    border-radius: var(--admin-radius);
    overflow: hidden;
    height: 8px;
    background: #e2e8f0;
  }

  .pipeline-progress-segment {
    height: 100%;
    transition: width 0.4s ease;
  }

  /* Toast notification */
  .toast-notification {
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
    animation: slideUp 0.3s ease;
  }

  .toast-notification.success { border-left: 4px solid #22c55e; }
  .toast-notification.error { border-left: 4px solid #ef4444; }

  @keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  @media (max-width: 1024px) {
    .kanban-column {
      min-width: 220px;
    }
  }

  @media (max-width: 640px) {
    .admin-page-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .pipeline-summary {
      flex-direction: column;
    }
  }
</style>

<?php
  $statutCounts = $statutCounts ?? [];
  $leadsByStatut = $leadsByStatut ?? [];
  $scoreData = $scoreData ?? [];
  $totalLeads = $totalLeads ?? 0;

  $columns = [
    'nouveau'        => ['Nouveau',        '#3b82f6', 'fa-plus-circle'],
    'contacte'       => ['Contact&eacute;',       '#f59e0b', 'fa-phone'],
    'rdv_pris'       => ['RDV Pris',       '#8b5cf6', 'fa-calendar-check'],
    'visite_realisee'=> ['Visite R&eacute;alis&eacute;e', '#ec4899', 'fa-home'],
    'mandat_simple'  => ['Mandat Simple',  '#0ea5e9', 'fa-file-contract'],
    'mandat_exclusif'=> ['Mandat Exclusif','#14b8a6', 'fa-file-signature'],
    'compromis_vente'=> ['Compromis',      '#f97316', 'fa-handshake'],
    'signe'          => ['Sign&eacute;',          '#22c55e', 'fa-check-circle'],
    'co_signature_partenaire' => ['Co-signature', '#a855f7', 'fa-users'],
    'assigne_autre'  => ['Assign&eacute;',       '#64748b', 'fa-share-square'],
  ];

  $statutLabels = [];
  foreach ($columns as $k => [$label, $c, $i]) {
    $statutLabels[$k] = $label;
  }

  $scoreLabels = ['chaud' => 'Chaud', 'tiede' => 'Ti&egrave;de', 'froid' => 'Froid'];
  $scoreIcons = ['chaud' => 'fa-fire', 'tiede' => 'fa-temperature-half', 'froid' => 'fa-snowflake'];
?>

<!-- PAGE HEADER -->
<div class="admin-page-header">
  <h1><i class="fas fa-columns"></i> Pipeline de Cat&eacute;gorisation</h1>
  <div class="header-actions">
    <a href="/admin/leads" class="btn-action"><i class="fas fa-list"></i> Liste</a>
    <a href="/admin/funnel" class="btn-action"><i class="fas fa-filter"></i> Entonnoir</a>
    <a href="/admin/dashboard" class="btn-action"><i class="fas fa-chart-line"></i> Dashboard</a>
  </div>
</div>

<!-- Score summary -->
<div class="pipeline-summary">
  <div class="pipeline-summary-item">
    <i class="fas fa-users" style="color: var(--admin-primary);"></i>
    <span class="count"><?= $totalLeads ?></span> leads qualifi&eacute;s
  </div>
  <div class="pipeline-summary-item hot">
    <i class="fas fa-fire" style="color: #ef4444;"></i>
    <span class="count"><?= (int)($scoreData['chaud'] ?? 0) ?></span> Chauds
  </div>
  <div class="pipeline-summary-item warm">
    <i class="fas fa-temperature-half" style="color: #f59e0b;"></i>
    <span class="count"><?= (int)($scoreData['tiede'] ?? 0) ?></span> Ti&egrave;des
  </div>
  <div class="pipeline-summary-item cold">
    <i class="fas fa-snowflake" style="color: #64748b;"></i>
    <span class="count"><?= (int)($scoreData['froid'] ?? 0) ?></span> Froids
  </div>
</div>

<!-- Pipeline progress bar -->
<?php if ($totalLeads > 0): ?>
<div class="pipeline-progress" title="R&eacute;partition par &eacute;tape">
  <?php foreach ($columns as $key => [$label, $color, $icon]):
    $cnt = (int)($statutCounts[$key] ?? 0);
    $pct = $totalLeads > 0 ? ($cnt / $totalLeads) * 100 : 0;
    if ($cnt > 0): ?>
      <div class="pipeline-progress-segment" style="width: <?= max($pct, 2) ?>%; background: <?= $color ?>;" title="<?= strip_tags($label) ?>: <?= $cnt ?>"></div>
    <?php endif;
  endforeach; ?>
</div>
<?php endif; ?>

<!-- KANBAN BOARD -->
<div class="kanban-board">
  <?php foreach ($columns as $statutKey => [$colLabel, $colColor, $colIcon]):
    $colLeads = $leadsByStatut[$statutKey] ?? [];
    $colCount = count($colLeads);
  ?>
    <div class="kanban-column">
      <div class="kanban-column-header" style="border-bottom-color: <?= $colColor ?>;">
        <div class="kanban-column-title" style="color: <?= $colColor ?>;">
          <i class="fas <?= $colIcon ?>"></i> <?= $colLabel ?>
        </div>
        <div class="kanban-column-count"><?= $colCount ?></div>
      </div>
      <div class="kanban-column-body" data-statut="<?= $statutKey ?>">
        <?php if (empty($colLeads)): ?>
          <div class="kanban-empty">Aucun lead</div>
        <?php else: ?>
          <?php foreach ($colLeads as $lead):
            $isTendance = ($lead['lead_type'] ?? 'qualifie') === 'tendance';
            $scoreKey = $lead['score'] ?? 'froid';
          ?>
            <div class="kanban-card" draggable="true" data-lead-id="<?= (int)$lead['id'] ?>" data-statut="<?= $statutKey ?>">
              <div class="kanban-card-header">
                <?php if ($isTendance): ?>
                  <div class="kanban-card-name anonymous">Anonyme</div>
                <?php else: ?>
                  <div class="kanban-card-name"><?= htmlspecialchars((string)($lead['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <span class="kanban-card-score <?= $scoreKey ?>">
                  <i class="fas <?= $scoreIcons[$scoreKey] ?? 'fa-snowflake' ?>"></i>
                  <?= $scoreLabels[$scoreKey] ?? 'Froid' ?>
                </span>
              </div>
              <div class="kanban-card-details">
                <?php if (!$isTendance && !empty($lead['email'])): ?>
                  <div class="detail-row"><i class="fas fa-envelope"></i> <?= htmlspecialchars((string)$lead['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if (!$isTendance && !empty($lead['telephone'])): ?>
                  <div class="detail-row"><i class="fas fa-phone"></i> <?= htmlspecialchars((string)$lead['telephone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <div class="detail-row"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars((string)$lead['ville'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php if (!empty($lead['type_bien'])): ?>
                  <div class="detail-row"><i class="fas fa-building"></i> <?= htmlspecialchars(ucfirst((string)$lead['type_bien']), ENT_QUOTES, 'UTF-8') ?><?php if (!empty($lead['surface_m2'])): ?> &middot; <?= number_format((float)$lead['surface_m2'], 0, ',', '') ?> m&sup2;<?php endif; ?></div>
                <?php endif; ?>
              </div>
              <div class="kanban-card-estimation">
                <span><?= number_format((float)$lead['estimation'], 0, ',', ' ') ?> &euro;</span>
                <span style="font-size: 0.72rem; color: var(--admin-muted); font-weight: 400;">#<?= (int)$lead['id'] ?></span>
              </div>
              <div class="kanban-card-actions">
                <select class="kanban-move-select" data-lead-id="<?= (int)$lead['id'] ?>" data-action="statut" title="Changer le statut">
                  <?php foreach ($statutLabels as $sKey => $sLabel): ?>
                    <option value="<?= $sKey ?>" <?= $statutKey === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
                  <?php endforeach; ?>
                </select>
                <select class="kanban-score-select" data-lead-id="<?= (int)$lead['id'] ?>" data-action="score" title="Changer le score">
                  <option value="chaud" <?= $scoreKey === 'chaud' ? 'selected' : '' ?>>Chaud</option>
                  <option value="tiede" <?= $scoreKey === 'tiede' ? 'selected' : '' ?>>Ti&egrave;de</option>
                  <option value="froid" <?= $scoreKey === 'froid' ? 'selected' : '' ?>>Froid</option>
                </select>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Toast -->
<div class="toast-notification" id="pipelineToast"></div>

<script>
(function() {
  var csrfToken = <?= json_encode($_SESSION['csrf_token'] ?? '', JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  var draggedCard = null;

  function showToast(message, type) {
    var toast = document.getElementById('pipelineToast');
    toast.textContent = message;
    toast.className = 'toast-notification ' + type;
    toast.style.display = 'block';
    setTimeout(function() { toast.style.display = 'none'; }, 2500);
  }

  function updateColumnCount(columnBody) {
    var column = columnBody.closest('.kanban-column');
    var countEl = column.querySelector('.kanban-column-count');
    var cards = columnBody.querySelectorAll('.kanban-card');
    countEl.textContent = cards.length;

    var emptyMsg = columnBody.querySelector('.kanban-empty');
    if (cards.length === 0 && !emptyMsg) {
      var div = document.createElement('div');
      div.className = 'kanban-empty';
      div.textContent = 'Aucun lead';
      columnBody.appendChild(div);
    } else if (cards.length > 0 && emptyMsg) {
      emptyMsg.remove();
    }
  }

  function updateCardStatutSelect(card, newStatut) {
    var sel = card.querySelector('.kanban-move-select');
    if (sel) { sel.value = newStatut; }
    card.dataset.statut = newStatut;
  }

  function updateLead(leadId, field, value, onSuccess, onError, isRetry) {
    var body = 'csrf_token=' + encodeURIComponent(csrfToken) + '&id=' + leadId + '&field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/leads/update-inline', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
      try {
        var resp = JSON.parse(xhr.responseText);
        if (resp.csrf_token) { csrfToken = resp.csrf_token; }
        if (resp.success) {
          showToast('Lead #' + leadId + ' mis \u00e0 jour', 'success');
          if (onSuccess) onSuccess();
        } else if (!isRetry && resp.csrf_token) {
          // CSRF expired — retry once with fresh token
          updateLead(leadId, field, value, onSuccess, onError, true);
        } else {
          showToast(resp.error || 'Erreur de mise \u00e0 jour', 'error');
          if (onError) onError();
        }
      } catch(e) {
        showToast('Erreur serveur', 'error');
        if (onError) onError();
      }
    };
    xhr.onerror = function() {
      showToast('Erreur r\u00e9seau', 'error');
      if (onError) onError();
    };
    xhr.send(body);
  }

  // --- Drag & Drop ---

  document.querySelectorAll('.kanban-card').forEach(function(card) {
    card.addEventListener('dragstart', function(e) {
      draggedCard = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', this.dataset.leadId);
    });

    card.addEventListener('dragend', function() {
      this.classList.remove('dragging');
      draggedCard = null;
      document.querySelectorAll('.kanban-column-body').forEach(function(col) {
        col.classList.remove('drag-over');
      });
    });
  });

  document.querySelectorAll('.kanban-column-body').forEach(function(colBody) {
    colBody.addEventListener('dragover', function(e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      this.classList.add('drag-over');
    });

    colBody.addEventListener('dragleave', function(e) {
      if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
      }
    });

    colBody.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('drag-over');

      if (!draggedCard) return;

      var newStatut = this.dataset.statut;
      var oldStatut = draggedCard.dataset.statut;
      if (newStatut === oldStatut) return;

      var leadId = draggedCard.dataset.leadId;
      var card = draggedCard;
      var oldColumnBody = card.parentElement;

      // Move card in the DOM immediately
      this.appendChild(card);
      updateCardStatutSelect(card, newStatut);
      updateColumnCount(oldColumnBody);
      updateColumnCount(this);

      // Persist to server
      var targetCol = this;
      updateLead(leadId, 'statut', newStatut, null, function() {
        // Revert on error
        oldColumnBody.appendChild(card);
        updateCardStatutSelect(card, oldStatut);
        updateColumnCount(oldColumnBody);
        updateColumnCount(targetCol);
      });
    });
  });

  // --- Dropdown selects (existing) ---

  document.querySelectorAll('.kanban-move-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      var leadId = this.dataset.leadId;
      var newStatut = this.value;
      var card = this.closest('.kanban-card');
      var oldStatut = card.dataset.statut;
      if (newStatut === oldStatut) return;

      var oldColumnBody = card.parentElement;
      var newColumnBody = document.querySelector('.kanban-column-body[data-statut="' + newStatut + '"]');

      // Move card in the DOM immediately
      newColumnBody.appendChild(card);
      card.dataset.statut = newStatut;
      updateColumnCount(oldColumnBody);
      updateColumnCount(newColumnBody);

      updateLead(leadId, 'statut', newStatut, null, function() {
        // Revert on error
        oldColumnBody.appendChild(card);
        card.dataset.statut = oldStatut;
        sel.value = oldStatut;
        updateColumnCount(oldColumnBody);
        updateColumnCount(newColumnBody);
      });
    });
  });

  document.querySelectorAll('.kanban-score-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      updateLead(this.dataset.leadId, 'score', this.value);
    });
  });
})();
</script>
