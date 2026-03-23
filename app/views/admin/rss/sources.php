<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fas fa-rss"></i> Sources RSS</h1>
    <p class="admin-page-desc">Gerez vos flux RSS pour la veille immobiliere.</p>
  </div>
  <a href="/admin/rss" class="admin-btn admin-btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour a la veille
  </a>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<!-- Seed Button -->
<?php if (empty($sources)): ?>
<div class="admin-card">
  <div class="admin-card-body" style="text-align: center; padding: 2rem;">
    <i class="fas fa-seedling" style="font-size: 2.5rem; color: var(--admin-primary); margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
    <h3 style="margin: 0 0 0.5rem;">Demarrer avec des sources pre-configurees</h3>
    <p style="color: var(--admin-muted); margin-bottom: 1rem; font-size: 0.9rem;">
      Ajoutez automatiquement ~19 flux RSS immobiliers (nationaux + Bordeaux/Nouvelle-Aquitaine).
    </p>
    <form method="post" action="/admin/rss/seed" style="display: inline;">
      <button type="submit" class="admin-btn admin-btn-primary">
        <i class="fas fa-magic"></i> Ajouter les sources par defaut
      </button>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Add Source Form -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-plus"></i> Ajouter une source</h2>
  </div>
  <div class="admin-card-body">
    <form method="post" action="/admin/rss/sources/add" class="rss-add-form">
      <div class="rss-form-grid">
        <div class="admin-form-group">
          <label class="admin-label">Nom *</label>
          <input type="text" name="name" class="admin-input" placeholder="Ex: Sud Ouest Immobilier" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-label">URL du flux RSS *</label>
          <input type="url" name="feed_url" class="admin-input" placeholder="https://example.com/feed.xml" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-label">URL du site</label>
          <input type="url" name="site_url" class="admin-input" placeholder="https://example.com">
        </div>
        <div class="admin-form-group">
          <label class="admin-label">Categorie</label>
          <select name="category" class="admin-input">
            <option value="actualites-immo">Actualites immobilieres</option>
            <option value="investissement">Investissement / Defiscalisation</option>
            <option value="neuf-defiscalisation">Immobilier neuf</option>
            <option value="medias-economiques">Medias economiques</option>
            <option value="institutionnel">Institutionnel / Logement</option>
            <option value="construction-urbanisme">Construction / Urbanisme</option>
            <option value="presse-locale">Presse locale</option>
            <option value="general">General</option>
          </select>
        </div>
        <div class="admin-form-group">
          <label class="admin-label">Zone</label>
          <select name="zone" class="admin-input">
            <option value="national">National</option>
            <option value="Bordeaux/Nouvelle-Aquitaine">Bordeaux / Nouvelle-Aquitaine</option>
          </select>
        </div>
        <div class="admin-form-group" style="display: flex; align-items: flex-end;">
          <button type="submit" class="admin-btn admin-btn-primary">
            <i class="fas fa-plus"></i> Ajouter
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Sources List -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-list"></i> Sources configurees</h2>
    <span class="admin-badge"><?= count($sources) ?> sources</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <?php if (empty($sources)): ?>
      <div style="text-align: center; padding: 2rem; color: var(--admin-muted);">Aucune source configuree.</div>
    <?php else: ?>
      <div class="admin-table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Categorie</th>
              <th>Zone</th>
              <th>Statut</th>
              <th>Dernier fetch</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sources as $s): ?>
              <tr>
                <td>
                  <strong><?= e($s['name']) ?></strong>
                  <div style="font-size: 0.75rem; color: var(--admin-muted); word-break: break-all; max-width: 300px;"><?= e($s['feed_url']) ?></div>
                </td>
                <td><span class="admin-badge"><?= e($s['category']) ?></span></td>
                <td>
                  <span class="rss-source-badge rss-zone-<?= $s['zone'] === 'Bordeaux/Nouvelle-Aquitaine' ? 'local' : 'national' ?>">
                    <?= e($s['zone']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($s['is_active']): ?>
                    <span class="admin-status admin-status-success"><i class="fas fa-check"></i> Actif</span>
                  <?php else: ?>
                    <span class="admin-status" style="background: rgba(0,0,0,0.05); color: var(--admin-muted);"><i class="fas fa-pause"></i> Inactif</span>
                  <?php endif; ?>
                  <?php if (!empty($s['last_error'])): ?>
                    <div style="font-size: 0.7rem; color: #dc2626; margin-top: 0.25rem;" title="<?= e($s['last_error']) ?>">
                      <i class="fas fa-exclamation-triangle"></i> Erreur
                    </div>
                  <?php endif; ?>
                </td>
                <td style="font-size: 0.8rem; color: var(--admin-muted);">
                  <?= $s['last_fetched_at'] ? e(date('d/m/Y H:i', strtotime($s['last_fetched_at']))) : 'Jamais' ?>
                </td>
                <td>
                  <div class="admin-actions">
                    <form method="post" action="/admin/rss/sources/fetch/<?= (int) $s['id'] ?>" style="display:inline;">
                      <button type="submit" class="admin-btn admin-btn-sm admin-btn-ghost" title="Recuperer ce flux">
                        <i class="fas fa-sync-alt"></i>
                      </button>
                    </form>
                    <form method="post" action="/admin/rss/sources/toggle/<?= (int) $s['id'] ?>" style="display:inline;">
                      <button type="submit" class="admin-btn admin-btn-sm admin-btn-ghost" title="<?= $s['is_active'] ? 'Desactiver' : 'Activer' ?>">
                        <i class="fas fa-<?= $s['is_active'] ? 'pause' : 'play' ?>"></i>
                      </button>
                    </form>
                    <form method="post" action="/admin/rss/sources/delete/<?= (int) $s['id'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette source et tous ses articles ?');">
                      <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  /* Sources page — page-specific styles */
  .rss-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; align-items: end; }
  .rss-source-badge { font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.03em; }
  .rss-zone-local { background: rgba(59, 130, 246, 0.1); color: #2563eb; }
  .rss-zone-national { background: rgba(139, 21, 56, 0.08); color: var(--admin-primary); }

  @media (max-width: 768px) {
    .rss-form-grid { grid-template-columns: 1fr; }
  }
</style>
