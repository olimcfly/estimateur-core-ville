<?php
$_city = (string) site('city', 'VotreVille');
$_citySlug = (string) site('city_slug', 'locale');
$silos = [
    [
        'name' => 'Estimation & Avis de valeur à ' . \$_city,
        'color' => '#8B1538',
        'number' => 1,
        'pillar_id' => 1,
        'articles' => [
            ['id' => 1, 'slug' => 'estimer-bien-immobilier-' . \$_citySlug', 'type' => 'Pilier', 'status' => 'published'],
            ['id' => 2, 'slug' => 'prix-immobilier-' . \$_citySlug . '-2025', 'type' => 'Pilier', 'status' => 'published'],
            ['id' => 3, 'slug' => 'avis-de-valeur-' . \$_citySlug', 'type' => 'Pilier', 'status' => 'published'],
            ['id' => 4, 'slug' => 'estimation-echoppe-locale', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 5, 'slug' => 'dpe-prix-immobilier-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 6, 'slug' => 'estimation-appartement-' . \$_citySlug . '-erreurs', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 7, 'slug' => 'valeur-maison-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 8, 'slug' => 'prix-immobilier-chartrons-' . \$_citySlug . '-2025', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 9, 'slug' => 'prix-immobilier-cauderan-2025', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 10, 'slug' => 'immobilier-la-bastide-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 11, 'slug' => 'immobilier-bacalan-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 12, 'slug' => 'prix-immobilier-merignac-vs-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 13, 'slug' => 'vendre-son-bien-' . \$_citySlug . '-etapes', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 14, 'slug' => 'choisir-conseiller-immobilier-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => 15, 'slug' => 'negociation-immobiliere-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
        ],
    ],
    [
        'name' => 'Vendre vite et au bon prix à ' . \$_city,
        'color' => '#1A3C5E',
        'number' => 2,
        'pillar_id' => 16,
        'articles' => [
            ['id' => 16, 'slug' => 'vendre-rapidement-bien-' . \$_citySlug', 'type' => 'Pilier', 'status' => 'published'],
            ['id' => 17, 'slug' => 'vendre-urgence-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'published'],
            ['id' => null, 'slug' => 'succession-immobiliere-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'vendre-appartement-loue-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'home-staging-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'vendre-sans-agence-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
        ],
    ],
    [
        'name' => 'Acheter à ' . \$_city,
        'color' => '#2D6A4F',
        'number' => 3,
        'pillar_id' => null,
        'articles' => [
            ['id' => null, 'slug' => 'guide-acheter-immobilier-' . \$_citySlug . '-2025', 'type' => 'Pilier', 'status' => 'todo'],
            ['id' => null, 'slug' => 'budget-acheter-' . \$_citySlug . '-quartier', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'frais-notaire-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'pieges-achat-immobilier-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'acheter-ou-louer-' . \$_citySlug . '-2025', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'offre-achat-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
        ],
    ],
    [
        'name' => 'Fiscalité et aspects juridiques',
        'color' => '#5C3317',
        'number' => 4,
        'pillar_id' => null,
        'articles' => [
            ['id' => null, 'slug' => 'plus-value-immobiliere-' . \$_citySlug', 'type' => 'Pilier', 'status' => 'todo'],
            ['id' => null, 'slug' => 'taxe-fonciere-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'exoneration-plus-value-residence-principale-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'ifi-immobilier-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'frais-agence-immobiliere-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'diagnostic-termites-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
        ],
    ],
    [
        'name' => 'Investissement locatif ' . \$_city . '',
        'color' => '#4A1942',
        'number' => 5,
        'pillar_id' => null,
        'articles' => [
            ['id' => null, 'slug' => 'investissement-locatif-' . \$_citySlug . '-2025', 'type' => 'Pilier', 'status' => 'todo'],
            ['id' => null, 'slug' => 'rendement-locatif-' . \$_citySlug . '-quartier', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'lmnp-' . \$_citySlug . '-quartiers-rentables', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'investir-echoppe-locale-location', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'colocation-' . \$_citySlug . '-prix-rentabilite', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'encadrement-loyers-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
        ],
    ],
    [
        'name' => 'Rénovation et valorisation',
        'color' => '#1D3461',
        'number' => 6,
        'pillar_id' => null,
        'articles' => [
            ['id' => null, 'slug' => 'renover-pour-vendre-' . \$_citySlug', 'type' => 'Pilier', 'status' => 'todo'],
            ['id' => null, 'slug' => 'renovation-energetique-avant-vente-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'passer-dpe-f-a-d-' . \$_citySlug . '-cout-gain', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'surelevation-echoppe-' . \$_citySlug . '-cout-rentabilite', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'home-staging-vs-travaux-' . \$_citySlug', 'type' => 'Satellite', 'status' => 'todo'],
            ['id' => null, 'slug' => 'aides-renovation-energetique-gironde-2025', 'type' => 'Satellite', 'status' => 'todo'],
        ],
    ],
];

$totalPublished = 0;
$totalTodo = 0;
$totalArticles = 0;
foreach ($silos as $silo) {
    foreach ($silo['articles'] as $article) {
        $totalArticles++;
        if ($article['status'] === 'published') {
            $totalPublished++;
        } else {
            $totalTodo++;
        }
    }
}
$progressPercent = $totalArticles > 0 ? round(($totalPublished / $totalArticles) * 100) : 0;
?>

<style>
.ideas-stat-card { background: #fff; border-radius: 8px; padding: 1.25rem; border: 1px solid #e8e8e8; text-align: center; }
.ideas-stat-card .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
.ideas-stat-card .stat-label { font-size: 0.8rem; color: #888; margin-top: 0.25rem; }

.silo-ideas-group { border-radius: 10px; margin-bottom: 1.5rem; overflow: hidden; border: 2px solid #e0e0e0; }
.silo-ideas-header { padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.silo-ideas-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
.silo-ideas-body { background: #fff; }

.idea-row { display: grid; grid-template-columns: 50px 1fr 100px 120px 140px; gap: 0.5rem; align-items: center; padding: 0.6rem 1.25rem; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
.idea-row:last-child { border-bottom: none; }
.idea-row-pilier { background: #fdf8f0; }
.idea-row-satellite { padding-left: 1rem; }

.idea-type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.idea-type-pilier { background: #8B1538; color: #fff; }
.idea-type-satellite { background: #D4AF37; color: #1a1a1a; }

.idea-status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.idea-status-published { background: #d4edda; color: #155724; }
.idea-status-todo { background: #fff3cd; color: #856404; }

.progress-bar-container { height: 10px; background: #f0f0f0; border-radius: 5px; overflow: hidden; }
.progress-bar-fill { height: 100%; border-radius: 5px; transition: width 0.3s ease; }

.recap-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.recap-table th, .recap-table td { padding: 0.5rem 0.75rem; text-align: center; border-bottom: 1px solid #eee; }
.recap-table th { background: #fafafa; font-weight: 600; color: #555; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
.recap-table tr:last-child { font-weight: 700; background: #f8f4e8; }
.recap-table .silo-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 0.5rem; vertical-align: middle; }

@media (max-width: 768px) {
    .idea-row { grid-template-columns: 40px 1fr 80px 100px; }
    .idea-row > *:last-child { display: none; }
}
</style>

<div class="container">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <a href="/admin/blog" class="btn btn-small btn-ghost" style="margin-bottom: 0.5rem; display: inline-block;">&larr; Retour Blog</a>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin: 0 0 0.25rem;">Planning des articles blog</h1>
            <p style="margin: 0; color: #888; font-size: 0.85rem;">Liste des idées d'articles organisées par silo SEO - Génération IA Claude</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/blog/wizard" class="btn" style="background: #D4AF37; color: #1a1a1a; font-weight: 600;">
                + Rédiger un article (IA)
            </a>
            <a href="/admin/blog/silos" class="btn btn-ghost">Silos SEO</a>
        </div>
    </div>

    <!-- Stats globales -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="ideas-stat-card">
            <div class="stat-value" style="color: #8B1538;"><?= $totalArticles ?></div>
            <div class="stat-label">Articles planifiés</div>
        </div>
        <div class="ideas-stat-card">
            <div class="stat-value" style="color: #0d7a3e;"><?= $totalPublished ?></div>
            <div class="stat-label">Publiés</div>
        </div>
        <div class="ideas-stat-card">
            <div class="stat-value" style="color: #D4AF37;"><?= $totalTodo ?></div>
            <div class="stat-label">À rédiger</div>
        </div>
        <div class="ideas-stat-card">
            <div class="stat-value" style="color: #555;"><?= count($silos) ?></div>
            <div class="stat-label">Silos thématiques</div>
        </div>
        <div class="ideas-stat-card">
            <div class="stat-value" style="color: #1A3C5E;"><?= $progressPercent ?>%</div>
            <div class="stat-label">Progression</div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-weight: 600; font-size: 0.9rem;">Progression globale</span>
            <span style="font-size: 0.85rem; color: #888;"><?= $totalPublished ?> / <?= $totalArticles ?> articles rédigés</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: <?= $progressPercent ?>%; background: linear-gradient(90deg, #0d7a3e, #2D6A4F);"></div>
        </div>
    </div>

    <!-- Silos détaillés -->
    <?php foreach ($silos as $silo): ?>
    <?php
    $siloPublished = 0;
    $siloTotal = count($silo['articles']);
    foreach ($silo['articles'] as $a) {
        if ($a['status'] === 'published') $siloPublished++;
    }
    $siloTodo = $siloTotal - $siloPublished;
    ?>
    <div class="silo-ideas-group" style="border-color: <?= $silo['color'] ?>;">
        <div class="silo-ideas-header" style="background: <?= $silo['color'] ?>15;">
            <h3>
                <span style="display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: <?= $silo['color'] ?>;"></span>
                <span style="color: <?= $silo['color'] ?>;">SILO <?= $silo['number'] ?> — <?= htmlspecialchars($silo['name']) ?></span>
            </h3>
            <div style="display: flex; gap: 1rem; align-items: center; font-size: 0.8rem; color: #666;">
                <span><strong style="color: #0d7a3e;"><?= $siloPublished ?></strong> publiés</span>
                <span><strong style="color: #D4AF37;"><?= $siloTodo ?></strong> à rédiger</span>
                <span><?= $siloTotal ?> total</span>
                <?php if ($silo['pillar_id'] !== null): ?>
                    <span style="font-size: 0.75rem; color: #999;">Pilier : Article ID <?= $silo['pillar_id'] ?></span>
                <?php else: ?>
                    <span style="font-size: 0.75rem; color: #c0392b; font-weight: 600;">Pilier : à rédiger</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="silo-ideas-body">
            <!-- En-tête colonnes -->
            <div style="display: grid; grid-template-columns: 50px 1fr 100px 120px 140px; gap: 0.5rem; padding: 0.4rem 1.25rem; font-size: 0.7rem; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee;">
                <div>ID</div>
                <div>Slug</div>
                <div>Type</div>
                <div>Statut</div>
                <div>Action</div>
            </div>
            <?php foreach ($silo['articles'] as $article): ?>
            <div class="idea-row <?= $article['type'] === 'Pilier' ? 'idea-row-pilier' : 'idea-row-satellite' ?>">
                <div style="color: #999; font-weight: 600;">
                    <?= $article['id'] !== null ? $article['id'] : '—' ?>
                </div>
                <div>
                    <code style="font-size: 0.8rem; background: #f5f5f5; padding: 2px 6px; border-radius: 3px;"><?= htmlspecialchars($article['slug']) ?></code>
                </div>
                <div>
                    <span class="idea-type-badge <?= $article['type'] === 'Pilier' ? 'idea-type-pilier' : 'idea-type-satellite' ?>">
                        <?= $article['type'] ?>
                    </span>
                </div>
                <div>
                    <?php if ($article['status'] === 'published'): ?>
                        <span class="idea-status-badge idea-status-published">Publié</span>
                    <?php else: ?>
                        <span class="idea-status-badge idea-status-todo">À rédiger</span>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($article['status'] === 'published'): ?>
                        <a href="/blog/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-small btn-ghost" style="font-size: 0.75rem;" target="_blank">Voir</a>
                    <?php else: ?>
                        <a href="/admin/blog/wizard" class="btn btn-small" style="font-size: 0.75rem; background: #D4AF37; color: #1a1a1a;">Rédiger (IA)</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Récap global -->
    <div class="card" style="margin-top: 2rem;">
        <h2 style="margin: 0 0 1rem; font-size: 1.1rem;">Récapitulatif global</h2>
        <table class="recap-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Silo</th>
                    <th>Articles rédigés</th>
                    <th>Articles à rédiger</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($silos as $silo): ?>
                <?php
                $sp = 0;
                $st = count($silo['articles']);
                foreach ($silo['articles'] as $a) {
                    if ($a['status'] === 'published') $sp++;
                }
                ?>
                <tr>
                    <td style="text-align: left;">
                        <span class="silo-dot" style="background: <?= $silo['color'] ?>;"></span>
                        Silo <?= $silo['number'] ?>
                    </td>
                    <td><strong style="color: #0d7a3e;"><?= $sp ?></strong></td>
                    <td><strong style="color: #D4AF37;"><?= $st - $sp ?></strong></td>
                    <td><?= $st ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="text-align: left;">Total</td>
                    <td><strong style="color: #0d7a3e;"><?= $totalPublished ?></strong></td>
                    <td><strong style="color: #D4AF37;"><?= $totalTodo ?></strong></td>
                    <td><strong><?= $totalArticles ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Légende -->
    <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #fafafa; border-radius: 8px; font-size: 0.8rem; color: #888; display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
        <span style="font-weight: 600;">Légende :</span>
        <span><span class="idea-type-badge idea-type-pilier">PILIER</span> Article principal du silo</span>
        <span><span class="idea-type-badge idea-type-satellite">SATELLITE</span> Article de support</span>
        <span><span class="idea-status-badge idea-status-published">Publié</span> Article en ligne</span>
        <span><span class="idea-status-badge idea-status-todo">À rédiger</span> À générer via IA Claude</span>
    </div>
</div>
