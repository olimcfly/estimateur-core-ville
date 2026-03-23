<?php
/** @var array $publication */

$statusLabels = [
    'draft' => ['label' => 'Brouillon', 'class' => 'secondary'],
    'scheduled' => ['label' => 'Planifie', 'class' => 'warning'],
    'notified' => ['label' => 'Notifie', 'class' => 'info'],
    'published' => ['label' => 'Publie', 'class' => 'success'],
    'expired' => ['label' => 'Expire', 'class' => 'danger'],
];
$st = $statusLabels[$publication['status']] ?? ['label' => $publication['status'], 'class' => 'secondary'];

$ctaLabels = [
    'learn_more' => 'En savoir plus',
    'book' => 'Reserver',
    'call_now' => 'Appeler',
    'sign_up' => 'S\'inscrire',
    'get_offer' => 'Obtenir offre',
    'order_online' => 'Commander',
    'buy' => 'Acheter',
];
?>

<div class="mb-3 d-flex gap-2">
    <a href="/admin/gmb" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
    <a href="/admin/gmb/edit/<?= $publication['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit"></i> Modifier</a>
    <?php if ($publication['status'] !== 'published'): ?>
    <form method="post" action="/admin/gmb/publish/<?= $publication['id'] ?>" class="d-inline">
        <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Marquer comme publiee ?')"><i class="fas fa-check"></i> Marquer publiee</button>
    </form>
    <?php endif; ?>
</div>

<div class="row">
    <!-- Preview GMB -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fab fa-google"></i> Apercu Google My Business</h5>
            </div>
            <div class="card-body">
                <div style="border: 1px solid #dadce0; border-radius: 8px; overflow: hidden; max-width: 400px; margin: 0 auto; font-family: 'Google Sans', Arial, sans-serif;">
                    <?php if (!empty($publication['image_path'])): ?>
                    <div style="background: #f0f0f0; height: 200px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?= e($publication['image_path']) ?>" alt="Image publication" style="max-width: 100%; max-height: 200px; object-fit: cover;">
                    </div>
                    <?php endif; ?>

                    <div style="padding: 16px;">
                        <div class="d-flex align-items-center mb-3">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #4285f4; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; margin-right: 12px; flex-shrink: 0;">E</div>
                            <div>
                                <div style="font-weight: 500; font-size: 14px; color: #202124;">Estimation Immobilier Bordeaux</div>
                                <div style="color: #70757a; font-size: 12px;">
                                    <?= !empty($publication['scheduled_at']) ? date('d M Y', strtotime($publication['scheduled_at'])) : 'A planifier' ?>
                                </div>
                            </div>
                        </div>

                        <div style="font-size: 14px; line-height: 1.5; color: #3c4043; white-space: pre-wrap; word-break: break-word;"><?= e($publication['content'] ?? '') ?></div>

                        <?php if (!empty($publication['cta_type'])): ?>
                        <div class="mt-3">
                            <a href="<?= e($publication['cta_url'] ?? '#') ?>" target="_blank" style="display: inline-block; background: #1a73e8; color: #fff; padding: 8px 24px; border-radius: 4px; text-decoration: none; font-size: 14px; font-weight: 500;">
                                <?= e($ctaLabels[$publication['cta_type']] ?? 'En savoir plus') ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details & Copy -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td class="fw-bold text-muted" style="width:140px;">Statut</td><td><span class="badge bg-<?= $st['class'] ?>"><?= $st['label'] ?></span></td></tr>
                    <tr><td class="fw-bold text-muted">Type</td><td><?= e($publication['post_type'] ?? 'update') ?></td></tr>
                    <?php if (!empty($publication['title'])): ?>
                    <tr><td class="fw-bold text-muted">Titre</td><td><?= e($publication['title']) ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="fw-bold text-muted">Caracteres</td><td><?= mb_strlen($publication['content'] ?? '') ?> / 1500</td></tr>
                    <?php if (!empty($publication['article_title'])): ?>
                    <tr><td class="fw-bold text-muted">Article source</td><td><i class="fas fa-pen-fancy"></i> <?= e($publication['article_title']) ?></td></tr>
                    <?php endif; ?>
                    <?php if (!empty($publication['actualite_title'])): ?>
                    <tr><td class="fw-bold text-muted">Actualite source</td><td><i class="fas fa-newspaper"></i> <?= e($publication['actualite_title']) ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="fw-bold text-muted">Planifie</td><td><?= !empty($publication['scheduled_at']) ? date('d/m/Y H:i', strtotime($publication['scheduled_at'])) : '-' ?></td></tr>
                    <tr><td class="fw-bold text-muted">Publie</td><td><?= !empty($publication['published_at']) ? date('d/m/Y H:i', strtotime($publication['published_at'])) : '-' ?></td></tr>
                    <tr><td class="fw-bold text-muted">Cree</td><td><?= date('d/m/Y H:i', strtotime($publication['created_at'])) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Copy content -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-copy"></i> Copier le contenu</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Cliquez pour copier le texte, puis collez-le dans votre fiche Google My Business.</p>
                <textarea id="copy-content" class="form-control mb-2" rows="6" readonly style="background: #f8f9fa;"><?= e($publication['content'] ?? '') ?></textarea>
                <button type="button" class="btn btn-primary w-100" onclick="copyContent()">
                    <i class="fas fa-copy"></i> Copier le contenu
                </button>
            </div>
        </div>

        <?php if (!empty($publication['event_start'])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Evenement</h5>
            </div>
            <div class="card-body">
                <p><strong>Debut :</strong> <?= date('d/m/Y H:i', strtotime($publication['event_start'])) ?></p>
                <?php if (!empty($publication['event_end'])): ?>
                <p class="mb-0"><strong>Fin :</strong> <?= date('d/m/Y H:i', strtotime($publication['event_end'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($publication['offer_code'])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tag"></i> Offre</h5>
            </div>
            <div class="card-body">
                <p><strong>Code promo :</strong> <code><?= e($publication['offer_code']) ?></code></p>
                <?php if (!empty($publication['offer_terms'])): ?>
                <p class="mb-0"><strong>Conditions :</strong> <?= e($publication['offer_terms']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function copyContent() {
    const textarea = document.getElementById('copy-content');
    textarea.select();
    document.execCommand('copy');
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Copie !';
    btn.classList.replace('btn-primary', 'btn-success');
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.replace('btn-success', 'btn-primary');
    }, 2000);
}
</script>
