<?php
/** @var array $publications */
/** @var array $stats */
/** @var array $calendarData */
/** @var array $settings */
/** @var int $month */
/** @var int $year */
/** @var string $message */
/** @var string $error */

$statusLabels = [
    'draft' => ['label' => 'Brouillon', 'class' => 'secondary'],
    'scheduled' => ['label' => 'Planifie', 'class' => 'warning'],
    'notified' => ['label' => 'Notifie', 'class' => 'info'],
    'published' => ['label' => 'Publie', 'class' => 'success'],
    'expired' => ['label' => 'Expire', 'class' => 'danger'],
];

$postTypeLabels = [
    'update' => 'Nouveaute',
    'event' => 'Evenement',
    'offer' => 'Offre',
    'product' => 'Produit',
];

$monthNames = [
    1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril',
    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Aout',
    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre',
];
?>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<!-- Actions bar -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <a href="/admin/gmb/create" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle publication</a>
        <form method="post" action="/admin/gmb/generate" class="d-inline">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-magic"></i> Generer depuis contenu</button>
        </form>
        <a href="/admin/gmb/guide" class="btn btn-outline-secondary"><i class="fas fa-book"></i> Guide</a>
    </div>
</div>

<!-- Stats cards -->
<?php if (!empty($stats)): ?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="fs-3 fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
            <div class="text-muted small">Ce mois</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="fs-3 fw-bold text-success"><?= $stats['published'] ?? 0 ?></div>
            <div class="text-muted small">Publiees</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="fs-3 fw-bold text-warning"><?= $stats['pending'] ?? 0 ?></div>
            <div class="text-muted small">En attente</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center p-3">
            <div class="fs-3 fw-bold text-info"><?= $stats['completion_rate'] ?? 0 ?>%</div>
            <div class="text-muted small">Taux completion</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Calendar -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Calendrier - <?= $monthNames[$month] ?? $month ?> <?= $year ?></h5>
        <div class="d-flex gap-2">
            <?php
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
            ?>
            <a href="/admin/gmb?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-sm btn-outline-secondary">&laquo; Precedent</a>
            <a href="/admin/gmb?month=<?= (int) date('n') ?>&year=<?= (int) date('Y') ?>" class="btn btn-sm btn-outline-primary">Aujourd'hui</a>
            <a href="/admin/gmb?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-sm btn-outline-secondary">Suivant &raquo;</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0" style="table-layout: fixed;">
            <thead>
                <tr class="text-center bg-light">
                    <th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th><th>Dim</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $firstDay = mktime(0, 0, 0, $month, 1, $year);
                $daysInMonth = (int) date('t', $firstDay);
                $startDow = ((int) date('N', $firstDay)); // 1=Monday
                $today = (int) date('j');
                $todayMonth = (int) date('n');
                $todayYear = (int) date('Y');
                $day = 1;
                $started = false;

                for ($week = 0; $week < 6 && $day <= $daysInMonth; $week++):
                ?>
                <tr>
                    <?php for ($dow = 1; $dow <= 7; $dow++):
                        if (!$started && $dow === $startDow) {
                            $started = true;
                        }
                        if (!$started || $day > $daysInMonth):
                    ?>
                        <td class="bg-light" style="height: 80px;"></td>
                    <?php else:
                        $isToday = ($day === $today && $month === $todayMonth && $year === $todayYear);
                        $dayPubs = $calendarData[$day] ?? [];
                    ?>
                        <td style="height: 80px; vertical-align: top; <?= $isToday ? 'background: #e8f0fe;' : '' ?>">
                            <div class="fw-bold small <?= $isToday ? 'text-primary' : 'text-muted' ?>"><?= $day ?></div>
                            <?php foreach ($dayPubs as $pub):
                                $st = $statusLabels[$pub['status']] ?? ['label' => $pub['status'], 'class' => 'secondary'];
                            ?>
                                <a href="/admin/gmb/edit/<?= $pub['id'] ?>" class="d-block text-decoration-none mb-1" title="<?= e($pub['content_preview'] ?? '') ?>">
                                    <span class="badge bg-<?= $st['class'] ?> text-truncate d-block" style="max-width: 100%; font-size: 0.7em;">
                                        <?= e(mb_substr($pub['title'] ?? $pub['content_preview'] ?? 'Pub', 0, 20)) ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </td>
                    <?php $day++; endif; ?>
                    <?php endfor; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Publications list -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> Publications</h5>
        <div class="d-flex gap-2">
            <a href="/admin/gmb" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : 'btn-outline-secondary' ?>">Toutes</a>
            <a href="/admin/gmb?status=draft" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'draft' ? 'btn-primary' : 'btn-outline-secondary' ?>">Brouillons</a>
            <a href="/admin/gmb?status=scheduled" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'scheduled' ? 'btn-primary' : 'btn-outline-secondary' ?>">Planifiees</a>
            <a href="/admin/gmb?status=published" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'published' ? 'btn-primary' : 'btn-outline-secondary' ?>">Publiees</a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($publications)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-store fa-3x mb-3 d-block opacity-25"></i>
                <p>Aucune publication GMB. <a href="/admin/gmb/create">Creer la premiere</a>.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Contenu</th>
                            <th>Source</th>
                            <th>CTA</th>
                            <th>Planifie</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publications as $pub):
                            $st = $statusLabels[$pub['status']] ?? ['label' => $pub['status'], 'class' => 'secondary'];
                        ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= e($postTypeLabels[$pub['post_type']] ?? $pub['post_type']) ?></span></td>
                            <td>
                                <div class="fw-semibold"><?= e($pub['title'] ?? '') ?></div>
                                <div class="text-muted small"><?= e($pub['content_preview'] ?? '') ?></div>
                            </td>
                            <td class="small text-muted">
                                <?php if (!empty($pub['article_title'])): ?>
                                    <i class="fas fa-pen-fancy"></i> <?= e(mb_substr($pub['article_title'], 0, 30)) ?>
                                <?php elseif (!empty($pub['actualite_title'])): ?>
                                    <i class="fas fa-newspaper"></i> <?= e(mb_substr($pub['actualite_title'], 0, 30)) ?>
                                <?php else: ?>
                                    <span class="text-muted">Manuel</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= e($pub['cta_type'] ?? '-') ?></td>
                            <td class="small"><?= $pub['scheduled_at'] ? date('d/m/Y H:i', strtotime($pub['scheduled_at'])) : '-' ?></td>
                            <td><span class="badge bg-<?= $st['class'] ?>"><?= $st['label'] ?></span></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/gmb/preview/<?= $pub['id'] ?>" class="btn btn-outline-info" title="Preview"><i class="fas fa-eye"></i></a>
                                    <a href="/admin/gmb/edit/<?= $pub['id'] ?>" class="btn btn-outline-primary" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <?php if ($pub['status'] !== 'published'): ?>
                                    <form method="post" action="/admin/gmb/publish/<?= $pub['id'] ?>" class="d-inline" onsubmit="return confirm('Marquer comme publiee ?')">
                                        <button type="submit" class="btn btn-outline-success" title="Marquer publiee"><i class="fas fa-check"></i></button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="post" action="/admin/gmb/delete/<?= $pub['id'] ?>" class="d-inline" onsubmit="return confirm('Supprimer cette publication ?')">
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer"><i class="fas fa-trash"></i></button>
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

<!-- Settings -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cog"></i> Parametres GMB</h5>
    </div>
    <div class="card-body">
        <form method="post" action="/admin/gmb/settings">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">URL fiche Google My Business</label>
                    <input type="url" name="gmb_profile_url" class="form-control" value="<?= e($settings['gmb_profile_url'] ?? '') ?>" placeholder="https://business.google.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email de notification</label>
                    <input type="email" name="notification_email" class="form-control" value="<?= e($settings['notification_email'] ?? '') ?>" placeholder="votre@email.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">CTA par defaut</label>
                    <select name="default_cta_type" class="form-select">
                        <?php
                        $ctaOptions = ['learn_more' => 'En savoir plus', 'book' => 'Reserver', 'call_now' => 'Appeler', 'sign_up' => 'S\'inscrire', 'get_offer' => 'Obtenir offre', 'order_online' => 'Commander', 'buy' => 'Acheter'];
                        foreach ($ctaOptions as $val => $label):
                        ?>
                        <option value="<?= $val ?>" <?= ($settings['default_cta_type'] ?? 'learn_more') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">URL CTA par defaut</label>
                    <input type="url" name="default_cta_url" class="form-control" value="<?= e($settings['default_cta_url'] ?? '') ?>" placeholder="https://...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Heure de notification</label>
                    <select name="notification_hour" class="form-select">
                        <?php for ($h = 6; $h <= 20; $h++): ?>
                        <option value="<?= $h ?>" <?= ((int) ($settings['notification_hour'] ?? 8)) === $h ? 'selected' : '' ?>><?= $h ?>h00</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jours de publication (1=Lun ... 7=Dim)</label>
                    <input type="text" name="posting_days" class="form-control" value="<?= e($settings['posting_days'] ?? '1,3,5') ?>" placeholder="1,3,5">
                    <div class="form-text">Separez par des virgules. Ex: 1,3,5 = Lundi, Mercredi, Vendredi</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Generation automatique</label>
                    <select name="auto_generate" class="form-select">
                        <option value="1" <?= ($settings['auto_generate'] ?? '1') === '1' ? 'selected' : '' ?>>Oui - Generer automatiquement depuis les articles</option>
                        <option value="0" <?= ($settings['auto_generate'] ?? '1') === '0' ? 'selected' : '' ?>>Non - Creation manuelle uniquement</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Sauvegarder les parametres</button>
            </div>
        </form>
    </div>
</div>
