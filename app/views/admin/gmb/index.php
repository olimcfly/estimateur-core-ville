<?php
$totalMonth   = (int) ($stats['total_month'] ?? 0);
$published    = (int) ($stats['published'] ?? 0);
$pending      = (int) ($stats['pending'] ?? 0);
$completion   = $totalMonth > 0 ? round(($published / $totalMonth) * 100) : 0;

$statusLabels = [
    'draft'     => ['Brouillon',  '#6b6459', '#f4f1ed'],
    'scheduled' => ['Planifiée',  '#0c5460', '#d1ecf1'],
    'published' => ['Publiée',    '#155724', '#d4edda'],
    'failed'    => ['Échouée',    '#721c24', '#f8d7da'],
];

$typeLabels = [
    'update'  => ['Update',  '#2563eb', '#dbeafe'],
    'event'   => ['Event',   '#7c3aed', '#ede9fe'],
    'offer'   => ['Offer',   '#0d7a3e', '#d4edda'],
    'product' => ['Product', '#d97706', '#fef3c7'],
];

$currentStatus = (string) ($filters['status'] ?? '');
$currentType   = (string) ($filters['type'] ?? '');
$currentMonth  = (int) ($filters['month'] ?? date('n'));
$currentYear   = (int) ($filters['year'] ?? date('Y'));

$settings = $settings ?? [];
$publishDays = $settings['publish_days'] ?? [];
?>

<style>
/* Stats cards */
.gmb-stat-card { background: #fff; border-radius: 8px; padding: 1.25rem; border: 1px solid #e8e8e8; text-align: center; }
.gmb-stat-card .gmb-stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
.gmb-stat-card .gmb-stat-label { font-size: 0.8rem; color: #888; margin-top: 0.25rem; }

/* Type badges */
.gmb-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.gmb-type-update  { background: #dbeafe; color: #2563eb; }
.gmb-type-event   { background: #ede9fe; color: #7c3aed; }
.gmb-type-offer   { background: #d4edda; color: #0d7a3e; }
.gmb-type-product  { background: #fef3c7; color: #d97706; }

/* Status badges */
.gmb-status-draft     { background: #f4f1ed; color: #6b6459; }
.gmb-status-scheduled { background: #d1ecf1; color: #0c5460; }
.gmb-status-published { background: #d4edda; color: #155724; }
.gmb-status-failed    { background: #f8d7da; color: #721c24; }

/* View toggle */
.gmb-view-toggle { display: inline-flex; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }
.gmb-view-toggle button { padding: 0.4rem 1rem; border: none; background: #fff; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #666; transition: all 0.2s; }
.gmb-view-toggle button.active { background: #8B1538; color: #fff; }
.gmb-view-toggle button:hover:not(.active) { background: #f5f5f5; }

/* Calendar */
.gmb-calendar { width: 100%; border-collapse: collapse; }
.gmb-calendar th { padding: 0.5rem; text-align: center; font-size: 0.8rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
.gmb-calendar td { border: 1px solid #e8e8e8; vertical-align: top; height: 100px; width: 14.28%; padding: 0.25rem; }
.gmb-calendar td.gmb-today { background: #fdf8f0; }
.gmb-calendar td.gmb-other-month { background: #fafafa; color: #ccc; }
.gmb-calendar td.gmb-recommended { border-left: 3px solid #D4AF37; }
.gmb-calendar td.gmb-missing { background: #fff5f5; }
.gmb-calendar .gmb-day-number { font-size: 0.8rem; font-weight: 600; color: #555; padding: 2px 4px; }
.gmb-calendar .gmb-day-posts { display: flex; flex-direction: column; gap: 2px; margin-top: 2px; }
.gmb-calendar .gmb-day-post { font-size: 0.7rem; padding: 2px 5px; border-radius: 3px; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
.gmb-calendar .gmb-day-post:hover { opacity: 0.8; }
.gmb-calendar td.gmb-clickable { cursor: pointer; }
.gmb-calendar td.gmb-clickable:hover { background: #f8f5ff; }

/* Calendar nav */
.gmb-cal-nav { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem; }
.gmb-cal-nav button { background: none; border: 1px solid #ddd; border-radius: 4px; padding: 0.3rem 0.75rem; cursor: pointer; font-size: 0.9rem; }
.gmb-cal-nav button:hover { background: #f5f5f5; }
.gmb-cal-nav .gmb-cal-title { font-size: 1.1rem; font-weight: 700; min-width: 180px; text-align: center; }

/* Progress bar */
.gmb-progress { background: #e8e8e8; border-radius: 4px; height: 8px; overflow: hidden; margin-top: 0.5rem; }
.gmb-progress-bar { height: 100%; border-radius: 4px; transition: width 0.3s; }

/* List view table */
.gmb-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.gmb-table th { padding: 0.65rem 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e8e8e8; }
.gmb-table td { padding: 0.65rem 0.75rem; border-bottom: 1px solid #f0f0f0; }
.gmb-table tr:hover { background: #fafafa; }
.gmb-table .gmb-actions { display: flex; gap: 0.25rem; flex-wrap: wrap; }

/* Filter bar */
.gmb-filters { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
.gmb-filters select { padding: 0.4rem 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; }

/* Modal */
.gmb-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.gmb-modal-overlay.active { display: flex; }
.gmb-modal { background: #fff; border-radius: 10px; padding: 2rem; max-width: 550px; width: 95%; max-height: 90vh; overflow-y: auto; }
.gmb-modal h2 { margin: 0 0 1.5rem; font-size: 1.2rem; font-family: 'Playfair Display', serif; }
.gmb-modal label { display: block; margin-bottom: 1rem; font-size: 0.85rem; font-weight: 600; color: #555; }
.gmb-modal label input,
.gmb-modal label select,
.gmb-modal label textarea { display: block; width: 100%; margin-top: 0.25rem; padding: 0.5rem 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; box-sizing: border-box; }
.gmb-modal .gmb-checkbox-group { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem; }
.gmb-modal .gmb-checkbox-group label { display: flex; align-items: center; gap: 0.3rem; font-weight: 500; margin-bottom: 0; }
.gmb-modal .gmb-checkbox-group input[type="checkbox"] { width: auto; display: inline; margin: 0; }

/* Day detail popover */
.gmb-day-detail { display: none; position: fixed; z-index: 500; background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 1rem; min-width: 260px; max-width: 340px; }
.gmb-day-detail.active { display: block; }
.gmb-day-detail h4 { margin: 0 0 0.5rem; font-size: 0.95rem; }
.gmb-day-detail .gmb-day-detail-list { list-style: none; padding: 0; margin: 0; }
.gmb-day-detail .gmb-day-detail-list li { padding: 0.4rem 0; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; }
.gmb-day-detail .gmb-day-detail-list li:last-child { border-bottom: none; }

/* Pagination */
.gmb-pagination { display: flex; justify-content: center; gap: 0.25rem; margin-top: 1rem; }
.gmb-pagination a, .gmb-pagination span { padding: 0.4rem 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; text-decoration: none; color: #555; }
.gmb-pagination span.current { background: #8B1538; color: #fff; border-color: #8B1538; }
.gmb-pagination a:hover { background: #f5f5f5; }
</style>

<div class="container">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">
                📌 Publications Google My Business
            </h1>
            <p style="margin: 0; color: #888; font-size: 0.85rem;">Gérez et planifiez vos publications GMB pour votre fiche Google</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/gmb/create" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">
                + Nouvelle publication
            </a>
            <a href="/admin/gmb/guide" class="btn btn-ghost">Guide GMB</a>
            <button onclick="GMB.openSettings()" class="btn btn-ghost">Paramètres</button>
        </div>
    </div>

    <?php if (!empty($message)): ?><p class="success"><?= e($message) ?></p><?php endif; ?>
    <?php if (!empty($error)): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="gmb-stat-card">
            <div class="gmb-stat-value" style="color: #8B1538;"><?= $totalMonth ?></div>
            <div class="gmb-stat-label">Publications ce mois</div>
        </div>
        <div class="gmb-stat-card">
            <div class="gmb-stat-value" style="color: #0d7a3e;"><?= $published ?></div>
            <div class="gmb-stat-label">Publiées</div>
        </div>
        <div class="gmb-stat-card">
            <div class="gmb-stat-value" style="color: #d97706;"><?= $pending ?></div>
            <div class="gmb-stat-label">En attente</div>
        </div>
        <div class="gmb-stat-card">
            <?php $compColor = $completion >= 80 ? '#0d7a3e' : ($completion >= 50 ? '#D4AF37' : '#c0392b'); ?>
            <div class="gmb-stat-value" style="color: <?= $compColor ?>;"><?= $completion ?>%</div>
            <div class="gmb-stat-label">Taux de complétion</div>
            <div class="gmb-progress">
                <div class="gmb-progress-bar" style="width: <?= $completion ?>%; background: <?= $compColor ?>;"></div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
            <h2 style="margin: 0; font-size: 1.1rem;">Publications</h2>
            <div class="gmb-view-toggle">
                <button id="btnViewCalendar" class="active" onclick="GMB.switchView('calendar')">Calendrier</button>
                <button id="btnViewList" onclick="GMB.switchView('list')">Liste</button>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="gmbViewCalendar">
            <div class="gmb-cal-nav">
                <button onclick="GMB.prevMonth()" title="Mois précédent">&laquo; Préc.</button>
                <div class="gmb-cal-title" id="gmbCalTitle"></div>
                <button onclick="GMB.nextMonth()" title="Mois suivant">Suiv. &raquo;</button>
            </div>
            <table class="gmb-calendar">
                <thead>
                    <tr>
                        <th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th>
                    </tr>
                </thead>
                <tbody id="gmbCalBody"></tbody>
            </table>

            <!-- Legend -->
            <div style="margin-top: 0.75rem; display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.8rem; color: #888;">
                <span><span class="gmb-badge gmb-type-update">Update</span></span>
                <span><span class="gmb-badge gmb-type-event">Event</span></span>
                <span><span class="gmb-badge gmb-type-offer">Offer</span></span>
                <span><span class="gmb-badge gmb-type-product">Product</span></span>
                <span style="margin-left: auto; display: flex; align-items: center; gap: 0.3rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; border-left: 3px solid #D4AF37;"></span>
                    Jour recommandé
                </span>
                <span style="display: flex; align-items: center; gap: 0.3rem;">
                    <span style="display: inline-block; width: 12px; height: 12px; background: #fff5f5; border: 1px solid #e8e8e8;"></span>
                    Publication manquante
                </span>
            </div>
        </div>

        <!-- List View (hidden by default) -->
        <div id="gmbViewList" style="display: none;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                <div class="gmb-filters">
                    <select id="gmbFilterStatus" onchange="GMB.applyFilters()">
                        <option value="">Tous les statuts</option>
                        <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="scheduled" <?= $currentStatus === 'scheduled' ? 'selected' : '' ?>>Planifiée</option>
                        <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>Publiée</option>
                        <option value="failed" <?= $currentStatus === 'failed' ? 'selected' : '' ?>>Échouée</option>
                    </select>
                    <select id="gmbFilterType" onchange="GMB.applyFilters()">
                        <option value="">Tous les types</option>
                        <option value="update" <?= $currentType === 'update' ? 'selected' : '' ?>>Update</option>
                        <option value="event" <?= $currentType === 'event' ? 'selected' : '' ?>>Event</option>
                        <option value="offer" <?= $currentType === 'offer' ? 'selected' : '' ?>>Offer</option>
                        <option value="product" <?= $currentType === 'product' ? 'selected' : '' ?>>Product</option>
                    </select>
                    <select id="gmbFilterMonth" onchange="GMB.applyFilters()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $currentMonth === $m ? 'selected' : '' ?>><?= strftime_compat($m) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="table-wrap">
                <table class="gmb-table" id="gmbTable">
                    <thead>
                        <tr>
                            <th>Date planifiée</th>
                            <th>Type</th>
                            <th>Titre / Extrait</th>
                            <th>Article lié</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gmbTableBody">
                        <?php if (!empty($publications)): ?>
                        <?php foreach ($publications as $pub):
                            $type = (string) ($pub['post_type'] ?? 'update');
                            $status = (string) ($pub['status'] ?? 'draft');
                            $st = $statusLabels[$status] ?? ['?', '#999', '#eee'];
                            $tp = $typeLabels[$type] ?? ['?', '#999', '#eee'];
                        ?>
                        <tr data-status="<?= e($status) ?>" data-type="<?= e($type) ?>">
                            <td><?= date('d/m/Y H:i', strtotime($pub['scheduled_at'])) ?></td>
                            <td><span class="gmb-badge gmb-type-<?= e($type) ?>"><?= e($tp[0]) ?></span></td>
                            <td>
                                <a href="/admin/gmb/edit/<?= (int) $pub['id'] ?>" style="color: #8B1538; font-weight: 600; text-decoration: none;">
                                    <?= e(mb_strimwidth((string) ($pub['title'] ?? $pub['content'] ?? ''), 0, 60, '...')) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($pub['article_id'])): ?>
                                    <a href="/admin/blog/edit/<?= (int) $pub['article_id'] ?>" style="font-size: 0.8rem; color: #555;">
                                        <?= e(mb_strimwidth((string) ($pub['article_title'] ?? '#' . $pub['article_id']), 0, 40, '...')) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #ccc;">--</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="gmb-badge" style="color: <?= $st[1] ?>; background: <?= $st[2] ?>;"><?= e($st[0]) ?></span></td>
                            <td>
                                <div class="gmb-actions">
                                    <a href="/admin/gmb/edit/<?= (int) $pub['id'] ?>" class="btn btn-small btn-ghost" title="Voir">Voir</a>
                                    <a href="/admin/gmb/edit/<?= (int) $pub['id'] ?>" class="btn btn-small btn-ghost" title="Éditer">Éditer</a>
                                    <form method="post" action="/admin/gmb/duplicate/<?= (int) $pub['id'] ?>" style="display:inline;">
                                        <button type="submit" class="btn btn-small btn-ghost" title="Dupliquer">Dupliquer</button>
                                    </form>
                                    <?php if ($status !== 'published'): ?>
                                    <form method="post" action="/admin/gmb/mark-published/<?= (int) $pub['id'] ?>" style="display:inline;">
                                        <button type="submit" class="btn btn-small" style="background: #0d7a3e; color: #fff; font-size: 0.75rem;" title="Marquer publié">Publié</button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="post" action="/admin/gmb/delete/<?= (int) $pub['id'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette publication ?');">
                                        <button type="submit" class="btn btn-small" style="font-size: 0.75rem;" title="Supprimer">Suppr.</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: #888;">Aucune publication trouvée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="gmb-pagination">
                <?php if ($pagination['current'] > 1): ?>
                    <a href="?page=<?= $pagination['current'] - 1 ?>&status=<?= e($currentStatus) ?>&type=<?= e($currentType) ?>&month=<?= $currentMonth ?>">&laquo;</a>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                    <?php if ($p === $pagination['current']): ?>
                        <span class="current"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>&status=<?= e($currentStatus) ?>&type=<?= e($currentType) ?>&month=<?= $currentMonth ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($pagination['current'] < $pagination['total_pages']): ?>
                    <a href="?page=<?= $pagination['current'] + 1 ?>&status=<?= e($currentStatus) ?>&type=<?= e($currentType) ?>&month=<?= $currentMonth ?>">&raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Empty state -->
    <?php if ($totalMonth === 0 && empty($publications)): ?>
    <section class="card" style="text-align: center; padding: 3rem;">
        <p style="color: #888; font-size: 1.1rem; margin: 0 0 1rem;">Aucune publication GMB. Commencez par en créer une !</p>
        <a href="/admin/gmb/create" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">+ Nouvelle publication</a>
    </section>
    <?php endif; ?>
</div>

<!-- Day Detail Popover -->
<div class="gmb-day-detail" id="gmbDayDetail">
    <h4 id="gmbDayDetailTitle"></h4>
    <ul class="gmb-day-detail-list" id="gmbDayDetailList"></ul>
</div>

<!-- Settings Modal -->
<div class="gmb-modal-overlay" id="gmbSettingsModal">
    <div class="gmb-modal">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Paramètres GMB</h2>
            <button onclick="GMB.closeSettings()" style="background: none; border: none; font-size: 1.3rem; cursor: pointer; color: #888;">&times;</button>
        </div>
        <form id="gmbSettingsForm" onsubmit="GMB.saveSettings(event)">
            <label>
                Email de notification
                <input type="email" name="notification_email" value="<?= e((string) ($settings['notification_email'] ?? '')) ?>" placeholder="email@exemple.com">
            </label>
            <label>
                Heure d'envoi des notifications
                <select name="notification_hour">
                    <?php for ($h = 6; $h <= 12; $h++): ?>
                    <option value="<?= $h ?>" <?= ((int) ($settings['notification_hour'] ?? 8)) === $h ? 'selected' : '' ?>><?= $h ?>h00</option>
                    <?php endfor; ?>
                </select>
            </label>
            <label>
                Jours de publication
                <div class="gmb-checkbox-group">
                    <?php
                    $dayNames = ['lun' => 'Lun', 'mar' => 'Mar', 'mer' => 'Mer', 'jeu' => 'Jeu', 'ven' => 'Ven', 'sam' => 'Sam', 'dim' => 'Dim'];
                    foreach ($dayNames as $key => $label):
                    ?>
                    <label>
                        <input type="checkbox" name="publish_days[]" value="<?= $key ?>" <?= in_array($key, $publishDays) ? 'checked' : '' ?>>
                        <?= $label ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </label>
            <label>
                URL de la fiche Google My Business
                <input type="url" name="gmb_url" value="<?= e((string) ($settings['gmb_url'] ?? '')) ?>" placeholder="https://business.google.com/...">
            </label>
            <label>
                CTA par défaut
                <select name="default_cta">
                    <?php
                    $ctaOptions = ['LEARN_MORE' => 'En savoir plus', 'BOOK' => 'Réserver', 'ORDER' => 'Commander', 'SHOP' => 'Acheter', 'SIGN_UP' => "S'inscrire", 'CALL' => 'Appeler'];
                    foreach ($ctaOptions as $val => $lbl):
                    ?>
                    <option value="<?= $val ?>" <?= ((string) ($settings['default_cta'] ?? '')) === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                URL CTA par défaut
                <input type="url" name="default_cta_url" value="<?= e((string) ($settings['default_cta_url'] ?? '')) ?>" placeholder="https://votresite.com">
            </label>
            <label>
                Auto-génération activée
                <select name="auto_generate">
                    <option value="1" <?= !empty($settings['auto_generate']) ? 'selected' : '' ?>>Oui</option>
                    <option value="0" <?= empty($settings['auto_generate']) ? 'selected' : '' ?>>Non</option>
                </select>
            </label>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                <button type="button" onclick="GMB.closeSettings()" class="btn btn-ghost">Annuler</button>
                <button type="submit" class="btn" style="background: #8B1538; color: #fff;">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>

<script>
const GMB = (() => {
    const MONTHS_FR = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    const DAY_MAP = { 0: 'dim', 1: 'lun', 2: 'mar', 3: 'mer', 4: 'jeu', 5: 'ven', 6: 'sam' };
    const TYPE_CLASSES = { update: 'gmb-type-update', event: 'gmb-type-event', offer: 'gmb-type-offer', product: 'gmb-type-product' };

    let calMonth = <?= $currentMonth ?>;
    let calYear = <?= $currentYear ?>;
    let calData = {};
    const publishDays = <?= json_encode($publishDays) ?>;

    function switchView(view) {
        const calEl = document.getElementById('gmbViewCalendar');
        const listEl = document.getElementById('gmbViewList');
        const btnCal = document.getElementById('btnViewCalendar');
        const btnList = document.getElementById('btnViewList');
        if (view === 'calendar') {
            calEl.style.display = '';
            listEl.style.display = 'none';
            btnCal.classList.add('active');
            btnList.classList.remove('active');
        } else {
            calEl.style.display = 'none';
            listEl.style.display = '';
            btnCal.classList.remove('active');
            btnList.classList.add('active');
        }
        closeDayDetail();
    }

    function prevMonth() {
        calMonth--;
        if (calMonth < 1) { calMonth = 12; calYear--; }
        loadCalendar();
    }

    function nextMonth() {
        calMonth++;
        if (calMonth > 12) { calMonth = 1; calYear++; }
        loadCalendar();
    }

    function loadCalendar() {
        document.getElementById('gmbCalTitle').textContent = MONTHS_FR[calMonth - 1] + ' ' + calYear;

        fetch('/admin/gmb/api/calendar/' + calMonth + '/' + calYear)
            .then(r => r.json())
            .then(data => {
                calData = data.posts || {};
                renderCalendar();
            })
            .catch(() => {
                calData = {};
                renderCalendar();
            });
    }

    function renderCalendar() {
        const body = document.getElementById('gmbCalBody');
        body.innerHTML = '';

        const firstDay = new Date(calYear, calMonth - 1, 1);
        const lastDay = new Date(calYear, calMonth, 0);
        const daysInMonth = lastDay.getDate();

        // Monday-based: 0=Mon ... 6=Sun
        let startDay = firstDay.getDay() - 1;
        if (startDay < 0) startDay = 6;

        const today = new Date();
        const isCurrentMonth = (today.getFullYear() === calYear && today.getMonth() + 1 === calMonth);

        let html = '<tr>';
        // Empty cells before first day
        for (let i = 0; i < startDay; i++) {
            html += '<td class="gmb-other-month"></td>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(calYear, calMonth - 1, day);
            const dayKey = DAY_MAP[date.getDay()];
            const isRecommended = publishDays.includes(dayKey);
            const dayPosts = calData[day] || [];
            const isToday = isCurrentMonth && today.getDate() === day;
            const isMissing = isRecommended && dayPosts.length === 0 && (date <= today || !isCurrentMonth);

            let classes = [];
            if (isToday) classes.push('gmb-today');
            if (isRecommended) classes.push('gmb-recommended');
            if (isMissing) classes.push('gmb-missing');
            if (dayPosts.length > 0) classes.push('gmb-clickable');

            html += '<td class="' + classes.join(' ') + '" data-day="' + day + '"' +
                    (dayPosts.length > 0 ? ' onclick="GMB.showDayDetail(event, ' + day + ')"' : '') + '>';
            html += '<div class="gmb-day-number">' + day + '</div>';
            html += '<div class="gmb-day-posts">';
            dayPosts.forEach(function(post) {
                const cls = TYPE_CLASSES[post.type] || 'gmb-type-update';
                const title = (post.title || '').substring(0, 20) || post.type;
                html += '<div class="gmb-day-post ' + cls + '" title="' + escHtml(post.title || '') + '">' + escHtml(title) + '</div>';
            });
            html += '</div></td>';

            if ((startDay + day) % 7 === 0) {
                html += '</tr>';
                if (day < daysInMonth) html += '<tr>';
            }
        }

        // Fill remaining cells
        const remaining = (startDay + daysInMonth) % 7;
        if (remaining > 0) {
            for (let i = remaining; i < 7; i++) {
                html += '<td class="gmb-other-month"></td>';
            }
            html += '</tr>';
        }

        body.innerHTML = html;
    }

    function showDayDetail(event, day) {
        event.stopPropagation();
        const posts = calData[day] || [];
        if (posts.length === 0) return;

        const detail = document.getElementById('gmbDayDetail');
        const title = document.getElementById('gmbDayDetailTitle');
        const list = document.getElementById('gmbDayDetailList');

        title.textContent = day + ' ' + MONTHS_FR[calMonth - 1] + ' ' + calYear;
        let html = '';
        posts.forEach(function(post) {
            const cls = TYPE_CLASSES[post.type] || 'gmb-type-update';
            html += '<li>';
            html += '<span class="gmb-badge ' + cls + '">' + escHtml(post.type) + '</span>';
            html += '<a href="/admin/gmb/edit/' + post.id + '" style="color: #8B1538; text-decoration: none; font-weight: 600;">' + escHtml(post.title || 'Sans titre') + '</a>';
            html += '</li>';
        });
        list.innerHTML = html;

        // Position near the clicked cell
        const rect = event.currentTarget.getBoundingClientRect();
        detail.style.top = (rect.bottom + window.scrollY + 5) + 'px';
        detail.style.left = Math.min(rect.left, window.innerWidth - 350) + 'px';
        detail.classList.add('active');
    }

    function closeDayDetail() {
        document.getElementById('gmbDayDetail').classList.remove('active');
    }

    function openSettings() {
        document.getElementById('gmbSettingsModal').classList.add('active');
    }

    function closeSettings() {
        document.getElementById('gmbSettingsModal').classList.remove('active');
    }

    function saveSettings(e) {
        e.preventDefault();
        const form = document.getElementById('gmbSettingsForm');
        const formData = new FormData(form);

        fetch('/admin/gmb/save-settings', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeSettings();
                location.reload();
            } else {
                alert(data.error || 'Erreur lors de la sauvegarde.');
            }
        })
        .catch(() => alert('Erreur réseau. Réessayez.'));
    }

    function applyFilters() {
        const status = document.getElementById('gmbFilterStatus').value;
        const type = document.getElementById('gmbFilterType').value;
        const month = document.getElementById('gmbFilterMonth').value;
        window.location.href = '/admin/gmb?status=' + encodeURIComponent(status) +
            '&type=' + encodeURIComponent(type) +
            '&month=' + encodeURIComponent(month);
    }

    function escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Close day detail on outside click
    document.addEventListener('click', function(e) {
        const detail = document.getElementById('gmbDayDetail');
        if (detail.classList.contains('active') && !detail.contains(e.target)) {
            closeDayDetail();
        }
    });

    // Close modal on overlay click
    document.getElementById('gmbSettingsModal').addEventListener('click', function(e) {
        if (e.target === this) closeSettings();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSettings();
            closeDayDetail();
        }
    });

    // Init calendar on load
    loadCalendar();

    return { switchView, prevMonth, nextMonth, loadCalendar, showDayDetail, openSettings, closeSettings, saveSettings, applyFilters };
})();
</script>
