<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">Actualites</h1>
    <p class="admin-page-desc">Generez des actualites immobilieres a partir de vos flux RSS. L'IA filtre, selectionne et redige automatiquement.</p>
  </div>
  <a href="/admin/actualites/create" class="admin-btn admin-btn-primary"><i class="fas fa-plus"></i> Nouvelle actualite</a>
</div>

<?php if (($message ?? '') !== ''): ?><div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> <?= e($message) ?></div><?php endif; ?>
<?php if (($error ?? '') !== ''): ?><div class="admin-alert admin-alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div><?php endif; ?>

<!-- RSS Pipeline Generation Panel -->
<div class="admin-card ai-panel">
  <div class="admin-card-header">
    <h2><i class="fas fa-rss"></i> Generation depuis les flux RSS</h2>
    <span class="admin-badge" style="background: rgba(139,21,56,0.1); color: var(--admin-primary);">Pipeline RSS + IA</span>
  </div>
  <div class="admin-card-body">
    <div class="ai-pipeline-steps">
      <div class="ai-step"><span class="ai-step-num">1</span> Collecte RSS</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">2</span> Filtrage IA</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">3</span> Selection locale</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">4</span> Redaction actualite</div>
      <div class="ai-step-arrow"><i class="fas fa-arrow-right"></i></div>
      <div class="ai-step"><span class="ai-step-num">5</span> Image IA</div>
    </div>

    <?php
      $rssReady = ($rssStats['filtered_ready'] ?? 0);
      $rssTotal = ($rssStats['total_candidates'] ?? 0);
      $topArticles = $rssStats['top_articles'] ?? [];
    ?>

    <div class="rss-status-bar">
      <div class="rss-status-item">
        <i class="fas fa-database"></i>
        <strong><?= $rssTotal ?></strong> articles RSS disponibles
      </div>
      <div class="rss-status-item">
        <i class="fas fa-filter"></i>
        <strong><?= $rssReady ?></strong> articles apres filtrage IA
      </div>
      <div class="rss-status-item">
        <i class="fas fa-star"></i>
        <strong><?= count($topArticles) ?></strong> candidats top
      </div>
    </div>

    <?php if (!empty($topArticles)): ?>
    <div class="rss-preview">
      <h4 style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
        <i class="fas fa-eye"></i> Apercu des articles candidats (top 5)
      </h4>
      <div class="rss-preview-list">
        <?php foreach ($topArticles as $rssArt): ?>
          <div class="rss-preview-item">
            <div class="rss-preview-meta">
              <?php if (($rssArt['source_zone'] ?? '') === 'Bordeaux/Nouvelle-Aquitaine'): ?>
                <span class="zone-badge zone-local"><i class="fas fa-map-marker-alt"></i> Local</span>
              <?php else: ?>
                <span class="zone-badge zone-national"><i class="fas fa-globe"></i> National</span>
              <?php endif; ?>
              <span class="rss-source-name"><?= e((string) ($rssArt['source_name'] ?? '')) ?></span>
              <span class="rss-score" title="Score de pertinence">
                <i class="fas fa-chart-bar"></i> <?= (int) ($rssArt['relevance_score'] ?? 0) ?>
              </span>
            </div>
            <div class="rss-preview-title"><?= e((string) ($rssArt['title'] ?? '')) ?></div>
            <div class="rss-preview-date"><?= e(date('d/m/Y', strtotime((string) ($rssArt['pub_date'] ?? $rssArt['created_at'] ?? 'now')))) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div style="display: flex; gap: 0.75rem; margin-top: 1rem; flex-wrap: wrap;">
      <form method="post" action="/admin/actualites/generate-rss" id="form-rss-generate" style="flex: 1;">
        <button type="submit" class="admin-btn admin-btn-primary" id="btn-rss-generate" style="width: 100%;" <?= $rssReady === 0 ? 'disabled title="Aucun article RSS disponible"' : '' ?>>
          <i class="fas fa-magic"></i> Generer une actualite depuis RSS
        </button>
      </form>
      <a href="/admin/rss" class="admin-btn admin-btn-secondary" style="white-space: nowrap;">
        <i class="fas fa-rss"></i> Gerer les flux RSS
      </a>
    </div>
  </div>
</div>

<!-- AI Configuration Panel (collapsible) -->
<div class="admin-card">
  <div class="admin-card-header" style="cursor: pointer;" onclick="document.getElementById('ai-config-body').classList.toggle('hidden');">
    <h2><i class="fas fa-cog"></i> Configuration IA pour les actualites</h2>
    <span class="admin-badge"><i class="fas fa-chevron-down"></i> Parametres</span>
  </div>
  <div class="admin-card-body hidden" id="ai-config-body">
    <form method="post" action="/admin/actualites/save-ai-config" class="ai-config-form">

      <div class="config-section">
        <h3><i class="fas fa-map-marker-alt"></i> Filtrage geographique</h3>
        <div class="config-grid">
          <div class="admin-form-group">
            <label class="admin-label">Priorite geographique</label>
            <select name="zone_priority" class="admin-input">
              <option value="local_first" <?= ($aiConfig['zone_priority'] ?? '') === 'local_first' ? 'selected' : '' ?>>Local d'abord (Bordeaux prioritaire)</option>
              <option value="local_only" <?= ($aiConfig['zone_priority'] ?? '') === 'local_only' ? 'selected' : '' ?>>Local uniquement</option>
              <option value="mixed" <?= ($aiConfig['zone_priority'] ?? '') === 'mixed' ? 'selected' : '' ?>>Mixte (local + national)</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Age max des articles (jours)</label>
            <input type="number" name="max_article_age_days" class="admin-input" value="<?= e((string) ($aiConfig['max_article_age_days'] ?? '7')) ?>" min="1" max="30">
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Score de pertinence minimum (0-10)</label>
            <input type="number" name="min_relevance_score" class="admin-input" value="<?= e((string) ($aiConfig['min_relevance_score'] ?? '6')) ?>" min="0" max="10">
          </div>
        </div>
      </div>

      <div class="config-section">
        <h3><i class="fas fa-filter"></i> Filtrage du contenu</h3>
        <div class="config-grid">
          <div class="admin-form-group">
            <label class="admin-label">
              <input type="checkbox" name="exclude_agencies" value="1" <?= ($aiConfig['exclude_agencies'] ?? '1') === '1' ? 'checked' : '' ?>>
              Exclure le contenu d'agences / promoteurs
            </label>
          </div>
          <div class="admin-form-group" style="grid-column: 1/-1;">
            <label class="admin-label">Mots-cles a exclure (separes par des virgules)</label>
            <input type="text" name="exclude_keywords" class="admin-input" value="<?= e((string) ($aiConfig['exclude_keywords'] ?? '')) ?>" placeholder="annonce,vente appartement,location meublée...">
            <small class="admin-help">Les articles contenant ces mots seront exclus du pipeline.</small>
          </div>
          <div class="admin-form-group" style="grid-column: 1/-1;">
            <label class="admin-label">Mots-cles requis pour la pertinence (separes par des virgules)</label>
            <input type="text" name="require_keywords" class="admin-input" value="<?= e((string) ($aiConfig['require_keywords'] ?? '')) ?>" placeholder="bordeaux,gironde,nouvelle-aquitaine...">
            <small class="admin-help">Les articles contenant ces mots auront un score de pertinence plus eleve.</small>
          </div>
        </div>
      </div>

      <div class="config-section">
        <h3><i class="fas fa-pen-fancy"></i> Parametres de redaction IA</h3>
        <div class="config-grid">
          <div class="admin-form-group">
            <label class="admin-label">Ton de l'article</label>
            <select name="article_tone" class="admin-input">
              <option value="journalistique" <?= ($aiConfig['article_tone'] ?? '') === 'journalistique' ? 'selected' : '' ?>>Journalistique (factuel, source)</option>
              <option value="expert" <?= ($aiConfig['article_tone'] ?? '') === 'expert' ? 'selected' : '' ?>>Expert (analyse approfondie)</option>
              <option value="accessible" <?= ($aiConfig['article_tone'] ?? '') === 'accessible' ? 'selected' : '' ?>>Accessible (grand public)</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Longueur cible (mots)</label>
            <select name="article_length" class="admin-input">
              <option value="500-800" <?= ($aiConfig['article_length'] ?? '') === '500-800' ? 'selected' : '' ?>>Court (500-800 mots)</option>
              <option value="800-1200" <?= ($aiConfig['article_length'] ?? '') === '800-1200' ? 'selected' : '' ?>>Standard (800-1200 mots)</option>
              <option value="1200-1800" <?= ($aiConfig['article_length'] ?? '') === '1200-1800' ? 'selected' : '' ?>>Long (1200-1800 mots)</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Appel a l'action (CTA)</label>
            <select name="cta_style" class="admin-input">
              <option value="soft" <?= ($aiConfig['cta_style'] ?? '') === 'soft' ? 'selected' : '' ?>>Soft (suggestion naturelle)</option>
              <option value="direct" <?= ($aiConfig['cta_style'] ?? '') === 'direct' ? 'selected' : '' ?>>Direct (estimation gratuite)</option>
              <option value="none" <?= ($aiConfig['cta_style'] ?? '') === 'none' ? 'selected' : '' ?>>Aucun CTA</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label class="admin-label">Modele IA de generation</label>
            <select name="generation_model" class="admin-input">
              <option value="anthropic" <?= ($aiConfig['generation_model'] ?? '') === 'anthropic' ? 'selected' : '' ?>>Claude (Anthropic)</option>
              <option value="openai" <?= ($aiConfig['generation_model'] ?? '') === 'openai' ? 'selected' : '' ?>>GPT (OpenAI)</option>
            </select>
          </div>
          <div class="admin-form-group" style="grid-column: 1/-1;">
            <label class="admin-label">Mots-cles SEO cibles (separes par des virgules)</label>
            <input type="text" name="seo_focus" class="admin-input" value="<?= e((string) ($aiConfig['seo_focus'] ?? '')) ?>" placeholder="estimation immobilière bordeaux,prix immobilier bordeaux...">
          </div>
          <div class="admin-form-group" style="grid-column: 1/-1;">
            <label class="admin-label">Consigne d'angle local (instructions pour l'IA)</label>
            <textarea name="local_angle" class="admin-input" rows="3" placeholder="Instructions pour orienter l'article..."><?= e((string) ($aiConfig['local_angle'] ?? '')) ?></textarea>
          </div>
        </div>
      </div>

      <div class="config-section">
        <h3><i class="fas fa-cogs"></i> Options avancees</h3>
        <div class="config-grid">
          <div class="admin-form-group">
            <label class="admin-label">
              <input type="checkbox" name="source_citation" value="1" <?= ($aiConfig['source_citation'] ?? '1') === '1' ? 'checked' : '' ?>>
              Ajouter les citations de sources en bas de l'article
            </label>
          </div>
          <div class="admin-form-group">
            <label class="admin-label">
              <input type="checkbox" name="auto_publish" value="1" <?= ($aiConfig['auto_publish'] ?? '0') === '1' ? 'checked' : '' ?>>
              Publication automatique (cron) — sinon brouillon
            </label>
          </div>
        </div>
      </div>

      <button type="submit" class="admin-btn admin-btn-primary">
        <i class="fas fa-save"></i> Sauvegarder la configuration
      </button>
    </form>
  </div>
</div>

<!-- Legacy Perplexity Pipeline (collapsed) -->
<div class="admin-card">
  <div class="admin-card-header" style="cursor: pointer;" onclick="document.getElementById('perplexity-body').classList.toggle('hidden');">
    <h2><i class="fas fa-robot"></i> Generation Perplexity (alternative)</h2>
    <span class="admin-badge"><i class="fas fa-chevron-down"></i></span>
  </div>
  <div class="admin-card-body hidden" id="perplexity-body">
    <p class="admin-help" style="margin-bottom: 1rem;">Pipeline alternatif : recherche via Perplexity + generation OpenAI. Utile si les flux RSS ne contiennent pas assez de contenu.</p>
    <form method="post" action="/admin/actualites/generate" class="admin-form-inline" id="form-generate">
      <div class="admin-form-group" style="flex:1;">
        <input type="text" name="query" class="admin-input" placeholder="Theme de recherche (optionnel, ex: prix immobilier Bordeaux 2026)" value="">
      </div>
      <button type="submit" class="admin-btn admin-btn-secondary" id="btn-generate">
        <i class="fas fa-magic"></i> Generer via Perplexity
      </button>
    </form>
  </div>
</div>

<!-- Stats Bar -->
<?php
  $countPublished = 0;
  $countDraft = 0;
  $countAi = 0;
  $countCron = 0;
  foreach ($actualites as $a) {
      if (($a['status'] ?? '') === 'published') $countPublished++;
      else $countDraft++;
      if (($a['generated_by'] ?? '') === 'ai') $countAi++;
      if (($a['generated_by'] ?? '') === 'cron') $countCron++;
  }
?>
<div class="stats-bar">
  <div class="stat-item">
    <span class="stat-value"><?= count($actualites) ?></span>
    <span class="stat-label">Total</span>
  </div>
  <div class="stat-item stat-success">
    <span class="stat-value"><?= $countPublished ?></span>
    <span class="stat-label">Publies</span>
  </div>
  <div class="stat-item stat-warning">
    <span class="stat-value"><?= $countDraft ?></span>
    <span class="stat-label">Brouillons</span>
  </div>
  <div class="stat-item stat-ai">
    <span class="stat-value"><?= $countAi + $countCron ?></span>
    <span class="stat-label">Generes IA</span>
  </div>
</div>

<!-- Articles List -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-newspaper"></i> Toutes les actualites</h2>
    <span class="admin-badge"><?= count($actualites) ?> articles</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Titre</th>
            <th>Statut</th>
            <th>Source</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($actualites)): ?>
            <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--admin-muted);">Aucune actualite. Cliquez sur "Generer depuis RSS" pour commencer.</td></tr>
          <?php else: ?>
            <?php foreach ($actualites as $actu): ?>
              <tr>
                <td style="width: 60px;">
                  <?php if (!empty($actu['image_url'])): ?>
                    <img src="<?= e((string) $actu['image_url']) ?>" alt="" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                  <?php else: ?>
                    <span style="color: var(--admin-muted); font-size: 0.8rem;"><i class="fas fa-image"></i></span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= e((string) $actu['title']) ?></strong>
                </td>
                <td>
                  <?php if ($actu['status'] === 'published'): ?>
                    <span class="admin-status admin-status-success">Publie</span>
                  <?php else: ?>
                    <span class="admin-status admin-status-warning">Brouillon</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                    $genIcon = match($actu['generated_by'] ?? 'manual') {
                      'ai' => '<i class="fas fa-robot" title="IA"></i> IA',
                      'cron' => '<i class="fas fa-clock" title="Cron"></i> Auto',
                      default => '<i class="fas fa-pen" title="Manuel"></i> Manuel',
                    };
                  ?>
                  <span style="font-size: 0.85rem;"><?= $genIcon ?></span>
                </td>
                <td style="font-size: 0.85rem; color: var(--admin-muted);">
                  <?= e(date('d/m/Y', strtotime((string) ($actu['published_at'] ?? $actu['created_at'])))) ?>
                </td>
                <td>
                  <div class="admin-actions">
                    <a href="/admin/actualites/edit/<?= (int) $actu['id'] ?>" class="admin-btn admin-btn-sm admin-btn-ghost" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="/actualites/<?= e((string) $actu['slug']) ?>" class="admin-btn admin-btn-sm admin-btn-ghost" target="_blank" title="Voir">
                      <i class="fas fa-eye"></i>
                    </a>
                    <form method="post" action="/admin/actualites/delete/<?= (int) $actu['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cette actualite ?');">
                      <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Cron Logs -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-history"></i> Historique des generations automatiques</h2>
    <span class="admin-badge"><?= count($cronLogs) ?> entrees</span>
  </div>
  <div class="admin-card-body" style="padding: 0;">
    <?php if (empty($cronLogs)): ?>
      <div style="text-align: center; padding: 2rem; color: var(--admin-muted);">
        <i class="fas fa-clock" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.3;"></i>
        Aucune generation enregistree. Utilisez le bouton "Generer" ou configurez le cron.
        <div style="margin-top: 0.75rem; font-size: 0.8rem; font-family: monospace; background: var(--admin-bg); padding: 0.5rem 1rem; border-radius: 4px; display: inline-block;">
          0 8 * * 1 php cron/generate-actualite.php
        </div>
      </div>
    <?php else: ?>
      <div class="admin-table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Requete</th>
              <th>Articles trouves</th>
              <th>Article publie</th>
              <th>Statut</th>
              <th>Erreur</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cronLogs as $log): ?>
              <tr>
                <td style="font-size: 0.85rem;"><?= e(date('d/m/Y H:i', strtotime((string) $log['created_at']))) ?></td>
                <td style="font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e((string) $log['query_used']) ?>">
                  <?= e(mb_substr((string) $log['query_used'], 0, 60)) ?>
                  <?= mb_strlen((string) $log['query_used']) > 60 ? '...' : '' ?>
                </td>
                <td style="text-align: center;"><?= (int) $log['articles_found'] ?></td>
                <td style="text-align: center;">
                  <?php if (!empty($log['article_published_id'])): ?>
                    <a href="/admin/actualites/edit/<?= (int) $log['article_published_id'] ?>" class="admin-btn admin-btn-sm admin-btn-ghost" title="Voir l'article">
                      #<?= (int) $log['article_published_id'] ?> <i class="fas fa-external-link-alt"></i>
                    </a>
                  <?php else: ?>
                    <span style="color: var(--admin-muted);">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($log['status'] === 'success'): ?>
                    <span class="admin-status admin-status-success"><i class="fas fa-check"></i> OK</span>
                  <?php else: ?>
                    <span class="admin-status admin-status-danger"><i class="fas fa-times"></i> Erreur</span>
                  <?php endif; ?>
                </td>
                <td style="font-size: 0.8rem; color: #dc2626; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e((string) ($log['error_message'] ?? '')) ?>">
                  <?= e((string) ($log['error_message'] ?? '')) ?>
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
  .admin-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
  .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; }
  .admin-page-desc { font-size: 0.9rem; color: var(--admin-muted); margin-top: 0.25rem; }
  .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); margin-bottom: 1.5rem; overflow: hidden; }
  .admin-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); }
  .admin-card-header h2 { font-size: 1rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
  .admin-card-body { padding: 1.5rem; }
  .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
  .admin-btn-primary { background: var(--admin-primary); color: #fff; }
  .admin-btn-primary:hover { opacity: 0.9; }
  .admin-btn-secondary { background: var(--admin-bg); color: var(--admin-text); border: 1px solid var(--admin-border); }
  .admin-btn-secondary:hover { background: var(--admin-border); }
  .admin-btn-ghost { background: transparent; color: var(--admin-muted); padding: 0.4rem 0.6rem; }
  .admin-btn-ghost:hover { color: var(--admin-primary); background: rgba(139,21,56,0.06); }
  .admin-btn-danger { background: transparent; color: #dc2626; padding: 0.4rem 0.6rem; }
  .admin-btn-danger:hover { background: rgba(239, 68, 68, 0.1); }
  .admin-btn-sm { padding: 0.35rem 0.5rem; font-size: 0.8rem; }
  .admin-btn:disabled { opacity: 0.6; cursor: not-allowed; }
  .admin-form-inline { display: flex; gap: 0.75rem; align-items: flex-end; }
  .admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; background: #fff; }
  .admin-input:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(139,21,56,0.1); }
  .admin-table-responsive { overflow-x: auto; }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); background: var(--admin-bg); }
  .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-border); font-size: 0.9rem; }
  .admin-table tbody tr:hover { background: rgba(0,0,0,0.02); }
  .admin-status { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
  .admin-status-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
  .admin-status-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
  .admin-status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
  .admin-badge { background: var(--admin-bg); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--admin-muted); font-weight: 600; }
  .admin-actions { display: flex; gap: 0.25rem; }
  .admin-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
  .admin-alert-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
  .admin-alert-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }

  /* AI Pipeline Steps */
  .ai-pipeline-steps { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.25rem; padding: 1rem; background: var(--admin-bg); border-radius: 8px; flex-wrap: wrap; }
  .ai-step { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--admin-text); }
  .ai-step-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: var(--admin-primary); color: #fff; font-size: 0.7rem; font-weight: 700; }
  .ai-step-arrow { color: var(--admin-muted); font-size: 0.7rem; }

  /* RSS Status Bar */
  .rss-status-bar { display: flex; gap: 1.5rem; margin-bottom: 1rem; padding: 0.75rem 1rem; background: var(--admin-bg); border-radius: 6px; flex-wrap: wrap; }
  .rss-status-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: var(--admin-muted); }
  .rss-status-item strong { color: var(--admin-text); }
  .rss-status-item i { color: var(--admin-primary); font-size: 0.8rem; }

  /* RSS Preview */
  .rss-preview { margin-top: 0.75rem; }
  .rss-preview-list { display: flex; flex-direction: column; gap: 0.5rem; }
  .rss-preview-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; background: var(--admin-bg); border-radius: 6px; border: 1px solid var(--admin-border); }
  .rss-preview-meta { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
  .rss-preview-title { flex: 1; font-size: 0.85rem; font-weight: 500; color: var(--admin-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rss-preview-date { font-size: 0.75rem; color: var(--admin-muted); flex-shrink: 0; }
  .rss-source-name { font-size: 0.75rem; color: var(--admin-muted); max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .rss-score { font-size: 0.75rem; color: var(--admin-primary); font-weight: 600; }

  /* Zone Badges */
  .zone-badge { display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600; white-space: nowrap; }
  .zone-local { background: rgba(34,197,94,0.1); color: #16a34a; }
  .zone-national { background: rgba(59,130,246,0.1); color: #2563eb; }

  /* Stats Bar */
  .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
  .stat-item { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem 1.25rem; text-align: center; }
  .stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: var(--admin-text); }
  .stat-label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); margin-top: 0.15rem; }
  .stat-success .stat-value { color: #16a34a; }
  .stat-warning .stat-value { color: #d97706; }
  .stat-ai .stat-value { color: var(--admin-primary); }

  /* AI Config Form */
  .ai-config-form { }
  .config-section { margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border); }
  .config-section:last-of-type { border-bottom: none; }
  .config-section h3 { font-size: 0.9rem; font-weight: 600; color: var(--admin-text); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem; }
  .config-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
  .admin-form-group { display: flex; flex-direction: column; gap: 0.3rem; }
  .admin-label { font-size: 0.8rem; font-weight: 600; color: var(--admin-text); display: flex; align-items: center; gap: 0.4rem; }
  .admin-help { font-size: 0.75rem; color: var(--admin-muted); }
  .hidden { display: none !important; }

  /* Loading spinner */
  .btn-loading .fa-magic, .btn-loading .fa-search, .btn-loading .fa-rss { display: none; }
  .btn-loading::before {
    content: '';
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    flex-shrink: 0;
  }
  .admin-btn-secondary.btn-loading::before {
    border-color: rgba(0,0,0,0.15);
    border-top-color: var(--admin-text);
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  @media (max-width: 768px) {
    .admin-form-inline { flex-direction: column; }
    .admin-page-header { flex-direction: column; }
    .ai-pipeline-steps { gap: 0.25rem; }
    .ai-step-arrow { display: none; }
    .rss-preview-item { flex-direction: column; align-items: flex-start; }
    .rss-status-bar { flex-direction: column; gap: 0.5rem; }
    .config-grid { grid-template-columns: 1fr; }
  }
</style>

<script>
(function() {
  function setupLoadingForm(formId, btnId, loadingText) {
    var form = document.getElementById(formId);
    var btn = document.getElementById(btnId);
    if (!form || !btn) return;
    form.addEventListener('submit', function() {
      btn.classList.add('btn-loading');
      btn.disabled = true;
      var textNode = btn.lastChild;
      if (textNode && textNode.nodeType === 3) {
        textNode.textContent = ' ' + loadingText;
      }
    });
  }
  setupLoadingForm('form-rss-generate', 'btn-rss-generate', 'Generation en cours...');
  setupLoadingForm('form-generate', 'btn-generate', 'Generation en cours...');
})();
</script>
