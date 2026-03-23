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
  <h1>Google Ads Campaigns</h1>
  <p>Gerez vos campagnes, annonces et landing pages</p>
</div>

<!-- KPI Cards -->
<div class="gads-kpi-grid">
  <div class="gads-kpi-card">
    <div class="gads-kpi-value"><?= (int) $kpi['total'] ?></div>
    <div class="gads-kpi-label">Total campagnes</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-value"><?= (int) $kpi['active'] ?></div>
    <div class="gads-kpi-label">Actives</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-value"><?= (int) $kpi['draft'] ?></div>
    <div class="gads-kpi-label">Brouillons</div>
  </div>
  <div class="gads-kpi-card">
    <div class="gads-kpi-value"><?= (int) $kpi['with_landing'] ?></div>
    <div class="gads-kpi-label">Avec landing</div>
  </div>
</div>

<!-- Filter bar -->
<div class="gads-section" style="margin-bottom:1.5rem;">
  <form method="GET" action="/admin/google-ads/" id="gads-filter-form" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
    <div class="gads-form-group" style="margin-bottom:0;flex:1;min-width:150px;">
      <label>Type</label>
      <select name="campaign_type" class="gads-select" onchange="this.form.submit()">
        <option value="">Tous</option>
        <?php foreach ($type_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterType === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gads-form-group" style="margin-bottom:0;flex:1;min-width:150px;">
      <label>Statut</label>
      <select name="status" class="gads-select" onchange="this.form.submit()">
        <option value="">Tous</option>
        <?php foreach ($status_labels as $val => $lbl): ?>
        <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="gads-form-group" style="margin-bottom:0;flex:1;min-width:200px;">
      <label>Ville</label>
      <input type="text" name="ville" class="gads-input" value="<?= htmlspecialchars($filterVille) ?>" placeholder="Rechercher une ville..." id="gads-filter-ville">
    </div>
  </form>
</div>

<?php if (empty($campaigns)): ?>
<!-- Empty state -->
<div class="gads-section" style="text-align:center;padding:3rem;">
  <p style="font-size:1.1rem;color:#718096;margin-bottom:1rem;">Aucune campagne trouvee</p>
  <a href="/admin/google-ads/wizard.php" class="gads-btn gads-btn--primary">Creer une campagne</a>
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
  ?>
  <div class="gads-campaign-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
      <h3><i class="<?= $typeIcon ?>" style="margin-right:0.4rem;color:var(--gads-accent);"></i><?= htmlspecialchars($c['campaign_label']) ?></h3>
      <span class="gads-badge gads-badge--<?= $status_colors[$c['status']] ?? 'draft' ?>">
        <?= $status_labels[$c['status']] ?? $c['status'] ?>
      </span>
    </div>
    <div style="font-size:0.85rem;color:#718096;margin-bottom:0.75rem;">
      <?= htmlspecialchars($c['ville']) ?> &middot; <?= $type_labels[$c['campaign_type']] ?? $c['campaign_type'] ?>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem;font-size:0.82rem;">
      <span style="background:#ebf5ff;color:#1a73e8;padding:0.15rem 0.5rem;border-radius:6px;">
        <?= $adsData ? 'Annonces OK' : 'Pas d\'annonce' ?>
      </span>
      <span style="background:#ebf5ff;color:#1a73e8;padding:0.15rem 0.5rem;border-radius:6px;">
        <?= count($kwData) ?> mots-cles
      </span>
      <?php if ($hasLanding): ?>
      <span style="background:#ecfdf5;color:#0d9f6e;padding:0.15rem 0.5rem;border-radius:6px;">Landing OK</span>
      <?php endif; ?>
      <span style="padding:0.15rem 0.5rem;border-radius:6px;background:#f7fafc;color:#4a5568;">
        <?= number_format((float) $c['budget_daily'], 2) ?> &euro;/jour
      </span>
    </div>

    <?php if ($qs > 0): ?>
    <div style="margin-bottom:0.75rem;">
      <div style="font-size:0.75rem;color:#718096;margin-bottom:0.2rem;">Quality Score: <?= $qs ?>/10</div>
      <div style="background:var(--gads-border);border-radius:4px;height:6px;overflow:hidden;">
        <div style="width:<?= $qs * 10 ?>%;height:100%;background:<?= $qsColor ?>;border-radius:4px;"></div>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
      <a href="/admin/google-ads/wizard.php?id=<?= $c['id'] ?>" class="gads-btn gads-btn--secondary" style="font-size:0.82rem;">Modifier</a>
      <?php if ($hasLanding): ?>
      <a href="/admin/google-ads/preview.php?id=<?= $c['id'] ?>" target="_blank" class="gads-btn gads-btn--secondary" style="font-size:0.82rem;">Previsualiser</a>
      <?php endif; ?>
      <?php
        $toggleStatus = $c['status'] === 'active' ? 'paused' : 'active';
        $toggleLabel  = $c['status'] === 'active' ? 'Pause' : 'Activer';
      ?>
      <button class="gads-btn gads-btn--primary" style="font-size:0.82rem;"
        onclick="gadsToggle(<?= $c['id'] ?>, '<?= $toggleStatus ?>')"><?= $toggleLabel ?></button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div style="display:flex;justify-content:center;gap:0.5rem;margin-bottom:2rem;">
  <?php if ($page > 1): ?>
    <a href="<?= gadsFilterUrl(['page' => $page - 1]) ?>" class="gads-btn gads-btn--secondary">&laquo; Precedent</a>
  <?php endif; ?>
  <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="<?= gadsFilterUrl(['page' => $p]) ?>"
       class="gads-btn <?= $p === $page ? 'gads-btn--primary' : 'gads-btn--secondary' ?>"
       style="min-width:2.5rem;text-align:center;"><?= $p ?></a>
  <?php endfor; ?>
  <?php if ($page < $totalPages): ?>
    <a href="<?= gadsFilterUrl(['page' => $page + 1]) ?>" class="gads-btn gads-btn--secondary">Suivant &raquo;</a>
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
