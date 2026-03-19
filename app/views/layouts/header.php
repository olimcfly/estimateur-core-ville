<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?= e((string) ($metaDescription ?? 'Obtenez une estimation immobilière instantanée à Bordeaux avec une interface premium et un accompagnement professionnel.')) ?>">
  <title><?= e((string) ($metaTitle ?? 'Estimateur Immobilier Bordeaux | Estimation fiable et rapide')) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrapper">
    <a href="/" class="brand">Bordeaux<span>Estimate</span></a>

    <nav class="top-nav" aria-label="Navigation principale">
      <div class="nav-item has-dropdown">
        <a href="/estimation" class="nav-link">Estimation</a>
        <ul class="dropdown-menu" aria-label="Sous-menu estimation">
          <li><a href="/estimation#form-estimation">Estimer mon bien</a></li>
          <li><a href="/estimation#example-result">Voir un exemple</a></li>
          <li><a href="/estimation#how-it-works">Comment ça marche</a></li>
          <li><a href="/estimation#faq">FAQ Estimation</a></li>
        </ul>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/blog" class="nav-link">Blog</a>
        <ul class="dropdown-menu" aria-label="Sous-menu blog">
          <li><a href="/blog">Tous les articles</a></li>
          <li><a href="/blog?cat=vendre">Vendre son bien</a></li>
          <li><a href="/blog?cat=marche">Marché immobilier</a></li>
          <li><a href="/blog?cat=conseil">Conseils &amp; astuces</a></li>
          <li><a href="/blog?cat=legal">Aspect juridique</a></li>
        </ul>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/services" class="nav-link">Services</a>
        <ul class="dropdown-menu" aria-label="Sous-menu services">
          <li><a href="/services#estimation-detaillee">Estimation détaillée</a></li>
          <li><a href="/services#accompagnement">Accompagnement</a></li>
          <li><a href="/services#conseil-immobilier">Conseil immobilier</a></li>
          <li><a href="/services#marketing-immobilier">Marketing immobilier</a></li>
        </ul>
      </div>

      <a href="/about" class="nav-link">À propos</a>
      <a href="/contact" class="nav-link">Contact</a>

      <div class="nav-item has-dropdown">
        <a href="/guides" class="nav-link">Ressources</a>
        <ul class="dropdown-menu" aria-label="Sous-menu ressources">
          <li><a href="/guides">Guides complets</a></li>
          <li><a href="/tools/calculatrice">Calculatrice prix</a></li>
          <li><a href="/quartiers">Quartiers Bordeaux</a></li>
          <li><a href="/podcast">Podcast immobilier</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
        </ul>
      </div>
    </nav>

    <a href="/estimation#form-estimation" class="btn btn-small">Estimer mon bien</a>
  </div>
</header>

<main>
