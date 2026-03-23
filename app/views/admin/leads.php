<?php
  $tblExists = $tableExists ?? false;
  $flash = $_SESSION['leads_flash'] ?? null;
  unset($_SESSION['leads_flash']);
  $allLeads = $leads ?? [];
  $totalLeads = $total ?? count($allLeads);
  $stats = $allStats ?? ['total' => $totalLeads, 'chaud' => 0, 'today' => 0];
  $f = $filters ?? [];
  $currentPage = $currentPage ?? 1;
  $totalPages = $totalPages ?? 1;
  $perPage = $perPage ?? 25;
  $villesList = $villes ?? [];

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

  // Build query string for pagination/export (preserving current filters)
  $qs = http_build_query(array_filter([
    'q' => $f['q'] ?? '',
    'score' => $f['score'] ?? '',
    'type' => $f['type'] ?? '',
    'statut' => $f['statut'] ?? '',
    'ville' => $f['ville'] ?? '',
    'date_from' => $f['date_from'] ?? '',
    'date_to' => $f['date_to'] ?? '',
    'sort' => $f['sort'] ?? '',
    'dir' => $f['dir'] ?? '',
  ], fn($v) => $v !== '' && $v !== null));
  $hasFilters = !empty($f['q']) || !empty($f['score']) || !empty($f['type']) || !empty($f['statut']) || !empty($f['ville']) || !empty($f['date_from']) || !empty($f['date_to']);
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

  /* ── Lead Detail Modal ── */
  .lead-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26,20,16,0.5);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: leadModalFadeIn 0.2s ease;
  }
  .lead-modal-overlay.visible { display: flex; }

  @keyframes leadModalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes leadModalSlideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  .lead-modal {
    background: var(--admin-surface, #ffffff);
    border-radius: var(--admin-radius, 12px);
    width: 100%;
    max-width: 780px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: leadModalSlideIn 0.25s ease;
    border: 1px solid var(--admin-border, #e8dfd7);
  }

  .lead-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
    position: sticky;
    top: 0;
    background: var(--admin-surface, #ffffff);
    z-index: 2;
    border-radius: var(--admin-radius, 12px) var(--admin-radius, 12px) 0 0;
  }

  .lead-modal-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .lead-modal-header h2 i { color: var(--admin-primary, #8B1538); }

  .lead-modal-close {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: #fff;
    color: var(--admin-muted, #6b6459);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    transition: all 0.15s;
  }
  .lead-modal-close:hover { background: #f1f5f9; color: var(--admin-text, #1a1410); }

  .lead-modal-body { padding: 1.5rem; }

  .lead-modal-section {
    margin-bottom: 1.5rem;
  }
  .lead-modal-section:last-child { margin-bottom: 0; }

  .lead-modal-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--admin-primary, #8B1538);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .lead-modal-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1.5rem;
  }

  .lead-modal-field {
    display: flex;
    flex-direction: column;
    padding: 0.4rem 0;
  }

  .lead-modal-field-label {
    font-size: 0.7rem;
    color: var(--admin-muted, #6b6459);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-weight: 600;
    margin-bottom: 2px;
  }

  .lead-modal-field-value {
    font-size: 0.88rem;
    color: var(--admin-text, #1a1410);
    font-weight: 500;
  }

  .lead-modal-field-value.empty {
    color: var(--admin-muted, #6b6459);
    font-style: italic;
    font-weight: 400;
  }

  .lead-modal-divider {
    border: none;
    border-top: 1px solid var(--admin-border, #e8dfd7);
    margin: 0;
  }

  .lead-modal-neuropersona {
    background: linear-gradient(135deg, rgba(139,21,56,0.04), rgba(212,175,55,0.04));
    border: 1px solid rgba(139,21,56,0.12);
    border-radius: 8px;
    padding: 1rem 1.25rem;
  }

  .lead-modal-neuropersona-title {
    font-family: 'Playfair Display', serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--admin-primary, #8B1538);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .lead-modal-neuropersona-desc {
    font-size: 0.84rem;
    line-height: 1.6;
    color: var(--admin-text, #1a1410);
  }

  .lead-modal-actions-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .lead-modal-actions-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.84rem;
    color: var(--admin-text, #1a1410);
    line-height: 1.5;
  }

  .lead-modal-actions-list li:last-child { border-bottom: none; }

  .lead-modal-actions-list li i {
    color: var(--admin-primary, #8B1538);
    margin-top: 3px;
    flex-shrink: 0;
    font-size: 0.75rem;
  }

  .lead-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--admin-border, #e8dfd7);
    background: #fafaf8;
    border-radius: 0 0 var(--admin-radius, 12px) var(--admin-radius, 12px);
  }

  .lead-modal-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: #fff;
    color: var(--admin-text, #1a1410);
    text-decoration: none;
    transition: all 0.15s;
  }
  .lead-modal-btn:hover { background: #f1f5f9; }
  .lead-modal-btn.primary { background: var(--admin-primary, #8B1538); color: #fff; border-color: var(--admin-primary, #8B1538); }
  .lead-modal-btn.primary:hover { opacity: 0.9; }
  .lead-modal-btn.danger { color: #dc2626; border-color: #fca5a5; }
  .lead-modal-btn.danger:hover { background: #fef2f2; }

  /* ── Delete Confirm Modal ── */
  .lead-confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26,20,16,0.6);
    backdrop-filter: blur(4px);
    z-index: 10001;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .lead-confirm-overlay.visible { display: flex; }

  .lead-confirm-box {
    background: var(--admin-surface, #ffffff);
    border-radius: var(--admin-radius, 12px);
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: leadModalSlideIn 0.2s ease;
    border: 1px solid var(--admin-border, #e8dfd7);
    text-align: center;
    padding: 2rem;
  }

  .lead-confirm-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: rgba(239,68,68,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.3rem;
    color: #ef4444;
  }

  .lead-confirm-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    margin-bottom: 0.5rem;
  }

  .lead-confirm-desc {
    font-size: 0.85rem;
    color: var(--admin-muted, #6b6459);
    margin-bottom: 1.5rem;
    line-height: 1.5;
  }

  .lead-confirm-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
  }

  .lead-confirm-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.25rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: #fff;
    color: var(--admin-text, #1a1410);
    transition: all 0.15s;
  }
  .lead-confirm-btn:hover { background: #f1f5f9; }
  .lead-confirm-btn.confirm-delete {
    background: #ef4444;
    color: #fff;
    border-color: #ef4444;
  }
  .lead-confirm-btn.confirm-delete:hover { background: #dc2626; }

  .lead-modal-notes { margin-top: 0.5rem; }
  .lead-modal-note-item {
    background: #f8fafc;
    border-radius: 6px;
    padding: 0.6rem 0.8rem;
    margin-bottom: 0.5rem;
    font-size: 0.82rem;
    line-height: 1.5;
  }
  .lead-modal-note-meta {
    font-size: 0.7rem;
    color: var(--admin-muted, #6b6459);
    margin-top: 0.25rem;
  }

  .lead-modal-activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.35rem 0;
    font-size: 0.78rem;
    color: var(--admin-muted, #6b6459);
  }
  .lead-modal-activity-item i {
    color: var(--admin-primary, #8B1538);
    margin-top: 2px;
    font-size: 0.65rem;
  }

  .lead-modal-loading {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-muted, #6b6459);
  }
  .lead-modal-loading i { font-size: 1.5rem; margin-bottom: 0.75rem; display: block; }

  @media (max-width: 640px) {
    .lead-modal { max-width: 100%; }
    .lead-modal-grid { grid-template-columns: 1fr; }
    .lead-modal-footer { flex-direction: column; gap: 0.5rem; }
  }

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

  /* ── Liste View hidden state ── */
  .leads-liste-view.hidden { display: none !important; }
  .leads-pagination.hidden { display: none !important; }

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
    border-radius: 8px;
    font-size: 0.8rem;
    font-family: inherit;
    color: var(--admin-text, #1a1410);
    margin: 0;
    padding: 1rem;
    text-align: left;
  }

  .leads-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .leads-card-name {
    font-weight: 600;
    font-size: 0.9rem;
  }

  .leads-card-id {
    font-size: 0.75rem;
    color: var(--admin-muted, #6b6459);
  }

  .leads-card-badges {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 0.75rem;
  }

  .leads-card-info {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.3rem 0.75rem;
    margin: 0 0 0.75rem 0;
    font-size: 0.78rem;
  }

  .leads-card-info dt {
    font-weight: 500;
    color: var(--admin-muted, #6b6459);
  }

  .leads-card-info dd {
    margin: 0;
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

  .leads-kanban-col-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.85rem;
    border-bottom: 1px solid var(--admin-border, #e8dfd7);
  }

  .leads-kanban-count {
    background: var(--admin-border, #e8dfd7);
    color: var(--admin-muted, #6b6459);
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 10px;
  }

  .leads-kanban-cards {
    padding: 0.5rem;
    overflow-y: auto;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .leads-kanban-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    padding: 0.7rem;
    cursor: pointer;
    transition: box-shadow 0.15s;
  }

  .leads-kanban-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }

  .leads-kanban-card-name {
    font-weight: 600;
    font-size: 0.82rem;
    margin-bottom: 0.25rem;
  }

  .leads-kanban-card-detail {
    font-size: 0.75rem;
    color: var(--admin-muted, #6b6459);
    margin-bottom: 0.4rem;
  }

  .leads-kanban-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .leads-kanban-card-est {
    font-weight: 600;
    font-size: 0.78rem;
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

  /* ── Search & Filters Bar ── */
  .leads-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
    align-items: flex-end;
  }

  .leads-search-box {
    position: relative;
    flex: 1 1 280px;
    min-width: 200px;
  }

  .leads-search-box input {
    width: 100%;
    padding: 0.55rem 0.75rem 0.55rem 2.2rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    font-size: 0.85rem;
    background: var(--admin-surface, #fff);
    color: var(--admin-text, #1a1410);
    transition: border-color 0.15s;
  }

  .leads-search-box input:focus {
    outline: none;
    border-color: var(--admin-primary, #8B1538);
    box-shadow: 0 0 0 2px rgba(139,21,56,0.1);
  }

  .leads-search-box i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--admin-muted, #6b6459);
    font-size: 0.8rem;
  }

  .leads-filter-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
  }

  .leads-filter-select {
    padding: 0.5rem 0.65rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    font-size: 0.8rem;
    background: var(--admin-surface, #fff);
    color: var(--admin-text, #1a1410);
    cursor: pointer;
    min-width: 110px;
  }

  .leads-filter-select:focus {
    outline: none;
    border-color: var(--admin-primary, #8B1538);
  }

  .leads-filter-date {
    padding: 0.48rem 0.6rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 8px;
    font-size: 0.8rem;
    background: var(--admin-surface, #fff);
    color: var(--admin-text, #1a1410);
  }

  .leads-filter-date:focus {
    outline: none;
    border-color: var(--admin-primary, #8B1538);
  }

  .leads-toolbar-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-shrink: 0;
  }

  .leads-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 0.85rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: var(--admin-surface, #fff);
    color: var(--admin-text, #1a1410);
    text-decoration: none;
    transition: all 0.15s;
    white-space: nowrap;
  }

  .leads-btn:hover { background: #f1f5f9; }
  .leads-btn.primary { background: var(--admin-primary, #8B1538); color: #fff; border-color: var(--admin-primary); }
  .leads-btn.primary:hover { opacity: 0.9; }
  .leads-btn.success { background: #16a34a; color: #fff; border-color: #16a34a; }
  .leads-btn.success:hover { background: #15803d; }
  .leads-btn.danger { color: #dc2626; border-color: #fca5a5; }
  .leads-btn.danger:hover { background: #fef2f2; }
  .leads-btn.sm { padding: 0.35rem 0.65rem; font-size: 0.75rem; }

  .leads-clear-filters {
    font-size: 0.78rem;
    color: var(--admin-primary, #8B1538);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
  }

  .leads-clear-filters:hover { text-decoration: underline; }

  /* ── Bulk Actions Bar ── */
  .leads-bulk-bar {
    display: none;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 1rem;
    background: rgba(139,21,56,0.05);
    border-bottom: 1px solid rgba(139,21,56,0.15);
    font-size: 0.82rem;
  }

  .leads-bulk-bar.visible { display: flex; }

  .leads-bulk-count {
    font-weight: 600;
    color: var(--admin-primary, #8B1538);
  }

  .leads-bulk-select {
    padding: 0.35rem 0.5rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    font-size: 0.8rem;
    background: #fff;
  }

  /* ── Checkbox ── */
  .leads-check {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--admin-primary, #8B1538);
  }

  /* ── Pagination ── */
  .leads-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.25rem;
    border-top: 1px solid var(--admin-border, #e8dfd7);
    font-size: 0.82rem;
    color: var(--admin-muted, #6b6459);
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .leads-pagination-info {
    font-size: 0.8rem;
  }

  .leads-pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .leads-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 0.5rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    background: #fff;
    color: var(--admin-text, #1a1410);
    font-size: 0.8rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s;
  }

  .leads-page-btn:hover { background: #f1f5f9; }
  .leads-page-btn.active { background: var(--admin-primary, #8B1538); color: #fff; border-color: var(--admin-primary); }
  .leads-page-btn.disabled { opacity: 0.4; pointer-events: none; }

  /* ── Sortable Headers ── */
  .leads-admin-table th a {
    color: inherit;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
  }
  .leads-admin-table th a:hover { color: var(--admin-primary, #8B1538); }
  .leads-admin-table th .sort-icon { font-size: 0.6rem; opacity: 0.5; }
  .leads-admin-table th .sort-icon.active { opacity: 1; color: var(--admin-primary, #8B1538); }

  /* ── Active filter badges ── */
  .leads-active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
  }

  .leads-filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.55rem;
    background: rgba(139,21,56,0.08);
    border: 1px solid rgba(139,21,56,0.15);
    border-radius: 20px;
    font-size: 0.72rem;
    color: var(--admin-primary, #8B1538);
  }

  .leads-filter-badge a {
    color: var(--admin-primary, #8B1538);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.8rem;
    line-height: 1;
  }

  .leads-filter-badge a:hover { color: #dc2626; }

  @media (max-width: 640px) {
    .leads-stats-grid { grid-template-columns: 1fr 1fr; }
    .leads-empty-steps { gap: 1rem; }
    .leads-grille-grid { grid-template-columns: 1fr; }
    .leads-kanban-col { width: 260px; min-width: 260px; }
    .leads-view-btn span.view-label { display: none; }
    .leads-toolbar { flex-direction: column; }
    .leads-filter-group { width: 100%; }
    .leads-toolbar-actions { width: 100%; justify-content: flex-end; }
    .leads-pagination { flex-direction: column; align-items: center; }
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
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
          <?php if (!empty($allLeads) || $hasFilters): ?>
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
          <a href="/admin/leads/export-csv<?= $qs ? '?' . $qs : '' ?>" class="leads-btn success" title="Exporter en CSV">
            <i class="fas fa-file-csv"></i> <span class="view-label">Export CSV</span>
          </a>
        </div>
      </div>

      <!-- STATS -->
      <div class="leads-stats-grid">
        <div class="leads-stat-card">
          <div class="leads-stat-icon total"><i class="fas fa-users"></i></div>
          <div>
            <div class="leads-stat-value"><?= $stats['total'] ?></div>
            <div class="leads-stat-label">Total Leads</div>
          </div>
        </div>
        <div class="leads-stat-card">
          <div class="leads-stat-icon hot"><i class="fas fa-fire"></i></div>
          <div>
            <div class="leads-stat-value"><?= $stats['chaud'] ?></div>
            <div class="leads-stat-label">Leads chauds</div>
          </div>
        </div>
        <div class="leads-stat-card">
          <div class="leads-stat-icon new"><i class="fas fa-clock"></i></div>
          <div>
            <div class="leads-stat-value"><?= $stats['today'] ?></div>
            <div class="leads-stat-label">Aujourd'hui</div>
          </div>
        </div>
      </div>

      <!-- SEARCH & FILTERS -->
      <form method="GET" action="/admin/leads" class="leads-toolbar" id="leadsFilterForm">
        <div class="leads-search-box">
          <i class="fas fa-search"></i>
          <input type="text" name="q" placeholder="Rechercher par nom, email, téléphone, ville..." value="<?= htmlspecialchars($f['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
        </div>
        <div class="leads-filter-group">
          <select name="type" class="leads-filter-select" onchange="this.form.submit()">
            <option value="">Tous types</option>
            <option value="qualifie" <?= ($f['type'] ?? '') === 'qualifie' ? 'selected' : '' ?>>Qualifié</option>
            <option value="tendance" <?= ($f['type'] ?? '') === 'tendance' ? 'selected' : '' ?>>Tendance</option>
          </select>
          <select name="score" class="leads-filter-select" onchange="this.form.submit()">
            <option value="">Tous scores</option>
            <option value="chaud" <?= ($f['score'] ?? '') === 'chaud' ? 'selected' : '' ?>>Chaud</option>
            <option value="tiede" <?= ($f['score'] ?? '') === 'tiede' ? 'selected' : '' ?>>Tiède</option>
            <option value="froid" <?= ($f['score'] ?? '') === 'froid' ? 'selected' : '' ?>>Froid</option>
          </select>
          <select name="statut" class="leads-filter-select" onchange="this.form.submit()">
            <option value="">Tous statuts</option>
            <?php foreach ($statutLabels as $sKey => $sLabel): ?>
              <option value="<?= $sKey ?>" <?= ($f['statut'] ?? '') === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($villesList)): ?>
          <select name="ville" class="leads-filter-select" onchange="this.form.submit()">
            <option value="">Toutes villes</option>
            <?php foreach ($villesList as $v): ?>
              <option value="<?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?>" <?= ($f['ville'] ?? '') === $v ? 'selected' : '' ?>><?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
          <?php endif; ?>
          <input type="date" name="date_from" class="leads-filter-date" value="<?= htmlspecialchars($f['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>" title="Date début" onchange="this.form.submit()">
          <input type="date" name="date_to" class="leads-filter-date" value="<?= htmlspecialchars($f['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>" title="Date fin" onchange="this.form.submit()">
        </div>
        <div class="leads-toolbar-actions">
          <button type="submit" class="leads-btn primary"><i class="fas fa-search"></i> Filtrer</button>
          <?php if ($hasFilters): ?>
            <a href="/admin/leads" class="leads-clear-filters"><i class="fas fa-times-circle"></i> Effacer filtres</a>
          <?php endif; ?>
        </div>
      </form>

      <?php if ($hasFilters): ?>
      <div class="leads-active-filters">
        <?php if (!empty($f['q'])): ?>
          <span class="leads-filter-badge"><i class="fas fa-search"></i> "<?= htmlspecialchars($f['q'], ENT_QUOTES, 'UTF-8') ?>"</span>
        <?php endif; ?>
        <?php if (!empty($f['type'])): ?>
          <span class="leads-filter-badge">Type: <?= $f['type'] === 'qualifie' ? 'Qualifié' : 'Tendance' ?></span>
        <?php endif; ?>
        <?php if (!empty($f['score'])): ?>
          <span class="leads-filter-badge">Score: <?= htmlspecialchars($f['score'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
        <?php if (!empty($f['statut'])): ?>
          <span class="leads-filter-badge">Statut: <?= $statutLabels[$f['statut']] ?? $f['statut'] ?></span>
        <?php endif; ?>
        <?php if (!empty($f['ville'])): ?>
          <span class="leads-filter-badge">Ville: <?= htmlspecialchars($f['ville'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
        <?php if (!empty($f['date_from'])): ?>
          <span class="leads-filter-badge">Depuis: <?= htmlspecialchars($f['date_from'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
        <?php if (!empty($f['date_to'])): ?>
          <span class="leads-filter-badge">Jusqu'au: <?= htmlspecialchars($f['date_to'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- TABLE CARD -->
      <div class="leads-table-card">
        <div class="leads-table-header">
          <span class="leads-table-title"><i class="fas fa-list"></i> Liste des leads</span>
          <span style="font-size: 0.8rem; color: var(--admin-muted, #6b6459);"><?= $totalLeads ?> résultat<?= $totalLeads > 1 ? 's' : '' ?><?= $hasFilters ? ' (filtré)' : '' ?></span>
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
          $csrfToken = htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8');
          $sortIcon = function(string $col) use ($f) {
            $currentSort = $f['sort'] ?? 'created_at';
            $currentDir = $f['dir'] ?? 'DESC';
            $isActive = $currentSort === $col;
            $nextDir = ($isActive && $currentDir === 'ASC') ? 'DESC' : 'ASC';
            $icon = $isActive ? ($currentDir === 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
            $activeClass = $isActive ? ' active' : '';
            return '<i class="fas ' . $icon . ' sort-icon' . $activeClass . '"></i>';
          };
          $sortUrl = function(string $col) use ($f, $currentPage) {
            $currentSort = $f['sort'] ?? 'created_at';
            $currentDir = $f['dir'] ?? 'DESC';
            $nextDir = ($currentSort === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
            $params = array_filter([
              'q' => $f['q'] ?? '',
              'score' => $f['score'] ?? '',
              'type' => $f['type'] ?? '',
              'statut' => $f['statut'] ?? '',
              'ville' => $f['ville'] ?? '',
              'date_from' => $f['date_from'] ?? '',
              'date_to' => $f['date_to'] ?? '',
              'sort' => $col,
              'dir' => $nextDir,
              'page' => $currentPage > 1 ? $currentPage : '',
            ], fn($v) => $v !== '' && $v !== null);
            return '/admin/leads?' . http_build_query($params);
          };
        ?>

        <!-- Bulk Actions Bar -->
        <div class="leads-bulk-bar" id="leadsBulkBar">
          <input type="checkbox" class="leads-check" id="leadsCheckAll" title="Tout sélectionner">
          <span class="leads-bulk-count"><span id="bulkCount">0</span> sélectionné(s)</span>
          <form method="POST" action="/admin/leads/bulk-action" id="bulkForm" style="display:inline-flex;gap:0.5rem;align-items:center;">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div id="bulkIdsContainer"></div>
            <select name="bulk_action" class="leads-bulk-select" id="bulkActionSelect">
              <option value="">-- Action groupée --</option>
              <optgroup label="Changer le score">
                <option value="score_chaud">Score: Chaud</option>
                <option value="score_tiede">Score: Tiède</option>
                <option value="score_froid">Score: Froid</option>
              </optgroup>
              <optgroup label="Changer le statut">
                <?php foreach ($statutLabels as $sKey => $sLabel): ?>
                  <option value="statut_<?= $sKey ?>">Statut: <?= $sLabel ?></option>
                <?php endforeach; ?>
              </optgroup>
              <optgroup label="Danger">
                <option value="delete">Supprimer</option>
              </optgroup>
            </select>
            <button type="submit" class="leads-btn sm danger" id="bulkSubmitBtn" disabled onclick="return confirmBulk()"><i class="fas fa-check"></i> Appliquer</button>
          </form>
        </div>

        <div class="table-wrapper leads-liste-view" style="overflow-x: auto;">
            <table class="leads-admin-table">
              <thead>
                <tr>
                  <th style="width:36px;"><input type="checkbox" class="leads-check" id="leadsCheckAllHead" title="Tout sélectionner"></th>
                  <th><a href="<?= $sortUrl('id') ?>">ID <?= $sortIcon('id') ?></a></th>
                  <th>Type</th>
                  <th><a href="<?= $sortUrl('nom') ?>">Nom <?= $sortIcon('nom') ?></a></th>
                  <th><a href="<?= $sortUrl('email') ?>">Email <?= $sortIcon('email') ?></a></th>
                  <th>Téléphone</th>
                  <th><a href="<?= $sortUrl('ville') ?>">Ville <?= $sortIcon('ville') ?></a></th>
                  <th>Bien</th>
                  <th><a href="<?= $sortUrl('estimation') ?>">Estimation <?= $sortIcon('estimation') ?></a></th>
                  <th>Score</th>
                  <th>Statut</th>
                  <th><a href="<?= $sortUrl('created_at') ?>">Créé le <?= $sortIcon('created_at') ?></a></th>
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

                    $typeClass = ($lead['lead_type'] ?? '') === 'qualifie' ? 'leads-badge-qualifie' : 'leads-badge-tendance';
                    $typeLabel = ($lead['lead_type'] ?? '') === 'qualifie' ? 'Qualifié' : 'Tendance';
                  ?>
                  <tr>
                    <td><input type="checkbox" class="leads-check leads-row-check" value="<?= $leadId ?>" data-lead-id="<?= $leadId ?>"></td>
                    <td style="font-weight: 600; color: var(--admin-muted, #6b6459);">#<?= e((string) $lead['id']) ?></td>
                    <td><span class="leads-badge <?= $typeClass ?>"><?= $typeLabel ?></span></td>
                    <td style="font-weight: 500;"><?= e((string) $lead['nom']) ?></td>
                    <td><?= e((string) $lead['email']) ?></td>
                    <td><?= e((string) $lead['telephone']) ?></td>
                    <td><?= e((string) $lead['ville']) ?></td>
                    <td><?= e((string) ($lead['type_bien'] ?? '')) ?></td>
                    <td style="font-weight: 600;"><?= number_format((float) $lead['estimation'], 0, ',', ' ') ?> €</td>
                    <td>
                      <select class="leads-inline-select" data-lead-id="<?= $leadId ?>" data-field="score" style="font-size:0.72rem;padding:0.2rem 0.35rem;border:1px solid var(--admin-border,#e8dfd7);border-radius:5px;background:#fff;cursor:pointer;max-width:90px;">
                        <option value="chaud" <?= $score === 'chaud' ? 'selected' : '' ?>>Chaud</option>
                        <option value="tiede" <?= $score === 'tiede' || $score === 'tiède' ? 'selected' : '' ?>>Tiède</option>
                        <option value="froid" <?= $score === 'froid' ? 'selected' : '' ?>>Froid</option>
                      </select>
                    </td>
                    <td>
                      <select class="leads-inline-select" data-lead-id="<?= $leadId ?>" data-field="statut" style="font-size:0.72rem;padding:0.2rem 0.35rem;border:1px solid var(--admin-border,#e8dfd7);border-radius:5px;background:#fff;cursor:pointer;max-width:120px;">
                        <?php foreach ($statutLabels as $sKey => $sLabel): ?>
                          <option value="<?= $sKey ?>" <?= $statutKey === $sKey ? 'selected' : '' ?>><?= $sLabel ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td style="white-space: nowrap; color: var(--admin-muted, #6b6459); font-size: 0.78rem;"><?= date('d/m/Y H:i', strtotime($lead['created_at'])) ?></td>
                    <td class="leads-actions-cell">
                      <button type="button" class="leads-action-btn view" title="Voir la fiche" onclick="openLeadModal(<?= $leadId ?>)"><i class="fas fa-eye"></i></button>
                      <a href="/admin/leads/edit/<?= $leadId ?>" class="leads-action-btn edit" title="Modifier"><i class="fas fa-edit"></i></a>
                      <button type="button" class="leads-action-btn delete" title="Supprimer" onclick="openDeleteConfirm(<?= $leadId ?>, '<?= htmlspecialchars($lead['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>')"><i class="fas fa-trash-alt"></i></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          <!-- PAGINATION -->
          <?php if ($totalPages > 1): ?>
          <div class="leads-pagination">
            <div class="leads-pagination-info">
              <?php
                $from = ($currentPage - 1) * $perPage + 1;
                $to = min($currentPage * $perPage, $totalLeads);
              ?>
              Affichage <?= $from ?>-<?= $to ?> sur <?= $totalLeads ?> leads
            </div>
            <div class="leads-pagination-nav">
              <?php
                $pageQs = function(int $p) use ($qs) {
                  $sep = $qs ? '&' : '';
                  return '/admin/leads?' . $qs . $sep . 'page=' . $p;
                };
              ?>
              <a href="<?= $pageQs(1) ?>" class="leads-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>" title="Première"><i class="fas fa-angle-double-left"></i></a>
              <a href="<?= $pageQs(max(1, $currentPage - 1)) ?>" class="leads-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>"><i class="fas fa-angle-left"></i></a>
              <?php
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                for ($p = $start; $p <= $end; $p++):
              ?>
                <a href="<?= $pageQs($p) ?>" class="leads-page-btn <?= $p === $currentPage ? 'active' : '' ?>"><?= $p ?></a>
              <?php endfor; ?>
              <a href="<?= $pageQs(min($totalPages, $currentPage + 1)) ?>" class="leads-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"><i class="fas fa-angle-right"></i></a>
              <a href="<?= $pageQs($totalPages) ?>" class="leads-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>" title="Dernière"><i class="fas fa-angle-double-right"></i></a>
            </div>
          </div>
          <?php endif; ?>

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
          var pagination = document.querySelector('.leads-pagination');
          var title = document.querySelector('.leads-table-title');
          if (!liste) return;

          // Hide all views
          liste.classList.add('hidden');
          if (pagination) pagination.classList.add('hidden');
          if (grille) grille.classList.remove('visible');
          if (kanban) kanban.classList.remove('visible');

          // Update active button
          btns.forEach(function(b) { b.classList.remove('active'); });
          var activeBtn = document.querySelector('.leads-view-btn[data-view="' + view + '"]');
          if (activeBtn) activeBtn.classList.add('active');

          // Show selected view
          if (view === 'liste') {
            liste.classList.remove('hidden');
            if (pagination) pagination.classList.remove('hidden');
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

      <!-- Bulk Actions JS -->
      <script>
      (function() {
        var bulkBar = document.getElementById('leadsBulkBar');
        var checkAll1 = document.getElementById('leadsCheckAll');
        var checkAll2 = document.getElementById('leadsCheckAllHead');
        var rowChecks = document.querySelectorAll('.leads-row-check');
        var bulkCount = document.getElementById('bulkCount');
        var bulkSubmit = document.getElementById('bulkSubmitBtn');
        var bulkAction = document.getElementById('bulkActionSelect');
        var bulkIdsContainer = document.getElementById('bulkIdsContainer');

        if (!bulkBar || !rowChecks.length) return;

        function updateBulkState() {
          var checked = document.querySelectorAll('.leads-row-check:checked');
          var count = checked.length;
          bulkCount.textContent = count;
          bulkBar.classList.toggle('visible', count > 0);
          bulkSubmit.disabled = count === 0 || !bulkAction.value;

          // Sync checkAll states
          var allChecked = rowChecks.length > 0 && checked.length === rowChecks.length;
          if (checkAll1) checkAll1.checked = allChecked;
          if (checkAll2) checkAll2.checked = allChecked;

          // Update hidden inputs
          bulkIdsContainer.innerHTML = '';
          checked.forEach(function(cb) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'lead_ids[]';
            input.value = cb.value;
            bulkIdsContainer.appendChild(input);
          });
        }

        function toggleAll(checked) {
          rowChecks.forEach(function(cb) { cb.checked = checked; });
          updateBulkState();
        }

        if (checkAll1) checkAll1.addEventListener('change', function() { toggleAll(this.checked); });
        if (checkAll2) checkAll2.addEventListener('change', function() { toggleAll(this.checked); if (checkAll1) checkAll1.checked = this.checked; });

        rowChecks.forEach(function(cb) {
          cb.addEventListener('change', updateBulkState);
        });

        if (bulkAction) bulkAction.addEventListener('change', updateBulkState);
      })();

      function confirmBulk() {
        var action = document.getElementById('bulkActionSelect').value;
        var count = document.querySelectorAll('.leads-row-check:checked').length;
        if (!action || count === 0) return false;
        var msg = 'Appliquer cette action sur ' + count + ' lead(s) ?';
        if (action === 'delete') {
          msg = 'SUPPRIMER ' + count + ' lead(s) ? Cette action est irréversible.';
        }
        return confirm(msg);
      }
      </script>

    <?php endif; ?>
</div>

<!-- Lead Detail Modal -->
<div class="lead-modal-overlay" id="leadModalOverlay">
  <div class="lead-modal" id="leadModal">
    <div class="lead-modal-header">
      <h2><i class="fas fa-user-circle"></i> <span id="leadModalTitle">Fiche Lead</span></h2>
      <button type="button" class="lead-modal-close" onclick="closeLeadModal()" title="Fermer"><i class="fas fa-times"></i></button>
    </div>
    <div class="lead-modal-body" id="leadModalBody">
      <div class="lead-modal-loading">
        <i class="fas fa-spinner fa-spin"></i>
        Chargement...
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="lead-confirm-overlay" id="leadConfirmOverlay">
  <div class="lead-confirm-box">
    <div class="lead-confirm-icon"><i class="fas fa-trash-alt"></i></div>
    <div class="lead-confirm-title">Supprimer ce lead ?</div>
    <div class="lead-confirm-desc" id="leadConfirmDesc">Cette action est irréversible. Toutes les données, notes et activités associées seront définitivement supprimées.</div>
    <div class="lead-confirm-buttons">
      <button type="button" class="lead-confirm-btn" onclick="closeConfirmModal()"><i class="fas fa-times"></i> Annuler</button>
      <form id="leadDeleteForm" method="POST" action="" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Controllers\AuthController::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>" id="deleteFormCsrf">
        <input type="hidden" name="id" value="" id="deleteFormId">
        <button type="submit" class="lead-confirm-btn confirm-delete"><i class="fas fa-trash-alt"></i> Supprimer</button>
      </form>
    </div>
  </div>
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
        if (data.csrf_token) { csrfToken = data.csrf_token; }
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

// ── Lead Detail Modal Logic ──
var _leadModalCsrf = <?= json_encode(\App\Controllers\AuthController::generateCsrfToken(), JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function openLeadModal(id) {
  var overlay = document.getElementById('leadModalOverlay');
  var body = document.getElementById('leadModalBody');
  var title = document.getElementById('leadModalTitle');
  overlay.classList.add('visible');
  document.body.style.overflow = 'hidden';
  body.innerHTML = '<div class="lead-modal-loading"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>';
  title.textContent = 'Lead #' + id;

  fetch('/admin/leads/ajax-detail?id=' + id)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) {
        body.innerHTML = '<div class="lead-modal-loading"><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> ' + esc(data.error || 'Erreur') + '</div>';
        return;
      }
      if (data.csrf_token) _leadModalCsrf = data.csrf_token;
      renderLeadModal(data);
    })
    .catch(function() {
      body.innerHTML = '<div class="lead-modal-loading"><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Erreur réseau</div>';
    });
}

function closeLeadModal() {
  document.getElementById('leadModalOverlay').classList.remove('visible');
  document.body.style.overflow = '';
}

function closeConfirmModal() {
  document.getElementById('leadConfirmOverlay').classList.remove('visible');
}

function openDeleteConfirm(id, nom) {
  var overlay = document.getElementById('leadConfirmOverlay');
  document.getElementById('leadDeleteForm').action = '/admin/leads/delete/' + id;
  document.getElementById('deleteFormId').value = id;
  document.getElementById('deleteFormCsrf').value = _leadModalCsrf;
  document.getElementById('leadConfirmDesc').textContent = 'Supprimer le lead ' + (nom ? '"' + nom + '"' : '#' + id) + ' ? Cette action est irréversible.';
  overlay.classList.add('visible');
}

// Close modal on overlay click
document.getElementById('leadModalOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeLeadModal();
});
document.getElementById('leadConfirmOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeConfirmModal();
});
// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    if (document.getElementById('leadConfirmOverlay').classList.contains('visible')) {
      closeConfirmModal();
    } else if (document.getElementById('leadModalOverlay').classList.contains('visible')) {
      closeLeadModal();
    }
  }
});

function esc(s) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(s || ''));
  return d.innerHTML;
}

function renderLeadModal(data) {
  var lead = data.lead;
  var notes = data.notes || [];
  var activities = data.activities || [];
  var partenaire = data.partenaire;

  var title = document.getElementById('leadModalTitle');
  title.textContent = (lead.nom ? lead.nom : 'Lead') + ' #' + lead.id;

  var scoreBadge = getScoreBadgeClass(lead.score);
  var statutLabel = getStatutLabel(lead.statut);
  var estimation = lead.estimation ? numberFormat(lead.estimation) + ' \u20ac' : '-';
  var surface = lead.surface_m2 ? lead.surface_m2 + ' m\u00b2' : '-';

  var html = '';

  // ── Contact & Bien ──
  html += '<div class="lead-modal-section">';
  html += '<div class="lead-modal-section-title"><i class="fas fa-user"></i> Contact & Bien</div>';
  html += '<div class="lead-modal-grid">';
  html += field('Nom', lead.nom);
  html += field('Email', lead.email);
  html += field('T\u00e9l\u00e9phone', lead.telephone);
  html += field('Ville', lead.ville);
  html += field('Adresse', lead.adresse);
  html += field('Type de bien', lead.type_bien);
  html += field('Surface', surface);
  html += field('Pi\u00e8ces', lead.pieces);
  html += field('Estimation', estimation);
  html += field('Urgence', lead.urgence);
  html += field('Motivation', lead.motivation);
  html += field('Score', lead.score ? '<span class="leads-badge ' + scoreBadge + '">' + esc(lead.score) + '</span>' : '-', true);
  html += field('Statut', '<span class="leads-badge leads-badge-nouveau">' + esc(statutLabel) + '</span>', true);
  html += field('Type', lead.lead_type === 'qualifie' ? '<span class="leads-badge leads-badge-qualifie">Qualifi\u00e9</span>' : '<span class="leads-badge leads-badge-tendance">Tendance</span>', true);
  html += '</div></div>';

  // ── NeuropPersona ──
  html += '<hr class="lead-modal-divider">';
  html += '<div class="lead-modal-section">';
  html += '<div class="lead-modal-section-title"><i class="fas fa-brain"></i> Profil NeuropPersona</div>';
  html += '<div class="lead-modal-neuropersona">';
  html += '<div class="lead-modal-neuropersona-title"><i class="fas fa-lightbulb"></i> ' + getPersonaTitle(lead) + '</div>';
  html += '<div class="lead-modal-neuropersona-desc">' + getPersonaDescription(lead) + '</div>';
  html += '</div></div>';

  // ── Actions conseill\u00e9es ──
  html += '<hr class="lead-modal-divider">';
  html += '<div class="lead-modal-section">';
  html += '<div class="lead-modal-section-title"><i class="fas fa-tasks"></i> Actions conseill\u00e9es</div>';
  html += '<ul class="lead-modal-actions-list">';
  var actions = getRecommendedActions(lead);
  for (var i = 0; i < actions.length; i++) {
    html += '<li><i class="fas fa-chevron-right"></i> ' + actions[i] + '</li>';
  }
  html += '</ul></div>';

  // ── Pipeline ──
  if (lead.partenaire_id || lead.commission_taux || lead.date_mandat || lead.prix_vente) {
    html += '<hr class="lead-modal-divider">';
    html += '<div class="lead-modal-section">';
    html += '<div class="lead-modal-section-title"><i class="fas fa-chart-line"></i> Pipeline</div>';
    html += '<div class="lead-modal-grid">';
    if (partenaire) html += field('Partenaire', partenaire.nom || partenaire.entreprise);
    html += field('Commission taux', lead.commission_taux ? lead.commission_taux + ' %' : null);
    html += field('Commission montant', lead.commission_montant ? numberFormat(lead.commission_montant) + ' \u20ac' : null);
    html += field('Assign\u00e9 \u00e0', lead.assigne_a);
    html += field('Date mandat', lead.date_mandat);
    html += field('Date compromis', lead.date_compromis);
    html += field('Date signature', lead.date_signature);
    html += field('Prix de vente', lead.prix_vente ? numberFormat(lead.prix_vente) + ' \u20ac' : null);
    html += '</div></div>';
  }

  // ── Notes CRM ──
  if (notes.length > 0) {
    html += '<hr class="lead-modal-divider">';
    html += '<div class="lead-modal-section">';
    html += '<div class="lead-modal-section-title"><i class="fas fa-sticky-note"></i> Notes CRM (' + notes.length + ')</div>';
    html += '<div class="lead-modal-notes">';
    for (var n = 0; n < notes.length; n++) {
      html += '<div class="lead-modal-note-item">';
      html += esc(notes[n].content);
      html += '<div class="lead-modal-note-meta">' + esc(notes[n].author) + ' \u00b7 ' + esc(notes[n].created_at) + '</div>';
      html += '</div>';
    }
    html += '</div></div>';
  }

  // ── Activit\u00e9 r\u00e9cente ──
  if (activities.length > 0) {
    html += '<hr class="lead-modal-divider">';
    html += '<div class="lead-modal-section">';
    html += '<div class="lead-modal-section-title"><i class="fas fa-history"></i> Activit\u00e9 r\u00e9cente</div>';
    for (var a = 0; a < Math.min(activities.length, 8); a++) {
      html += '<div class="lead-modal-activity-item"><i class="fas fa-circle"></i> <span>' + esc(activities[a].description) + ' <em style="font-size:0.7rem;">(' + esc(activities[a].created_at) + ')</em></span></div>';
    }
    html += '</div>';
  }

  // ── Dates ──
  html += '<hr class="lead-modal-divider">';
  html += '<div class="lead-modal-section">';
  html += '<div class="lead-modal-section-title"><i class="fas fa-calendar"></i> Informations</div>';
  html += '<div class="lead-modal-grid">';
  html += field('Cr\u00e9\u00e9 le', lead.created_at);
  html += field('ID', '#' + lead.id);
  html += '</div></div>';

  // ── Footer ──
  html += '<div class="lead-modal-footer">';
  html += '<div>';
  html += '<button type="button" class="lead-modal-btn danger" onclick="openDeleteConfirm(' + lead.id + ', \'' + esc(lead.nom || '').replace(/'/g, "\\'") + '\')"><i class="fas fa-trash-alt"></i> Supprimer</button>';
  html += '</div>';
  html += '<div style="display:flex;gap:0.5rem;">';
  html += '<a href="/admin/leads/edit/' + lead.id + '" class="lead-modal-btn"><i class="fas fa-edit"></i> Modifier</a>';
  html += '<a href="/admin/leads/' + lead.id + '" class="lead-modal-btn primary"><i class="fas fa-external-link-alt"></i> Fiche compl\u00e8te</a>';
  html += '</div>';
  html += '</div>';

  document.getElementById('leadModalBody').innerHTML = html;
}

function field(label, value, isHtml) {
  var v = isHtml ? (value || '<span class="lead-modal-field-value empty">-</span>') : (value ? esc(String(value)) : '<span class="lead-modal-field-value empty">-</span>');
  return '<div class="lead-modal-field"><span class="lead-modal-field-label">' + label + '</span><span class="lead-modal-field-value">' + v + '</span></div>';
}

function getScoreBadgeClass(score) {
  if (score === 'chaud') return 'leads-badge-chaud';
  if (score === 'tiede' || score === 'ti\u00e8de') return 'leads-badge-tiede';
  return 'leads-badge-froid';
}

function getStatutLabel(statut) {
  var labels = {
    'nouveau': 'Nouveau', 'contacte': 'Contact\u00e9', 'rdv_pris': 'RDV pris',
    'visite_realisee': 'Visite r\u00e9alis\u00e9e', 'mandat_simple': 'Mandat simple',
    'mandat_exclusif': 'Mandat exclusif', 'compromis_vente': 'Compromis',
    'signe': 'Sign\u00e9', 'co_signature_partenaire': 'Co-sign\u00e9', 'assigne_autre': 'Assign\u00e9'
  };
  return labels[statut] || statut || '-';
}

function getPersonaTitle(lead) {
  var persona = lead.neuropersona;
  var niveau = lead.niveau_conscience;
  var titles = {
    'analytique': 'Profil Analytique',
    'expressif': 'Profil Expressif',
    'directif': 'Profil Directif',
    'aimable': 'Profil Aimable'
  };
  var niveaux = {
    'inconscient': 'Inconscient du besoin',
    'probleme': 'Conscient du probl\u00e8me',
    'solution': 'Conscient de la solution',
    'produit': 'Conscient du produit',
    'tres_conscient': 'Tr\u00e8s conscient'
  };
  var parts = [];
  if (persona && titles[persona]) parts.push(titles[persona]);
  if (niveau && niveaux[niveau]) parts.push(niveaux[niveau]);
  if (parts.length === 0) return 'Profil \u00e0 d\u00e9terminer';
  return parts.join(' \u2014 ');
}

function getPersonaDescription(lead) {
  var score = lead.score || 'froid';
  var statut = lead.statut || 'nouveau';
  var persona = lead.neuropersona;
  var urgence = (lead.urgence || '').toLowerCase();
  var motivation = (lead.motivation || '').toLowerCase();
  var estimation = parseFloat(lead.estimation) || 0;
  var hasEmail = !!(lead.email);
  var hasPhone = !!(lead.telephone);

  var desc = '';

  // Persona-based description
  if (persona === 'analytique') {
    desc = 'Ce prospect a un profil analytique : il recherche des donn\u00e9es pr\u00e9cises, des comparatifs et des preuves tangibles. Privil\u00e9giez les rapports d\u00e9taill\u00e9s, les statistiques du march\u00e9 local et les \u00e9tudes de cas concr\u00e8tes pour le convaincre.';
  } else if (persona === 'expressif') {
    desc = 'Ce prospect a un profil expressif : il est guid\u00e9 par les \u00e9motions et la vision. Misez sur le storytelling, les t\u00e9moignages clients et la projection dans son projet immobilier pour cr\u00e9er un lien fort.';
  } else if (persona === 'directif') {
    desc = 'Ce prospect a un profil directif : il veut des r\u00e9sultats rapides et concrets. Soyez direct, pr\u00e9sentez les solutions efficacement et montrez votre expertise avec des r\u00e9sultats mesurables.';
  } else if (persona === 'aimable') {
    desc = 'Ce prospect a un profil aimable : il privil\u00e9gie la relation de confiance et la s\u00e9curit\u00e9. Prenez le temps d\'\u00e9couter ses besoins, rassurez-le et montrez votre accompagnement personnalis\u00e9.';
  } else {
    // Auto-generate based on available data
    if (score === 'chaud') {
      desc = 'Ce prospect montre un fort int\u00e9r\u00eat avec un score chaud. ';
    } else if (score === 'tiede') {
      desc = 'Ce prospect a un niveau d\'int\u00e9r\u00eat mod\u00e9r\u00e9. ';
    } else {
      desc = 'Ce prospect est encore en phase d\'\u00e9valuation. ';
    }

    if (estimation > 500000) {
      desc += 'Avec une estimation de ' + numberFormat(estimation) + ' \u20ac, c\'est un bien haut de gamme qui n\u00e9cessite une approche premium.';
    } else if (estimation > 300000) {
      desc += 'Le bien estim\u00e9 \u00e0 ' + numberFormat(estimation) + ' \u20ac se situe dans le segment milieu-haut du march\u00e9 bordelais.';
    } else if (estimation > 0) {
      desc += 'Le bien estim\u00e9 \u00e0 ' + numberFormat(estimation) + ' \u20ac repr\u00e9sente une opportunit\u00e9 accessible sur le march\u00e9.';
    }

    if (urgence === 'rapide' || urgence === 'urgent') {
      desc += ' Le prospect exprime une urgence \u00e9lev\u00e9e \u2014 la r\u00e9activit\u00e9 est cl\u00e9.';
    }

    if (motivation === 'vente') {
      desc += ' Sa motivation est la vente de son bien.';
    } else if (motivation === 'achat') {
      desc += ' Sa motivation est l\'achat d\'un nouveau bien.';
    }
  }

  return desc;
}

function getRecommendedActions(lead) {
  var actions = [];
  var score = lead.score || 'froid';
  var statut = lead.statut || 'nouveau';
  var hasEmail = !!(lead.email);
  var hasPhone = !!(lead.telephone);
  var urgence = (lead.urgence || '').toLowerCase();

  if (statut === 'nouveau') {
    if (hasPhone) actions.push('<strong>Appeler le prospect</strong> dans les 24h pour \u00e9tablir le premier contact et qualifier son besoin.');
    if (hasEmail) actions.push('<strong>Envoyer un email de bienvenue</strong> avec le r\u00e9sultat de l\'estimation et une pr\u00e9sentation de vos services.');
    actions.push('<strong>Qualifier le lead</strong> : v\u00e9rifier ses coordonn\u00e9es, son projet et son timing.');
  }

  if (statut === 'contacte') {
    actions.push('<strong>Planifier un rendez-vous</strong> pour une visite ou un \u00e9change approfondi sur son projet.');
    actions.push('<strong>Envoyer un dossier comparatif</strong> du march\u00e9 de son secteur pour d\u00e9montrer votre expertise.');
  }

  if (statut === 'rdv_pris') {
    actions.push('<strong>Pr\u00e9parer le rendez-vous</strong> : \u00e9tude de march\u00e9 locale, comparables r\u00e9cents, argumentaire personnalis\u00e9.');
    actions.push('<strong>Envoyer une confirmation</strong> par SMS/email avec l\'adresse et l\'heure du rendez-vous.');
  }

  if (statut === 'visite_realisee') {
    actions.push('<strong>Envoyer un compte-rendu</strong> de la visite avec votre avis de valeur d\u00e9taill\u00e9.');
    actions.push('<strong>Proposer un mandat</strong> en mettant en avant vos atouts diff\u00e9renciateurs.');
  }

  if (statut === 'mandat_simple' || statut === 'mandat_exclusif') {
    actions.push('<strong>Lancer la commercialisation</strong> : photos pro, annonces, r\u00e9seaux, base acheteurs.');
    actions.push('<strong>Reporter r\u00e9guli\u00e8rement</strong> au vendeur : visites planifi\u00e9es, retours acheteurs.');
  }

  if (statut === 'compromis_vente') {
    actions.push('<strong>Suivre les conditions suspensives</strong> et la lev\u00e9e de financement.');
    actions.push('<strong>Coordonner avec le notaire</strong> pour la pr\u00e9paration de l\'acte authentique.');
  }

  if (score === 'chaud' && statut === 'nouveau') {
    actions.unshift('<strong>\u26a0\ufe0f Priorit\u00e9 haute</strong> : ce lead chaud doit \u00eatre contact\u00e9 dans l\'heure si possible.');
  }

  if (score === 'froid' && (statut === 'nouveau' || statut === 'contacte')) {
    actions.push('<strong>Mettre en nurturing</strong> : ajouter \u00e0 une s\u00e9quence email automatique pour maintenir le contact.');
  }

  if (urgence === 'rapide' || urgence === 'urgent') {
    actions.push('<strong>Acc\u00e9l\u00e9rer le processus</strong> : le prospect a signal\u00e9 une urgence. Privil\u00e9gier les actions imm\u00e9diates.');
  }

  if (!lead.neuropersona) {
    actions.push('<strong>D\u00e9finir le profil NeuropPersona</strong> via la <a href="/admin/leads/' + lead.id + '" style="color:var(--admin-primary);">fiche compl\u00e8te</a> pour personnaliser l\'approche commerciale.');
  }

  if (actions.length === 0) {
    actions.push('Aucune action prioritaire identifi\u00e9e. Continuez le suivi r\u00e9gulier.');
  }

  return actions;
}

function numberFormat(n) {
  return Math.round(parseFloat(n)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}
</script>
