<style>
  /* ─── Period selector ──────────────────────────── */
  .ac-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .ac-toolbar h1 {
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
  }

  .ac-toolbar h1 i { color: var(--admin-primary, #8B1538); }

  .ac-period-selector {
    display: flex;
    gap: 0;
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 8px;
    overflow: hidden;
  }

  .ac-period-btn {
    padding: 0.55rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #6b6459;
    background: transparent;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
    border-right: 1px solid #e8dfd7;
  }

  .ac-period-btn:last-child { border-right: none; }

  .ac-period-btn:hover {
    background: rgba(139, 21, 56, 0.06);
    color: var(--admin-primary, #8B1538);
  }

  .ac-period-btn.active {
    background: var(--admin-primary, #8B1538);
    color: #fff;
  }

  /* ─── Summary cards (like Anthropic console) ──── */
  .ac-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .ac-summary-card {
    background: #faf8f5;
    border: 1px solid #e8dfd7;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
  }

  .ac-summary-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #6b6459;
    margin-bottom: 0.25rem;
  }

  .ac-summary-sublabel {
    font-size: 0.72rem;
    color: #9b958d;
    margin-bottom: 0.5rem;
    line-height: 1.3;
  }

  .ac-summary-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1a1410;
    font-family: 'DM Sans', sans-serif;
  }

  .ac-summary-value small {
    font-size: 1rem;
    font-weight: 600;
    color: #6b6459;
  }

  /* ─── Chart container ─────────────────────────── */
  .ac-chart-card {
    background: #faf8f5;
    border: 1px solid #e8dfd7;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .ac-chart-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1410;
    margin-bottom: 1.25rem;
  }

  .ac-chart-area {
    position: relative;
    height: 260px;
    display: flex;
    align-items: flex-end;
    gap: 0;
    padding-left: 50px;
    padding-bottom: 30px;
  }

  .ac-chart-y-axis {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 30px;
    width: 48px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: right;
    padding-right: 8px;
  }

  .ac-chart-y-label {
    font-size: 0.7rem;
    color: #9b958d;
    font-weight: 500;
  }

  .ac-chart-bars {
    flex: 1;
    display: flex;
    align-items: flex-end;
    gap: 2px;
    height: 100%;
    position: relative;
  }

  .ac-chart-gridlines {
    position: absolute;
    left: 50px;
    right: 0;
    top: 0;
    bottom: 30px;
    pointer-events: none;
  }

  .ac-chart-gridline {
    position: absolute;
    left: 0;
    right: 0;
    border-top: 1px dashed #e8dfd7;
  }

  .ac-bar-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    justify-content: flex-end;
    min-width: 0;
    position: relative;
  }

  .ac-bar-stack {
    width: 70%;
    max-width: 40px;
    min-width: 6px;
    display: flex;
    flex-direction: column-reverse;
    border-radius: 4px 4px 0 0;
    overflow: hidden;
    cursor: pointer;
    transition: opacity 0.15s;
  }

  .ac-bar-stack:hover { opacity: 0.85; }

  .ac-bar-segment {
    width: 100%;
    min-height: 0;
    transition: height 0.3s ease;
  }

  .ac-bar-label {
    position: absolute;
    bottom: -24px;
    font-size: 0.65rem;
    color: #9b958d;
    white-space: nowrap;
    transform: translateX(-50%);
    left: 50%;
  }

  .ac-bar-tooltip {
    display: none;
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #1a1410;
    color: #fff;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 10;
    pointer-events: none;
  }

  .ac-bar-group:hover .ac-bar-tooltip { display: block; }

  /* ─── Legend ───────────────────────────────────── */
  .ac-legend {
    display: flex;
    gap: 1.25rem;
    flex-wrap: wrap;
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e8dfd7;
  }

  .ac-legend-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    color: #6b6459;
    font-weight: 500;
  }

  .ac-legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
  }

  /* ─── Detail tables ───────────────────────────── */
  .ac-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 960px) {
    .ac-detail-grid { grid-template-columns: 1fr; }
  }

  .ac-detail-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
  }

  .ac-detail-card h2 {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .ac-detail-card h2 i {
    color: var(--admin-primary, #8B1538);
    font-size: 0.9rem;
  }

  .ac-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
  }

  .ac-table th {
    text-align: left;
    font-weight: 600;
    color: #6b6459;
    padding: 0.5rem 0.75rem;
    border-bottom: 2px solid #e8dfd7;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .ac-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #f0ebe4;
    color: #1a1410;
  }

  .ac-table tr:last-child td { border-bottom: none; }

  .ac-table .ac-cost {
    font-weight: 700;
    font-family: 'DM Sans', monospace;
  }

  .ac-provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-weight: 600;
    font-size: 0.8rem;
  }

  .ac-provider-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .ac-model-tag {
    display: inline-block;
    padding: 0.15rem 0.55rem;
    background: #f4f1ed;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b6459;
  }

  .ac-feature-tag {
    display: inline-block;
    padding: 0.15rem 0.55rem;
    background: rgba(139, 21, 56, 0.08);
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--admin-primary, #8B1538);
  }

  /* ─── Cost donut ──────────────────────────────── */
  .ac-donut-container {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1rem;
  }

  .ac-donut-svg { flex-shrink: 0; }

  .ac-donut-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .ac-donut-legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
  }

  .ac-donut-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
  }

  .ac-donut-legend-label { color: #6b6459; }

  .ac-donut-legend-value {
    font-weight: 700;
    color: #1a1410;
    margin-left: auto;
  }

  /* ─── Empty state ─────────────────────────────── */
  .ac-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: #9b958d;
  }

  .ac-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.4;
  }

  .ac-empty p {
    font-size: 0.95rem;
    line-height: 1.6;
  }

  .ac-empty .ac-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 1rem;
    text-decoration: none;
    transition: background 0.15s;
  }

  .ac-empty .ac-btn:hover { background: #6b0f2d; }

  /* ─── Links to consoles ───────────────────────── */
  .ac-console-links {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
  }

  .ac-console-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b6459;
    text-decoration: none;
    transition: all 0.15s;
  }

  .ac-console-link:hover {
    border-color: var(--admin-primary, #8B1538);
    color: var(--admin-primary, #8B1538);
    background: rgba(139, 21, 56, 0.04);
  }

  .ac-console-link i { font-size: 0.9rem; }
</style>

<?php
  // Provider colors & icons
  $providerColors = [
      'openai'     => '#10a37f',
      'claude'     => '#d97706',
      'perplexity' => '#1fb8cd',
      'google'     => '#4285f4',
      'mistral'    => '#ff7000',
      'groq'       => '#f55036',
      'deepseek'   => '#0066ff',
  ];

  $providerIcons = [
      'openai'     => 'fa-brain',
      'claude'     => 'fa-robot',
      'perplexity' => 'fa-magnifying-glass',
      'google'     => 'fa-gem',
      'mistral'    => 'fa-wind',
      'groq'       => 'fa-bolt',
      'deepseek'   => 'fa-microscope',
  ];

  $providerNames = [
      'openai'     => 'OpenAI',
      'claude'     => 'Claude (Anthropic)',
      'perplexity' => 'Perplexity AI',
      'google'     => 'Google',
      'mistral'    => 'Mistral AI',
      'groq'       => 'Groq',
      'deepseek'   => 'DeepSeek',
  ];

  $featureLabels = [
      'article_generation' => 'Generation articles',
      'email_generation'   => 'Generation emails',
      'seo_analysis'       => 'Analyse SEO',
      'market_research'    => 'Recherche marche',
      'image_generation'   => 'Generation images',
      'lead_scoring'       => 'Scoring leads',
  ];
?>

<!-- Toolbar -->
<div class="ac-toolbar">
  <h1><i class="fas fa-chart-line"></i> Couts & Utilisation API</h1>

  <div class="ac-period-selector">
    <a href="?period=7d" class="ac-period-btn <?= $period === '7d' ? 'active' : '' ?>">7 jours</a>
    <a href="?period=30d" class="ac-period-btn <?= $period === '30d' ? 'active' : '' ?>">30 jours</a>
    <a href="?period=month" class="ac-period-btn <?= $period === 'month' ? 'active' : '' ?>">Mois en cours</a>
    <a href="?period=3m" class="ac-period-btn <?= $period === '3m' ? 'active' : '' ?>">3 mois</a>
    <a href="?period=6m" class="ac-period-btn <?= $period === '6m' ? 'active' : '' ?>">6 mois</a>
    <a href="?period=12m" class="ac-period-btn <?= $period === '12m' ? 'active' : '' ?>">12 mois</a>
  </div>
</div>

<?php if (!$stats['table_exists']): ?>

  <div class="ac-empty">
    <i class="fas fa-chart-bar"></i>
    <p>La table de suivi n'existe pas encore.<br>Creez-la pour commencer a tracker les couts API.</p>
    <button class="ac-btn" onclick="createTable()">
      <i class="fas fa-database"></i> Creer la table de suivi
    </button>
    <div id="ac-table-msg" style="margin-top:1rem;font-size:0.82rem;"></div>
  </div>

<?php elseif (empty($stats['providers'])): ?>

  <div class="ac-empty">
    <i class="fas fa-chart-pie"></i>
    <p>Aucune donnee d'utilisation pour cette periode.<br>Les couts apparaitront automatiquement quand les API seront utilisees.</p>
    <a href="/admin/smtp-api?tab=ia-credits" class="ac-btn">
      <i class="fas fa-cogs"></i> Gerer les API
    </a>
  </div>

<?php else: ?>

  <!-- ═══ Summary Cards ═══ -->
  <div class="ac-summary">
    <div class="ac-summary-card">
      <div class="ac-summary-label">Cout total tokens</div>
      <div class="ac-summary-sublabel"><?= htmlspecialchars($stats['period_label'], ENT_QUOTES, 'UTF-8') ?></div>
      <div class="ac-summary-value">
        <?= number_format($stats['total_cost'], 2) ?> <small>USD</small>
      </div>
    </div>

    <div class="ac-summary-card">
      <div class="ac-summary-label">Appels API</div>
      <div class="ac-summary-sublabel"><?= htmlspecialchars($stats['period_label'], ENT_QUOTES, 'UTF-8') ?></div>
      <div class="ac-summary-value"><?= number_format($stats['total_calls']) ?></div>
    </div>

    <div class="ac-summary-card">
      <div class="ac-summary-label">Tokens utilises</div>
      <div class="ac-summary-sublabel">Input: <?= number_format($stats['total_input']) ?> / Output: <?= number_format($stats['total_output']) ?></div>
      <div class="ac-summary-value"><?= number_format($stats['total_tokens']) ?></div>
    </div>

    <div class="ac-summary-card">
      <div class="ac-summary-label">Fournisseurs actifs</div>
      <div class="ac-summary-sublabel"><?= htmlspecialchars($stats['period_label'], ENT_QUOTES, 'UTF-8') ?></div>
      <div class="ac-summary-value"><?= count($stats['providers']) ?></div>
    </div>
  </div>

  <!-- ═══ Daily Cost Chart ═══ -->
  <?php
    // Build daily data for chart
    $dailyByDate = [];
    $allProviders = [];
    foreach ($stats['daily'] as $d) {
        $day = $d['day'];
        $prov = $d['provider'];
        $dailyByDate[$day][$prov] = (float) $d['cost'];
        $allProviders[$prov] = true;
    }
    $allProviders = array_keys($allProviders);

    // Find max daily cost for Y axis
    $maxDailyCost = 0.01;
    foreach ($dailyByDate as $day => $provCosts) {
        $dayTotal = array_sum($provCosts);
        if ($dayTotal > $maxDailyCost) $maxDailyCost = $dayTotal;
    }

    // Round up max for nice Y axis
    if ($maxDailyCost <= 0.10) {
        $yMax = ceil($maxDailyCost * 100) / 100;
    } elseif ($maxDailyCost <= 1) {
        $yMax = ceil($maxDailyCost * 10) / 10;
    } else {
        $yMax = ceil($maxDailyCost);
    }
    if ($yMax == 0) $yMax = 0.10;

    $ySteps = 5;
    $yStep = $yMax / $ySteps;
    $chartHeight = 200; // px for bars area
  ?>

  <div class="ac-chart-card">
    <div class="ac-chart-title">Cout journalier par fournisseur</div>

    <?php if (!empty($dailyByDate)): ?>
    <div class="ac-chart-area" style="height:<?= $chartHeight + 30 ?>px">
      <!-- Y Axis labels -->
      <div class="ac-chart-y-axis" style="bottom:30px">
        <?php for ($i = $ySteps; $i >= 0; $i--): ?>
          <span class="ac-chart-y-label">$<?= number_format($yStep * $i, 2) ?></span>
        <?php endfor; ?>
      </div>

      <!-- Gridlines -->
      <div class="ac-chart-gridlines">
        <?php for ($i = 0; $i <= $ySteps; $i++): ?>
          <div class="ac-chart-gridline" style="top:<?= ($i / $ySteps) * 100 ?>%"></div>
        <?php endfor; ?>
      </div>

      <!-- Bars -->
      <div class="ac-chart-bars">
        <?php foreach ($dailyByDate as $day => $provCosts): ?>
          <?php
            $dayTotal = array_sum($provCosts);
            $dayFormatted = date('d M', strtotime($day));
          ?>
          <div class="ac-bar-group">
            <div class="ac-bar-tooltip">
              <strong><?= $dayFormatted ?></strong><br>
              <?php foreach ($provCosts as $prov => $cost): ?>
                <?= htmlspecialchars(ucfirst($prov), ENT_QUOTES, 'UTF-8') ?>: $<?= number_format($cost, 4) ?><br>
              <?php endforeach; ?>
              <strong>Total: $<?= number_format($dayTotal, 4) ?></strong>
            </div>
            <div class="ac-bar-stack" style="height:<?= ($dayTotal / $yMax) * $chartHeight ?>px">
              <?php foreach ($allProviders as $prov): ?>
                <?php if (isset($provCosts[$prov]) && $provCosts[$prov] > 0): ?>
                  <div class="ac-bar-segment" style="
                    height:<?= ($provCosts[$prov] / $dayTotal) * 100 ?>%;
                    background:<?= htmlspecialchars($providerColors[$prov] ?? '#9b958d', ENT_QUOTES, 'UTF-8') ?>;
                  "></div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <span class="ac-bar-label"><?= $dayFormatted ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Legend -->
    <div class="ac-legend">
      <?php foreach ($allProviders as $prov): ?>
        <span class="ac-legend-item">
          <span class="ac-legend-dot" style="background:<?= htmlspecialchars($providerColors[$prov] ?? '#9b958d', ENT_QUOTES, 'UTF-8') ?>"></span>
          <?= htmlspecialchars($providerNames[$prov] ?? ucfirst($prov), ENT_QUOTES, 'UTF-8') ?>
        </span>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
      <p style="color:#9b958d;text-align:center;padding:2rem">Pas de donnees journalieres pour cette periode.</p>
    <?php endif; ?>
  </div>

  <!-- ═══ Detail Grid: By Provider + By Feature ═══ -->
  <div class="ac-detail-grid">

    <!-- By Model -->
    <div class="ac-detail-card">
      <h2><i class="fas fa-microchip"></i> Detail par modele</h2>
      <table class="ac-table">
        <thead>
          <tr>
            <th>Fournisseur / Modele</th>
            <th>Appels</th>
            <th>Tokens</th>
            <th>Cout</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stats['by_model'] as $m): ?>
            <tr>
              <td>
                <span class="ac-provider-badge">
                  <span class="ac-provider-dot" style="background:<?= htmlspecialchars($providerColors[$m['provider']] ?? '#9b958d', ENT_QUOTES, 'UTF-8') ?>"></span>
                  <?= htmlspecialchars($providerNames[$m['provider']] ?? ucfirst($m['provider']), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <br>
                <span class="ac-model-tag"><?= htmlspecialchars($m['model'], ENT_QUOTES, 'UTF-8') ?></span>
              </td>
              <td><?= number_format((int) $m['calls']) ?></td>
              <td>
                <?= number_format((int) $m['input_t']) ?> <span style="color:#9b958d">in</span><br>
                <?= number_format((int) $m['output_t']) ?> <span style="color:#9b958d">out</span>
              </td>
              <td class="ac-cost">$<?= number_format((float) $m['cost'], 4) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- By Feature -->
    <div class="ac-detail-card">
      <h2><i class="fas fa-tags"></i> Utilisation par fonctionnalite</h2>
      <?php if (!empty($stats['by_feature'])): ?>
      <table class="ac-table">
        <thead>
          <tr>
            <th>Fonctionnalite</th>
            <th>Appels</th>
            <th>Tokens</th>
            <th>Cout</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stats['by_feature'] as $feat): ?>
            <tr>
              <td>
                <span class="ac-feature-tag">
                  <?= htmlspecialchars($featureLabels[$feat['feature']] ?? $feat['feature'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td><?= number_format((int) $feat['calls']) ?></td>
              <td><?= number_format((int) $feat['tokens']) ?></td>
              <td class="ac-cost">$<?= number_format((float) $feat['cost'], 4) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p style="color:#9b958d;font-size:0.85rem;text-align:center;padding:1.5rem">Pas de donnees par fonctionnalite.</p>
      <?php endif; ?>

      <!-- Repartition par fournisseur -->
      <h2 style="margin-top:1.5rem"><i class="fas fa-chart-pie"></i> Repartition des couts</h2>
      <?php
        $totalCost = $stats['total_cost'] ?: 1;
      ?>
      <div style="display:flex;flex-direction:column;gap:0.6rem">
        <?php foreach ($stats['providers'] as $prov => $data): ?>
          <?php $pct = ($data['cost'] / $totalCost) * 100; ?>
          <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem">
              <span class="ac-provider-badge">
                <span class="ac-provider-dot" style="background:<?= htmlspecialchars($providerColors[$prov] ?? '#9b958d', ENT_QUOTES, 'UTF-8') ?>"></span>
                <?= htmlspecialchars($providerNames[$prov] ?? ucfirst($prov), ENT_QUOTES, 'UTF-8') ?>
              </span>
              <span style="font-weight:700;font-size:0.82rem">$<?= number_format($data['cost'], 4) ?> (<?= number_format($pct, 1) ?>%)</span>
            </div>
            <div style="background:#f0ebe4;border-radius:4px;height:8px;overflow:hidden">
              <div style="height:100%;border-radius:4px;width:<?= number_format($pct, 1) ?>%;background:<?= htmlspecialchars($providerColors[$prov] ?? '#9b958d', ENT_QUOTES, 'UTF-8') ?>"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <!-- ═══ Console Links ═══ -->
  <div class="ac-console-links">
    <a href="https://console.anthropic.com/settings/billing" target="_blank" rel="noopener" class="ac-console-link">
      <i class="fas fa-robot"></i> Console Anthropic
    </a>
    <a href="https://platform.openai.com/usage" target="_blank" rel="noopener" class="ac-console-link">
      <i class="fas fa-brain"></i> Console OpenAI
    </a>
    <a href="https://docs.perplexity.ai/" target="_blank" rel="noopener" class="ac-console-link">
      <i class="fas fa-magnifying-glass"></i> Perplexity AI
    </a>
    <a href="/admin/smtp-api?tab=ia-credits" class="ac-console-link">
      <i class="fas fa-cogs"></i> Gestion SMTP / API / IA
    </a>
  </div>

<?php endif; ?>

<script>
function createTable() {
  var msg = document.getElementById('ac-table-msg');
  msg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creation de la table...';
  fetch('/admin/smtp-api/create-table', { method: 'POST' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        msg.innerHTML = '<span style="color:#16a34a"><i class="fas fa-check-circle"></i> ' + data.message + '. Rechargement...</span>';
        setTimeout(function() { location.reload(); }, 1000);
      } else {
        msg.innerHTML = '<span style="color:#dc2626"><i class="fas fa-times-circle"></i> ' + (data.error || 'Erreur') + '</span>';
      }
    })
    .catch(function(err) {
      msg.innerHTML = '<span style="color:#dc2626">Erreur reseau: ' + err.message + '</span>';
    });
}
</script>
