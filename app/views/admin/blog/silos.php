<div class="container">
    <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 1rem; display: inline-block;">&larr; Retour Blog</a>
    <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.5rem;">Silos SEO</h1>
    <p style="color: #666; margin-bottom: 1.5rem;">Organisez vos contenus par thématique avec des articles piliers et satellites.</p>

    <?php if (($message ?? '') !== ''): ?><p class="success"><?= e((string) $message) ?></p><?php endif; ?>
    <?php if (($error ?? '') !== ''): ?><p class="alert"><?= e((string) $error) ?></p><?php endif; ?>

    <!-- Explanation -->
    <section class="card" style="margin-bottom: 1.5rem; background: #f8f4e8; border-left: 4px solid #D4AF37;">
        <h3 style="margin: 0 0 0.5rem; color: #8B1538;">Qu'est-ce qu'un Silo SEO ?</h3>
        <p style="margin: 0 0 0.5rem; font-size: 0.9rem; color: #555;">
            Un silo SEO organise vos contenus par thématique avec :
        </p>
        <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9rem; color: #555;">
            <li><strong>1 article PILIER</strong> (page principale sur un sujet large)</li>
            <li><strong>5-10 articles SATELLITES</strong> (articles détaillés sur des sous-thèmes)</li>
            <li><strong>Maillage interne</strong> (liens entre articles du même silo)</li>
        </ul>
        <p style="margin: 0.5rem 0 0; font-size: 0.85rem; color: #8B1538; font-weight: 600;">
            Chaque article satellite DOIT faire un lien vers l'article pilier. L'article pilier fait des liens vers tous les satellites.
        </p>
    </section>

    <!-- Create Silo -->
    <section class="card" style="margin-bottom: 1.5rem;">
        <h2 style="margin: 0 0 1rem;">Créer un Silo</h2>
        <form method="post" action="/admin/blog/silos/create" class="form-grid" style="gap: 1rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                <label>Nom du silo
                    <input type="text" name="name" required placeholder="Ex: Vendre son Bien, Investissement Locatif...">
                </label>
                <label>Description
                    <input type="text" name="description" placeholder="Brève description du thème">
                </label>
                <label>Couleur
                    <input type="color" name="color" value="#8B1538" style="height: 38px; width: 60px;">
                </label>
            </div>
            <button type="submit" class="btn">Créer le silo</button>
        </form>
    </section>

    <!-- Existing Silos -->
    <?php if (!empty($silos)): ?>
    <section class="card">
        <h2 style="margin: 0 0 1rem;">Silos existants (<?= count($silos) ?>)</h2>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($silos as $silo): ?>
            <div style="border: 2px solid <?= e((string) $silo['color']) ?>; border-radius: 8px; padding: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <div>
                        <h3 style="margin: 0; color: <?= e((string) $silo['color']) ?>;"><?= e((string) $silo['name']) ?></h3>
                        <?php if (!empty($silo['description'])): ?>
                        <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: #888;"><?= e((string) $silo['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="font-size: 0.85rem; color: #666;">
                            <?= (int) ($silo['article_count'] ?? 0) ?> articles |
                            Score moyen: <?= (int) ($silo['avg_seo_score'] ?? 0) ?>/100
                        </span>
                        <form method="post" action="/admin/blog/silos/delete/<?= (int) $silo['id'] ?>" style="display: inline;"
                            onsubmit="return confirm('Supprimer ce silo ? Les articles seront désassociés mais pas supprimés.');">
                            <button type="submit" class="btn btn-small" style="font-size: 0.75rem;">Supprimer</button>
                        </form>
                    </div>
                </div>

                <!-- Visual silo structure -->
                <div style="font-size: 0.85rem; color: #555; padding: 0.5rem; background: #fafafa; border-radius: 4px;">
                    <div style="font-family: monospace; white-space: pre-wrap;">SILO : <?= e((string) $silo['name']) ?>
│
├── Article PILIER : (à définir)
│
├── Article satellite 1
├── Article satellite 2
├── Article satellite 3
└── ...</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php else: ?>
    <section class="card" style="text-align: center; padding: 2rem;">
        <p style="color: #888; font-size: 1rem;">Aucun silo créé. Commencez par créer votre premier silo thématique.</p>
    </section>
    <?php endif; ?>
</div>
