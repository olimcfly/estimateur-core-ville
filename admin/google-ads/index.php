<?php
/**
 * Google Ads Campaign Dashboard
 */

declare(strict_types=1);
defined('IMMO_ADMIN') or die();

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
    'draft'    => 'draft',
    'active'   => 'active',
    'paused'   => 'paused',
    'archived' => 'archived',
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
function gadsFilterUrl(array $override = []): string {
    $params = [
        'campaign_type' => $_GET['campaign_type'] ?? '',
        'status'        => $_GET['status'] ?? '',
        'ville'         => $_GET['ville'] ?? '',
        'page'          => $_GET['page'] ?? '1',
    ];
    $params = array_merge($params, $override);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== '1');
    $qs = http_build_query($params);
    return '/admin/google-ads/' . ($qs ? '?' . $qs : '');
}

include dirname(__DIR__) . '/partials/sidebar.php';
include dirname(__DIR__) . '/partials/topbar.php';
?>

<link rel="stylesheet" href="/admin/google-ads/assets/google-ads.css">

<div class="gads-wrap">

<!-- Hero banner -->
<div class="gads-hero">
  <h1><i class="fas fa-bullhorn"></i> Google Ads Campaigns</h1>
  <p>Gerez vos campagnes, annonces et landing pages</p>
</div>

<!-- KPI Cards -->
<div class="gads-kpi-grid">
  <div class="gads-kpi-card">
    <div class="gads-kpi-icon gads-kpi-icon--blue"><i class="fas fa-bullhorn"></i></div>
    <div class="gads-kpi-value"><?= (int) $kpi['total'] ?></div>
    <div class="gads-kpi-label">Total campagnes</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-icon gads-kpi-icon--green"><i class="fas fa-play-circle"></i></div>
    <div class="gads-kpi-value"><?= (int) $kpi['active'] ?></div>
    <div class="gads-kpi-label">Actives</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-icon gads-kpi-icon--yellow"><i class="fas fa-pencil-alt"></i></div>
    <div class="gads-kpi-value"><?= (int) $kpi['draft'] ?></div>
    <div class="gads-kpi-label">Brouillons</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-icon gads-kpi-icon--purple"><i class="fas fa-file-code"></i></div>
    <div class="gads-kpi-value"><?= (int) $kpi['with_landing'] ?></div>
    <div class="gads-kpi-label">Avec landing</div>
  </div>
</div>

<!-- Filter bar -->
<div class="gads-filter-bar">
  <form method="GET" action="/admin/google-ads/" id="gads-filter-form">
    <div class="gads-form-group">
      <label><i class="fas fa-tag"></i> Type</label>
      <select name="campaign_type" class="gads-select" onchange="this.form.submit()">
        <option value="">Tous les types</option>
        <?php foreach ($type_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterType === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gads-form-group">
      <label><i class="fas fa-toggle-on"></i> Statut</label>
      <select name="status" class="gads-select" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <?php foreach ($status_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gads-form-group" style="min-width:220px;">
      <label><i class="fas fa-map-marker-alt"></i> Ville</label>
      <div class="gads-input-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="ville" class="gads-input" value="<?= htmlspecialchars($filterVille) ?>" placeholder="Rechercher une ville..." id="gads-filter-ville">
      </div>
    </div>
  </form>
</div>

<?php if (empty($campaigns)): ?>
<!-- Empty state -->
<div class="gads-empty-state">
  <div class="gads-empty-icon"><i class="fas fa-bullhorn"></i></div>
  <p>Aucune campagne trouvee</p>
  <div class="gads-empty-hint">Commencez par creer votre premiere campagne Google Ads</div>
  <a href="/admin/google-ads/wizard.php" class="gads-btn gads-btn--primary"><i class="fas fa-plus"></i> Creer une campagne</a>
</div>

<?php else: ?>
<!-- Campaign grid -->
<div class="gads-campaign-list">
  <?php foreach ($campaigns as $c):
    $adsData = $c['ads_json'] ? json_decode($c['ads_json'], true) : null;
    $kwData  = $c['keywords_json'] ? json_decode($c['keywords_json'], true) : [];
    $hasLanding = !empty($c['landing_html']);
    $qs = (int) $c['quality_score'];
    $qsColor = $qs >= 7 ? 'var(--gads-green)' : ($qs >= 5 ? 'var(--gads-yellow)' : 'var(--gads-red)');
    $typeIcon = $type_icons[$c['campaign_type']] ?? 'fas fa-bullhorn';
    $typeClass = $c['campaign_type'] ?? 'estimation';
  ?>
  <div class="gads-campaign-card">
    <div class="gads-card-header">
      <div class="gads-card-type-icon gads-card-type-icon--<?= $typeClass ?>">
        <i class="<?= $typeIcon ?>"></i>
      </div>
      <div class="gads-card-title-wrap">
        <h3><?= htmlspecialchars($c['campaign_label']) ?></h3>
        <div class="gads-card-subtitle">
          <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['ville']) ?> &middot; <?= $type_labels[$c['campaign_type']] ?? $c['campaign_type'] ?>
        </div>
      </div>
      <span class="gads-badge gads-badge--<?= $status_colors[$c['status']] ?? 'draft' ?>">
        <?= $status_labels[$c['status']] ?? $c['status'] ?>
      </span>
    </div>

    <div class="gads-card-body">
      <div class="gads-card-tags">
        <?php if ($adsData): ?>
        <span class="gads-card-tag gads-card-tag--success"><i class="fas fa-check-circle"></i> Annonces</span>
        <?php else: ?>
        <span class="gads-card-tag gads-card-tag--neutral"><i class="fas fa-times-circle"></i> Pas d'annonce</span>
        <?php endif; ?>
        <span class="gads-card-tag gads-card-tag--info"><i class="fas fa-key"></i> <?= count($kwData) ?> mots-cles</span>
        <?php if ($hasLanding): ?>
        <span class="gads-card-tag gads-card-tag--success"><i class="fas fa-file-alt"></i> Landing</span>
        <?php endif; ?>
        <span class="gads-card-tag gads-card-tag--neutral"><i class="fas fa-coins"></i> <?= number_format((float) $c['budget_daily'], 2) ?> &euro;/j</span>
      </div>

      <?php if ($qs > 0): ?>
      <div class="gads-qs-bar">
        <div class="gads-qs-header">
          <span>Quality Score</span>
          <span><?= $qs ?>/10</span>
        </div>
        <div class="gads-qs-track">
          <div class="gads-qs-fill" style="width:<?= $qs * 10 ?>%;background:<?= $qsColor ?>;"></div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="gads-card-footer">
      <a href="/admin/google-ads/wizard.php?id=<?= $c['id'] ?>" class="gads-btn gads-btn--secondary" style="font-size:0.82rem;"><i class="fas fa-edit"></i> Modifier</a>
      <?php if ($hasLanding): ?>
      <a href="/admin/google-ads/preview.php?id=<?= $c['id'] ?>" target="_blank" class="gads-btn gads-btn--secondary" style="font-size:0.82rem;"><i class="fas fa-eye"></i> Preview</a>
      <?php endif; ?>
      <?php
        $toggleStatus = $c['status'] === 'active' ? 'paused' : 'active';
        $toggleLabel  = $c['status'] === 'active' ? 'Pause' : 'Activer';
        $toggleIcon   = $c['status'] === 'active' ? 'fa-pause' : 'fa-play';
      ?>
      <button class="gads-btn gads-btn--primary" style="font-size:0.82rem;margin-left:auto;"
        onclick="gadsToggle(<?= $c['id'] ?>, '<?= $toggleStatus ?>')"><i class="fas <?= $toggleIcon ?>"></i> <?= $toggleLabel ?></button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="gads-pagination">
  <?php if ($page > 1): ?>
    <a href="<?= gadsFilterUrl(['page' => $page - 1]) ?>" class="gads-btn gads-btn--secondary"><i class="fas fa-chevron-left"></i></a>
  <?php endif; ?>
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= gadsFilterUrl(['page' => $p]) ?>"
       class="gads-btn <?= $p === $page ? 'gads-btn--primary' : 'gads-btn--secondary' ?>"><?= $p ?></a>
  <?php endfor; ?>
  <?php if ($page < $totalPages): ?>
    <a href="<?= gadsFilterUrl(['page' => $page + 1]) ?>" class="gads-btn gads-btn--secondary"><i class="fas fa-chevron-right"></i></a>
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
