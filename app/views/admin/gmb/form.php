<?php
/** @var array|null $publication */
/** @var array $settings */
/** @var string $nextSlot */
/** @var array $errors */
/** @var string $action */
/** @var string $submitLabel */

$isEdit = !empty($publication['id']);
$postType = $publication['post_type'] ?? 'update';
$content = $publication['content'] ?? '';
$contentLen = mb_strlen($content);
?>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (!empty($_GET['message'])): ?>
<div class="alert alert-success"><?= e($_GET['message']) ?></div>
<?php endif; ?>

<div class="row">
    <!-- Main form -->
    <div class="col-lg-8">
        <form method="post" action="<?= e($action) ?>" id="gmb-form">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Publication Google My Business</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Type de publication</label>
                            <select name="post_type" id="post_type" class="form-select" onchange="toggleEventFields()">
                                <option value="update" <?= $postType === 'update' ? 'selected' : '' ?>>Nouveaute / Mise a jour</option>
                                <option value="event" <?= $postType === 'event' ? 'selected' : '' ?>>Evenement</option>
                                <option value="offer" <?= $postType === 'offer' ? 'selected' : '' ?>>Offre</option>
                                <option value="product" <?= $postType === 'product' ? 'selected' : '' ?>>Produit / Service</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= ($publication['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                                <option value="scheduled" <?= ($publication['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Planifie</option>
                                <option value="published" <?= ($publication['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publie</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Titre <span class="text-muted">(requis pour event/offre/produit, max 58 car.)</span></label>
                            <input type="text" name="title" class="form-control" value="<?= e($publication['title'] ?? '') ?>" maxlength="58" id="gmb-title">
                            <div class="form-text"><span id="title-count"><?= mb_strlen($publication['title'] ?? '') ?></span>/58 caracteres</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Contenu <span class="text-danger">*</span> <span class="text-muted">(max 1500 car.)</span></label>
                            <textarea name="content" class="form-control" rows="8" maxlength="1500" required id="gmb-content"><?= e($content) ?></textarea>
                            <div class="form-text">
                                <span id="content-count" class="<?= $contentLen > 1400 ? 'text-warning' : '' ?> <?= $contentLen > 1500 ? 'text-danger fw-bold' : '' ?>"><?= $contentLen ?></span>/1500 caracteres
                            </div>
                        </div>

                        <!-- CTA -->
                        <div class="col-md-6">
                            <label class="form-label">Bouton CTA</label>
                            <select name="cta_type" class="form-select">
                                <option value="">Aucun CTA</option>
                                <?php
                                $ctaOptions = ['learn_more' => 'En savoir plus', 'book' => 'Reserver', 'call_now' => 'Appeler', 'sign_up' => 'S\'inscrire', 'get_offer' => 'Obtenir offre', 'order_online' => 'Commander', 'buy' => 'Acheter'];
                                foreach ($ctaOptions as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= ($publication['cta_type'] ?? ($settings['default_cta_type'] ?? '')) === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL du CTA</label>
                            <input type="url" name="cta_url" class="form-control" value="<?= e($publication['cta_url'] ?? ($settings['default_cta_url'] ?? '')) ?>" placeholder="https://...">
                        </div>

                        <!-- Scheduling -->
                        <div class="col-md-6">
                            <label class="form-label">Date/heure de publication planifiee</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= !empty($publication['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($publication['scheduled_at'])) : date('Y-m-d\TH:i', strtotime($nextSlot)) ?>">
                            <div class="form-text">Prochain creneau disponible : <?= date('d/m/Y H:i', strtotime($nextSlot)) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image (URL ou chemin)</label>
                            <input type="text" name="image_path" class="form-control" value="<?= e($publication['image_path'] ?? '') ?>" placeholder="/public/images/...">
                        </div>

                        <!-- Event fields (hidden by default) -->
                        <div class="col-md-6 event-fields" style="display: none;">
                            <label class="form-label">Debut evenement</label>
                            <input type="datetime-local" name="event_start" class="form-control" value="<?= !empty($publication['event_start']) ? date('Y-m-d\TH:i', strtotime($publication['event_start'])) : '' ?>">
                        </div>
                        <div class="col-md-6 event-fields" style="display: none;">
                            <label class="form-label">Fin evenement</label>
                            <input type="datetime-local" name="event_end" class="form-control" value="<?= !empty($publication['event_end']) ? date('Y-m-d\TH:i', strtotime($publication['event_end'])) : '' ?>">
                        </div>

                        <!-- Offer fields (hidden by default) -->
                        <div class="col-md-6 offer-fields" style="display: none;">
                            <label class="form-label">Code promo</label>
                            <input type="text" name="offer_code" class="form-control" value="<?= e($publication['offer_code'] ?? '') ?>" maxlength="50">
                        </div>
                        <div class="col-md-6 offer-fields" style="display: none;">
                            <label class="form-label">Conditions de l'offre</label>
                            <textarea name="offer_terms" class="form-control" rows="2"><?= e($publication['offer_terms'] ?? '') ?></textarea>
                        </div>

                        <!-- Hidden source fields -->
                        <input type="hidden" name="article_id" value="<?= e((string) ($publication['article_id'] ?? '')) ?>">
                        <input type="hidden" name="actualite_id" value="<?= e((string) ($publication['actualite_id'] ?? '')) ?>">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="/admin/gmb" class="btn btn-outline-secondary">Retour</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= e($submitLabel) ?></button>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4 sticky-top" style="top: 80px;">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fab fa-google"></i> Apercu GMB</h6>
            </div>
            <div class="card-body p-3">
                <div style="border: 1px solid #dadce0; border-radius: 8px; padding: 16px; font-family: 'Google Sans', Arial, sans-serif;">
                    <div class="d-flex align-items-center mb-2">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #4285f4; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; margin-right: 10px;">E</div>
                        <div>
                            <div style="font-weight: 500; font-size: 14px;">Estimation Immobilier <?= htmlspecialchars((string) site("city", ""), ENT_QUOTES, "UTF-8") ?></div>
                            <div style="color: #70757a; font-size: 12px;">Il y a quelques instants</div>
                        </div>
                    </div>
                    <div id="preview-content" style="font-size: 14px; line-height: 1.5; white-space: pre-wrap; word-break: break-word; max-height: 200px; overflow-y: auto;">
                        <?= e($content ?: 'Votre contenu apparaitra ici...') ?>
                    </div>
                    <div id="preview-cta" class="mt-3" style="display: none;">
                        <a href="#" style="display: inline-block; background: #1a73e8; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;" id="preview-cta-btn">En savoir plus</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb text-warning"></i> Conseils</h6>
            </div>
            <div class="card-body small">
                <ul class="mb-0">
                    <li><strong>1500 car. max</strong> pour le contenu</li>
                    <li><strong>58 car. max</strong> pour le titre</li>
                    <li>Utilisez des <strong>emojis</strong> pour attirer l'attention</li>
                    <li>Ajoutez toujours un <strong>CTA</strong> pour guider l'action</li>
                    <li>Publiez <strong>2-3 fois par semaine</strong> minimum</li>
                    <li>Les <strong>photos</strong> augmentent l'engagement de 42%</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEventFields() {
    const type = document.getElementById('post_type').value;
    document.querySelectorAll('.event-fields').forEach(el => {
        el.style.display = (type === 'event') ? '' : 'none';
    });
    document.querySelectorAll('.offer-fields').forEach(el => {
        el.style.display = (type === 'offer') ? '' : 'none';
    });
}

// Character counters
document.getElementById('gmb-title')?.addEventListener('input', function() {
    document.getElementById('title-count').textContent = this.value.length;
});
document.getElementById('gmb-content')?.addEventListener('input', function() {
    const len = this.value.length;
    const counter = document.getElementById('content-count');
    counter.textContent = len;
    counter.className = len > 1500 ? 'text-danger fw-bold' : (len > 1400 ? 'text-warning' : '');
    // Update preview
    document.getElementById('preview-content').textContent = this.value || 'Votre contenu apparaitra ici...';
});

// Init
toggleEventFields();
</script>
