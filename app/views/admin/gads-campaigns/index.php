<?php
$total    = (int) ($stats['total'] ?? 0);
$drafts   = (int) ($stats['drafts'] ?? 0);
$ready    = (int) ($stats['ready'] ?? 0);
$exported = (int) ($stats['exported'] ?? 0);
$live     = (int) ($stats['live'] ?? 0);

$statusLabels = [
    'draft'    => ['Brouillon',  '#6b6459', '#f4f1ed'],
    'ready'    => ['Prêt',       '#0d7a3e', '#d4edda'],
    'exported' => ['Exporté',    '#0c5460', '#d1ecf1'],
    'active'   => ['Actif',      '#155724', '#d4edda'],
    'paused'   => ['En pause',   '#856404', '#fff3cd'],
    'archived' => ['Archivé',    '#6b6459', '#e8e8e8'],
];
?>

<link rel="stylesheet" href="/assets/css/google-ads.css">

<div class="container">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">
                <i class="fas fa-bullhorn" style="color: var(--admin-primary);"></i> Campagnes Google Ads
            </h1>
            <p style="margin: 0; color: #888; font-size: 0.85rem;">Créez, gérez et exportez vos campagnes Search Ads avec génération IA</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/gads-campaigns/wizard" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">
                <i class="fas fa-magic"></i> Nouvelle campagne
            </a>
            <a href="/admin/google-ads" class="btn btn-ghost">
                <i class="fas fa-book"></i> Guide Google Ads
            </a>
        </div>
    </div>

    <?php if ($message !== ''): ?><p class="success"><i class="fas fa-check-circle"></i> <?= e($message) ?></p><?php endif; ?>
    <?php if ($error !== ''): ?><p class="alert"><i class="fas fa-exclamation-triangle"></i> <?= e($error) ?></p><?php endif; ?>

    <!-- Dashboard Stats -->
    <div class="gads-stats-grid">
        <a href="/admin/gads-campaigns" class="gads-stat-card <?= $current_status === '' ? 'active' : '' ?>">
            <div class="gads-stat-value" style="color: var(--admin-primary);"><?= $total ?></div>
            <div class="gads-stat-label">Total</div>
        </a>
        <a href="/admin/gads-campaigns?status=draft" class="gads-stat-card <?= $current_status === 'draft' ? 'active' : '' ?>">
            <div class="gads-stat-value" style="color: #6b6459;"><?= $drafts ?></div>
            <div class="gads-stat-label">Brouillons</div>
        </a>
        <a href="/admin/gads-campaigns?status=ready" class="gads-stat-card <?= $current_status === 'ready' ? 'active' : '' ?>">
            <div class="gads-stat-value" style="color: #0d7a3e;"><?= $ready ?></div>
            <div class="gads-stat-label">Prêts</div>
        </a>
        <a href="/admin/gads-campaigns?status=exported" class="gads-stat-card <?= $current_status === 'exported' ? 'active' : '' ?>">
            <div class="gads-stat-value" style="color: #0c5460;"><?= $exported ?></div>
            <div class="gads-stat-label">Exportés</div>
        </a>
        <a href="/admin/gads-campaigns?status=active" class="gads-stat-card <?= $current_status === 'active' ? 'active' : '' ?>">
            <div class="gads-stat-value" style="color: #155724;"><?= $live ?></div>
            <div class="gads-stat-label">Live</div>
        </a>
    </div>

    <!-- Campaign List -->
    <?php if (empty($campaigns)): ?>
        <div class="gads-empty-state">
            <i class="fas fa-bullhorn"></i>
            <h3>Aucune campagne</h3>
            <p>Créez votre première campagne Google Ads avec l'assistant IA.</p>
            <a href="/admin/gads-campaigns/wizard" class="btn" style="background: var(--admin-primary); color: #fff;">
                <i class="fas fa-magic"></i> Créer une campagne
            </a>
        </div>
    <?php else: ?>
        <div class="gads-campaign-list">
            <?php foreach ($campaigns as $c):
                $st = $statusLabels[$c['status']] ?? ['?', '#999', '#eee'];
            ?>
            <div class="gads-campaign-card" data-id="<?= (int) $c['id'] ?>">
                <div class="gads-campaign-header">
                    <div>
                        <h3 class="gads-campaign-name">
                            <a href="/admin/gads-campaigns/wizard?id=<?= (int) $c['id'] ?>"><?= e($c['name']) ?></a>
                        </h3>
                        <div class="gads-campaign-meta">
                            <span class="gads-badge" style="color: <?= $st[1] ?>; background: <?= $st[2] ?>;"><?= $st[0] ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?= e($c['target_location']) ?></span>
                            <span><i class="fas fa-layer-group"></i> <?= (int) $c['ad_group_count'] ?> groupe(s)</span>
                            <span><i class="fas fa-ad"></i> <?= (int) $c['ad_count'] ?> annonce(s)</span>
                            <?php if ((float) $c['daily_budget'] > 0): ?>
                            <span><i class="fas fa-euro-sign"></i> <?= number_format((float) $c['daily_budget'], 2, ',', ' ') ?>/jour</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="gads-campaign-actions">
                        <a href="/admin/gads-campaigns/wizard?id=<?= (int) $c['id'] ?>" class="gads-btn-icon" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="/admin/gads-campaigns/preview?id=<?= (int) $c['id'] ?>" class="gads-btn-icon" title="Aperçu">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/gads-campaigns/export?id=<?= (int) $c['id'] ?>" class="gads-btn-icon" title="Exporter CSV">
                            <i class="fas fa-download"></i>
                        </a>
                        <button onclick="GADS.deleteCampaign(<?= (int) $c['id'] ?>)" class="gads-btn-icon gads-btn-danger" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="gads-campaign-footer">
                    <span class="gads-campaign-date">
                        <i class="fas fa-clock"></i>
                        Modifié le <?= date('d/m/Y H:i', strtotime($c['updated_at'])) ?>
                    </span>
                    <?php if ($c['status'] === 'draft'): ?>
                    <button onclick="GADS.setStatus(<?= (int) $c['id'] ?>, 'ready')" class="gads-btn-sm gads-btn-success">
                        <i class="fas fa-check"></i> Marquer prêt
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="/assets/js/google-ads.js"></script>
