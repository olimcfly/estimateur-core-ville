<?php
$page_title = 'Prix Immobilier par Quartier à Bordeaux et Métropole | Estimation Immobilière 2026';
$meta_description = 'Découvrez les prix au m² par quartier à Bordeaux et Métropole : Chartrons, Caudéran, Bastide, Mérignac, Pessac, Talence... Guide complet avec tendances et estimation gratuite.';

$quartiers = [
    // === QUARTIERS DE BORDEAUX ===
    [
        'nom' => 'Chartrons',
        'description' => "Quartier emblématique du nord de Bordeaux, les Chartrons séduisent par leur charme historique, leurs antiquaires, galeries d'art et ambiance village. Très prisé des familles et jeunes actifs.",
        'prix_m2' => 5200,
        'prix_moyen' => 520000,
        'caracteristiques' => ['Patrimoine', 'Commerces', 'Marché', 'Vie culturelle'],
        'population' => '~15 000 habitants',
        'transports' => 'Tram B, Bus, Pistes cyclables',
        'attractivite' => 'Très haute',
        'coords' => '44.8530,-0.5700',
        'tendance' => '+4.8%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Saint-Pierre',
        'description' => "Coeur historique de Bordeaux, quartier piéton animé avec restaurants, bars et boutiques. Architecture XVIIIe siècle remarquable. Idéal pour les amoureux du centre-ville.",
        'prix_m2' => 5800,
        'prix_moyen' => 480000,
        'caracteristiques' => ['Centre historique', 'Piéton', 'Gastronomie', 'Vie nocturne'],
        'population' => '~8 000 habitants',
        'transports' => 'Tram A/B, Bus, Piéton',
        'attractivite' => 'Très haute',
        'coords' => '44.8378,-0.5717',
        'tendance' => '+3.5%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Saint-Michel',
        'description' => "Quartier populaire et cosmopolite autour de la basilique et de son marché. Ambiance multiculturelle, prix encore accessibles et forte dynamique de rénovation urbaine.",
        'prix_m2' => 4200,
        'prix_moyen' => 350000,
        'caracteristiques' => ['Multiculturel', 'Marché', 'Patrimoine', 'Dynamique'],
        'population' => '~12 000 habitants',
        'transports' => 'Tram A, Bus, Gare Saint-Jean proche',
        'attractivite' => 'Haute',
        'coords' => '44.8330,-0.5650',
        'tendance' => '+6.2%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Caudéran',
        'description' => "Quartier résidentiel prisé des familles, Caudéran offre de belles maisons avec jardins, un cadre de vie paisible et d'excellentes écoles. L'un des secteurs les plus recherchés de Bordeaux.",
        'prix_m2' => 4900,
        'prix_moyen' => 580000,
        'caracteristiques' => ['Résidentiel', 'Familles', 'Espaces verts', 'Écoles'],
        'population' => '~42 000 habitants',
        'transports' => 'Tram A/D, Bus, Accès rocade',
        'attractivite' => 'Très haute',
        'coords' => '44.8500,-0.6100',
        'tendance' => '+3.1%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Bastide',
        'description' => "Rive droite en pleine transformation, la Bastide offre des vues imprenables sur la ville, des projets urbains ambitieux (Darwin, Euratlantique) et des prix encore attractifs. Fort potentiel de plus-value.",
        'prix_m2' => 4100,
        'prix_moyen' => 380000,
        'caracteristiques' => ['Rive droite', 'Renouveau urbain', 'Vue Garonne', 'Investissement'],
        'population' => '~18 000 habitants',
        'transports' => 'Tram A, Bus, Pont de Pierre',
        'attractivite' => 'Haute',
        'coords' => '44.8400,-0.5550',
        'tendance' => '+7.3%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Mériadeck',
        'description' => "Quartier d'affaires et administratif de Bordeaux, Mériadeck offre des appartements spacieux à des prix raisonnables. Proche du centre, bien desservi et en cours de réhabilitation.",
        'prix_m2' => 4400,
        'prix_moyen' => 390000,
        'caracteristiques' => ['Affaires', 'Spacieux', 'Central', 'Services'],
        'population' => '~10 000 habitants',
        'transports' => 'Tram A/B, Bus, Gare proche',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.8370,-0.5830',
        'tendance' => '+2.8%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Nansouty – Saint-Genès',
        'description' => "Quartier résidentiel et étudiant entre la gare et le campus. Ambiance de village en plein centre, nombreux commerces de proximité et prix encore abordables pour du Bordeaux intra-boulevard.",
        'prix_m2' => 4600,
        'prix_moyen' => 420000,
        'caracteristiques' => ['Résidentiel', 'Étudiants', 'Commerces', 'Central'],
        'population' => '~20 000 habitants',
        'transports' => 'Tram B, Bus, Vélos',
        'attractivite' => 'Haute',
        'coords' => '44.8280,-0.5780',
        'tendance' => '+3.9%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Saint-Augustin',
        'description' => "Quartier calme et verdoyant à l'ouest de Bordeaux, Saint-Augustin séduit les familles avec ses maisons individuelles, sa proximité du stade Matmut Atlantique et ses espaces verts.",
        'prix_m2' => 4700,
        'prix_moyen' => 540000,
        'caracteristiques' => ['Résidentiel', 'Calme', 'Espaces verts', 'Stade'],
        'population' => '~12 000 habitants',
        'transports' => 'Tram C, Bus, Rocade proche',
        'attractivite' => 'Haute',
        'coords' => '44.8450,-0.6200',
        'tendance' => '+2.5%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Bacalan',
        'description' => "Ancien quartier portuaire en pleine mutation grâce à la Cité du Vin et aux Bassins à Flot. Bacalan attire investisseurs et jeunes actifs avec des programmes neufs et une identité forte.",
        'prix_m2' => 4500,
        'prix_moyen' => 380000,
        'caracteristiques' => ['Renouveau', 'Cité du Vin', 'Bassins à Flot', 'Investissement'],
        'population' => '~10 000 habitants',
        'transports' => 'Tram B, Bus, Pistes cyclables',
        'attractivite' => 'Haute',
        'coords' => '44.8620,-0.5580',
        'tendance' => '+5.6%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Bordeaux Sud – Bègles Gare',
        'description' => "Secteur en plein essor autour d'Euratlantique, le plus grand projet urbain de France. Programmes neufs, accessibilité TGV et prix en forte hausse. Quartier d'avenir pour l'investissement locatif.",
        'prix_m2' => 4000,
        'prix_moyen' => 340000,
        'caracteristiques' => ['Euratlantique', 'Neuf', 'TGV', 'Investissement'],
        'population' => '~15 000 habitants',
        'transports' => 'Tram C, Gare Saint-Jean, Bus',
        'attractivite' => 'Haute',
        'coords' => '44.8200,-0.5680',
        'tendance' => '+8.1%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Jardin Public – Fondaudège',
        'description' => "Quartier huppé autour du Jardin Public, prisé pour ses hôtels particuliers, ses larges avenues bordelaises et sa proximité du centre. L'un des secteurs les plus chers de Bordeaux.",
        'prix_m2' => 5600,
        'prix_moyen' => 620000,
        'caracteristiques' => ['Prestige', 'Jardin Public', 'Patrimoine', 'Haussmannien'],
        'population' => '~8 000 habitants',
        'transports' => 'Tram B/D, Bus, Piéton',
        'attractivite' => 'Très haute',
        'coords' => '44.8480,-0.5770',
        'tendance' => '+2.3%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Victoire – Capucins',
        'description' => "Quartier animé et populaire autour de la place de la Victoire et du marché des Capucins, le ventre de Bordeaux. Forte vie étudiante, commerces variés et prix accessibles.",
        'prix_m2' => 4300,
        'prix_moyen' => 320000,
        'caracteristiques' => ['Étudiants', 'Marché Capucins', 'Animé', 'Accessible'],
        'population' => '~14 000 habitants',
        'transports' => 'Tram B, Bus, Gare proche',
        'attractivite' => 'Haute',
        'coords' => '44.8310,-0.5730',
        'tendance' => '+4.2%',
        'zone' => 'Bordeaux',
    ],
    [
        'nom' => 'Grands Hommes – Triangle d\'Or',
        'description' => "Le Triangle d'Or est le quartier le plus prestigieux de Bordeaux : commerces de luxe, grands restaurants et architecture XVIIIe remarquable autour du cours de l'Intendance et des allées de Tourny.",
        'prix_m2' => 6200,
        'prix_moyen' => 580000,
        'caracteristiques' => ['Luxe', 'Prestige', 'Commerce haut de gamme', 'Patrimoine UNESCO'],
        'population' => '~5 000 habitants',
        'transports' => 'Tram B, Bus, Piéton',
        'attractivite' => 'Très haute',
        'coords' => '44.8420,-0.5770',
        'tendance' => '+1.8%',
        'zone' => 'Bordeaux',
    ],
    // === COMMUNES DE LA MÉTROPOLE ===
    [
        'nom' => 'Mérignac',
        'description' => "Deuxième ville de la métropole, Mérignac offre un cadre de vie équilibré entre zones résidentielles, commerces (Mérignac Soleil) et espaces naturels. Proche de l'aéroport et bien connectée au centre.",
        'prix_m2' => 3900,
        'prix_moyen' => 380000,
        'caracteristiques' => ['Métropole', 'Aéroport', 'Commerces', 'Résidentiel'],
        'population' => '~74 000 habitants',
        'transports' => 'Tram A, Bus, Rocade, Aéroport',
        'attractivite' => 'Haute',
        'coords' => '44.8386,-0.6436',
        'tendance' => '+3.4%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Pessac',
        'description' => "Ville universitaire majeure de la métropole avec le campus de Bordeaux-Montaigne et Bordeaux. Quartiers résidentiels variés, des grands crus classés de Pessac-Léognan et un accès tram direct.",
        'prix_m2' => 3600,
        'prix_moyen' => 340000,
        'caracteristiques' => ['Université', 'Résidentiel', 'Vignobles', 'Tram'],
        'population' => '~65 000 habitants',
        'transports' => 'Tram B, Bus, TER, Rocade',
        'attractivite' => 'Haute',
        'coords' => '44.8066,-0.6311',
        'tendance' => '+3.8%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Talence',
        'description' => "Commune étudiante et résidentielle au sud de Bordeaux, Talence bénéficie du campus universitaire, de nombreux espaces verts et d'une bonne desserte tram. Prix attractifs pour la proximité du centre.",
        'prix_m2' => 3800,
        'prix_moyen' => 350000,
        'caracteristiques' => ['Université', 'Espaces verts', 'Résidentiel', 'Tram'],
        'population' => '~44 000 habitants',
        'transports' => 'Tram B, Bus, Pistes cyclables',
        'attractivite' => 'Haute',
        'coords' => '44.8025,-0.5878',
        'tendance' => '+4.1%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Bègles',
        'description' => "Commune en plein renouveau grâce au projet Euratlantique. Bègles offre des prix encore accessibles, une vie culturelle dynamique et des quartiers en transformation avec de nombreux programmes neufs.",
        'prix_m2' => 3500,
        'prix_moyen' => 310000,
        'caracteristiques' => ['Euratlantique', 'Accessible', 'Renouveau', 'Culture'],
        'population' => '~28 000 habitants',
        'transports' => 'Tram C, Bus, Gare proche',
        'attractivite' => 'Haute',
        'coords' => '44.8094,-0.5486',
        'tendance' => '+5.9%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Villenave-d\'Ornon',
        'description' => "Commune résidentielle du sud de la métropole, Villenave-d'Ornon séduit les familles avec ses zones pavillonnaires, ses espaces verts et ses prix parmi les plus abordables de la première couronne.",
        'prix_m2' => 3200,
        'prix_moyen' => 300000,
        'caracteristiques' => ['Résidentiel', 'Familles', 'Abordable', 'Nature'],
        'population' => '~36 000 habitants',
        'transports' => 'Bus, Rocade, Futur tram',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.7803,-0.5561',
        'tendance' => '+4.5%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Le Bouscat',
        'description' => "Commune résidentielle prisée au nord de Bordeaux, Le Bouscat offre un cadre de vie verdoyant avec le parc de la Chêneraie, des commerces de qualité et une ambiance village très recherchée.",
        'prix_m2' => 4800,
        'prix_moyen' => 520000,
        'caracteristiques' => ['Résidentiel', 'Verdoyant', 'Commerces', 'Village'],
        'population' => '~24 000 habitants',
        'transports' => 'Tram C, Bus, Pistes cyclables',
        'attractivite' => 'Très haute',
        'coords' => '44.8600,-0.5950',
        'tendance' => '+2.9%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Gradignan',
        'description' => "Commune verte et familiale au sud-ouest de Bordeaux, Gradignan est appréciée pour son cadre de vie paisible, ses parcs (Prieuré de Cayac) et sa proximité du campus universitaire.",
        'prix_m2' => 3700,
        'prix_moyen' => 370000,
        'caracteristiques' => ['Nature', 'Familles', 'Calme', 'Campus proche'],
        'population' => '~25 000 habitants',
        'transports' => 'Bus, Rocade, Pistes cyclables',
        'attractivite' => 'Haute',
        'coords' => '44.7722,-0.6156',
        'tendance' => '+3.2%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Cenon',
        'description' => "Commune de la rive droite avec vue panoramique sur Bordeaux depuis le parc Palmer. Cenon bénéficie du renouveau de la rive droite avec des prix attractifs et une bonne desserte tram.",
        'prix_m2' => 3100,
        'prix_moyen' => 260000,
        'caracteristiques' => ['Rive droite', 'Panorama', 'Accessible', 'Tram'],
        'population' => '~25 000 habitants',
        'transports' => 'Tram A, Bus, Rocade',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.8567,-0.5319',
        'tendance' => '+5.3%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Lormont',
        'description' => "Commune de la rive droite en pleine transformation, Lormont offre des prix parmi les plus bas de la métropole avec un fort potentiel de valorisation. Vue sur le pont d'Aquitaine et espaces naturels.",
        'prix_m2' => 2800,
        'prix_moyen' => 230000,
        'caracteristiques' => ['Rive droite', 'Investissement', 'Nature', 'Abordable'],
        'population' => '~22 000 habitants',
        'transports' => 'Tram A, Bus, Rocade, Pont d\'Aquitaine',
        'attractivite' => 'Moyenne',
        'coords' => '44.8700,-0.5250',
        'tendance' => '+6.1%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Floirac',
        'description' => "Commune rive droite en mutation grâce au projet Garonne Eiffel. Floirac offre des vues sur Bordeaux, des prix accessibles et de nombreux programmes immobiliers neufs. Bon potentiel d'investissement.",
        'prix_m2' => 3000,
        'prix_moyen' => 250000,
        'caracteristiques' => ['Rive droite', 'Neuf', 'Vue Garonne', 'Investissement'],
        'population' => '~17 000 habitants',
        'transports' => 'Tram A, Bus, Rocade',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.8350,-0.5250',
        'tendance' => '+5.7%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Bruges',
        'description' => "Commune résidentielle au nord de Bordeaux, Bruges offre un bon rapport qualité-prix avec ses quartiers pavillonnaires, la proximité du lac et des zones commerciales. Accès rapide à la rocade.",
        'prix_m2' => 3400,
        'prix_moyen' => 320000,
        'caracteristiques' => ['Résidentiel', 'Lac', 'Commerces', 'Rocade'],
        'population' => '~18 000 habitants',
        'transports' => 'Tram C, Bus, Rocade',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.8780,-0.6050',
        'tendance' => '+3.5%',
        'zone' => 'Métropole',
    ],
    [
        'nom' => 'Blanquefort',
        'description' => "Commune viticole et résidentielle au nord de la métropole, Blanquefort offre un cadre semi-rural avec vignobles, parcs et prix modérés. Idéale pour les familles cherchant de l'espace.",
        'prix_m2' => 3300,
        'prix_moyen' => 340000,
        'caracteristiques' => ['Vignobles', 'Résidentiel', 'Nature', 'Familles'],
        'population' => '~16 000 habitants',
        'transports' => 'Bus, TER, Rocade',
        'attractivite' => 'Moyenne à haute',
        'coords' => '44.9117,-0.6367',
        'tendance' => '+2.7%',
        'zone' => 'Métropole',
    ],
];

// Séparer quartiers Bordeaux et Métropole pour l'affichage
$quartiersBordeaux = array_filter($quartiers, fn($q) => $q['zone'] === 'Bordeaux');
$quartiersMetropole = array_filter($quartiers, fn($q) => $q['zone'] === 'Métropole');
?>

<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="eyebrow">
        <i class="fas fa-map-marked-alt"></i> Quartiers de Bordeaux
      </p>
      <h1>Prix immobilier par quartier à Bordeaux et Métropole</h1>
      <p class="lead">
        Comparez les prix au m², les tendances du marché et les atouts de chaque quartier de Bordeaux et des communes de la métropole bordelaise pour affiner votre estimation immobilière.
      </p>
      <p style="font-size: var(--size-sm); color: var(--text-muted); margin-top: var(--space-2);">
        <i class="fas fa-database"></i> Données basées sur les transactions immobilières récentes en Gironde &mdash; Sources : DVF, bases notariales, observatoires locaux.
      </p>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- CARTE INTERACTIVE -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-map-pin"></i> Carte Interactive
      </p>
      <h2>Visualisez les quartiers sur la carte</h2>
    </div>

    <div class="card">
      <p class="quartier-card-description" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
        <i class="fas fa-info-circle"></i> Cliquez sur un quartier pour centrer la carte et découvrir ses caractéristiques.
      </p>

      <div class="quartier-card-badges-list" style="margin-bottom: 1.5rem;">
        <?php foreach ($quartiers as $index => $quartier): ?>
          <button
            type="button"
            class="btn btn-outline quartier-map-btn"
            data-nom="<?= htmlspecialchars($quartier['nom']); ?>"
            data-coords="<?= htmlspecialchars($quartier['coords']); ?>"
            data-zoom="15"
            data-index="<?= $index; ?>"
          >
            <i class="fas fa-location-dot"></i> <?= htmlspecialchars($quartier['nom']); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <iframe
        id="google-map-quartiers"
        title="Carte des quartiers de Bordeaux"
        src="https://maps.google.com/maps?q=44.8378,-0.5792&z=13&output=embed"
        width="100%"
        height="480"
        style="border: 0; border-radius: 14px; display: block;"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- GRILLE QUARTIERS AVEC STATS -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-chart-bar"></i> Détails par Quartier
      </p>
      <h2>Prix et caractéristiques clés</h2>
    </div>

    <!-- Quartiers de Bordeaux -->
    <h3 class="quartier-zone-title">
      <i class="fas fa-city"></i> Quartiers de Bordeaux
    </h3>
    <div class="quartier-grid">
      <?php foreach ($quartiersBordeaux as $index => $quartier): ?>
        <article class="card quartier-card" data-quartier="<?= htmlspecialchars($quartier['nom']); ?>">
          <div class="quartier-card-header">
            <div>
              <h3><?= htmlspecialchars($quartier['nom']); ?></h3>
              <p class="quartier-card-population">
                <i class="fas fa-users"></i> <?= htmlspecialchars($quartier['population']); ?>
              </p>
            </div>
            <div class="quartier-card-price">
              <p class="quartier-card-price-value">
                <?= number_format((int) $quartier['prix_m2'], 0, ',', ' '); ?> €/m²
              </p>
              <p class="quartier-card-price-trend">
                <i class="fas fa-arrow-trend-up"></i> <?= htmlspecialchars($quartier['tendance']); ?>
              </p>
            </div>
          </div>

          <p class="quartier-card-description">
            <?= htmlspecialchars($quartier['description']); ?>
          </p>

          <div class="quartier-card-estimated">
            <p class="quartier-card-estimated-label">Prix moyen estimé</p>
            <p class="quartier-card-estimated-value">
              <?= number_format((int) $quartier['prix_moyen'], 0, ',', ' '); ?> €
            </p>
          </div>

          <div class="quartier-card-badges">
            <p class="quartier-card-badges-label">
              <i class="fas fa-check-circle"></i> Caractéristiques
            </p>
            <div class="quartier-card-badges-list">
              <?php foreach ($quartier['caracteristiques'] as $caracteristique): ?>
                <span class="badge badge-primary">
                  <?= htmlspecialchars($caracteristique); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="quartier-card-info">
            <div class="quartier-card-info-row">
              <span class="quartier-card-info-label">
                <i class="fas fa-bus"></i> Transports
              </span>
              <span class="quartier-card-info-value">
                <?= htmlspecialchars($quartier['transports']); ?>
              </span>
            </div>
            <div class="quartier-card-info-row">
              <span class="quartier-card-info-label">
                <i class="fas fa-star"></i> Attractivité
              </span>
              <span class="quartier-card-info-value">
                <?= htmlspecialchars($quartier['attractivite']); ?>
              </span>
            </div>
          </div>

          <a href="/estimation#form-estimation" class="btn btn-primary full-width">
            <i class="fas fa-calculator"></i> Estimer mon bien ici
          </a>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Communes de la Métropole -->
    <h3 class="quartier-zone-title">
      <i class="fas fa-map-marked-alt"></i> Communes de la Métropole
    </h3>
    <div class="quartier-grid">
      <?php foreach ($quartiersMetropole as $index => $quartier): ?>
        <article class="card quartier-card" data-quartier="<?= htmlspecialchars($quartier['nom']); ?>">
          <div class="quartier-card-header">
            <div>
              <h3><?= htmlspecialchars($quartier['nom']); ?></h3>
              <p class="quartier-card-population">
                <i class="fas fa-users"></i> <?= htmlspecialchars($quartier['population']); ?>
              </p>
            </div>
            <div class="quartier-card-price">
              <p class="quartier-card-price-value">
                <?= number_format((int) $quartier['prix_m2'], 0, ',', ' '); ?> €/m²
              </p>
              <p class="quartier-card-price-trend">
                <i class="fas fa-arrow-trend-up"></i> <?= htmlspecialchars($quartier['tendance']); ?>
              </p>
            </div>
          </div>

          <p class="quartier-card-description">
            <?= htmlspecialchars($quartier['description']); ?>
          </p>

          <div class="quartier-card-estimated">
            <p class="quartier-card-estimated-label">Prix moyen estimé</p>
            <p class="quartier-card-estimated-value">
              <?= number_format((int) $quartier['prix_moyen'], 0, ',', ' '); ?> €
            </p>
          </div>

          <div class="quartier-card-badges">
            <p class="quartier-card-badges-label">
              <i class="fas fa-check-circle"></i> Caractéristiques
            </p>
            <div class="quartier-card-badges-list">
              <?php foreach ($quartier['caracteristiques'] as $caracteristique): ?>
                <span class="badge badge-primary">
                  <?= htmlspecialchars($caracteristique); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="quartier-card-info">
            <div class="quartier-card-info-row">
              <span class="quartier-card-info-label">
                <i class="fas fa-bus"></i> Transports
              </span>
              <span class="quartier-card-info-value">
                <?= htmlspecialchars($quartier['transports']); ?>
              </span>
            </div>
            <div class="quartier-card-info-row">
              <span class="quartier-card-info-label">
                <i class="fas fa-star"></i> Attractivité
              </span>
              <span class="quartier-card-info-value">
                <?= htmlspecialchars($quartier['attractivite']); ?>
              </span>
            </div>
          </div>

          <a href="/estimation#form-estimation" class="btn btn-primary full-width">
            <i class="fas fa-calculator"></i> Estimer mon bien ici
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- COMPARATIF PRIX -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-chart-line"></i> Comparatif des Prix
      </p>
      <h2>Évolution des prix au m² par quartier</h2>
    </div>

    <div class="card" style="overflow-x: auto;">
      <table class="quartier-table">
        <thead>
          <tr>
            <th>Quartier</th>
            <th>Prix/m²</th>
            <th>Prix Moyen</th>
            <th>Tendance</th>
            <th>Dynamisme</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($quartiers as $quartier):
            $prix_m2 = (int) $quartier['prix_m2'];
            $prix_moyen = (int) $quartier['prix_moyen'];
            $tendance = $quartier['tendance'];
            $dynamisme = match(true) {
              str_contains($quartier['attractivite'], 'Très haute') => '★★★★★',
              str_contains($quartier['attractivite'], 'Haute') => '★★★★',
              str_contains($quartier['attractivite'], 'Moyenne à haute') => '★★★★',
              default => '★★★'
            };
          ?>
            <tr>
              <td><?= htmlspecialchars($quartier['nom']); ?></td>
              <td><?= number_format($prix_m2, 0, ',', ' '); ?> €</td>
              <td><?= number_format($prix_moyen, 0, ',', ' '); ?> €</td>
              <td>
                <span class="quartier-table-trend">
                  <?= htmlspecialchars($tendance); ?>
                </span>
              </td>
              <td>
                <span class="quartier-table-stars"><?= $dynamisme; ?></span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- GALERIE PHOTOS -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-image"></i> Galerie Visuelle
      </p>
      <h2>Ambiances et paysages de Bordeaux</h2>
    </div>

    <div class="quartier-gallery">
      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1560969184-10fe8719e047?auto=format&fit=crop&w=500&q=80"
            alt="Quartier des Chartrons à Bordeaux"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-wine-glass-alt"></i> Chartrons
        </figcaption>
      </figure>

      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Saint-Pierre Bordeaux"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-landmark"></i> Saint-Pierre
        </figcaption>
      </figure>

      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Saint-Michel Bordeaux"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-church"></i> Saint-Michel
        </figcaption>
      </figure>

      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=500&q=80"
            alt="Quartier résidentiel Caudéran"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-home"></i> Caudéran
        </figcaption>
      </figure>

      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Bastide - rive droite"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-water"></i> Bastide
        </figcaption>
      </figure>

      <figure>
        <div class="quartier-gallery-img">
          <img
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&q=80"
            alt="Quartier Mériadeck"
            loading="lazy"
          >
        </div>
        <figcaption>
          <i class="fas fa-building"></i> Mériadeck
        </figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- ================================================ -->
<!-- FAQ QUARTIERS -->
<!-- ================================================ -->
<section class="section section-alt">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-question-circle"></i> Questions Fréquentes
      </p>
      <h2>Vos questions sur les quartiers</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel est le quartier le plus dynamique ?
        </h3>
        <p>
          La Bastide affiche la tendance la plus forte (+7.3%) grâce aux projets urbains majeurs (Darwin, Euratlantique). Saint-Michel suit avec +6.2% porté par la rénovation du quartier.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel quartier pour une famille ?
        </h3>
        <p>
          Caudéran est le quartier familial par excellence avec ses maisons avec jardin, ses écoles réputées et son ambiance résidentielle calme. Les Chartrons offrent aussi un excellent cadre de vie.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Où trouver le meilleur investissement ?
        </h3>
        <p>
          La Bastide et Saint-Michel combinent des prix encore accessibles avec de fortes perspectives de plus-value grâce aux projets de rénovation urbaine en cours.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Quel quartier offre le meilleur rapport qualité/prix ?
        </h3>
        <p>
          Mériadeck et Saint-Michel proposent des prix au m² plus abordables tout en restant très centraux. Idéal pour les primo-accédants souhaitant rester intra-rocade.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Les prix varient-ils beaucoup d'un quartier à l'autre ?
        </h3>
        <p>
          Oui, de 2 800 €/m² (Lormont) à 6 200 €/m² (Triangle d'Or) en incluant la métropole. L'écart reflète la centralité, le patrimoine architectural et la demande. Bordeaux reste attractif comparé aux métropoles similaires.
        </p>
      </article>

      <article class="card faq-card">
        <h3>
          <i class="fas fa-question-circle"></i> Comment choisir son quartier pour vendre ?
        </h3>
        <p>
          Votre bien s'adapte à un profil de client. Utilisez notre estimation pour connaître le prix du marché, puis explorez les tendances de votre quartier pour fixer le bon prix de vente.
        </p>
      </article>
    </div>
  </div>
</section>

<!-- Schema.org FAQPage -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Quel est le quartier le plus dynamique de Bordeaux ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "La Bastide affiche la tendance la plus forte (+7.3%) grâce aux projets urbains majeurs (Darwin, Euratlantique). Saint-Michel suit avec +6.2% porté par la rénovation du quartier."
      }
    },
    {
      "@type": "Question",
      "name": "Quel quartier de Bordeaux choisir pour une famille ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Caudéran est le quartier familial par excellence avec ses maisons avec jardin, ses écoles réputées et son ambiance résidentielle calme. Les Chartrons offrent aussi un excellent cadre de vie."
      }
    },
    {
      "@type": "Question",
      "name": "Où trouver le meilleur investissement immobilier à Bordeaux ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "La Bastide et Saint-Michel combinent des prix encore accessibles avec de fortes perspectives de plus-value grâce aux projets de rénovation urbaine en cours."
      }
    },
    {
      "@type": "Question",
      "name": "Quel quartier de Bordeaux offre le meilleur rapport qualité/prix ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Mériadeck et Saint-Michel proposent des prix au m² plus abordables tout en restant très centraux. Idéal pour les primo-accédants souhaitant rester intra-rocade."
      }
    },
    {
      "@type": "Question",
      "name": "Les prix immobiliers varient-ils beaucoup d'un quartier à l'autre à Bordeaux ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Oui, de 2 800 €/m² (Lormont) à 6 200 €/m² (Triangle d'Or). L'écart reflète la centralité, le patrimoine architectural et la demande. Bordeaux reste attractif comparé aux métropoles similaires."
      }
    },
    {
      "@type": "Question",
      "name": "Comment choisir son quartier pour vendre à Bordeaux ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Votre bien s'adapte à un profil de client. Utilisez notre estimation pour connaître le prix du marché, puis explorez les tendances de votre quartier pour fixer le bon prix de vente."
      }
    }
  ]
}
</script>

<!-- ================================================ -->
<!-- CTA FINAL -->
<!-- ================================================ -->
<section class="section">
  <div class="container">
    <div class="cta-final card">
      <p class="eyebrow">
        <i class="fas fa-lightbulb"></i> Prêt à connaître la valeur de votre bien ?
      </p>
      <h2>Estimez votre propriété dès maintenant</h2>
      <p class="lead">
        Quel que soit votre quartier, notre outil vous donne une estimation fiable et précise en quelques secondes.
      </p>
      <a href="/estimation#form-estimation" class="btn btn-primary">
        <i class="fas fa-calculator"></i> Commencer une estimation
      </a>
    </div>
  </div>
</section>

<script>
  (function () {
    const mapIframe = document.getElementById('google-map-quartiers');
    const buttons = document.querySelectorAll('.quartier-map-btn');

    if (!mapIframe || !buttons.length) {
      return;
    }

    buttons.forEach((button) => {
      button.addEventListener('click', () => {
        const coords = button.getAttribute('data-coords');
        const zoom = button.getAttribute('data-zoom') || '15';
        const nom = button.getAttribute('data-nom');

        if (!coords) {
          return;
        }

        // Update map
        mapIframe.setAttribute('src', `https://maps.google.com/maps?q=${coords}&z=${zoom}&output=embed`);

        // Update button states
        buttons.forEach((btn) => btn.classList.remove('active'));
        button.classList.add('active');

        // Smooth scroll to map
        mapIframe.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      });
    });

    // Set first button as active on load
    if (buttons.length > 0) {
      buttons[0].classList.add('active');
    }
  })();
</script>
