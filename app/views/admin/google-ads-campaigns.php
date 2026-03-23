<?php
/**
 * Google Ads Campaign Dashboard (admin view)
 */

use App\Core\Database;

$pdo = Database::connection();
$siteConfig = getSiteConfig();
$websiteId = (int) ($siteConfig['website_id'] ?? 1);

// ── Display maps ───────────────────────────────────────────
$type_labels = [
    'estimation' => 'Estimation',
    'vendre'     => 'Vendre',
    'avis'       => 'Avis',
];
$status_labels = [
    'draft'    => 'Brouillon',
    'active'   => 'Active',
    'paused'   => 'En pause',
    'archived' => 'Archivee',
];
$status_colors = [
    'draft'    => ['#6b6459', '#f4f1ed'],
    'active'   => ['#155724', '#d4edda'],
    'paused'   => ['#856404', '#fff3cd'],
    'archived' => ['#6b6459', '#e8e8e8'],
];
$type_icons = [
    'estimation' => 'fas fa-chart-line',
    'vendre'     => 'fas fa-home',
    'avis'       => 'fas fa-star',
];

// ── Filters (GET) ──────────────────────────────────────────
$filterType   = trim($_GET['campaign_type'] ?? '');
$filterStatus = trim($_GET['status'] ?? '');
$filterVille  = trim($_GET['ville'] ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 12;
$offset       = ($page - 1) * $perPage;

// ── KPI query ──────────────────────────────────────────────
$kpiStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'active') AS active,
        SUM(status = 'draft') AS draft,
        SUM(landing_html IS NOT NULL AND landing_html != '') AS with_landing
    FROM google_ads_campaigns
    WHERE website_id = ?
");
$kpiStmt->execute([$websiteId]);
$kpi = $kpiStmt->fetch();

// ── Build filtered query ───────────────────────────────────
$where  = ['website_id = ?'];
$params = [$websiteId];

if ($filterType !== '' && isset($type_labels[$filterType])) {
    $where[]  = 'campaign_type = ?';
    $params[] = $filterType;
}
if ($filterStatus !== '' && isset($status_labels[$filterStatus])) {
    $where[]  = 'status = ?';
    $params[] = $filterStatus;
}
if ($filterVille !== '') {
    $where[]  = 'ville LIKE ?';
    $params[] = '%' . $filterVille . '%';
}

$whereSql = implode(' AND ', $where);

// Count for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM google_ads_campaigns WHERE {$whereSql}");
$countStmt->execute($params);
$totalRows  = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $perPage));

// Fetch page
$dataParams = array_merge($params, [$perPage, $offset]);
$dataStmt = $pdo->prepare("
    SELECT * FROM google_ads_campaigns
    WHERE {$whereSql}
    ORDER BY updated_at DESC
    LIMIT ? OFFSET ?
");
$dataStmt->execute($dataParams);
$campaigns = $dataStmt->fetchAll();

// ── Filter query string helper ─────────────────────────────
$gadsFilterUrl = function (array $override = []): string {
    $params = [
        'campaign_type' => $_GET['campaign_type'] ?? '',
        'status'        => $_GET['status'] ?? '',
        'ville'         => $_GET['ville'] ?? '',
        'page'          => $_GET['page'] ?? '1',
    ];
    $params = array_merge($params, $override);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== '1');
    $qs = http_build_query($params);
    return '/admin/google-ads/campaigns' . ($qs ? '?' . $qs : '');
};
?>

<style>
  .gc-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .gc-page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
  }

  .gc-page-header h1 i { color: var(--admin-primary, #8B1538); }

  .gc-page-header p {
    color: var(--admin-muted, #6b6459);
    font-size: 0.9rem;
    margin: 0.25rem 0 0;
  }

  .gc-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  /* ── Stats Grid ─────────────────────────── */
  .gc-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .gc-stat-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .gc-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }

  .gc-stat-icon.total { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }
  .gc-stat-icon.active { background: rgba(34,197,94,0.1); color: #22c55e; }
  .gc-stat-icon.draft { background: rgba(212,175,55,0.1); color: var(--admin-accent, #D4AF37); }
  .gc-stat-icon.landing { background: rgba(99,102,241,0.1); color: #6366f1; }

  .gc-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    line-height: 1;
  }

  .gc-stat-label {
    font-size: 0.8rem;
    color: var(--admin-muted, #6b6459);
    margin-top: 4px;
  }

  /* ── Filter Card ────────────────────────── */
  .gc-filter-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
  }

  .gc-filter-card form {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: flex-end;
  }

  .gc-filter-group {
    flex: 1;
    min-width: 150px;
  }

  .gc-filter-group label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--admin-muted, #6b6459);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.3rem;
  }

  .gc-filter-group label i {
    margin-right: 0.25rem;
    font-size: 0.7rem;
  }

  .gc-filter-group select,
  .gc-filter-group input {
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    background: #fff;
    color: var(--admin-text, #1a1410);
    transition: border-color 0.15s;
  }

  .gc-filter-group select:focus,
  .gc-filter-group input:focus {
    outline: none;
    border-color: var(--admin-primary, #8B1538);
    box-shadow: 0 0 0 2px rgba(139, 21, 56, 0.1);
  }

  .gc-input-wrap {
    position: relative;
  }

  .gc-input-wrap > i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0a0a0;
    font-size: 0.8rem;
    pointer-events: none;
  }

  .gc-input-wrap input {
    padding-left: 2.25rem;
  }

  /* ── Campaign List ──────────────────────── */
  .gc-campaign-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
  }

  .gc-campaign-card {
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
    overflow: hidden;
    transition: box-shadow 0.15s;
  }

  .gc-campaign-card:hover {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  }

  .gc-card-header {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .gc-card-left {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
  }

  .gc-card-type-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
  }

  .gc-card-type-icon.estimation { background: rgba(139,21,56,0.1); color: var(--admin-primary, #8B1538); }
  .gc-card-type-icon.vendre { background: rgba(34,197,94,0.1); color: #22c55e; }
  .gc-card-type-icon.avis { background: rgba(212,175,55,0.1); color: var(--admin-accent, #D4AF37); }

  .gc-card-info { flex: 1; min-width: 0; }

  .gc-card-info h3 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    font-weight: 700;
    color: var(--admin-text, #1a1410);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .gc-card-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    font-size: 0.82rem;
    color: var(--admin-muted, #6b6459);
  }

  .gc-card-meta i {
    font-size: 0.72rem;
    opacity: 0.7;
    margin-right: 0.2rem;
  }

  .gc-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
  }

  .gc-card-tags {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
  }

  .gc-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
  }

  .gc-tag i { font-size: 0.68rem; }
  .gc-tag.success { background: rgba(34,197,94,0.1); color: #15803d; }
  .gc-tag.info { background: rgba(139,21,56,0.08); color: var(--admin-primary, #8B1538); }
  .gc-tag.neutral { background: #f4f1ed; color: #6b6459; }

  /* Quality score */
  .gc-qs-bar { margin-top: 0.5rem; }

  .gc-qs-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.72rem;
    color: var(--admin-muted, #6b6459);
    margin-bottom: 0.25rem;
    font-weight: 500;
  }

  .gc-qs-track {
    background: #e8dfd7;
    border-radius: 999px;
    height: 5px;
    overflow: hidden;
  }

  .gc-qs-fill {
    height: 100%;
    border-radius: 999px;
    transition: width 0.5s ease;
  }

  .gc-card-actions {
    display: flex;
    gap: 0.4rem;
    flex-shrink: 0;
  }

  .gc-btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 6px;
    border: 1px solid var(--admin-border, #e8dfd7);
    background: #fff;
    color: #6b6459;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.12s;
  }

  .gc-btn-icon:hover {
    background: #f4f1ed;
    color: var(--admin-primary, #8B1538);
    border-color: #ccc;
  }

  .gc-btn-icon.primary {
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border-color: var(--admin-primary, #8B1538);
  }

  .gc-btn-icon.primary:hover {
    opacity: 0.9;
  }

  .gc-card-footer {
    padding: 0.6rem 1.25rem;
    background: #faf9f7;
    border-top: 1px solid #f0ece6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .gc-card-date {
    font-size: 0.78rem;
    color: #999;
  }

  .gc-card-date i {
    margin-right: 0.2rem;
    font-size: 0.72rem;
  }

  /* ── Empty State ────────────────────────── */
  .gc-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background: var(--admin-surface, #ffffff);
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: var(--admin-radius, 12px);
  }

  .gc-empty-state .gc-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(139,21,56,0.1);
    color: var(--admin-primary, #8B1538);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }

  .gc-empty-state h3 {
    font-size: 1.1rem;
    margin: 0 0 0.5rem;
    color: var(--admin-text, #1a1410);
  }

  .gc-empty-state p {
    color: var(--admin-muted, #6b6459);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }

  /* ── Pagination ─────────────────────────── */
  .gc-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 1.5rem;
  }

  .gc-pagination a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 0.5rem;
    border: 1px solid var(--admin-border, #e8dfd7);
    border-radius: 6px;
    background: #fff;
    color: var(--admin-text, #1a1410);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.12s;
  }

  .gc-pagination a:hover {
    background: #f4f1ed;
    border-color: #ccc;
  }

  .gc-pagination a.active {
    background: var(--admin-primary, #8B1538);
    color: #fff;
    border-color: var(--admin-primary, #8B1538);
  }

  @media (max-width: 768px) {
    .gc-stats-grid { grid-template-columns: 1fr 1fr; }
    .gc-filter-card form { flex-direction: column; }
    .gc-filter-group { min-width: 100%; }
    .gc-card-header { flex-direction: column; }
    .gc-card-actions { align-self: flex-end; }
  }
</style>

<div class="container">

<!-- Page Header -->
<div class="gc-page-header">
  <div>
    <h1><i class="fas fa-rocket"></i> Generateur de Campagnes</h1>
    <p>Creez et gerez vos campagnes Google Ads, annonces et landing pages avec l'IA</p>
  </div>
  <div class="gc-header-actions">
    <a href="/admin/google-ads/wizard.php" class="btn" style="background: var(--admin-accent, #D4AF37); color: #1a1a1a; font-weight: 600;">
      <i class="fas fa-plus"></i> Nouvelle campagne
    </a>
    <a href="/admin/google-ads" class="btn btn-ghost">
      <i class="fas fa-book-open"></i> Guide Google Ads
    </a>
  </div>
</div>

<!-- KPI Cards -->
<div class="gc-stats-grid">
  <div class="gc-stat-card">
    <div class="gc-stat-icon total"><i class="fas fa-bullhorn"></i></div>
    <div>
      <div class="gc-stat-value"><?= (int) $kpi['total'] ?></div>
      <div class="gc-stat-label">Total campagnes</div>
    </div>
  </div>
  <div class="gc-stat-card">
    <div class="gc-stat-icon active"><i class="fas fa-play-circle"></i></div>
    <div>
      <div class="gc-stat-value"><?= (int) $kpi['active'] ?></div>
      <div class="gc-stat-label">Actives</div>
    </div>
  </div>
  <div class="gc-stat-card">
    <div class="gc-stat-icon draft"><i class="fas fa-pencil-alt"></i></div>
    <div>
      <div class="gc-stat-value"><?= (int) $kpi['draft'] ?></div>
      <div class="gc-stat-label">Brouillons</div>
    </div>
  </div>
  <div class="gc-stat-card">
    <div class="gc-stat-icon landing"><i class="fas fa-file-code"></i></div>
    <div>
      <div class="gc-stat-value"><?= (int) $kpi['with_landing'] ?></div>
      <div class="gc-stat-label">Avec landing</div>
    </div>
  </div>
</div>

<!-- Filter bar -->
<div class="gc-filter-card">
  <form method="GET" action="/admin/google-ads/campaigns" id="gads-filter-form">
    <div class="gc-filter-group">
      <label><i class="fas fa-tag"></i> Type</label>
      <select name="campaign_type" onchange="this.form.submit()">
        <option value="">Tous les types</option>
        <?php foreach ($type_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterType === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gc-filter-group">
      <label><i class="fas fa-toggle-on"></i> Statut</label>
      <select name="status" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <?php foreach ($status_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gc-filter-group" style="min-width:220px;">
      <label><i class="fas fa-map-marker-alt"></i> Ville</label>
      <div class="gc-input-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="ville" value="<?= htmlspecialchars($filterVille) ?>" placeholder="Rechercher une ville..." id="gads-filter-ville">
      </div>
    </div>
  </form>
</div>

<?php if (empty($campaigns)): ?>
<!-- Empty state -->
<div class="gc-empty-state">
  <div class="gc-empty-icon"><i class="fas fa-bullhorn"></i></div>
  <h3>Aucune campagne trouvee</h3>
  <p>Commencez par creer votre premiere campagne Google Ads avec l'IA</p>
  <a href="/admin/google-ads/wizard.php" class="btn" style="background: var(--admin-primary, #8B1538); color: #fff;">
    <i class="fas fa-plus"></i> Creer une campagne
  </a>
</div>

<?php else: ?>
<!-- Campaign list -->
<div class="gc-campaign-list">
  <?php foreach ($campaigns as $c):
    $adsData = $c['ads_json'] ? json_decode($c['ads_json'], true) : null;
    $kwData  = $c['keywords_json'] ? json_decode($c['keywords_json'], true) : [];
    $hasLanding = !empty($c['landing_html']);
    $qs = (int) $c['quality_score'];
    $qsColor = $qs >= 7 ? '#22c55e' : ($qs >= 5 ? '#D4AF37' : '#e24b4a');
    $typeIcon = $type_icons[$c['campaign_type']] ?? 'fas fa-bullhorn';
    $typeClass = $c['campaign_type'] ?? 'estimation';
    $stColor = $status_colors[$c['status']] ?? ['#6b6459', '#e8e8e8'];
  ?>
  <div class="gc-campaign-card">
    <div class="gc-card-header">
      <div class="gc-card-left">
        <div class="gc-card-type-icon <?= $typeClass ?>">
          <i class="<?= $typeIcon ?>"></i>
        </div>
        <div class="gc-card-info">
          <h3><?= htmlspecialchars($c['campaign_label']) ?></h3>
          <div class="gc-card-meta">
            <span class="gc-badge" style="color: <?= $stColor[0] ?>; background: <?= $stColor[1] ?>;">
              <?= $status_labels[$c['status']] ?? $c['status'] ?>
            </span>
            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['ville']) ?></span>
            <span><?= $type_labels[$c['campaign_type']] ?? $c['campaign_type'] ?></span>
          </div>
          <div class="gc-card-tags">
            <?php if ($adsData): ?>
            <span class="gc-tag success"><i class="fas fa-check-circle"></i> Annonces</span>
            <?php else: ?>
            <span class="gc-tag neutral"><i class="fas fa-times-circle"></i> Pas d'annonce</span>
            <?php endif; ?>
            <span class="gc-tag info"><i class="fas fa-key"></i> <?= count($kwData) ?> mots-cles</span>
            <?php if ($hasLanding): ?>
            <span class="gc-tag success"><i class="fas fa-file-alt"></i> Landing</span>
            <?php endif; ?>
            <span class="gc-tag neutral"><i class="fas fa-coins"></i> <?= number_format((float) $c['budget_daily'], 2) ?> &euro;/j</span>
          </div>

          <?php if ($qs > 0): ?>
          <div class="gc-qs-bar">
            <div class="gc-qs-header">
              <span>Quality Score</span>
              <span><?= $qs ?>/10</span>
            </div>
            <div class="gc-qs-track">
              <div class="gc-qs-fill" style="width:<?= $qs * 10 ?>%;background:<?= $qsColor ?>;"></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="gc-card-actions">
        <a href="/admin/google-ads/wizard.php?id=<?= $c['id'] ?>" class="gc-btn-icon" title="Modifier">
          <i class="fas fa-edit"></i>
        </a>
        <?php if ($hasLanding): ?>
        <a href="/admin/google-ads/preview.php?id=<?= $c['id'] ?>" target="_blank" class="gc-btn-icon" title="Preview">
          <i class="fas fa-eye"></i>
        </a>
        <?php endif; ?>
        <?php
          $toggleStatus = $c['status'] === 'active' ? 'paused' : 'active';
          $toggleIcon   = $c['status'] === 'active' ? 'fa-pause' : 'fa-play';
          $toggleTitle  = $c['status'] === 'active' ? 'Mettre en pause' : 'Activer';
        ?>
        <button class="gc-btn-icon primary" title="<?= $toggleTitle ?>"
          onclick="gadsToggle(<?= $c['id'] ?>, '<?= $toggleStatus ?>')">
          <i class="fas <?= $toggleIcon ?>"></i>
        </button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="gc-pagination">
  <?php if ($page > 1): ?>
    <a href="<?= $gadsFilterUrl(['page' => $page - 1]) ?>"><i class="fas fa-chevron-left"></i></a>
  <?php endif; ?>
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= $gadsFilterUrl(['page' => $p]) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
  <?php endfor; ?>
  <?php if ($page < $totalPages): ?>
    <a href="<?= $gadsFilterUrl(['page' => $page + 1]) ?>"><i class="fas fa-chevron-right"></i></a>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>
</div>

<script>
// Debounce ville filter
(function() {
  var villeInput = document.getElementById('gads-filter-ville');
  var timer;
  if (villeInput) {
    villeInput.addEventListener('input', function() {
      clearTimeout(timer);
      timer = setTimeout(function() {
        document.getElementById('gads-filter-form').submit();
      }, 500);
    });
  }
})();

// Toggle status
function gadsToggle(id, status) {
  fetch('/admin/google-ads/api/save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'toggle_status', id: id, status: status })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.ok) {
      location.reload();
    } else {
      alert('Erreur: ' + (data.error || 'Echec'));
    }
  })
  .catch(function(err) { alert('Erreur reseau: ' + err.message); });
}
</script>
