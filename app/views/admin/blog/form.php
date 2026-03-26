<?php
$hasAnalysis = !empty($analysis);
$seoScore = $hasAnalysis ? (int) $analysis['seo_score'] : 0;
$semanticScore = $hasAnalysis ? (int) $analysis['semantic_score'] : 0;
$serpPreview = $hasAnalysis ? $analysis['serp_preview'] : null;
$goldenRatio = $hasAnalysis ? $analysis['golden_ratio'] : null;
$contentStats = $hasAnalysis ? $analysis['content_stats'] : null;
$recommendations = $hasAnalysis ? ($analysis['recommendations'] ?? []) : [];
$technicalChecks = $hasAnalysis ? ($analysis['technical_checks'] ?? []) : [];
$semanticChecks = $hasAnalysis ? ($analysis['semantic_checks'] ?? []) : [];
?>

<style>
.seo-grid { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start; }
@media (max-width: 1100px) { .seo-grid { grid-template-columns: 1fr; } }
.score-ring { position: relative; width: 90px; height: 90px; }
.score-ring svg { transform: rotate(-90deg); }
.score-ring .value { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 800; }
.serp-preview { background: #fff; border: 1px solid #dfe1e5; border-radius: 8px; padding: 1rem; font-family: Arial, sans-serif; }
.serp-title { color: #1a0dab; font-size: 1.1rem; line-height: 1.3; text-decoration: none; cursor: pointer; }
.serp-title:hover { text-decoration: underline; }
.serp-url { color: #006621; font-size: 0.85rem; margin: 2px 0; }
.serp-desc { color: #545454; font-size: 0.85rem; line-height: 1.4; }
.check-list { list-style: none; padding: 0; margin: 0; }
.check-list li { padding: 0.4rem 0; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; display: flex; align-items: flex-start; gap: 0.5rem; }
.check-pass { color: #0d7a3e; }
.check-fail { color: #c0392b; }
.badge-pill { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.badge-green { background: #d4edda; color: #155724; }
.badge-yellow { background: #fff3cd; color: #856404; }
.badge-red { background: #f8d7da; color: #721c24; }
.golden-meter { height: 8px; background: #eee; border-radius: 4px; position: relative; overflow: hidden; }
.golden-meter-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
.tab-btn { background: none; border: none; padding: 0.5rem 1rem; cursor: pointer; font-size: 0.85rem; border-bottom: 2px solid transparent; color: #666; }
.tab-btn.active { border-bottom-color: #8B1538; color: #8B1538; font-weight: 600; }
.tab-content { display: none; }
.tab-content.active { display: block; }
</style>

<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour Blog</a>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0;"><?= e($submitLabel) ?></h1>

        <?php if ($hasAnalysis): ?>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <!-- SEO Score Ring -->
            <div style="text-align: center;">
                <div class="score-ring">
                    <?php
                    $seoColor = $seoScore >= 80 ? '#0d7a3e' : ($seoScore >= 50 ? '#D4AF37' : '#c0392b');
                    $seoOffset = 283 - (283 * $seoScore / 100);
                    ?>
                    <svg width="90" height="90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#eee" stroke-width="8"/>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="<?= $seoColor ?>" stroke-width="8"
                            stroke-dasharray="283" stroke-dashoffset="<?= $seoOffset ?>" stroke-linecap="round"/>
                    </svg>
                    <div class="value" style="color: <?= $seoColor ?>"><?= $seoScore ?></div>
                </div>
                <div style="font-size: 0.75rem; color: #666; margin-top: 2px;">SEO Technique</div>
            </div>

            <!-- Semantic Score Ring -->
            <div style="text-align: center;">
                <div class="score-ring">
                    <?php
                    $semColor = $semanticScore >= 80 ? '#0d7a3e' : ($semanticScore >= 50 ? '#D4AF37' : '#c0392b');
                    $semOffset = 283 - (283 * $semanticScore / 100);
                    ?>
                    <svg width="90" height="90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#eee" stroke-width="8"/>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="<?= $semColor ?>" stroke-width="8"
                            stroke-dasharray="283" stroke-dashoffset="<?= $semOffset ?>" stroke-linecap="round"/>
                    </svg>
                    <div class="value" style="color: <?= $semColor ?>"><?= $semanticScore ?></div>
                </div>
                <div style="font-size: 0.75rem; color: #666; margin-top: 2px;">Sémantique</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (($message ?? '') !== ''): ?><p class="success"><?= e((string) $message) ?></p><?php endif; ?>
    <?php if (($error ?? '') !== ''): ?><p class="alert"><?= e((string) $error) ?></p><?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <p class="alert"><?= e((string) $err) ?></p>
    <?php endforeach; ?>

    <div class="seo-grid">
        <!-- LEFT: Article Form -->
        <div>
            <form method="post" action="<?= e($action) ?>" class="card form-grid" id="articleForm" style="gap: 1rem;">

                <!-- Focus Keyword (prominent) -->
                <div style="background: #f8f4e8; border: 2px solid #D4AF37; border-radius: 8px; padding: 1rem;">
                    <label style="font-weight: 700; color: #8B1538; margin-bottom: 0.5rem; display: block;">
                        Mot-clé Focus
                    </label>
                    <input type="text" name="focus_keyword" id="focusKeyword"
                        value="<?= e((string) ($article['focus_keyword'] ?? '')) ?>"
                        placeholder="Ex: vendre appartement centre-ville"
                        style="font-size: 1.1rem; font-weight: 600; border-color: #D4AF37;">
                    <?php if ($goldenRatio): ?>
                    <div style="margin-top: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px;">
                            <span>Densité: <strong><?= $goldenRatio['current_density'] ?>%</strong></span>
                            <span>Cible Golden Ratio: <strong>1.618%</strong></span>
                            <span><?= $goldenRatio['current_count'] ?>/<?= $goldenRatio['ideal_count'] ?> occurrences</span>
                        </div>
                        <div class="golden-meter">
                            <?php
                            $gWidth = min(100, ($goldenRatio['current_density'] / 3) * 100);
                            $gColor = $goldenRatio['status'] === 'optimal' ? '#0d7a3e' : ($goldenRatio['status'] === 'under' ? '#D4AF37' : '#c0392b');
                            ?>
                            <div class="golden-meter-fill" style="width: <?= $gWidth ?>%; background: <?= $gColor ?>;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #999; margin-top: 2px;">
                            <span>0%</span><span>1.0%</span><span style="color: #0d7a3e; font-weight: 600;">1.618%</span><span>2.5%</span><span>3%</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <label>Mots-clés secondaires
                    <textarea name="secondary_keywords" rows="2" placeholder="Séparés par des virgules"><?= e((string) ($article['secondary_keywords'] ?? '')) ?></textarea>
                </label>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label>Titre de l'article (H1)
                        <input type="text" name="title" id="articleTitle" value="<?= e((string) ($article['title'] ?? '')) ?>" required>
                    </label>
                    <label>Slug (URL)
                        <input type="text" name="slug" id="articleSlug" value="<?= e((string) ($article['slug'] ?? '')) ?>" required>
                    </label>
                </div>

                <label>H1 (si différent du titre)
                    <input type="text" name="h1_tag" value="<?= e((string) ($article['h1_tag'] ?? '')) ?>" placeholder="Laissez vide pour utiliser le titre">
                </label>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <label>Titre SEO (50-60 car.)
                        <input type="text" name="meta_title" id="metaTitle"
                            value="<?= e((string) ($article['meta_title'] ?? '')) ?>" required
                            oninput="updateSerpPreview(); updateCharCount(this, 'metaTitleCount', 50, 60)">
                        <small id="metaTitleCount" style="color: #888;"><?= mb_strlen((string) ($article['meta_title'] ?? '')) ?>/60</small>
                    </label>
                    <label>Persona
                        <input type="text" name="persona" value="<?= e((string) ($article['persona'] ?? '')) ?>" required>
                    </label>
                </div>

                <label>Meta Description (150-160 car.)
                    <textarea name="meta_description" id="metaDesc" rows="2" required
                        oninput="updateSerpPreview(); updateCharCount(this, 'metaDescCount', 150, 160)"><?= e((string) ($article['meta_description'] ?? '')) ?></textarea>
                    <small id="metaDescCount" style="color: #888;"><?= mb_strlen((string) ($article['meta_description'] ?? '')) ?>/160</small>
                </label>

                <label>Contenu HTML</label>
                <textarea name="content" id="articleContent" rows="20" required style="font-family: 'Courier New', monospace; font-size: 0.85rem;"><?= e((string) ($article['content'] ?? '')) ?></textarea>

                <!-- Open Graph -->
                <details style="border: 1px solid #e0e0e0; border-radius: 6px; padding: 1rem;">
                    <summary style="cursor: pointer; font-weight: 600; color: #8B1538;">Open Graph & Avancé</summary>
                    <div class="form-grid" style="gap: 1rem; margin-top: 1rem;">
                        <label>OG Title
                            <input type="text" name="og_title" value="<?= e((string) ($article['og_title'] ?? '')) ?>" placeholder="Titre pour les réseaux sociaux">
                        </label>
                        <label>OG Description
                            <textarea name="og_description" rows="2" placeholder="Description pour les réseaux sociaux"><?= e((string) ($article['og_description'] ?? '')) ?></textarea>
                        </label>
                        <label>OG Image URL
                            <input type="text" name="og_image" value="<?= e((string) ($article['og_image'] ?? '')) ?>" placeholder="URL image 1200x630px">
                        </label>
                        <label>URL Canonique
                            <input type="text" name="canonical_url" value="<?= e((string) ($article['canonical_url'] ?? '')) ?>" placeholder="Laissez vide pour URL par défaut">
                        </label>
                        <label>FAQ Schema JSON-LD
                            <textarea name="faq_schema" rows="4" placeholder='[{"question": "...", "answer": "..."}]'><?= e((string) ($article['faq_schema'] ?? '')) ?></textarea>
                        </label>
                    </div>
                </details>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <label>Niveau de conscience
                        <input type="text" name="awareness_level" value="<?= e((string) ($article['awareness_level'] ?? '')) ?>" required>
                    </label>
                    <label>Type d'article
                        <select name="article_type" style="width: 100%;">
                            <option value="standalone" <?= (($article['article_type'] ?? '') === 'standalone') ? 'selected' : '' ?>>Indépendant</option>
                            <option value="pilier" <?= (($article['article_type'] ?? '') === 'pilier') ? 'selected' : '' ?>>Pilier</option>
                            <option value="satellite" <?= (($article['article_type'] ?? '') === 'satellite') ? 'selected' : '' ?>>Satellite</option>
                        </select>
                    </label>
                    <label>Silo SEO
                        <select name="silo_id" style="width: 100%;">
                            <option value="">-- Aucun --</option>
                            <?php foreach (($silos ?? []) as $silo): ?>
                                <option value="<?= (int) $silo['id'] ?>" <?= (($article['silo_id'] ?? '') == $silo['id']) ? 'selected' : '' ?>><?= e((string) $silo['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <input type="hidden" name="target_audience" value="<?= e((string) ($article['target_audience'] ?? '')) ?>">
                <input type="hidden" name="article_goal" value="<?= e((string) ($article['article_goal'] ?? '')) ?>">

                <div style="display: flex; gap: 1rem; align-items: center;">
                    <label style="flex: 0 0 auto;">Statut
                        <select name="status" required>
                            <option value="draft" <?= (($article['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
                            <option value="published" <?= (($article['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
                        </select>
                    </label>
                    <div style="flex: 1;"></div>
                    <button class="btn" type="submit" style="padding: 0.75rem 2rem;"><?= e($submitLabel) ?></button>
                    <button type="button" class="btn btn-ghost" onclick="analyzeSeo()" style="padding: 0.75rem 1.5rem;">
                        Analyser SEO
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT: SEO Sidebar -->
        <div>
            <!-- SERP Preview -->
            <section class="card" style="margin-bottom: 1rem;">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #8B1538;">Aperçu SERP Google</h3>
                <div class="serp-preview" id="serpPreview">
                    <div class="serp-title" id="serpTitle"><?= e($serpPreview ? $serpPreview['title'] : (($article['meta_title'] ?? '') ?: 'Titre SEO de votre article')) ?></div>
                    <div class="serp-url" id="serpUrl"><?= e($serpPreview ? $serpPreview['url'] : 'votre-site.fr/blog/' . ($article['slug'] ?? 'slug-article')) ?></div>
                    <div class="serp-desc" id="serpDesc"><?= e($serpPreview ? $serpPreview['description'] : (($article['meta_description'] ?? '') ?: 'Meta description de votre article...')) ?></div>
                </div>
            </section>

            <?php if ($hasAnalysis): ?>
            <!-- Content Stats -->
            <section class="card" style="margin-bottom: 1rem;">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #8B1538;">Statistiques</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem;">
                    <div><strong><?= $contentStats['word_count'] ?? 0 ?></strong> mots</div>
                    <div><strong><?= $contentStats['reading_time'] ?? 0 ?></strong> min lecture</div>
                    <div><strong><?= $contentStats['h2_count'] ?? 0 ?></strong> sections H2</div>
                    <div><strong><?= $contentStats['h3_count'] ?? 0 ?></strong> sous-sections H3</div>
                    <div><strong><?= $contentStats['images_count'] ?? 0 ?></strong> images</div>
                    <div><strong><?= $contentStats['links_count'] ?? 0 ?></strong> liens</div>
                </div>
            </section>

            <!-- Tabs: Technical / Semantic / Recommendations -->
            <section class="card" style="margin-bottom: 1rem;">
                <div style="border-bottom: 1px solid #e0e0e0; margin-bottom: 0.75rem;">
                    <button class="tab-btn active" onclick="showTab('tech')">Technique</button>
                    <button class="tab-btn" onclick="showTab('sem')">Sémantique</button>
                    <button class="tab-btn" onclick="showTab('reco')">Actions (<?= count($recommendations) ?>)</button>
                </div>

                <!-- Technical Checks -->
                <div id="tab-tech" class="tab-content active">
                    <ul class="check-list">
                        <?php foreach ($technicalChecks as $key => $check): ?>
                        <li>
                            <span style="font-size: 1rem;"><?= $check['pass'] ? '<span class="check-pass">&#10003;</span>' : '<span class="check-fail">&#10007;</span>' ?></span>
                            <div>
                                <strong><?= e($check['label']) ?></strong><br>
                                <span style="color: #888;"><?= e($check['detail']) ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Semantic Checks -->
                <div id="tab-sem" class="tab-content">
                    <ul class="check-list">
                        <?php foreach ($semanticChecks as $key => $check): ?>
                        <li>
                            <span style="font-size: 1rem;"><?= $check['pass'] ? '<span class="check-pass">&#10003;</span>' : '<span class="check-fail">&#10007;</span>' ?></span>
                            <div>
                                <strong><?= e($check['label']) ?></strong><br>
                                <span style="color: #888;"><?= e($check['detail']) ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Recommendations -->
                <div id="tab-reco" class="tab-content">
                    <?php if (empty($recommendations)): ?>
                        <p style="color: #0d7a3e; font-weight: 600;">Excellent ! Aucune recommandation.</p>
                    <?php else: ?>
                        <ul class="check-list">
                            <?php foreach ($recommendations as $rec): ?>
                            <li>
                                <?php
                                $badgeClass = match($rec['priority']) {
                                    'critical' => 'badge-red',
                                    'high' => 'badge-yellow',
                                    default => 'badge-green',
                                };
                                ?>
                                <span class="badge-pill <?= $badgeClass ?>"><?= e($rec['priority']) ?></span>
                                <div>
                                    <strong><?= e($rec['label']) ?></strong><br>
                                    <span style="color: #555;"><?= e($rec['action']) ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Golden Ratio Detail -->
            <?php if ($goldenRatio): ?>
            <section class="card" style="margin-bottom: 1rem;">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #8B1538;">Golden Ratio Mot-Clé</h3>
                <div style="text-align: center; margin-bottom: 0.5rem;">
                    <?php
                    $grBadge = match($goldenRatio['status']) {
                        'optimal' => 'badge-green',
                        'under' => 'badge-yellow',
                        'over' => 'badge-red',
                    };
                    $grLabel = match($goldenRatio['status']) {
                        'optimal' => 'Optimal',
                        'under' => 'Sous-optimisé',
                        'over' => 'Sur-optimisé',
                    };
                    ?>
                    <span class="badge-pill <?= $grBadge ?>" style="font-size: 0.9rem; padding: 4px 16px;"><?= $grLabel ?></span>
                </div>
                <div style="font-size: 0.85rem; color: #555; text-align: center;">
                    <p><strong>"<?= e($goldenRatio['keyword']) ?>"</strong></p>
                    <p><?= $goldenRatio['current_count'] ?> occurrences sur <?= $goldenRatio['word_count'] ?> mots</p>
                    <p>Densité: <?= $goldenRatio['current_density'] ?>% (cible: 1.618%)</p>
                    <p style="font-size: 0.8rem; color: #888;"><?= e($goldenRatio['message']) ?></p>
                </div>
            </section>
            <?php endif; ?>

            <?php endif; /* hasAnalysis */ ?>

            <!-- Indexation & Position Google -->
            <?php if (!empty($article['id']) && ($article['status'] ?? '') === 'published'): ?>
            <section class="card" style="margin-bottom: 1rem;" id="indexingCard">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #8B1538;">Google & Trafic</h3>

                <!-- Page Views -->
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.75rem; background: #f8f8f8; border-radius: 6px;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: #8B1538;" id="pageViewsCount"><?= (int) ($article['page_views'] ?? 0) ?></div>
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 600;">Visites</div>
                        <div style="font-size: 0.75rem; color: #888;">Depuis la publication</div>
                    </div>
                </div>

                <!-- Indexation Status -->
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.85rem; font-weight: 600;">Indexation Google</span>
                        <span id="indexStatus" style="font-size: 0.8rem; color: #888;">--</span>
                    </div>
                    <div id="indexStatusBar" style="height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                        <div id="indexStatusFill" style="height: 100%; width: 0; border-radius: 3px; transition: width 0.5s;"></div>
                    </div>
                </div>

                <!-- Position in SERPs -->
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.85rem; font-weight: 600;">Position Google</span>
                        <span id="serpPosition" style="font-size: 0.8rem; color: #888;">--</span>
                    </div>
                    <div style="font-size: 0.75rem; color: #999;" id="serpKeyword">
                        Mot-clé : <?= e((string) ($article['focus_keyword'] ?? '')) ?>
                    </div>
                </div>

                <button type="button" class="btn btn-small btn-ghost" onclick="checkIndexing()" style="width: 100%; margin-top: 0.5rem;" id="checkIndexBtn">
                    Vérifier indexation & position
                </button>
            </section>
            <?php endif; ?>

            <!-- Quick Links -->
            <section class="card" style="margin-bottom: 1rem;">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #8B1538;">Ressources SEO</h3>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.85rem;">
                    <li style="padding: 0.3rem 0;"><a href="/admin/blog/seo-guide" style="color: #8B1538;">Guide SEO Local Immobilier</a></li>
                    <li style="padding: 0.3rem 0;"><a href="/admin/blog/silos" style="color: #8B1538;">Gérer les Silos SEO</a></li>
                    <li style="padding: 0.3rem 0;"><a href="/admin/google-ads" style="color: #8B1538;">Guide Google Ads</a></li>
                </ul>
            </section>

            <!-- GMB Publication -->
            <?php
            $gmbPub = $gmbPublication ?? null;
            ?>
            <section class="card" style="border: 1px solid #4285f4; border-radius: 8px;">
                <h3 style="margin: 0 0 0.75rem; font-size: 1rem; color: #4285f4; display: flex; align-items: center; gap: 0.4rem;">
                    <span style="font-weight: 800; font-size: 1.1rem;">G</span> Publication GMB
                </h3>
                <?php if ($gmbPub !== null): ?>
                    <?php
                    $gmbStatus = (string) ($gmbPub['status'] ?? 'draft');
                    $gmbStatusColors = [
                        'draft' => ['bg' => '#e8e8e8', 'color' => '#666'],
                        'scheduled' => ['bg' => '#e8f0fe', 'color' => '#1a56db'],
                        'notified' => ['bg' => '#fff3cd', 'color' => '#856404'],
                        'published' => ['bg' => '#d4edda', 'color' => '#155724'],
                        'expired' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                    ];
                    $sc = $gmbStatusColors[$gmbStatus] ?? $gmbStatusColors['draft'];
                    ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; background: <?= $sc['bg'] ?>; color: <?= $sc['color'] ?>;">
                            <?= e(ucfirst($gmbStatus)) ?>
                        </span>
                        <?php if (!empty($gmbPub['scheduled_at'])): ?>
                            <span style="font-size: 0.8rem; color: #888;">
                                Planifiée : <?= e(date('d/m/Y H:i', strtotime((string) $gmbPub['scheduled_at']))) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($gmbPub['title'])): ?>
                    <div style="font-size: 0.85rem; color: #333; margin-bottom: 0.5rem;">
                        <strong><?= e((string) $gmbPub['title']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <a href="/admin/gmb/edit/<?= (int) $gmbPub['id'] ?>" class="btn btn-small" style="width: 100%; text-align: center; background: #4285f4; color: #fff; border: none;">
                        Voir / Modifier la publication GMB
                    </a>
                <?php elseif (!empty($article['id'])): ?>
                    <p style="font-size: 0.85rem; color: #888; margin: 0 0 0.75rem;">Aucune publication GMB pour cet article.</p>
                    <a href="/admin/gmb/create?article_id=<?= (int) $article['id'] ?>" class="btn btn-small btn-ghost" style="width: 100%; text-align: center; border-color: #4285f4; color: #4285f4;">
                        Générer la publication GMB
                    </a>
                <?php else: ?>
                    <p style="font-size: 0.85rem; color: #888; margin: 0;">Sauvegardez l'article pour pouvoir générer une publication GMB.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <?php if (!empty($article['id']) && !empty($revisions)): ?>
    <section class="card" style="margin-top:1.5rem;">
        <h2>Historique des révisions</h2>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revisions as $revision): ?>
                    <tr>
                        <td>v<?= (int) $revision['revision_number'] ?></td>
                        <td><?= e((string) $revision['title']) ?></td>
                        <td><?= e((string) $revision['status']) ?></td>
                        <td><?= e((string) $revision['created_at']) ?></td>
                        <td>
                            <form method="post" action="/admin/blog/restore/<?= (int) $article['id'] ?>/<?= (int) $revision['id'] ?>" onsubmit="return confirm('Restaurer cette version ?');">
                                <button type="submit" class="btn btn-small btn-ghost">Restaurer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}

function updateSerpPreview() {
    const title = document.getElementById('metaTitle');
    const desc = document.getElementById('metaDesc');
    const slug = document.getElementById('articleSlug');
    if (title) {
        const t = title.value || 'Titre SEO de votre article';
        document.getElementById('serpTitle').textContent = t.length > 60 ? t.substring(0, 57) + '...' : t;
    }
    if (desc) {
        const d = desc.value || 'Meta description de votre article...';
        document.getElementById('serpDesc').textContent = d.length > 160 ? d.substring(0, 157) + '...' : d;
    }
    if (slug) {
        document.getElementById('serpUrl').textContent = 'votre-site.fr/blog/' + (slug.value || 'slug-article');
    }
}

function updateCharCount(el, countId, min, max) {
    const len = el.value.length;
    const countEl = document.getElementById(countId);
    countEl.textContent = len + '/' + max;
    countEl.style.color = (len >= min && len <= max) ? '#0d7a3e' : (len > max ? '#c0392b' : '#888');
}

function analyzeSeo() {
    const form = document.getElementById('articleForm');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((v, k) => data[k] = v);

    fetch('/admin/blog/api/analyze', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            alert('Score SEO: ' + result.data.seo_score + '/100 | Score Sémantique: ' + result.data.semantic_score + '/100\n\nRechargez la page pour voir le détail.');
        } else {
            alert('Erreur: ' + result.error);
        }
    })
    .catch(err => alert('Erreur réseau: ' + err.message));
}

// Auto-generate slug from title
document.getElementById('articleTitle')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('articleSlug').value = slug;
    updateSerpPreview();
});

// Check Google indexation & position
function checkIndexing() {
    const btn = document.getElementById('checkIndexBtn');
    const articleId = <?= (int) ($article['id'] ?? 0) ?>;
    if (!articleId) return;

    btn.disabled = true;
    btn.textContent = 'Vérification en cours...';

    fetch('/admin/blog/api/check-indexing', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ article_id: articleId })
    })
    .then(r => r.json())
    .then(result => {
        btn.disabled = false;
        btn.textContent = 'Vérifier indexation & position';

        if (result.success) {
            const data = result.data;

            // Indexation
            const statusEl = document.getElementById('indexStatus');
            const fillEl = document.getElementById('indexStatusFill');
            if (data.is_indexed) {
                statusEl.innerHTML = '<span style="color: #0d7a3e; font-weight: 600;">Indexée</span>';
                fillEl.style.width = '100%';
                fillEl.style.background = '#0d7a3e';
            } else {
                statusEl.innerHTML = '<span style="color: #c0392b; font-weight: 600;">Non indexée</span>';
                fillEl.style.width = '100%';
                fillEl.style.background = '#c0392b';
            }

            // Position
            const posEl = document.getElementById('serpPosition');
            if (data.position !== null && data.position > 0) {
                const posColor = data.position <= 3 ? '#0d7a3e' : (data.position <= 10 ? '#D4AF37' : '#c0392b');
                posEl.innerHTML = '<span style="color: ' + posColor + '; font-weight: 700; font-size: 1.1rem;">#' + data.position + '</span>';
            } else if (data.is_indexed) {
                posEl.innerHTML = '<span style="color: #888;">Non classée pour ce mot-clé</span>';
            } else {
                posEl.innerHTML = '<span style="color: #888;">Page non indexée</span>';
            }

            if (data.last_checked) {
                btn.textContent = 'Vérifié le ' + new Date(data.last_checked).toLocaleDateString('fr-FR');
            }
        } else {
            alert('Erreur: ' + (result.error || 'Erreur inconnue'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Vérifier indexation & position';
        alert('Erreur réseau: ' + err.message);
    });
}
</script>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: '#articleContent',
        height: 500,
        language: 'fr_FR',
        menubar: 'file edit view insert format tools table',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code fullscreen | removeformat help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 1rem; }',
        branding: false,
        promotion: false,
        valid_elements: '*[*]',
        entity_encoding: 'raw',
        setup: function(editor) {
            editor.on('change keyup', function() {
                editor.save();
            });
        }
    });
}
</script>
