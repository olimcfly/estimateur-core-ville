<?php
$totalArticles = (int) ($stats['total'] ?? 0);
$published = (int) ($stats['published'] ?? 0);
$drafts = (int) ($stats['drafts'] ?? 0);
$avgSeo = (int) ($stats['avg_seo_score'] ?? 0);
$avgSemantic = (int) ($stats['avg_semantic_score'] ?? 0);
$avgWords = (int) ($stats['avg_word_count'] ?? 0);
$excellentSeo = (int) ($stats['excellent_seo'] ?? 0);
$goodSeo = (int) ($stats['good_seo'] ?? 0);
$poorSeo = (int) ($stats['poor_seo'] ?? 0);

// Group articles by silo
$articlesBySilo = [];
$articlesNoSilo = [];
$siloIndex = [];

foreach ($silos as $silo) {
    $siloId = (int) $silo['id'];
    $siloIndex[$siloId] = $silo;
    $articlesBySilo[$siloId] = ['pilier' => [], 'satellite' => [], 'standalone' => []];
}

foreach ($articles as $article) {
    $sid = $article['silo_id'] ? (int) $article['silo_id'] : null;
    $type = (string) ($article['article_type'] ?? 'standalone');
    if ($sid !== null && isset($articlesBySilo[$sid])) {
        $articlesBySilo[$sid][$type][] = $article;
    } else {
        $articlesNoSilo[] = $article;
    }
}

// Collect unique cities for the filter
$cities = [];
foreach ($silos as $silo) {
    $city = (string) ($silo['city'] ?? 'Bordeaux');
    if ($city !== '' && !in_array($city, $cities, true)) {
        $cities[] = $city;
    }
}
sort($cities);

$gmbByArticle = $gmbByArticle ?? [];
?>

<style>
.stat-card { background: #fff; border-radius: 8px; padding: 1.25rem; border: 1px solid #e8e8e8; text-align: center; }
.stat-card .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
.stat-card .stat-label { font-size: 0.8rem; color: #888; margin-top: 0.25rem; }
.seo-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.seo-excellent { background: #d4edda; color: #155724; }
.seo-good { background: #fff3cd; color: #856404; }
.seo-poor { background: #f8d7da; color: #721c24; }
.type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.type-pilier { background: #8B1538; color: #fff; }
.type-satellite { background: #D4AF37; color: #1a1a1a; }
.type-standalone { background: #e8e8e8; color: #555; }
.city-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; background: #e8f4f8; color: #1a6b8a; border: 1px solid #b8dde8; }
.persona-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; color: #6b4c8a; background: #f3eef8; border: 1px solid #d8cce8; }
.gmb-badge { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 12px; font-size: 0.68rem; font-weight: 600; text-decoration: none; cursor: pointer; }
.gmb-badge-draft { background: #e8e8e8; color: #666; }
.gmb-badge-scheduled { background: #e8f0fe; color: #1a56db; }
.gmb-badge-published { background: #d4edda; color: #155724; }
.gmb-badge-notified { background: #fff3cd; color: #856404; }
.gmb-badge-expired { background: #f8d7da; color: #721c24; }

/* Silo group card */
.silo-group { border-radius: 10px; margin-bottom: 1.5rem; overflow: hidden; border: 2px solid #e0e0e0; }
.silo-group-header { padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.silo-group-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
.silo-group-meta { display: flex; gap: 0.5rem; align-items: center; font-size: 0.8rem; }
.silo-group-body { background: #fff; }

/* Pillar article row */
.article-row { display: grid; grid-template-columns: 1fr 180px 80px 80px 90px 70px 130px; gap: 0.5rem; align-items: center; padding: 0.65rem 1.25rem; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
.article-row:last-child { border-bottom: none; }
.article-row-pillar { background: #fdf8f0; border-left: 4px solid #8B1538; }
.article-row-satellite { padding-left: 2.5rem; border-left: 4px solid #D4AF37; }
.article-row-standalone { border-left: 4px solid #e0e0e0; }

.article-row .article-title { font-weight: 600; color: #8B1538; text-decoration: none; }
.article-row .article-title:hover { text-decoration: underline; }
.article-row .article-sub { font-size: 0.75rem; color: #999; margin-top: 2px; }

/* Responsive: stack on mobile */
@media (max-width: 900px) {
    .article-row { grid-template-columns: 1fr; gap: 0.25rem; }
    .article-row > *:not(:first-child) { font-size: 0.75rem; }
}

/* Filter bar */
.filter-bar { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
.filter-bar select, .filter-bar input[type="text"] { padding: 0.4rem 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; }
</style>

<div class="container">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Blog / CMS SEO</h1>
            <p style="margin: 0; color: #888; font-size: 0.85rem;">Gestion avancée avec scores SEO, analyse sémantique et SERP preview</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/blog/wizard" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">
                + Nouvel article (Assistant IA)
            </a>
            <a href="/admin/blog/create" class="btn btn-ghost">+ Article manuel</a>
            <a href="/admin/blog/silos" class="btn btn-ghost">Silos SEO</a>
            <a href="/admin/blog/ideas" class="btn btn-ghost" style="border-color: #D4AF37; color: #D4AF37;">Planning articles</a>
            <a href="/admin/blog/seo-guide" class="btn btn-ghost">Guide SEO</a>
        </div>
    </div>

    <?php if ($message !== ''): ?><p class="success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error !== ''): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>

    <!-- Dashboard Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value" style="color: #8B1538;"><?= $totalArticles ?></div>
            <div class="stat-label">Articles total</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #0d7a3e;"><?= $published ?></div>
            <div class="stat-label">Publiés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #888;"><?= $drafts ?></div>
            <div class="stat-label">Brouillons</div>
        </div>
        <div class="stat-card">
            <?php $seoColor = $avgSeo >= 80 ? '#0d7a3e' : ($avgSeo >= 50 ? '#D4AF37' : '#c0392b'); ?>
            <div class="stat-value" style="color: <?= $seoColor ?>;"><?= $avgSeo ?></div>
            <div class="stat-label">Score SEO moyen</div>
        </div>
        <div class="stat-card">
            <?php $semColor = $avgSemantic >= 80 ? '#0d7a3e' : ($avgSemantic >= 50 ? '#D4AF37' : '#c0392b'); ?>
            <div class="stat-value" style="color: <?= $semColor ?>;"><?= $avgSemantic ?></div>
            <div class="stat-label">Score Sémantique</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #555;"><?= number_format($avgWords) ?></div>
            <div class="stat-label">Mots/article moy.</div>
        </div>
    </div>

    <!-- SEO Quality Distribution -->
    <?php if ($totalArticles > 0): ?>
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin: 0 0 0.75rem; font-size: 1rem;">Répartition Qualité SEO</h3>
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.85rem;">
            <div><span class="seo-badge seo-excellent"><?= $excellentSeo ?></span> Excellent (80+)</div>
            <div><span class="seo-badge seo-good"><?= $goodSeo ?></span> Bon (50-79)</div>
            <div><span class="seo-badge seo-poor"><?= $poorSeo ?></span> À améliorer (&lt;50)</div>
        </div>
        <div style="display: flex; height: 8px; border-radius: 4px; overflow: hidden; margin-top: 0.75rem; background: #f0f0f0;">
            <?php if ($totalArticles > 0): ?>
            <div style="width: <?= ($excellentSeo / $totalArticles) * 100 ?>%; background: #0d7a3e;"></div>
            <div style="width: <?= ($goodSeo / $totalArticles) * 100 ?>%; background: #D4AF37;"></div>
            <div style="width: <?= ($poorSeo / $totalArticles) * 100 ?>%; background: #c0392b;"></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- AI Generation (quick) -->
    <section class="card" style="margin-bottom: 1.5rem;">
        <details>
            <summary style="cursor: pointer; font-weight: 600; color: #8B1538;">Génération rapide IA</summary>
            <form method="post" action="/admin/blog/generate" class="form-grid" style="margin-top: 1rem; gap: 1rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label>Persona
                        <select name="persona" required>
                            <option>Propriétaire hésitant</option>
                            <option>Propriétaire pressé</option>
                            <option>Propriétaire méfiant</option>
                            <option>Succession / divorce</option>
                            <option>Investisseur vendeur</option>
                            <option>Primo-accédant</option>
                            <option>Famille en expansion</option>
                            <option>Investisseur rentabilité</option>
                            <option>Vendeur senior</option>
                            <option>Expatrié / mobilité</option>
                        </select>
                    </label>
                    <label>Niveau de conscience
                        <select name="awareness_level" required>
                            <option>inconscient</option>
                            <option>problème</option>
                            <option>solution</option>
                            <option>produit</option>
                        </select>
                    </label>
                </div>
                <label>Sujet de l'article
                    <input type="text" name="topic" placeholder="Ex: Est-ce le bon moment pour vendre à Bordeaux ?" required>
                </label>
                <button type="submit" class="btn">Générer avec IA</button>
            </form>
        </details>
    </section>

    <!-- Filters -->
    <section class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <h2 style="margin: 0;">Articles par Silo (<?= count($articles) ?>)</h2>
            <div class="filter-bar">
                <input type="text" id="searchArticles" placeholder="Rechercher..." oninput="filterArticles()">
                <select id="filterType" onchange="filterArticles()">
                    <option value="">Tous les types</option>
                    <option value="pilier">Pilier</option>
                    <option value="satellite">Satellite</option>
                    <option value="standalone">Indépendant</option>
                </select>
                <?php if (count($cities) > 1): ?>
                <select id="filterCity" onchange="filterArticles()">
                    <option value="">Toutes les villes</option>
                    <?php foreach ($cities as $city): ?>
                    <option value="<?= e($city) ?>"><?= e($city) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <select id="filterSeo" onchange="filterArticles()">
                    <option value="">Tous scores</option>
                    <option value="excellent">SEO 80+</option>
                    <option value="good">SEO 50-79</option>
                    <option value="poor">SEO &lt;50</option>
                </select>
            </div>
        </div>
        <div class="table-wrap">
            <table class="admin-table" id="articlesTable">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Mot-clé Focus</th>
                        <th>Type</th>
                        <th style="text-align: center;">SEO</th>
                        <th style="text-align: center;">Sémantique</th>
                        <th>Mots</th>
                        <th style="text-align: center;">Visites</th>
                        <th style="text-align: center;">Index</th>
                        <th style="text-align: center;">Position</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <?php
                    $artSeo = (int) ($article['seo_score'] ?? 0);
                    $artSem = (int) ($article['semantic_score'] ?? 0);
                    $artType = (string) ($article['article_type'] ?? 'standalone');
                    $seoBadgeClass = $artSeo >= 80 ? 'seo-excellent' : ($artSeo >= 50 ? 'seo-good' : 'seo-poor');
                    $semBadgeClass = $artSem >= 80 ? 'seo-excellent' : ($artSem >= 50 ? 'seo-good' : 'seo-poor');
                    $typeBadgeClass = 'type-' . $artType;
                    ?>
                    <tr data-title="<?= e(mb_strtolower((string) $article['title'])) ?>"
                        data-keyword="<?= e(mb_strtolower((string) ($article['focus_keyword'] ?? ''))) ?>"
                        data-type="<?= e($artType) ?>"
                        data-seo="<?= $artSeo ?>">
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" style="color: #8B1538; font-weight: 600; text-decoration: none;">
                                    <?= e((string) $article['title']) ?>
                                </a>
                                <?php if (isset($gmbByArticle[(int) $article['id']])): ?>
                                    <?php $gmb = $gmbByArticle[(int) $article['id']]; $gmbStatus = (string) ($gmb['status'] ?? 'draft'); ?>
                                    <a href="/admin/gmb/edit/<?= (int) $gmb['id'] ?>" class="gmb-badge gmb-badge-<?= e($gmbStatus) ?>" title="Publication GMB : <?= e($gmbStatus) ?>">
                                        <span style="font-weight: 800;">G</span> <?= e($gmbStatus) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #999;"><?= e((string) $article['persona']) ?></div>
                        </td>
                        <td>
                            <?php if (!empty($article['focus_keyword'])): ?>
                                <code style="font-size: 0.8rem; background: #f5f5f5; padding: 2px 6px; border-radius: 3px;"><?= e((string) $article['focus_keyword']) ?></code>
                            <?php else: ?>
                                <span style="color: #ccc;">--</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="type-badge <?= $typeBadgeClass ?>"><?= e($artType) ?></span></td>
                        <td style="text-align: center;"><span class="seo-badge <?= $seoBadgeClass ?>"><?= $artSeo ?></span></td>
                        <td style="text-align: center;"><span class="seo-badge <?= $semBadgeClass ?>"><?= $artSem ?></span></td>
                        <td style="font-size: 0.85rem;"><?= number_format((int) ($article['word_count'] ?? 0)) ?></td>
                        <td style="text-align: center; font-size: 0.85rem;">
                            <?php $views = (int) ($article['page_views'] ?? 0); ?>
                            <?= $views > 0 ? number_format($views) : '<span style="color:#ccc;">0</span>' ?>
                        </td>
                        <td style="text-align: center;">
                            <?php $indexed = (int) ($article['is_indexed'] ?? 0); ?>
                            <?php if (($article['status'] ?? '') === 'published'): ?>
                                <?php if ($indexed): ?>
                                    <span style="color: #0d7a3e; font-weight: 700;" title="Indexée">&#10003;</span>
                                <?php else: ?>
                                    <span style="color: #c0392b;" title="Non indexée">&#10007;</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color:#ccc;">--</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-size: 0.85rem;">
                            <?php $pos = $article['google_position'] ?? null; ?>
                            <?php if ($pos !== null && (int) $pos > 0): ?>
                                <?php $posColor = (int) $pos <= 3 ? '#0d7a3e' : ((int) $pos <= 10 ? '#D4AF37' : '#c0392b'); ?>
                                <span style="color: <?= $posColor ?>; font-weight: 700;">#<?= (int) $pos ?></span>
                            <?php else: ?>
                                <span style="color:#ccc;">--</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (($article['status'] ?? '') === 'published'): ?>
                                <span class="seo-badge seo-excellent">Publié</span>
                            <?php else: ?>
                                <span class="seo-badge" style="background: #e8e8e8; color: #666;">Brouillon</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="btn btn-small btn-ghost">Modifier</a>
                            <form method="post" action="/admin/blog/delete/<?= (int) $article['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                                <button type="submit" class="btn btn-small" style="font-size: 0.75rem;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Column headers -->
    <div style="display: grid; grid-template-columns: 1fr 180px 80px 80px 90px 70px 130px; gap: 0.5rem; padding: 0.5rem 1.25rem; font-size: 0.75rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px;">
        <div>Titre</div>
        <div>Mot-clé Focus</div>
        <div style="text-align: center;">SEO</div>
        <div style="text-align: center;">Séma.</div>
        <div>Persona</div>
        <div>Mots</div>
        <div>Actions</div>
    </div>

    <!-- Articles grouped by Silo -->
    <?php foreach ($articlesBySilo as $siloId => $grouped): ?>
    <?php
    $silo = $siloIndex[$siloId];
    $siloColor = e((string) $silo['color']);
    $siloCity = e((string) ($silo['city'] ?? 'Bordeaux'));
    $siloArticleCount = count($grouped['pilier']) + count($grouped['satellite']) + count($grouped['standalone']);
    if ($siloArticleCount === 0) continue;
    ?>
    <div class="silo-group" data-city="<?= $siloCity ?>" style="border-color: <?= $siloColor ?>;">
        <div class="silo-group-header" style="background: <?= $siloColor ?>15;">
            <h3>
                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?= $siloColor ?>;"></span>
                <span style="color: <?= $siloColor ?>;"><?= e((string) $silo['name']) ?></span>
            </h3>
            <div class="silo-group-meta">
                <span class="city-badge"><?= $siloCity ?></span>
                <span style="color: #888;"><?= $siloArticleCount ?> articles</span>
                <span style="color: #888;">|</span>
                <span style="color: #888;">SEO moy. <?= (int) ($silo['avg_seo_score'] ?? 0) ?>/100</span>
            </div>
        </div>
        <div class="silo-group-body">
            <?php
            // Render pillar articles first, then satellites, then standalone
            $allGrouped = array_merge($grouped['pilier'], $grouped['satellite'], $grouped['standalone']);
            foreach ($allGrouped as $article):
                $artSeo = (int) ($article['seo_score'] ?? 0);
                $artSem = (int) ($article['semantic_score'] ?? 0);
                $artType = (string) ($article['article_type'] ?? 'standalone');
                $seoBadgeClass = $artSeo >= 80 ? 'seo-excellent' : ($artSeo >= 50 ? 'seo-good' : 'seo-poor');
                $semBadgeClass = $artSem >= 80 ? 'seo-excellent' : ($artSem >= 50 ? 'seo-good' : 'seo-poor');
                $rowClass = 'article-row article-row-' . $artType;
            ?>
            <div class="<?= $rowClass ?>"
                 data-title="<?= e(mb_strtolower((string) $article['title'])) ?>"
                 data-keyword="<?= e(mb_strtolower((string) ($article['focus_keyword'] ?? ''))) ?>"
                 data-type="<?= e($artType) ?>"
                 data-seo="<?= $artSeo ?>">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="type-badge type-<?= e($artType) ?>"><?= $artType === 'pilier' ? 'PILIER' : ($artType === 'satellite' ? 'SATELLITE' : 'INDÉP.') ?></span>
                        <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="article-title"><?= e((string) $article['title']) ?></a>
                        <?php if (isset($gmbByArticle[(int) $article['id']])): ?>
                            <?php $gmb = $gmbByArticle[(int) $article['id']]; $gmbStatus = (string) ($gmb['status'] ?? 'draft'); ?>
                            <a href="/admin/gmb/edit/<?= (int) $gmb['id'] ?>" class="gmb-badge gmb-badge-<?= e($gmbStatus) ?>" title="Publication GMB : <?= e($gmbStatus) ?>">
                                <span style="font-weight: 800;">G</span> <?= e($gmbStatus) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if (($article['status'] ?? '') === 'draft'): ?>
                    <div class="article-sub">Brouillon</div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!empty($article['focus_keyword'])): ?>
                    <code style="font-size: 0.78rem; background: #f5f5f5; padding: 2px 6px; border-radius: 3px;"><?= e((string) $article['focus_keyword']) ?></code>
                    <?php else: ?>
                    <span style="color: #ccc;">--</span>
                    <?php endif; ?>
                </div>
                <div style="text-align: center;"><span class="seo-badge <?= $seoBadgeClass ?>"><?= $artSeo ?></span></div>
                <div style="text-align: center;"><span class="seo-badge <?= $semBadgeClass ?>"><?= $artSem ?></span></div>
                <div><span class="persona-badge"><?= e((string) ($article['persona'] ?? '')) ?></span></div>
                <div style="font-size: 0.85rem; color: #666;"><?= number_format((int) ($article['word_count'] ?? 0)) ?></div>
                <div style="display: flex; gap: 0.25rem;">
                    <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="btn btn-small btn-ghost">Modifier</a>
                    <form method="post" action="/admin/blog/delete/<?= (int) $article['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                        <button type="submit" class="btn btn-small" style="font-size: 0.75rem;">Suppr.</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Articles without silo -->
    <?php if (!empty($articlesNoSilo)): ?>
    <div class="silo-group" data-city="" style="border-color: #ccc;">
        <div class="silo-group-header" style="background: #f5f5f5;">
            <h3>
                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: #ccc;"></span>
                <span style="color: #888;">Sans silo</span>
            </h3>
            <div class="silo-group-meta">
                <span style="color: #888;"><?= count($articlesNoSilo) ?> articles non assignés</span>
            </div>
        </div>
        <div class="silo-group-body">
            <?php foreach ($articlesNoSilo as $article):
                $artSeo = (int) ($article['seo_score'] ?? 0);
                $artSem = (int) ($article['semantic_score'] ?? 0);
                $artType = (string) ($article['article_type'] ?? 'standalone');
                $seoBadgeClass = $artSeo >= 80 ? 'seo-excellent' : ($artSeo >= 50 ? 'seo-good' : 'seo-poor');
                $semBadgeClass = $artSem >= 80 ? 'seo-excellent' : ($artSem >= 50 ? 'seo-good' : 'seo-poor');
                $rowClass = 'article-row article-row-' . $artType;
            ?>
            <div class="<?= $rowClass ?>"
                 data-title="<?= e(mb_strtolower((string) $article['title'])) ?>"
                 data-keyword="<?= e(mb_strtolower((string) ($article['focus_keyword'] ?? ''))) ?>"
                 data-type="<?= e($artType) ?>"
                 data-seo="<?= $artSeo ?>">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="type-badge type-<?= e($artType) ?>"><?= $artType === 'pilier' ? 'PILIER' : ($artType === 'satellite' ? 'SATELLITE' : 'INDÉP.') ?></span>
                        <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="article-title"><?= e((string) $article['title']) ?></a>
                        <?php if (isset($gmbByArticle[(int) $article['id']])): ?>
                            <?php $gmb = $gmbByArticle[(int) $article['id']]; $gmbStatus = (string) ($gmb['status'] ?? 'draft'); ?>
                            <a href="/admin/gmb/edit/<?= (int) $gmb['id'] ?>" class="gmb-badge gmb-badge-<?= e($gmbStatus) ?>" title="Publication GMB : <?= e($gmbStatus) ?>">
                                <span style="font-weight: 800;">G</span> <?= e($gmbStatus) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if (($article['status'] ?? '') === 'draft'): ?>
                    <div class="article-sub">Brouillon</div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!empty($article['focus_keyword'])): ?>
                    <code style="font-size: 0.78rem; background: #f5f5f5; padding: 2px 6px; border-radius: 3px;"><?= e((string) $article['focus_keyword']) ?></code>
                    <?php else: ?>
                    <span style="color: #ccc;">--</span>
                    <?php endif; ?>
                </div>
                <div style="text-align: center;"><span class="seo-badge <?= $seoBadgeClass ?>"><?= $artSeo ?></span></div>
                <div style="text-align: center;"><span class="seo-badge <?= $semBadgeClass ?>"><?= $artSem ?></span></div>
                <div><span class="persona-badge"><?= e((string) ($article['persona'] ?? '')) ?></span></div>
                <div style="font-size: 0.85rem; color: #666;"><?= number_format((int) ($article['word_count'] ?? 0)) ?></div>
                <div style="display: flex; gap: 0.25rem;">
                    <a href="/admin/blog/edit/<?= (int) $article['id'] ?>" class="btn btn-small btn-ghost">Modifier</a>
                    <form method="post" action="/admin/blog/delete/<?= (int) $article['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cet article ?');">
                        <button type="submit" class="btn btn-small" style="font-size: 0.75rem;">Suppr.</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Empty state -->
    <?php if ($totalArticles === 0): ?>
    <section class="card" style="text-align: center; padding: 3rem;">
        <p style="color: #888; font-size: 1.1rem; margin: 0 0 1rem;">Aucun article. Commencez par créer un silo et un article pilier.</p>
        <a href="/admin/blog/wizard" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">+ Créer un article avec l'IA</a>
    </section>
    <?php endif; ?>

    <!-- Legend -->
    <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #fafafa; border-radius: 8px; font-size: 0.8rem; color: #888; display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
        <span style="font-weight: 600;">Légende :</span>
        <span><span class="type-badge type-pilier">PILIER</span> Article principal du silo</span>
        <span><span class="type-badge type-satellite">SATELLITE</span> Article de support</span>
        <span><span class="type-badge type-standalone">INDÉP.</span> Article indépendant</span>
        <span style="margin-left: auto;"><span class="city-badge">Ville</span> Ville cible du silo</span>
    </div>
</div>

<script>
function filterArticles() {
    const search = document.getElementById('searchArticles').value.toLowerCase();
    const type = document.getElementById('filterType').value;
    const citySelect = document.getElementById('filterCity');
    const city = citySelect ? citySelect.value : '';
    const seo = document.getElementById('filterSeo').value;

    // Filter individual article rows
    const rows = document.querySelectorAll('.article-row');
    rows.forEach(row => {
        const title = row.dataset.title || '';
        const keyword = row.dataset.keyword || '';
        const rowType = row.dataset.type || '';
        const rowSeo = parseInt(row.dataset.seo || '0');

        let show = true;
        if (search && !title.includes(search) && !keyword.includes(search)) show = false;
        if (type && rowType !== type) show = false;
        if (seo === 'excellent' && rowSeo < 80) show = false;
        if (seo === 'good' && (rowSeo < 50 || rowSeo >= 80)) show = false;
        if (seo === 'poor' && rowSeo >= 50) show = false;

        row.style.display = show ? '' : 'none';
    });

    // Filter silo groups by city and hide empty groups
    const groups = document.querySelectorAll('.silo-group');
    groups.forEach(group => {
        const groupCity = group.dataset.city || '';
        if (city && groupCity !== city && groupCity !== '') {
            group.style.display = 'none';
            return;
        }

        const visibleRows = group.querySelectorAll('.article-row:not([style*="display: none"])');
        group.style.display = visibleRows.length > 0 ? '' : 'none';
    });
}
</script>
