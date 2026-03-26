<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

$config = getSiteConfig(); // Appel unique

$ville = (string) ($config['ville'] ?? '');

$pageMeta = [
    'title' => strtr((string) ($config['meta']['title_estimation'] ?? ''), ['{ville}' => $ville]),
    'description' => strtr((string) ($config['meta']['description_estimation'] ?? ''), ['{ville}' => $ville]),
];

$assets = [
    'css' => (string) ($config['assets']['estimation_css'] ?? '/front/css/estimation.css'),
    'js' => (string) ($config['assets']['estimation_js'] ?? '/front/js/estimation.js'),
];

$sections = [
    'hero' => $config['estimation']['hero'] ?? [],
    'form' => $config['estimation']['form'] ?? [],
    'result' => $config['estimation']['result'] ?? [],
    'trust' => $config['estimation']['confiance'] ?? [],
    'faq' => $config['estimation']['faq'] ?? [],
    'cta' => $config['estimation']['cta_final'] ?? [],
    'popup' => $config['estimation']['lead_popup'] ?? [],
];

$endpoints = [
    'lead' => (string) ($config['api']['lead_endpoint'] ?? '/api/leads.php'),
];

require __DIR__ . '/../partials/head.php';
?>
<link rel="stylesheet" href="<?= htmlspecialchars($assets['css'], ENT_QUOTES, 'UTF-8') ?>">

<main id="page-estimation" data-api-lead="<?= htmlspecialchars($endpoints['lead'], ENT_QUOTES, 'UTF-8') ?>">
  <!-- HERO -->
  <?php require __DIR__ . '/../sections/estimation/hero.php'; ?>

  <!-- FORMULAIRE -->
  <?php require __DIR__ . '/../sections/estimation/formulaire.php'; ?>

  <!-- RÉSULTAT (hidden) -->
  <section id="estimation-result" hidden aria-live="polite">
    <?php require __DIR__ . '/../sections/estimation/resultat.php'; ?>
  </section>

  <!-- CONFIANCE -->
  <?php require __DIR__ . '/../sections/estimation/confiance.php'; ?>

  <!-- FAQ -->
  <?php require __DIR__ . '/../sections/estimation/faq.php'; ?>

  <!-- CTA FINAL -->
  <?php require __DIR__ . '/../sections/estimation/cta-final.php'; ?>
</main>

<!-- POPUP LEAD -->
<aside id="lead-popup" class="lead-popup" hidden>
  <?php require __DIR__ . '/../sections/estimation/popup-lead.php'; ?>
</aside>

<script>
  window.ESTIMATION_CONFIG = {
    ville: <?= json_encode($ville, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
    endpoints: {
      lead: <?= json_encode($endpoints['lead'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    },
    popup: <?= json_encode($sections['popup'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
  };
</script>
<script src="<?= htmlspecialchars($assets['js'], ENT_QUOTES, 'UTF-8') ?>" defer></script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
