<?php
$totalKw = count($keywords);
$totalClicks = 0;
$totalImpressions = 0;
$avgPosition = 0;
$avgCtr = 0;

if ($totalKw > 0) {
    foreach ($keywords as $kw) {
        $totalClicks += (int) $kw['clicks'];
        $totalImpressions += (int) $kw['impressions'];
    }
    $avgPosition = round(array_sum(array_column($keywords, 'position')) / $totalKw, 1);
    $avgCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
}

$lastFetched = $cache_info['last_fetched'] ?? null;
?>

<style>
.seo-hub-header {
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    flex-wrap: wrap; margin-bottom: 1.5rem;
}
.seo-hub-header h2 { margin: 0; font-size: 1.4rem; }
.seo-hub-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.gsc-connect-card {
    background: linear-gradient(135deg, #1a1410 0%, #2d2318 100%);
    color: #fff; border-radius: 12px; padding: 2.5rem; text-align: center;
    max-width: 600px; margin: 3rem auto;
}
.gsc-connect-card h3 { font-size: 1.5rem; margin: 0 0 0.75rem; font-family: 'Playfair Display', serif; }
.gsc-connect-card p { color: #c8c0b8; margin: 0 0 1.5rem; line-height: 1.6; }
.gsc-connect-card .gsc-icon { font-size: 3rem; margin-bottom: 1rem; color: #D4AF37; }

.btn-gsc {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;
    text-decoration: none; font-size: 0.9rem; cursor: pointer; border: none;
    transition: all 0.2s;
}
.btn-gsc-primary { background: #D4AF37; color: #1a1410; }
.btn-gsc-primary:hover { background: #E8C547; transform: translateY(-1px); }
.btn-gsc-danger { background: #e24b4a; color: #fff; }
.btn-gsc-danger:hover { background: #c0392b; }
.btn-gsc-outline {
    background: transparent; color: #8B1538; border: 2px solid #8B1538;
}
.btn-gsc-outline:hover { background: #8B1538; color: #fff; }
.btn-gsc-sm { padding: 0.4rem 0.8rem; font-size: 0.8rem; }

.stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card {
    background: #fff; border-radius: 10px; padding: 1.25rem; border: 1px solid #e8dfd7;
    text-align: center;
}
.stat-card .stat-icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
.stat-card .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #1a1410; }
.stat-card .stat-label { font-size: 0.78rem; color: #6b6459; margin-top: 0.25rem; }
.stat-card.clicks .stat-icon { color: #8B1538; }
.stat-card.impressions .stat-icon { color: #3b82f6; }
.stat-card.ctr .stat-icon { color: #22c55e; }
.stat-card.position .stat-icon { color: #D4AF37; }

.kw-controls {
    display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;
}
.kw-search {
    flex: 1; min-width: 200px; padding: 0.6rem 1rem; border: 1px solid #e8dfd7;
    border-radius: 8px; font-size: 0.9rem; background: #fff;
}
.kw-search:focus { outline: none; border-color: #8B1538; box-shadow: 0 0 0 3px rgba(139,21,56,0.1); }

.kw-table-wrapper { overflow-x: auto; background: #fff; border-radius: 10px; border: 1px solid #e8dfd7; }
.kw-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.kw-table th {
    background: #f9f7f5; padding: 0.75rem 1rem; text-align: left; font-weight: 600;
    color: #6b6459; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 2px solid #e8dfd7; white-space: nowrap; cursor: pointer;
    user-select: none;
}
.kw-table th:hover { color: #8B1538; }
.kw-table th.sorted { color: #8B1538; }
.kw-table th .sort-icon { margin-left: 0.3rem; font-size: 0.7rem; }
.kw-table td { padding: 0.65rem 1rem; border-bottom: 1px solid #f0ece8; }
.kw-table tr:hover td { background: #faf9f7; }
.kw-table tr:last-child td { border-bottom: none; }

.kw-keyword { font-weight: 500; color: #1a1410; max-width: 400px; word-break: break-word; }
.kw-metric { font-weight: 600; font-variant-numeric: tabular-nums; }
.kw-clicks { color: #8B1538; }
.kw-impressions { color: #3b82f6; }
.kw-ctr { color: #22c55e; }
.kw-position { color: #D4AF37; }

.position-badge {
    display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.78rem; font-weight: 700;
}
.position-top3 { background: #d4edda; color: #155724; }
.position-top10 { background: #fff3cd; color: #856404; }
.position-top20 { background: #e2e3e5; color: #383d41; }
.position-low { background: #f8d7da; color: #721c24; }

.connection-info {
    display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem;
    background: #d4edda; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem;
}
.connection-info i { color: #155724; }
.connection-info .site-url { font-weight: 600; color: #155724; }
.connection-info .last-sync { color: #6b6459; margin-left: auto; }

.no-credentials-info {
    background: #fff3cd; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;
    border: 1px solid #ffc107; font-size: 0.88rem;
}
.no-credentials-info h4 { margin: 0 0 0.75rem; color: #856404; }
.no-credentials-info ol { margin: 0; padding-left: 1.25rem; color: #664d03; line-height: 1.8; }
.no-credentials-info code { background: #fef3c7; padding: 1px 6px; border-radius: 4px; font-size: 0.82rem; }

.kw-empty { text-align: center; padding: 2rem; color: #6b6459; }

.alert { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.88rem; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.opportunities-section { margin-top: 2rem; }
.opportunities-section h3 { font-size: 1.1rem; margin-bottom: 1rem; }
.opp-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
.opp-card {
    background: #fff; border-radius: 10px; padding: 1.25rem; border: 1px solid #e8dfd7;
}
.opp-card h4 { margin: 0 0 0.5rem; font-size: 0.95rem; }
.opp-card .opp-kw { font-weight: 600; color: #8B1538; }
.opp-card .opp-meta { font-size: 0.8rem; color: #6b6459; margin-top: 0.5rem; }
.opp-card .opp-tip { font-size: 0.82rem; margin-top: 0.5rem; padding: 0.5rem; background: #faf9f7; border-radius: 6px; }
</style>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!$connected): ?>
    <!-- Not connected state -->
    <?php if (!$has_credentials): ?>
    <div class="no-credentials-info">
        <h4><i class="fas fa-info-circle"></i> Configuration requise</h4>
        <ol>
            <li>Allez sur <strong>Google Cloud Console</strong> > APIs & Services > Credentials</li>
            <li>Créez un <strong>OAuth 2.0 Client ID</strong> (type: Web Application)</li>
            <li>Ajoutez l'URI de redirection : <code><?= htmlspecialchars(rtrim((string) ($connection['site_url'] ?? $_ENV['APP_BASE_URL'] ?? ''), '/'), ENT_QUOTES, 'UTF-8') ?>/admin/seo-hub/callback</code></li>
            <li>Activez l'API <strong>Google Search Console API</strong> dans votre projet</li>
            <li>Ajoutez ces variables à votre fichier <code>.env</code> :
                <br><code>GSC_CLIENT_ID="votre_client_id"</code>
                <br><code>GSC_CLIENT_SECRET="votre_client_secret"</code>
            </li>
        </ol>
    </div>
    <?php endif; ?>

    <div class="gsc-connect-card">
        <div class="gsc-icon"><i class="fas fa-chart-line"></i></div>
        <h3>Connectez Google Search Console</h3>
        <p>
            Accédez aux vrais mots-clés qui génèrent du trafic sur votre site.<br>
            Données réelles : clics, impressions, CTR et position moyenne.
        </p>
        <?php if ($has_credentials): ?>
        <a href="/admin/seo-hub/connect" class="btn-gsc btn-gsc-primary">
            <i class="fab fa-google"></i> Connecter Google Search Console
        </a>
        <?php else: ?>
        <p style="color: #D4AF37; font-size: 0.88rem;">
            <i class="fas fa-lock"></i> Configurez d'abord vos identifiants GSC (voir ci-dessus).
        </p>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Connected state -->
    <div class="seo-hub-header">
        <h2><i class="fas fa-chart-line" style="color:#8B1538"></i> SEO Hub — Mots-clés réels</h2>
        <div class="seo-hub-actions">
            <form action="/admin/seo-hub/refresh" method="POST" style="display:inline">
                <button type="submit" class="btn-gsc btn-gsc-outline btn-gsc-sm">
                    <i class="fas fa-sync-alt"></i> Rafraîchir
                </button>
            </form>
            <form action="/admin/seo-hub/disconnect" method="POST" style="display:inline"
                  onsubmit="return confirm('Déconnecter Google Search Console ?')">
                <button type="submit" class="btn-gsc btn-gsc-danger btn-gsc-sm">
                    <i class="fas fa-unlink"></i> Déconnecter
                </button>
            </form>
        </div>
    </div>

    <div class="connection-info">
        <i class="fas fa-check-circle"></i>
        <span>Connecté à <span class="site-url"><?= htmlspecialchars($connection['site_url'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></span>
        <?php if ($lastFetched): ?>
        <span class="last-sync"><i class="fas fa-clock"></i> Dernière sync : <?= htmlspecialchars($lastFetched, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>

    <!-- Stats cards -->
    <div class="stat-cards">
        <div class="stat-card clicks">
            <div class="stat-icon"><i class="fas fa-mouse-pointer"></i></div>
            <div class="stat-value"><?= number_format($totalClicks, 0, ',', ' ') ?></div>
            <div class="stat-label">Clics (28j)</div>
        </div>
        <div class="stat-card impressions">
            <div class="stat-icon"><i class="fas fa-eye"></i></div>
            <div class="stat-value"><?= number_format($totalImpressions, 0, ',', ' ') ?></div>
            <div class="stat-label">Impressions (28j)</div>
        </div>
        <div class="stat-card ctr">
            <div class="stat-icon"><i class="fas fa-percentage"></i></div>
            <div class="stat-value"><?= $avgCtr ?>%</div>
            <div class="stat-label">CTR moyen</div>
        </div>
        <div class="stat-card position">
            <div class="stat-icon"><i class="fas fa-sort-amount-up"></i></div>
            <div class="stat-value"><?= $avgPosition ?></div>
            <div class="stat-label">Position moyenne</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#6b6459"><i class="fas fa-key"></i></div>
            <div class="stat-value"><?= number_format($totalKw, 0, ',', ' ') ?></div>
            <div class="stat-label">Mots-clés</div>
        </div>
    </div>

    <!-- Search & filter -->
    <div class="kw-controls">
        <form method="GET" action="/admin/seo-hub" style="display:flex; gap:0.5rem; flex:1; min-width:200px;">
            <input type="text" name="q" class="kw-search" placeholder="Rechercher un mot-clé..."
                   value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="order" value="<?= htmlspecialchars($order, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn-gsc btn-gsc-outline btn-gsc-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <!-- Keywords table -->
    <?php if (empty($keywords)): ?>
        <div class="kw-empty">
            <i class="fas fa-search" style="font-size:2rem; color:#ccc; margin-bottom:0.5rem; display:block"></i>
            <?php if ($search !== ''): ?>
                Aucun mot-clé trouvé pour "<strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>".
            <?php else: ?>
                Aucun mot-clé en cache. Cliquez sur <strong>Rafraîchir</strong> pour importer les données.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php
        $sortUrl = function (string $col) use ($sort, $order, $search) {
            $newOrder = ($sort === $col && $order === 'DESC') ? 'ASC' : 'DESC';
            $params = ['sort' => $col, 'order' => $newOrder];
            if ($search !== '') $params['q'] = $search;
            return '/admin/seo-hub?' . http_build_query($params);
        };
        $sortIcon = function (string $col) use ($sort, $order) {
            if ($sort !== $col) return '';
            return $order === 'DESC' ? '<i class="fas fa-caret-down sort-icon"></i>' : '<i class="fas fa-caret-up sort-icon"></i>';
        };
        ?>
        <div class="kw-table-wrapper">
            <table class="kw-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th class="<?= $sort === 'keyword' ? 'sorted' : '' ?>">
                            <a href="<?= $sortUrl('keyword') ?>" style="color:inherit;text-decoration:none">Mot-clé <?= $sortIcon('keyword') ?></a>
                        </th>
                        <th class="<?= $sort === 'clicks' ? 'sorted' : '' ?>" style="text-align:right">
                            <a href="<?= $sortUrl('clicks') ?>" style="color:inherit;text-decoration:none">Clics <?= $sortIcon('clicks') ?></a>
                        </th>
                        <th class="<?= $sort === 'impressions' ? 'sorted' : '' ?>" style="text-align:right">
                            <a href="<?= $sortUrl('impressions') ?>" style="color:inherit;text-decoration:none">Impressions <?= $sortIcon('impressions') ?></a>
                        </th>
                        <th class="<?= $sort === 'ctr' ? 'sorted' : '' ?>" style="text-align:right">
                            <a href="<?= $sortUrl('ctr') ?>" style="color:inherit;text-decoration:none">CTR <?= $sortIcon('ctr') ?></a>
                        </th>
                        <th class="<?= $sort === 'position' ? 'sorted' : '' ?>" style="text-align:right">
                            <a href="<?= $sortUrl('position') ?>" style="color:inherit;text-decoration:none">Position <?= $sortIcon('position') ?></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($keywords, 0, 200) as $i => $kw): ?>
                    <?php
                        $pos = (float) $kw['position'];
                        $posClass = $pos <= 3 ? 'position-top3' : ($pos <= 10 ? 'position-top10' : ($pos <= 20 ? 'position-top20' : 'position-low'));
                    ?>
                    <tr>
                        <td style="color:#aaa; font-size:0.78rem"><?= $i + 1 ?></td>
                        <td class="kw-keyword"><?= htmlspecialchars($kw['keyword'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="kw-metric kw-clicks" style="text-align:right"><?= number_format((int) $kw['clicks'], 0, ',', ' ') ?></td>
                        <td class="kw-metric kw-impressions" style="text-align:right"><?= number_format((int) $kw['impressions'], 0, ',', ' ') ?></td>
                        <td class="kw-metric kw-ctr" style="text-align:right"><?= round((float) $kw['ctr'] * 100, 2) ?>%</td>
                        <td style="text-align:right">
                            <span class="position-badge <?= $posClass ?>"><?= $pos ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SEO Opportunities -->
        <?php
        // Find quick-win opportunities: high impressions but low position (could rank higher)
        $opportunities = array_filter($keywords, function ($kw) {
            $pos = (float) $kw['position'];
            $imp = (int) $kw['impressions'];
            return $pos > 5 && $pos <= 20 && $imp >= 10;
        });
        usort($opportunities, function ($a, $b) {
            return (int) $b['impressions'] - (int) $a['impressions'];
        });
        $opportunities = array_slice($opportunities, 0, 6);
        ?>

        <?php if (!empty($opportunities)): ?>
        <div class="opportunities-section">
            <h3><i class="fas fa-lightbulb" style="color:#D4AF37"></i> Opportunités Quick-Win</h3>
            <p style="font-size:0.85rem; color:#6b6459; margin-bottom:1rem">
                Mots-clés avec du potentiel : beaucoup d'impressions mais pas encore en top 5.
                Optimisez votre contenu pour ces requêtes.
            </p>
            <div class="opp-cards">
                <?php foreach ($opportunities as $opp): ?>
                <div class="opp-card">
                    <h4><span class="opp-kw"><?= htmlspecialchars($opp['keyword'], ENT_QUOTES, 'UTF-8') ?></span></h4>
                    <div class="opp-meta">
                        Position <strong><?= (float) $opp['position'] ?></strong>
                        &middot; <?= number_format((int) $opp['impressions'], 0, ',', ' ') ?> impressions
                        &middot; <?= (int) $opp['clicks'] ?> clics
                    </div>
                    <div class="opp-tip">
                        <i class="fas fa-arrow-up" style="color:#22c55e"></i>
                        <?php if ((float) $opp['position'] <= 10): ?>
                            Proche du top 5 ! Enrichissez le contenu existant.
                        <?php else: ?>
                            Créez un article ciblé ou optimisez une page existante.
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>
