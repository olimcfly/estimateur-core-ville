<style>
  /* ─── Tab Navigation ────────────────────────────── */
  .sam-tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e8dfd7;
    margin-bottom: 2rem;
    overflow-x: auto;
  }

  .sam-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 1.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b6459;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
    white-space: nowrap;
  }

  .sam-tab:hover {
    color: var(--admin-primary, #8B1538);
    background: rgba(139, 21, 56, 0.04);
  }

  .sam-tab.active {
    color: var(--admin-primary, #8B1538);
    border-bottom-color: var(--admin-primary, #8B1538);
  }

  .sam-tab i { font-size: 0.95rem; }

  .sam-tab .tab-badge {
    background: var(--admin-primary, #8B1538);
    color: #fff;
    font-size: 0.7rem;
    padding: 0.1rem 0.45rem;
    border-radius: 10px;
    font-weight: 700;
  }

  .sam-tab-content { display: none; }
  .sam-tab-content.active { display: block; }

  /* ─── Page header ───────────────────────────────── */
  .sam-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .sam-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .sam-header h1 i { color: var(--admin-primary, #8B1538); }

  /* ─── Cards ─────────────────────────────────────── */
  .sam-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .sam-card h2 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .sam-card h2 i { color: var(--admin-primary, #8B1538); font-size: 0.95rem; }

  /* ─── SMTP Section ──────────────────────────────── */
  .smtp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
  }

  @media (max-width: 900px) {
    .smtp-grid { grid-template-columns: 1fr; }
  }

  .smtp-field {
    margin-bottom: 0.85rem;
  }

  .smtp-field label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1a1410;
    margin-bottom: 0.3rem;
  }

  .smtp-field .value {
    font-size: 0.88rem;
    color: #6b6459;
    padding: 0.5rem 0.75rem;
    background: #faf9f7;
    border-radius: 6px;
    font-family: 'DM Sans', monospace;
    word-break: break-all;
  }

  .smtp-field .value.masked { color: #9b958d; }

  .smtp-status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
  }

  .smtp-status.ok { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
  .smtp-status.warn { background: rgba(245, 158, 11, 0.1); color: #d97706; }

  /* ─── API Grid ──────────────────────────────────── */
  .api-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
  }

  @media (max-width: 750px) {
    .api-grid { grid-template-columns: 1fr; }
  }

  .api-mini-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    transition: box-shadow 0.2s;
  }

  .api-mini-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  }

  .api-mini-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #fff;
    flex-shrink: 0;
  }

  .api-mini-info { flex: 1; min-width: 0; }

  .api-mini-name {
    font-weight: 700;
    font-size: 0.9rem;
    color: #1a1410;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .api-mini-desc {
    font-size: 0.78rem;
    color: #6b6459;
    margin-top: 0.1rem;
  }

  .api-mini-keys {
    font-size: 0.72rem;
    color: #9b958d;
    margin-top: 0.25rem;
  }

  .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    display: inline-block;
  }
  .dot.green { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.15); }
  .dot.gray { background: #d1d5db; box-shadow: 0 0 0 3px rgba(209,213,219,0.3); }

  /* ─── AI Services (Tab 2) ───────────────────────── */
  .ai-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b6459;
    margin: 2rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e8dfd7;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .ai-section-title:first-child { margin-top: 0; }

  .ai-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 1.25rem;
  }

  @media (max-width: 800px) {
    .ai-grid { grid-template-columns: 1fr; }
  }

  .ai-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s, border-color 0.2s;
  }

  .ai-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.07);
  }

  .ai-card.recommended {
    border-color: #d4af37;
    border-width: 2px;
  }

  .ai-card-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #f1f0ed;
  }

  .ai-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    color: #fff;
    flex-shrink: 0;
  }

  .ai-card-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: #1a1410;
  }

  .ai-card-type {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
    margin-top: 0.2rem;
  }

  .ai-card-type.free { background: rgba(34,197,94,0.1); color: #16a34a; }
  .ai-card-type.paid { background: rgba(239,68,68,0.08); color: #dc2626; }
  .ai-card-type.freemium { background: rgba(59,130,246,0.08); color: #3b82f6; }

  .ai-card-body {
    padding: 1rem 1.25rem;
  }

  .ai-card-desc {
    font-size: 0.82rem;
    color: #4a4540;
    line-height: 1.5;
    margin-bottom: 0.75rem;
  }

  .ai-card-models {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
  }

  .ai-model-badge {
    display: inline-flex;
    padding: 0.2rem 0.55rem;
    background: #f4f1ed;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #6b6459;
    font-family: 'DM Sans', monospace;
  }

  .ai-card-pricing {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 0.75rem;
    background: #faf9f7;
    border-radius: 6px;
    font-size: 0.78rem;
    color: #6b6459;
  }

  .ai-card-pricing i { color: #d4af37; font-size: 0.75rem; }

  .ai-card-recommendation {
    margin-top: 0.75rem;
    padding: 0.6rem 0.8rem;
    background: rgba(212, 175, 55, 0.08);
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: 6px;
    font-size: 0.78rem;
    color: #92700c;
    display: flex;
    align-items: flex-start;
    gap: 0.4rem;
  }

  .ai-card-recommendation i { color: #d4af37; margin-top: 0.1rem; flex-shrink: 0; }

  .ai-card-footer {
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #f1f0ed;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .ai-card-footer .status {
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.35rem;
  }

  .ai-card-footer .status.active { color: #16a34a; }
  .ai-card-footer .status.inactive { color: #9b958d; }

  .ai-card-footer a {
    font-size: 0.8rem;
    color: var(--admin-primary, #8B1538);
    text-decoration: none;
    font-weight: 600;
  }

  .ai-card-footer a:hover { text-decoration: underline; }

  /* ─── Credits / Usage (Tab 3) ───────────────────── */
  .credits-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .credit-stat {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    padding: 1.25rem;
    text-align: center;
  }

  .credit-stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--admin-primary, #8B1538);
    font-family: 'Playfair Display', serif;
  }

  .credit-stat-label {
    font-size: 0.78rem;
    color: #6b6459;
    margin-top: 0.25rem;
    font-weight: 500;
  }

  .provider-usage-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    margin-bottom: 1rem;
    overflow: hidden;
  }

  .provider-usage-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: #faf9f7;
    border-bottom: 1px solid #e8dfd7;
  }

  .provider-usage-header .provider-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.9rem;
    flex-shrink: 0;
  }

  .provider-usage-header .provider-name {
    font-weight: 700;
    font-size: 0.95rem;
    flex: 1;
  }

  .provider-usage-header .provider-total {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--admin-primary, #8B1538);
  }

  .provider-usage-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
  }

  .provider-usage-table th {
    text-align: left;
    padding: 0.6rem 1rem;
    font-weight: 600;
    color: #6b6459;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid #f1f0ed;
  }

  .provider-usage-table td {
    padding: 0.6rem 1rem;
    border-bottom: 1px solid #f8f7f5;
    color: #4a4540;
  }

  .provider-usage-table tr:last-child td { border-bottom: none; }

  .usage-bar-container {
    width: 100%;
    height: 6px;
    background: #f1f0ed;
    border-radius: 3px;
    overflow: hidden;
  }

  .usage-bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
  }

  .feature-tag {
    display: inline-flex;
    padding: 0.2rem 0.5rem;
    background: #f4f1ed;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #6b6459;
  }

  /* ─── Buttons ───────────────────────────────────── */
  .sam-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
    border: none;
  }

  .sam-btn-primary {
    background: var(--admin-primary, #8B1538);
    color: #fff;
  }

  .sam-btn-primary:hover { background: #6b0f2d; }

  .sam-btn-secondary {
    background: #fff;
    color: #6b6459;
    border: 1px solid #e8dfd7;
  }

  .sam-btn-secondary:hover { border-color: var(--admin-primary, #8B1538); color: var(--admin-primary, #8B1538); }

  .sam-btn:disabled { opacity: 0.6; cursor: not-allowed; }

  .sam-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #9b958d;
  }

  .empty-state i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
  }

  .empty-state p {
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }

  .alert-info {
    padding: 0.75rem 1rem;
    background: rgba(59,130,246,0.06);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 6px;
    color: #2563eb;
    font-size: 0.82rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .category-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9b958d;
    margin-bottom: 0.75rem;
    padding-bottom: 0.35rem;
    border-bottom: 1px solid #f1f0ed;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 1.5rem;
  }

  .category-label:first-child { margin-top: 0; }
</style>

<?php
  $configuredApis = 0;
  $totalApis = count($apis);
  foreach ($apis as $api) {
    if ($api['configured']) $configuredApis++;
  }

  $smtpConfigured = ($smtp_host !== '' && $smtp_user !== '');

  $categories = [
    'ia' => ['label' => 'Intelligence Artificielle', 'icon' => 'fa-brain'],
    'geo' => ['label' => 'Geolocalisation', 'icon' => 'fa-map'],
    'comm' => ['label' => 'Communication', 'icon' => 'fa-comments'],
    'data' => ['label' => 'Donnees Publiques', 'icon' => 'fa-database'],
  ];

  $providerColors = [
    'openai' => '#10a37f',
    'claude' => '#d97706',
    'perplexity' => '#1fb8cd',
  ];

  $providerIcons = [
    'openai' => 'fa-brain',
    'claude' => 'fa-robot',
    'perplexity' => 'fa-magnifying-glass',
  ];
?>

<!-- Header -->
<div class="sam-header">
  <h1><i class="fas fa-cogs"></i> Administration SMTP, API & IA</h1>
</div>

<!-- Tab Navigation -->
<div class="sam-tabs">
  <a href="?tab=smtp-api" class="sam-tab <?= $tab === 'smtp-api' ? 'active' : '' ?>">
    <i class="fas fa-plug"></i> SMTP & API
    <span class="tab-badge"><?= $configuredApis + ($smtpConfigured ? 1 : 0) ?>/<?= $totalApis + 1 ?></span>
  </a>
  <a href="?tab=ia-services" class="sam-tab <?= $tab === 'ia-services' ? 'active' : '' ?>">
    <i class="fas fa-brain"></i> Services IA
    <span class="tab-badge"><?= count($ai_used) + count($ai_recommended) ?></span>
  </a>
  <a href="?tab=ia-credits" class="sam-tab <?= $tab === 'ia-credits' ? 'active' : '' ?>">
    <i class="fas fa-chart-line"></i> Credits & Utilisation IA
  </a>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- TAB 1: SMTP & API                                       -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="sam-tab-content <?= $tab === 'smtp-api' ? 'active' : '' ?>" id="tab-smtp-api">

  <!-- SMTP Section -->
  <div class="sam-card">
    <h2><i class="fas fa-envelope"></i> Configuration SMTP</h2>

    <?php if ($has_overrides): ?>
    <div class="alert-info">
      <i class="fas fa-info-circle"></i>
      Des surcharges SMTP sont actives. Les valeurs affichees peuvent differer du fichier .env.
    </div>
    <?php endif; ?>

    <div class="smtp-grid">
      <div>
        <div class="smtp-field">
          <label>Serveur SMTP</label>
          <div class="value"><?= htmlspecialchars($smtp_host ?: 'Non configure', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="smtp-field">
          <label>Port</label>
          <div class="value"><?= $smtp_port ?></div>
        </div>
        <div class="smtp-field">
          <label>Chiffrement</label>
          <div class="value"><?= htmlspecialchars(strtoupper($smtp_enc ?: 'Aucun'), ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>
      <div>
        <div class="smtp-field">
          <label>Utilisateur</label>
          <div class="value"><?= htmlspecialchars($smtp_user ?: 'Non configure', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="smtp-field">
          <label>Mot de passe</label>
          <div class="value masked"><?= $smtp_pass !== '' ? str_repeat('*', min(strlen($smtp_pass), 20)) : 'Non configure' ?></div>
        </div>
        <div class="smtp-field">
          <label>Expediteur</label>
          <div class="value"><?= htmlspecialchars(($mail_from_name ? $mail_from_name . ' ' : '') . '<' . ($mail_from ?: 'non defini') . '>', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>
    </div>

    <div style="margin-top:1rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
      <span class="smtp-status <?= $smtpConfigured ? 'ok' : 'warn' ?>">
        <i class="fas <?= $smtpConfigured ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
        <?= $smtpConfigured ? 'SMTP configure' : 'SMTP non configure' ?>
      </span>
      <a href="/admin/test-smtp" class="sam-btn sam-btn-secondary">
        <i class="fas fa-vial"></i> Tester SMTP
      </a>
    </div>
  </div>

  <!-- API Section -->
  <div class="sam-card">
    <h2><i class="fas fa-key"></i> Cles API configurees
      <span style="margin-left:auto;font-size:0.78rem;font-weight:500;color:#6b6459;"><?= $configuredApis ?>/<?= $totalApis ?> actives</span>
    </h2>

    <?php foreach ($categories as $catKey => $catInfo):
      $catApis = array_filter($apis, fn($a) => ($a['category'] ?? '') === $catKey);
      if (empty($catApis)) continue;
    ?>
      <div class="category-label">
        <i class="fas <?= $catInfo['icon'] ?>"></i> <?= $catInfo['label'] ?>
      </div>
      <div class="api-grid">
        <?php foreach ($catApis as $apiKey => $api): ?>
          <div class="api-mini-card">
            <div class="api-mini-icon" style="background:<?= htmlspecialchars($api['color'], ENT_QUOTES, 'UTF-8') ?>">
              <i class="fas <?= htmlspecialchars($api['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
            </div>
            <div class="api-mini-info">
              <div class="api-mini-name">
                <?= htmlspecialchars($api['name'], ENT_QUOTES, 'UTF-8') ?>
                <span class="dot <?= $api['configured'] ? 'green' : 'gray' ?>"></span>
              </div>
              <div class="api-mini-desc"><?= htmlspecialchars($api['description'], ENT_QUOTES, 'UTF-8') ?></div>
              <div class="api-mini-keys">
                <?= htmlspecialchars($api['pricing_info'], ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <div style="margin-top:1.25rem;">
      <a href="/admin/api-management" class="sam-btn sam-btn-secondary">
        <i class="fas fa-cog"></i> Configurer les API
      </a>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- TAB 2: Services IA                                      -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="sam-tab-content <?= $tab === 'ia-services' ? 'active' : '' ?>" id="tab-ia-services">

  <!-- IA utilisees -->
  <div class="ai-section-title">
    <i class="fas fa-check-circle" style="color:#16a34a"></i>
    IA utilisees dans la plateforme (<?= count($ai_used) ?>)
  </div>

  <div class="ai-grid">
    <?php foreach ($ai_used as $ai): ?>
      <div class="ai-card">
        <div class="ai-card-header">
          <div class="ai-card-icon" style="background:<?= htmlspecialchars($ai['color'], ENT_QUOTES, 'UTF-8') ?>">
            <i class="fas <?= htmlspecialchars($ai['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
          </div>
          <div>
            <div class="ai-card-title"><?= htmlspecialchars($ai['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <span class="ai-card-type <?= $ai['free_tier'] ? 'freemium' : 'paid' ?>">
              <i class="fas <?= $ai['free_tier'] ? 'fa-gift' : 'fa-credit-card' ?>"></i>
              <?= htmlspecialchars($ai['type'], ENT_QUOTES, 'UTF-8') ?>
            </span>
          </div>
        </div>
        <div class="ai-card-body">
          <div class="ai-card-desc"><?= htmlspecialchars($ai['usage'], ENT_QUOTES, 'UTF-8') ?></div>
          <div class="ai-card-models">
            <?php foreach ($ai['models'] as $model): ?>
              <span class="ai-model-badge"><?= htmlspecialchars($model, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
          <div class="ai-card-pricing">
            <i class="fas fa-coins"></i>
            <span><?= htmlspecialchars($ai['pricing'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
        <div class="ai-card-footer">
          <span class="status <?= $ai['configured'] ? 'active' : 'inactive' ?>">
            <span class="dot <?= $ai['configured'] ? 'green' : 'gray' ?>"></span>
            <?= $ai['configured'] ? 'Active' : 'Non configuree' ?>
          </span>
          <a href="<?= htmlspecialchars($ai['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
            Console <i class="fas fa-external-link-alt" style="font-size:0.65rem"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- IA recommandees -->
  <div class="ai-section-title">
    <i class="fas fa-star" style="color:#d4af37"></i>
    IA recommandees (<?= count($ai_recommended) ?>)
  </div>

  <div class="ai-grid">
    <?php foreach ($ai_recommended as $ai): ?>
      <div class="ai-card recommended">
        <div class="ai-card-header">
          <div class="ai-card-icon" style="background:<?= htmlspecialchars($ai['color'], ENT_QUOTES, 'UTF-8') ?>">
            <i class="fas <?= htmlspecialchars($ai['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
          </div>
          <div>
            <div class="ai-card-title"><?= htmlspecialchars($ai['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <span class="ai-card-type <?= $ai['free_tier'] ? ($ai['type'] === 'gratuite' ? 'free' : 'freemium') : 'paid' ?>">
              <i class="fas <?= $ai['free_tier'] ? 'fa-gift' : 'fa-credit-card' ?>"></i>
              <?= htmlspecialchars($ai['type'], ENT_QUOTES, 'UTF-8') ?>
            </span>
          </div>
        </div>
        <div class="ai-card-body">
          <div class="ai-card-desc"><?= htmlspecialchars($ai['usage'], ENT_QUOTES, 'UTF-8') ?></div>
          <div class="ai-card-models">
            <?php foreach ($ai['models'] as $model): ?>
              <span class="ai-model-badge"><?= htmlspecialchars($model, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
          <div class="ai-card-pricing">
            <i class="fas fa-coins"></i>
            <span><?= htmlspecialchars($ai['pricing'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <?php if (!empty($ai['recommendation'])): ?>
          <div class="ai-card-recommendation">
            <i class="fas fa-lightbulb"></i>
            <span><?= htmlspecialchars($ai['recommendation'], ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <?php endif; ?>
        </div>
        <div class="ai-card-footer">
          <span class="status inactive">
            <span class="dot gray"></span> Non integree
          </span>
          <a href="<?= htmlspecialchars($ai['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
            Decouvrir <i class="fas fa-external-link-alt" style="font-size:0.65rem"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- TAB 3: Credits & Utilisation IA                         -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="sam-tab-content <?= $tab === 'ia-credits' ? 'active' : '' ?>" id="tab-ia-credits">

  <?php if (empty($ai_usage['providers'])): ?>
    <div class="empty-state">
      <i class="fas fa-chart-bar"></i>
      <p>Aucune donnee d'utilisation IA disponible.<br>Les statistiques apparaitront ici au fur et a mesure de l'utilisation des services IA.</p>
      <div class="sam-actions" style="justify-content:center">
        <button class="sam-btn sam-btn-primary" onclick="createUsageTable()">
          <i class="fas fa-database"></i> Creer la table de suivi
        </button>
        <button class="sam-btn sam-btn-secondary" onclick="seedSampleData()" id="btn-seed" style="display:none">
          <i class="fas fa-seedling"></i> Inserer des donnees de demo
        </button>
      </div>
      <div id="table-msg" style="margin-top:1rem;font-size:0.82rem;"></div>
    </div>

  <?php else: ?>

    <!-- Summary Cards -->
    <div class="credits-summary">
      <div class="credit-stat">
        <div class="credit-stat-value"><?= number_format($ai_usage['total_calls']) ?></div>
        <div class="credit-stat-label">Appels API (30j)</div>
      </div>
      <div class="credit-stat">
        <div class="credit-stat-value"><?= number_format($ai_usage['total_tokens']) ?></div>
        <div class="credit-stat-label">Tokens utilises (30j)</div>
      </div>
      <div class="credit-stat">
        <div class="credit-stat-value">$<?= number_format($ai_usage['total_cost'], 4) ?></div>
        <div class="credit-stat-label">Cout estime (30j)</div>
      </div>
      <div class="credit-stat">
        <div class="credit-stat-value"><?= count($ai_usage['providers']) ?></div>
        <div class="credit-stat-label">Fournisseurs actifs</div>
      </div>
    </div>

    <!-- Per-Provider Breakdown -->
    <?php
      $maxTokens = 1;
      foreach ($ai_usage['providers'] as $p) {
        if ($p['total_tokens'] > $maxTokens) $maxTokens = $p['total_tokens'];
      }
    ?>
    <?php foreach ($ai_usage['providers'] as $providerKey => $provider): ?>
      <div class="provider-usage-card">
        <div class="provider-usage-header">
          <div class="provider-icon" style="background:<?= htmlspecialchars($providerColors[$providerKey] ?? '#6b6459', ENT_QUOTES, 'UTF-8') ?>">
            <i class="fas <?= htmlspecialchars($providerIcons[$providerKey] ?? 'fa-microchip', ENT_QUOTES, 'UTF-8') ?>"></i>
          </div>
          <span class="provider-name"><?= htmlspecialchars(ucfirst($providerKey), ENT_QUOTES, 'UTF-8') ?></span>
          <span class="provider-total"><?= number_format($provider['total_calls']) ?> appels &middot; <?= number_format($provider['total_tokens']) ?> tokens &middot; $<?= number_format($provider['total_cost'], 4) ?></span>
        </div>
        <table class="provider-usage-table">
          <thead>
            <tr>
              <th>Modele</th>
              <th>Appels</th>
              <th>Tokens (in/out)</th>
              <th>Cout</th>
              <th>Utilisation</th>
              <th>Dernier usage</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($provider['models'] as $model): ?>
              <tr>
                <td><span class="ai-model-badge"><?= htmlspecialchars($model['model'], ENT_QUOTES, 'UTF-8') ?></span></td>
                <td><?= number_format($model['calls']) ?></td>
                <td><?= number_format($model['input_tokens']) ?> / <?= number_format($model['output_tokens']) ?></td>
                <td>$<?= number_format($model['cost'], 4) ?></td>
                <td>
                  <div class="usage-bar-container">
                    <div class="usage-bar" style="width:<?= round(($model['total_tokens'] / $maxTokens) * 100) ?>%;background:<?= htmlspecialchars($providerColors[$providerKey] ?? '#6b6459', ENT_QUOTES, 'UTF-8') ?>"></div>
                  </div>
                </td>
                <td style="font-size:0.75rem;color:#9b958d"><?= htmlspecialchars($model['last_used'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>

    <!-- By Feature -->
    <?php if (!empty($ai_usage['by_feature'])): ?>
    <div class="sam-card">
      <h2><i class="fas fa-tags"></i> Utilisation par fonctionnalite (30j)</h2>
      <table class="provider-usage-table">
        <thead>
          <tr>
            <th>Fonctionnalite</th>
            <th>Appels</th>
            <th>Tokens</th>
            <th>Cout</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ai_usage['by_feature'] as $feat): ?>
            <tr>
              <td><span class="feature-tag"><?= htmlspecialchars($feat['feature'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td><?= number_format((int) $feat['calls']) ?></td>
              <td><?= number_format((int) $feat['tokens']) ?></td>
              <td>$<?= number_format((float) $feat['cost'], 4) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

<script>
function createUsageTable() {
  var msg = document.getElementById('table-msg');
  msg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creation de la table...';

  fetch('/admin/smtp-api/create-table', { method: 'POST' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        msg.innerHTML = '<span style="color:#16a34a"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
        document.getElementById('btn-seed').style.display = 'inline-flex';
      } else {
        msg.innerHTML = '<span style="color:#dc2626"><i class="fas fa-times-circle"></i> ' + (data.error || 'Erreur') + '</span>';
      }
    })
    .catch(function(err) {
      msg.innerHTML = '<span style="color:#dc2626">Erreur reseau: ' + err.message + '</span>';
    });
}

function seedSampleData() {
  var msg = document.getElementById('table-msg');
  msg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Insertion des donnees de demo...';

  fetch('/admin/smtp-api/seed-data', { method: 'POST' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        msg.innerHTML = '<span style="color:#16a34a"><i class="fas fa-check-circle"></i> ' + data.message + '. Rechargement...</span>';
        setTimeout(function() { window.location.href = '?tab=ia-credits'; }, 1000);
      } else {
        msg.innerHTML = '<span style="color:#dc2626"><i class="fas fa-times-circle"></i> ' + (data.error || 'Erreur') + '</span>';
      }
    })
    .catch(function(err) {
      msg.innerHTML = '<span style="color:#dc2626">Erreur reseau: ' + err.message + '</span>';
    });
}
</script>
