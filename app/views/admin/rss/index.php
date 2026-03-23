<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fas fa-rss"></i> Veille RSS</h1>
    <p class="admin-page-desc">Surveillez l'actualite immobiliere et generez des articles de blog a partir de vos flux RSS.</p>
  </div>
  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
    <form method="post" action="/admin/rss/fetch-all" style="display:inline;">
      <button type="submit" class="admin-btn admin-btn-primary" id="btn-fetch">
        <i class="fas fa-sync-alt"></i> Recuperer les flux
      </button>
    </form>
    <a href="/admin/rss/sources" class="admin-btn admin-btn-secondary">
      <i class="fas fa-cog"></i> Gerer les sources
    </a>
  </div>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<!-- Stats -->
<?php
  $totalSources = count($sources);
  $activeSources = count(array_filter($sources, fn($s) => $s['is_active']));
  $totalArticles = count($articles);
  $starredCount = count(array_filter($articles, fn($a) => $a['is_starred']));
?>
<div class="stats-bar">
  <div class="stat-item">
    <span class="stat-value"><?= $totalSources ?></span>
    <span class="stat-label">Sources</span>
  </div>
  <div class="stat-item stat-success">
    <span class="stat-value"><?= $activeSources ?></span>
    <span class="stat-label">Actives</span>
  </div>
  <div class="stat-item">
    <span class="stat-value"><?= $totalArticles ?></span>
    <span class="stat-label">Articles</span>
  </div>
  <div class="stat-item stat-ai">
    <span class="stat-value"><?= $starredCount ?></span>
    <span class="stat-label">Favoris</span>
  </div>
</div>

<!-- Filters -->
<div class="admin-card">
  <div class="admin-card-body" style="padding: 0.75rem 1.5rem;">
    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
      <span style="font-size: 0.85rem; font-weight: 600; color: var(--admin-muted);">Filtrer :</span>
      <a href="/admin/rss" class="admin-btn admin-btn-sm <?= ($filter ?? 'all') === 'all' ? 'admin-btn-primary' : 'admin-btn-secondary' ?>">Tous</a>
      <a href="/admin/rss?filter=starred" class="admin-btn admin-btn-sm <?= ($filter ?? '') === 'starred' ? 'admin-btn-primary' : 'admin-btn-secondary' ?>"><i class="fas fa-star"></i> Favoris</a>
      <a href="/admin/rss?filter=unused" class="admin-btn admin-btn-sm <?= ($filter ?? '') === 'unused' ? 'admin-btn-primary' : 'admin-btn-secondary' ?>">Non utilises</a>
      <a href="/admin/rss?filter=used" class="admin-btn admin-btn-sm <?= ($filter ?? '') === 'used' ? 'admin-btn-primary' : 'admin-btn-secondary' ?>"><i class="fas fa-check"></i> Utilises</a>

      <?php if (!empty($sources)): ?>
      <span style="margin-left: 1rem; color: var(--admin-border);">|</span>
      <select onchange="if(this.value) window.location='/admin/rss?source='+this.value; else window.location='/admin/rss';" style="padding: 0.3rem 0.5rem; border: 1px solid var(--admin-border); border-radius: 4px; font-size: 0.8rem;">
        <option value="">Toutes les sources</option>
        <?php foreach ($sources as $s): ?>
          <option value="<?= (int) $s['id'] ?>" <?= ($sourceFilter ?? null) == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Article Generation Form -->
<div class="admin-card ai-panel">
  <div class="admin-card-header">
    <h2><i class="fas fa-robot"></i> Generer un article de blog</h2>
    <span class="admin-badge" style="background: rgba(139,21,56,0.1); color: var(--admin-primary);">Depuis RSS</span>
  </div>
  <div class="admin-card-body">
    <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 1rem;">
      Cochez les articles ci-dessous, puis cliquez sur "Generer l'article". Claude/OpenAI redigera un article de blog original oriente Bordeaux a partir des sources selectionnees.
    </p>
    <form method="post" action="/admin/rss/generate" id="form-generate">
      <div id="selected-articles-summary" style="margin-bottom: 1rem; display: none;">
        <span class="admin-badge" style="background: rgba(139,21,56,0.1); color: var(--admin-primary);">
          <span id="selected-count">0</span> article(s) selectionne(s)
        </span>
      </div>
      <button type="submit" class="admin-btn admin-btn-primary" id="btn-generate" disabled>
        <i class="fas fa-magic"></i> Generer l'article de blog
      </button>
    </form>
  </div>
</div>

<!-- Articles List -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-newspaper"></i> Articles des flux RSS</h2>
    <span class="admin-badge"><?= count($articles) ?> articles</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <?php if (empty($articles)): ?>
      <div style="text-align: center; padding: 3rem; color: var(--admin-muted);">
        <i class="fas fa-rss" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.2;"></i>
        <p>Aucun article RSS. Ajoutez des sources et cliquez sur "Recuperer les flux".</p>
        <a href="/admin/rss/sources" class="admin-btn admin-btn-primary" style="margin-top: 1rem;">
          <i class="fas fa-plus"></i> Ajouter des sources
        </a>
      </div>
    <?php else: ?>
      <div class="rss-article-list">
        <?php foreach ($articles as $art): ?>
          <div class="rss-article-item <?= $art['is_used'] ? 'rss-article-used' : '' ?>">
            <div class="rss-article-check">
              <input type="checkbox" name="article_ids[]" value="<?= (int) $art['id'] ?>" form="form-generate"
                     class="rss-checkbox" <?= $art['is_used'] ? 'disabled' : '' ?>>
            </div>
            <div class="rss-article-content">
              <div class="rss-article-meta">
                <span class="rss-source-badge rss-zone-<?= $art['source_zone'] === 'Bordeaux/Nouvelle-Aquitaine' ? 'local' : 'national' ?>">
                  <?= e($art['source_name']) ?>
                </span>
                <?php if ($art['pub_date']): ?>
                  <span class="rss-article-date"><?= e(date('d/m/Y H:i', strtotime($art['pub_date']))) ?></span>
                <?php endif; ?>
                <?php if ($art['is_used']): ?>
                  <span class="admin-status admin-status-success"><i class="fas fa-check"></i> Utilise</span>
                <?php endif; ?>
              </div>
              <h3 class="rss-article-title">
                <a href="<?= e($art['link']) ?>" target="_blank" rel="noopener"><?= e($art['title']) ?></a>
              </h3>
              <?php if (!empty($art['description'])): ?>
                <p class="rss-article-desc"><?= e(mb_substr(strip_tags($art['description']), 0, 250)) ?><?= mb_strlen(strip_tags($art['description'])) > 250 ? '...' : '' ?></p>
              <?php endif; ?>
            </div>
            <div class="rss-article-actions">
              <button class="admin-btn admin-btn-sm admin-btn-ghost rss-star-btn <?= $art['is_starred'] ? 'rss-starred' : '' ?>"
                      data-id="<?= (int) $art['id'] ?>" title="Favori">
                <i class="<?= $art['is_starred'] ? 'fas' : 'far' ?> fa-star"></i>
              </button>
              <a href="<?= e($art['link']) ?>" target="_blank" rel="noopener"
                 class="admin-btn admin-btn-sm admin-btn-ghost" title="Lire la source">
                <i class="fas fa-external-link-alt"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Generation Logs -->
<?php if (!empty($generationLogs)): ?>
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-history"></i> Historique des generations</h2>
    <span class="admin-badge"><?= count($generationLogs) ?> entrees</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Articles sources</th>
            <th>Statut</th>
            <th>Erreur</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($generationLogs as $log): ?>
            <tr>
              <td style="font-size: 0.85rem;"><?= e(date('d/m/Y H:i', strtotime($log['created_at']))) ?></td>
              <td style="font-size: 0.85rem;"><?= e($log['rss_article_ids']) ?></td>
              <td>
                <?php if ($log['status'] === 'success'): ?>
                  <span class="admin-status admin-status-success"><i class="fas fa-check"></i> OK</span>
                <?php else: ?>
                  <span class="admin-status admin-status-danger"><i class="fas fa-times"></i> Erreur</span>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.8rem; color: #dc2626;"><?= e((string) ($log['error_message'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
  /* RSS Article List — page-specific styles */
  .rss-article-list { max-height: 800px; overflow-y: auto; }
  .rss-article-item { display: flex; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); transition: background 0.1s; align-items: flex-start; }
  .rss-article-item:hover { background: rgba(0,0,0,0.02); }
  .rss-article-used { opacity: 0.6; }
  .rss-article-check { padding-top: 0.2rem; flex-shrink: 0; }
  .rss-checkbox { width: 18px; height: 18px; cursor: pointer; accent-color: var(--admin-primary); }
  .rss-article-content { flex: 1; min-width: 0; }
  .rss-article-meta { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; margin-bottom: 0.3rem; }
  .rss-source-badge { font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.03em; }
  .rss-zone-local { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
  .rss-zone-national { background: rgba(139, 21, 56, 0.08); color: var(--admin-primary); }
  .rss-article-date { font-size: 0.75rem; color: var(--admin-muted); }
  .rss-article-title { font-size: 0.95rem; font-weight: 600; margin: 0 0 0.25rem 0; line-height: 1.3; }
  .rss-article-title a { color: var(--admin-text); text-decoration: none; }
  .rss-article-title a:hover { color: var(--admin-primary); }
  .rss-article-desc { font-size: 0.82rem; color: var(--admin-muted); margin: 0; line-height: 1.4; }
  .rss-article-actions { display: flex; gap: 0.25rem; flex-shrink: 0; }
  .rss-star-btn { color: var(--admin-muted); }
  .rss-star-btn.rss-starred, .rss-star-btn.rss-starred i { color: #D4AF37; }

  @media (max-width: 768px) {
    .rss-article-item { flex-direction: column; gap: 0.5rem; }
    .rss-article-actions { align-self: flex-end; }
  }
</style>

<script>
(function() {
  // Checkbox selection tracking
  var checkboxes = document.querySelectorAll('.rss-checkbox');
  var generateBtn = document.getElementById('btn-generate');
  var summary = document.getElementById('selected-articles-summary');
  var countEl = document.getElementById('selected-count');

  function updateSelection() {
    var checked = document.querySelectorAll('.rss-checkbox:checked');
    var count = checked.length;
    generateBtn.disabled = count === 0;
    if (count > 0) {
      summary.style.display = 'block';
      countEl.textContent = count;
    } else {
      summary.style.display = 'none';
    }
  }

  checkboxes.forEach(function(cb) {
    cb.addEventListener('change', updateSelection);
  });

  // Star toggle
  document.querySelectorAll('.rss-star-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var id = this.dataset.id;
      var icon = this.querySelector('i');
      fetch('/admin/rss/toggle-star/' + id, { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (data.success) {
            btn.classList.toggle('rss-starred');
            icon.classList.toggle('fas');
            icon.classList.toggle('far');
          }
        });
    });
  });

  // Loading state for fetch button
  var fetchForm = document.querySelector('[action="/admin/rss/fetch-all"]');
  if (fetchForm) {
    fetchForm.addEventListener('submit', function() {
      var btn = document.getElementById('btn-fetch');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recuperation en cours...';
    });
  }
})();
</script>
