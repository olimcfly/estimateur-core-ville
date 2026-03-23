<?php
/** @var array $sites */
?>

<style>
.site-selection { max-width: 600px; margin: 2rem auto; }
.site-selection h2 {
    font-family: 'Playfair Display', serif; font-size: 1.4rem;
    margin-bottom: 0.5rem; text-align: center;
}
.site-selection p { color: #6b6459; text-align: center; margin-bottom: 1.5rem; }

.site-list { list-style: none; padding: 0; margin: 0; }
.site-item {
    background: #fff; border: 2px solid #e8dfd7; border-radius: 10px;
    padding: 1rem 1.25rem; margin-bottom: 0.75rem; cursor: pointer;
    transition: all 0.2s; display: flex; align-items: center; gap: 1rem;
}
.site-item:hover { border-color: #8B1538; background: #faf9f7; }
.site-item.selected { border-color: #8B1538; background: #f9f0f3; }
.site-item input[type="radio"] { accent-color: #8B1538; width: 18px; height: 18px; }
.site-item-info { flex: 1; }
.site-item-url { font-weight: 600; color: #1a1410; font-size: 0.95rem; }
.site-item-level { font-size: 0.78rem; color: #6b6459; margin-top: 0.15rem; }

.site-submit {
    display: block; width: 100%; padding: 0.85rem; margin-top: 1.5rem;
    background: #8B1538; color: #fff; border: none; border-radius: 8px;
    font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s;
}
.site-submit:hover { background: #6b0f2d; }
</style>

<div class="site-selection">
    <h2><i class="fab fa-google" style="color:#D4AF37"></i> Sélectionnez votre site</h2>
    <p>Choisissez le site dont vous souhaitez suivre les mots-clés :</p>

    <form action="/admin/seo-hub/confirm-site" method="POST">
        <ul class="site-list">
            <?php foreach ($sites as $i => $site): ?>
            <li class="site-item" onclick="this.querySelector('input').checked=true; document.querySelectorAll('.site-item').forEach(el=>el.classList.remove('selected')); this.classList.add('selected');">
                <input type="radio" name="site_url" value="<?= htmlspecialchars($site['siteUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       <?= $i === 0 ? 'checked' : '' ?> required>
                <div class="site-item-info">
                    <div class="site-item-url"><?= htmlspecialchars($site['siteUrl'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="site-item-level">
                        <?= htmlspecialchars($site['permissionLevel'] ?? 'siteOwner', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <button type="submit" class="site-submit">
            <i class="fas fa-check"></i> Valider et importer les mots-clés
        </button>
    </form>
</div>
