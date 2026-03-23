<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title"><i class="fas fa-magic"></i> Article genere depuis RSS</h1>
    <p class="admin-page-desc">Relisez, modifiez et publiez l'article genere par IA.</p>
  </div>
  <a href="/admin/rss" class="admin-btn admin-btn-secondary">
    <i class="fas fa-arrow-left"></i> Retour a la veille
  </a>
</div>

<?php if (!empty($article)): ?>
<!-- Preview -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-eye"></i> Apercu de l'article</h2>
    <span class="admin-badge" style="background: rgba(139,21,56,0.1); color: var(--admin-primary);">Genere par IA</span>
  </div>
  <div class="admin-card-body">
    <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;"><?= e($article['title'] ?? '') ?></h1>
    <p style="font-size: 0.85rem; color: var(--admin-muted); margin-bottom: 1rem;">
      <strong>Meta title :</strong> <?= e($article['meta_title'] ?? '') ?><br>
      <strong>Meta description :</strong> <?= e($article['meta_description'] ?? '') ?><br>
      <strong>Extrait :</strong> <?= e($article['excerpt'] ?? '') ?>
    </p>
    <div class="article-preview" style="border: 1px solid var(--admin-border); padding: 1.5rem; border-radius: 8px; background: #fff; line-height: 1.7; font-size: 0.95rem;">
      <?= $article['content_html'] ?? '' ?>
    </div>
  </div>
</div>

<!-- Sources Used -->
<?php if (!empty($sources_citation)): ?>
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-link"></i> Sources utilisees</h2>
  </div>
  <div class="admin-card-body">
    <ul><?= $sources_citation ?></ul>
  </div>
</div>
<?php endif; ?>

<!-- Publish Form -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2><i class="fas fa-paper-plane"></i> Publier dans le blog</h2>
  </div>
  <div class="admin-card-body">
    <form method="post" action="/admin/actualites/store">
      <input type="hidden" name="title" value="<?= e($article['title'] ?? '') ?>">
      <input type="hidden" name="slug" value="<?= e(slugify($article['title'] ?? 'article')) ?>">
      <input type="hidden" name="content" value="<?= e($article['content_html'] ?? '') ?>">
      <input type="hidden" name="excerpt" value="<?= e($article['excerpt'] ?? '') ?>">
      <input type="hidden" name="meta_title" value="<?= e($article['meta_title'] ?? '') ?>">
      <input type="hidden" name="meta_description" value="<?= e($article['meta_description'] ?? '') ?>">
      <input type="hidden" name="generated_by" value="ai">
      <input type="hidden" name="source_query" value="RSS Feed">

      <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <div>
          <label class="admin-label">Statut</label>
          <select name="status" class="admin-input" style="width: auto;">
            <option value="draft">Brouillon</option>
            <option value="published">Publier directement</option>
          </select>
        </div>
        <div style="display: flex; align-items: flex-end;">
          <button type="submit" class="admin-btn admin-btn-primary">
            <i class="fas fa-save"></i> Enregistrer dans les actualites
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php else: ?>
<div class="admin-card">
  <div class="admin-card-body" style="text-align: center; padding: 3rem; color: var(--admin-muted);">
    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
    <p>Aucun article n'a pu etre genere. Verifiez votre configuration API.</p>
  </div>
</div>
<?php endif; ?>

<style>
  .admin-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
  .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; display: flex; align-items: center; gap: 0.5rem; }
  .admin-page-desc { font-size: 0.9rem; color: var(--admin-muted); margin-top: 0.25rem; }
  .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius, 8px); margin-bottom: 1.5rem; overflow: hidden; }
  .admin-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); }
  .admin-card-header h2 { font-size: 1rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
  .admin-card-body { padding: 1.5rem; }
  .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
  .admin-btn-primary { background: var(--admin-primary); color: #fff; }
  .admin-btn-secondary { background: var(--admin-bg); color: var(--admin-text); border: 1px solid var(--admin-border); }
  .admin-badge { background: var(--admin-bg); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--admin-muted); font-weight: 600; }
  .admin-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-muted); margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.03em; }
  .admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; background: #fff; }

  .article-preview h1 { font-size: 1.5rem; color: var(--admin-text); }
  .article-preview h2 { font-size: 1.2rem; color: var(--admin-primary); margin-top: 1.5rem; }
  .article-preview h3 { font-size: 1.05rem; color: var(--admin-text); margin-top: 1rem; }
  .article-preview p { margin: 0.75rem 0; }
  .article-preview ul { padding-left: 1.5rem; }
  .article-preview li { margin-bottom: 0.4rem; }
  .article-preview blockquote { border-left: 4px solid var(--admin-primary); padding: 0.75rem 1rem; background: rgba(139,21,56,0.03); margin: 1rem 0; font-style: italic; }
</style>

<?php
if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        return trim($text, '-') !== '' ? trim($text, '-') : 'article';
    }
}
?>
