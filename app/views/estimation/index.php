<?php
$cityName = (string) (\App\Core\Config::get('city.name', '') ?: 'votre ville');
$page_title = 'Estimation Immobilière ' . $cityName . ' - Avis de Valeur Indicatif Gratuit';
$meta_description = 'Obtenez une fourchette de prix indicative gratuite pour votre bien immobilier à ' . $cityName . ' en 60 secondes. 3 informations suffisent. 100% gratuit, sans engagement.';
$estimationContext = isset($estimationContext) && is_array($estimationContext) ? $estimationContext : getEstimationContext();
$contextJson = json_encode($estimationContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($contextJson === false) {
  $contextJson = '{}';
}
$quartiers = isset($estimationContext['quartiers']) && is_array($estimationContext['quartiers']) ? $estimationContext['quartiers'] : [];
$quartiersCount = count($quartiers);
$zoneLabel = $quartiersCount > 0 ? ($quartiersCount . ' quartiers couverts') : ('Zone locale : ' . $cityName);
$localProofLine = $quartiersCount > 0
  ? 'Données locales sur ' . $quartiersCount . ' quartiers'
  : 'Données locales de marché vérifiées';

$page_title = 'Estimation vendeur à ' . $cityName . ' | Avis de valeur indicatif + accompagnement local';
$meta_description = 'Recevez une estimation vendeur à ' . $cityName . ' en 60 secondes. Première fourchette indicative gratuite, puis avis de valeur par conseiller local si besoin.';
$socialProof = function_exists('getSocialProofConfig') ? getSocialProofConfig() : [];
?>

<!-- ============================================ -->
<!-- HERO + FORMULAIRE SIMPLE -->
<!-- ============================================ -->
<section class="hero">
  <div class="container hero-grid">
    <!-- COLONNE 1: HEADLINE -->
    <div>
      <p class="eyebrow"><i class="fas fa-chart-line"></i> Estimation vendeur locale</p>
      <h1>Vendez au bon prix à <?= e($cityName) ?> : estimation instantanée + avis de valeur possible</h1>

      <p class="lead">
        Première fourchette de prix en moins d'une minute pour préparer votre vente.
        Ensuite, vous pouvez demander un avis de valeur détaillé avec un conseiller local.
      </p>

      <ul class="trust-list">
        <li>
          <i class="fas fa-bolt"></i>
          <strong>3 champs</strong> — Résultat immédiat
        </li>
        <li>
          <i class="fas fa-hand-holding-usd"></i>
          <strong>100% gratuit</strong> — Sans engagement
        </li>
        <li>
          <i class="fas fa-shield-alt"></i>
          <strong>Données sécurisées</strong> — RGPD conforme
        </li>
      </ul>
      <p class="est-inline-local-proof"><i class="fas fa-map-marker-alt"></i> <?= e($localProofLine) ?> · <?= e($zoneLabel) ?></p>

      <div class="est-social-proof" aria-label="Preuves sociales">
        <?php if (!empty($socialProof['google_reviews_count'])): ?>
          <span class="est-social-proof__item">
            <i class="fas fa-star"></i>
            <?= e((string) $socialProof['google_reviews_count']) ?> avis<?= !empty($socialProof['google_rating']) ? ' · ' . e((string) $socialProof['google_rating']) . '/5' : '' ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($socialProof['clients_supported'])): ?>
          <span class="est-social-proof__item">
            <i class="fas fa-users"></i>
            <?= e((string) $socialProof['clients_supported']) ?> vendeurs accompagnés
          </span>
        <?php endif; ?>
        <span class="est-social-proof__item">
          <i class="fas fa-clock"></i>
          Réponse moyenne <?= e((string) ((int) ($socialProof['avg_delay_hours'] ?? 24))) ?>h
        </span>
        <span class="est-social-proof__item">
          <i class="fas fa-location-dot"></i>
          <?= e((string) ($socialProof['local_support_label'] ?? ('Accompagnement local à ' . $cityName))) ?>
        </span>
      </div>

      <!-- SOCIAL PROOF -->
      <div class="est-inline-testimonial">
        <p class="est-inline-testimonial-label">
          <i class="fas fa-quote-left"></i> Témoignage client
        </p>
        <p class="est-inline-testimonial-quote">
          "L'avis de valeur était très proche de l'offre reçue. Recommandé pour avoir une estimation fiable avant de vendre !"
        </p>
        <p class="est-inline-testimonial-author">
          — Marie D. • <?= htmlspecialchars((string) site('city', ''), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <!-- CTA BUTTONS -->
      <div class="hero-actions">
        <a href="#form-estimation" class="btn btn-primary">
          <i class="fas fa-bolt"></i> Estimer gratuitement
        </a>
        <a href="#how-it-works" class="btn btn-ghost">
          <i class="fas fa-info-circle"></i> Comment ça marche
        </a>
      </div>
    </div>

    <!-- COLONNE 2: FORMULAIRE -->
    <aside class="hero-panel card" id="form-estimation">
      <div class="panel-header">
        <h2>
          <i class="fas fa-calculator"></i> Votre avis de valeur gratuit
        </h2>
        <p class="muted">Remplissez ces 3 informations pour obtenir une fourchette de prix.</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="est-inline-alert">
          <?php foreach ($errors as $error): ?>
            <p class="est-inline-alert-text"><i class="fas fa-exclamation-circle"></i> <?= e((string) $error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form action="/estimation" method="post" class="form-grid">
        <!-- CHAMP 1: TYPE DE BIEN -->
        <label for="property_type">
          <span><i class="fas fa-home"></i> Type de bien</span>
          <select id="property_type" name="type_bien" required>
            <option value="">-- Sélectionner --</option>
            <option value="appartement">Appartement</option>
            <option value="maison">Maison / Villa</option>
            <option value="studio">Studio</option>
            <option value="loft">Loft</option>
            <option value="maison de ville">Maison de ville</option>
          </select>
        </label>

        <!-- CHAMP 2: SUPERFICIE -->
        <label for="surface">
          <span><i class="fas fa-ruler-combined"></i> Superficie (m²)</span>
          <input
            type="number"
            id="surface"
            name="surface"
            min="10"
            max="500"
            step="1"
            placeholder="Ex: 75"
            required
          >
        </label>

        <!-- CHAMP 3: LOCALITÉ -->
        <label for="ville">
          <span><i class="fas fa-map-marker-alt"></i> Localité</span>
          <input
            type="text"
            id="ville"
            name="ville"
            placeholder="<?= htmlspecialchars((string) site('city', 'Votre ville'), ENT_QUOTES, 'UTF-8') ?>..."
            required
            autocomplete="off"
          >
        </label>

        <?php if (!empty($estimationContext['quartiers']) && is_array($estimationContext['quartiers'])): ?>
          <label for="quartier">
            <span><i class="fas fa-city"></i> Quartier (optionnel)</span>
            <select id="quartier" name="quartier">
              <option value="">-- Sélectionner un quartier --</option>
              <?php foreach ($estimationContext['quartiers'] as $quartier): ?>
                <option value="<?= e((string) $quartier) ?>"><?= e((string) $quartier) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        <?php endif; ?>

        <!-- BOUTON -->
        <button type="submit" class="btn btn-primary est-inline-btn-block">
          <i class="fas fa-bolt"></i> Obtenir mon estimation gratuite
        </button>

        <p class="form-footer est-inline-form-footer">
          <i class="fas fa-lock"></i> Aucune donnée personnelle requise
        </p>
      </form>

      <div class="est-inline-benefits">
        <ul class="est-inline-benefits-list">
          <li class="est-inline-benefits-item">
            <i class="fas fa-check-circle est-inline-benefits-icon"></i>
            <span><strong>100% gratuit</strong> — aucun frais caché</span>
          </li>
          <li class="est-inline-benefits-item">
            <i class="fas fa-check-circle est-inline-benefits-icon"></i>
            <span><strong>Résultat immédiat</strong> — en moins d'1 minute</span>
          </li>
          <li class="est-inline-benefits-item">
            <i class="fas fa-check-circle est-inline-benefits-icon"></i>
            <span><strong>Données réelles</strong> — 5000+ transactions locales</span>
          </li>
          <li class="est-inline-benefits-item est-inline-benefits-item--last">
            <i class="fas fa-check-circle est-inline-benefits-icon"></i>
            <span><strong>Sans engagement</strong> — aucune obligation</span>
          </li>
        </ul>

        <a href="#form-estimation" class="btn btn-primary est-inline-btn-block est-inline-btn-block--lg" onclick="document.getElementById('property_type').focus(); return false;">
          <i class="fas fa-bolt"></i> Lancer mon estimation gratuite
        </a>

        <p class="est-inline-text-centered-muted">
          <i class="fas fa-lock"></i> Données sécurisées & conformes RGPD
        </p>
      </div>
    </aside>
  </div>
</section>

<section class="section section-alt est-inline-legal-reassurance" aria-label="Cadre légal">
  <div class="container">
    <div class="card est-inline-legal-card">
      <h2>Important — cadre légal de l’estimation</h2>
      <p>
        Cette estimation est une <strong>fourchette indicative</strong> basée sur les données de marché locales.
        Elle ne remplace pas un <strong>avis de valeur professionnel</strong> après visite du bien.
      </p>
      <ul class="est-inline-legal-list">
        <li><i class="fas fa-check-circle"></i> Résultat instantané pour cadrer votre projet de vente.</li>
        <li><i class="fas fa-check-circle"></i> Aucun engagement, aucune obligation de mandat.</li>
        <li><i class="fas fa-check-circle"></i> Possibilité d’échange avec un conseiller local ensuite.</li>
      </ul>
    </div>
  </div>
</section>

<script id="estimation-context" type="application/json"><?= e($contextJson) ?></script>
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Service',
  'name' => 'Estimation vendeur immobilière',
  'areaServed' => $cityName,
  'serviceType' => 'Estimation immobilière indicative',
  'description' => 'Fourchette de prix indicative gratuite avant avis de valeur professionnel.',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
<script>
  (function () {
    var raw = document.getElementById('estimation-context');
    if (!raw) return;

    var context = {};
    try {
      context = JSON.parse(raw.textContent || '{}');
    } catch (e) {
      context = {};
    }

    window.ESTIMATION_CONTEXT = context;

    var villeInput = document.getElementById('ville');
    var quartierSelect = document.getElementById('quartier');

    if (villeInput && context.city_name && !villeInput.value) {
      villeInput.value = context.city_name;
    }

    if (!villeInput || !quartierSelect) return;

    var cityName = (context.city_name || '').trim();
    quartierSelect.addEventListener('change', function () {
      var quartier = (quartierSelect.value || '').trim();
      if (!cityName) return;
      villeInput.value = quartier ? cityName + ' - ' + quartier : cityName;
    });
  })();
</script>

<?php require __DIR__ . '/../partials/trust-block.php'; ?>

<!-- ============================================ -->
<!-- COMPRENDRE L'AVIS DE VALEUR -->
<!-- ============================================ -->
<section class="section section-alt" id="avis-de-valeur">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-gavel"></i> Ce qu'il faut savoir
      </p>
      <h2>Estimation en ligne vs. Avis de valeur réalisé par un conseiller immobilier</h2>
    </div>

    <div class="comparison-grid">

      <!-- COLONNE GAUCHE: CE QUE NOUS PROPOSONS -->
      <article class="card est-inline-comparison-card est-inline-comparison-card--accent">
        <h3 class="est-inline-comparison-title">
          <i class="fas fa-chart-bar est-inline-icon-accent"></i>
          Notre estimation en ligne
        </h3>
        <p class="est-inline-comparison-intro">
          Notre outil analyse les <strong>données statistiques du marché</strong> (transactions récentes, prix au m² par quartier, tendances)
          pour vous donner une <strong>fourchette indicative</strong> de la valeur de votre bien.
        </p>
        <ul class="est-inline-comparison-list">
          <li class="est-inline-comparison-item">
            <i class="fas fa-check est-inline-icon-success"></i>
            <span>Résultat instantané et gratuit</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-check est-inline-icon-success"></i>
            <span>Basé sur les données statistiques du marché local</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-info-circle est-inline-icon-warning"></i>
            <span>Donne une <strong>indication</strong>, pas un prix de vente garanti</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-info-circle est-inline-icon-warning"></i>
            <span>Ne prend pas en compte l'état précis du bien, les travaux, la vue, la luminosité, etc.</span>
          </li>
        </ul>
      </article>

      <!-- COLONNE DROITE: AVIS DE VALEUR DU CONSEILLER -->
      <article class="card est-inline-comparison-card est-inline-comparison-card--primary">
        <h3 class="est-inline-comparison-title">
          <i class="fas fa-user-tie est-inline-icon-primary"></i>
          L'avis de valeur d'un conseiller immobilier
        </h3>
        <p class="est-inline-comparison-intro">
          Un <strong>avis de valeur</strong> est une estimation rédigée par un <strong>professionnel de l'immobilier</strong> qui connaît le marché local.
          Il s'appuie sur une visite du bien et sur des références de ventes récentes pour proposer un prix de mise en vente cohérent.
        </p>
        <ul class="est-inline-comparison-list">
          <li class="est-inline-comparison-item">
            <i class="fas fa-certificate est-inline-icon-primary"></i>
            <span>Réalisé par un <strong>conseiller immobilier</strong> connaissant votre quartier</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-certificate est-inline-icon-primary"></i>
            <span>Visite physique du bien et analyse détaillée</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-certificate est-inline-icon-primary"></i>
            <span>Prend en compte l'état, les travaux, la situation, l'environnement et la demande sur le secteur</span>
          </li>
          <li class="est-inline-comparison-item">
            <i class="fas fa-certificate est-inline-icon-primary"></i>
            <span>Base de travail pour fixer un prix de mise en vente réaliste</span>
          </li>
        </ul>
      </article>

    </div>

    <!-- ENCART IMPORTANT -->
    <div class="card est-inline-important-note">
      <p class="est-inline-important-note-text">
        <i class="fas fa-exclamation-triangle est-inline-icon-primary"></i>
        <strong>Important :</strong> Tous les outils en ligne (y compris le nôtre) fournissent des <strong>estimations statistiques</strong> à partir de données de marché.
        Pour affiner le prix de vente de votre bien, l'idéal est de compléter cette première estimation par un <strong>avis de valeur</strong> réalisé par un conseiller immobilier
        qui se déplace chez vous et analyse votre bien dans le détail.
      </p>
    </div>

  </div>
</section>

<!-- ============================================ -->
<!-- 3 ÉTAPES -->
<!-- ============================================ -->
<section class="section" id="how-it-works">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-bolt"></i> Simple et rapide
      </p>
      <h2>Comment ça marche ?</h2>
    </div>

    <div class="steps-grid">
      <article class="card step-card">
        <div class="step-number">1</div>
        <h3>Remplissez 3 champs</h3>
        <p>Type de bien, superficie et localité. C'est tout ce dont nous avons besoin.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">2</div>
        <h3>Recevez votre fourchette</h3>
        <p>Notre moteur calcule une estimation basse, moyenne et haute basée sur les données du marché.</p>
      </article>

      <article class="card step-card">
        <div class="step-number">3</div>
        <h3>Allez plus loin</h3>
        <p>Pour une évaluation précise, demandez un avis de valeur à un conseiller immobilier.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- FAQ -->
<!-- ============================================ -->
<section class="section section-alt" id="faq">
  <div class="container">
    <div class="section-heading">
      <p class="eyebrow">
        <i class="fas fa-comments"></i> Questions fréquentes
      </p>
      <h2>Vos questions, nos réponses</h2>
    </div>

    <div class="faq-grid">
      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Cette estimation est-elle fiable ?</h3>
        <p>Notre outil donne une <strong>indication statistique</strong> basée sur les données du marché. Pour fixer un prix de mise en vente précis, il est recommandé de demander un <strong>avis de valeur</strong> à un conseiller immobilier qui visitera votre bien.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Qu'est-ce qu'un avis de valeur ?</h3>
        <p>C'est un document rédigé par un <strong>professionnel de l'immobilier</strong> (conseiller ou agent immobilier) après visite du bien. Il s'appuie sur l'analyse du marché local et sur les caractéristiques réelles de votre logement pour proposer un prix de mise en vente cohérent.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Pourquoi les outils en ligne ne suffisent pas ?</h3>
        <p>Les outils en ligne utilisent des <strong>statistiques générales</strong> (prix au m², tendances, historique des ventes). Ils ne voient pas l'état réel du bien, les travaux, la luminosité, la vue ou le voisinage. Seul un professionnel qui se rend sur place peut intégrer ces critères dans un avis de valeur.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> L'estimation en ligne est-elle gratuite ?</h3>
        <p>Oui, 100% gratuite et sans engagement. Vous obtenez une fourchette indicative en quelques secondes, sans donner vos coordonnées.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> Puis-je obtenir un avis de valeur ensuite ?</h3>
        <p>Oui ! Après votre estimation en ligne, nous vous proposons de demander un avis de valeur réalisé par un conseiller immobilier pour une évaluation complète de votre bien.</p>
      </article>

      <article class="card faq-card">
        <h3><i class="fas fa-question-circle"></i> En quoi est-ce utile alors ?</h3>
        <p>Notre outil vous donne une <strong>première indication</strong> rapide et gratuite. C'est un bon point de départ avant de faire appel à un conseiller immobilier pour un avis de valeur complet.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============================================ -->
<!-- CTA FINAL -->
<!-- ============================================ -->
<section class="section">
  <div class="container">
    <div class="card est-inline-final-cta">
      <p class="eyebrow est-inline-final-cta-eyebrow">
        <i class="fas fa-calculator"></i> Commencez maintenant
      </p>
      <h2 class="est-inline-final-cta-title">
        Obtenez votre fourchette de prix en 30 secondes
      </h2>
      <p class="lead est-inline-final-cta-lead">
        3 informations suffisent. Gratuit, sans engagement, sans inscription.
      </p>
      <a href="#form-estimation" class="btn btn-primary est-inline-final-cta-btn">
        <i class="fas fa-calculator"></i> Lancer mon estimation gratuite
      </a>
    </div>
  </div>
</section>
