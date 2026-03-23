<?php
$statusLabels = [
    'draft'    => ['Brouillon',  '#6b6459', '#f4f1ed'],
    'ready'    => ['Prêt',       '#0d7a3e', '#d4edda'],
    'exported' => ['Exporté',    '#0c5460', '#d1ecf1'],
    'active'   => ['Actif',      '#155724', '#d4edda'],
    'paused'   => ['En pause',   '#856404', '#fff3cd'],
    'archived' => ['Archivé',    '#6b6459', '#e8e8e8'],
];
$st = $statusLabels[$campaign['status']] ?? ['?', '#999', '#eee'];

// Index ads and keywords by ad_group_id
$adsByAg = [];
foreach ($ads as $ad) {
    $adsByAg[(int) $ad['ad_group_id']][] = $ad;
}
$kwByAg = [];
foreach ($keywords as $kw) {
    $kwByAg[(int) $kw['ad_group_id']][] = $kw;
}
?>

<link rel="stylesheet" href="/assets/css/google-ads.css">

<div class="container">
    <a href="/admin/gads-campaigns" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">
        &larr; Retour aux campagnes
    </a>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">
                <i class="fas fa-eye" style="color: var(--admin-primary);"></i>
                Aperçu : <?= e($campaign['name']) ?>
            </h1>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                <span class="gads-badge" style="color: <?= $st[1] ?>; background: <?= $st[2] ?>;"><?= $st[0] ?></span>
                <span style="color: #888; font-size: 0.85rem;">
                    <?= e(ucfirst($campaign['campaign_type'])) ?> &middot;
                    <?= e($campaign['target_location']) ?> (<?= (int) $campaign['target_radius_km'] ?> km) &middot;
                    <?= number_format((float) $campaign['daily_budget'], 2, ',', ' ') ?> €/jour
                </span>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/gads-campaigns/wizard?id=<?= (int) $campaign['id'] ?>" class="btn btn-ghost">
                <i class="fas fa-pen"></i> Modifier
            </a>
            <a href="/admin/gads-campaigns/export?id=<?= (int) $campaign['id'] ?>" class="btn" style="background: var(--admin-primary); color: #fff;">
                <i class="fas fa-download"></i> Exporter CSV
            </a>
        </div>
    </div>

    <!-- Campaign Info Card -->
    <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <div class="gads-preview-info-grid">
            <div>
                <strong>Stratégie d'enchères</strong><br>
                <span style="color: #666;"><?= e(str_replace('_', ' ', ucfirst($campaign['bid_strategy']))) ?></span>
            </div>
            <?php if ($campaign['target_cpa']): ?>
            <div>
                <strong>CPA cible</strong><br>
                <span style="color: #666;"><?= number_format((float) $campaign['target_cpa'], 2, ',', ' ') ?> €</span>
            </div>
            <?php endif; ?>
            <?php if ($campaign['start_date']): ?>
            <div>
                <strong>Début</strong><br>
                <span style="color: #666;"><?= date('d/m/Y', strtotime($campaign['start_date'])) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($campaign['end_date']): ?>
            <div>
                <strong>Fin</strong><br>
                <span style="color: #666;"><?= date('d/m/Y', strtotime($campaign['end_date'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ad Groups -->
    <?php foreach ($ad_groups as $ag):
        $agId = (int) $ag['id'];
        $agAds = $adsByAg[$agId] ?? [];
        $agKws = $kwByAg[$agId] ?? [];
    ?>
    <div class="gads-preview-group">
        <div class="gads-preview-group-header">
            <h2 style="font-size: 1.1rem; margin: 0;">
                <i class="fas fa-layer-group" style="color: var(--admin-accent);"></i>
                <?= e($ag['name']) ?>
            </h2>
            <?php if ($ag['landing_url'] !== ''): ?>
            <span style="color: #888; font-size: 0.8rem;">
                <i class="fas fa-link"></i> <?= e($ag['landing_url']) ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Keywords -->
        <?php if (!empty($agKws)): ?>
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e8dfd7;">
            <h4 style="font-size: 0.85rem; margin: 0 0 0.5rem; color: #666;">
                <i class="fas fa-key"></i> Mots-clés (<?= count($agKws) ?>)
            </h4>
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                <?php foreach ($agKws as $kw):
                    $matchIcon = ['broad' => '~', 'phrase' => '"…"', 'exact' => '[…]'];
                    $neg = (int) $kw['is_negative'];
                ?>
                <span class="gads-kw-chip <?= $neg ? 'gads-kw-negative' : '' ?>">
                    <small style="opacity: 0.6;"><?= $matchIcon[$kw['match_type']] ?? '' ?></small>
                    <?= $neg ? '<i class="fas fa-minus-circle" style="color:#e24b4a;font-size:0.7rem;"></i> ' : '' ?>
                    <?= e($kw['keyword']) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ads — Google SERP Preview -->
        <?php foreach ($agAds as $ad):
            $headlines    = json_decode($ad['headlines'], true) ?: [];
            $descriptions = json_decode($ad['descriptions'], true) ?: [];
            $sitelinks    = json_decode($ad['sitelinks'] ?? 'null', true) ?: [];
            $callouts     = json_decode($ad['callouts'] ?? 'null', true) ?: [];
            $displayUrl   = parse_url($ad['final_url'], PHP_URL_HOST) ?: 'example.com';
            $path = '';
            if ($ad['path1'] !== '') $path .= '/' . $ad['path1'];
            if ($ad['path2'] !== '') $path .= '/' . $ad['path2'];
        ?>
        <div class="gads-serp-preview">
            <div class="gads-serp-label">
                <i class="fas fa-ad"></i> Aperçu SERP
                <?php if ($ad['ai_generated']): ?>
                <span class="gads-badge" style="background: #ede9fe; color: #7c3aed; margin-left: 0.5rem;">
                    <i class="fas fa-robot"></i> IA
                </span>
                <?php endif; ?>
            </div>
            <div class="gads-serp-card">
                <div class="gads-serp-ad-label">Sponsorisé</div>
                <div class="gads-serp-url"><?= e($displayUrl) ?><?= e($path) ?></div>
                <div class="gads-serp-title">
                    <?= e(implode(' | ', array_slice($headlines, 0, 3))) ?>
                </div>
                <div class="gads-serp-desc">
                    <?= e(implode(' ', array_slice($descriptions, 0, 2))) ?>
                </div>
                <?php if (!empty($sitelinks)): ?>
                <div class="gads-serp-sitelinks">
                    <?php foreach (array_slice($sitelinks, 0, 4) as $sl): ?>
                    <div class="gads-serp-sitelink">
                        <div class="gads-serp-sitelink-title"><?= e($sl['text'] ?? '') ?></div>
                        <div class="gads-serp-sitelink-desc"><?= e($sl['description'] ?? '') ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($callouts)): ?>
                <div class="gads-serp-callouts">
                    <?= e(implode(' · ', $callouts)) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- All headlines & descriptions -->
            <div class="gads-serp-all-assets">
                <div>
                    <h5>Tous les titres (<?= count($headlines) ?>/15)</h5>
                    <ol>
                        <?php foreach ($headlines as $i => $h): ?>
                        <li>
                            <?= e($h) ?>
                            <span class="gads-char-count <?= mb_strlen($h) > 30 ? 'gads-over' : '' ?>">
                                <?= mb_strlen($h) ?>/30
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <div>
                    <h5>Toutes les descriptions (<?= count($descriptions) ?>/4)</h5>
                    <ol>
                        <?php foreach ($descriptions as $d): ?>
                        <li>
                            <?= e($d) ?>
                            <span class="gads-char-count <?= mb_strlen($d) > 90 ? 'gads-over' : '' ?>">
                                <?= mb_strlen($d) ?>/90
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if (empty($ad_groups)): ?>
    <div class="gads-empty-state" style="margin-top: 1rem;">
        <i class="fas fa-layer-group"></i>
        <h3>Aucun groupe d'annonces</h3>
        <p>Cette campagne ne contient pas encore de groupes d'annonces.</p>
        <a href="/admin/gads-campaigns/wizard?id=<?= (int) $campaign['id'] ?>" class="btn" style="background: var(--admin-primary); color: #fff;">
            <i class="fas fa-pen"></i> Modifier la campagne
        </a>
    </div>
    <?php endif; ?>
</div>
