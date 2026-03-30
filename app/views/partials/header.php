<?php
$branding = getBrandingConfig();
$siteName = trim((string) ($branding['site_name'] ?? 'Estimation Immobilière'));
if ($siteName === '') {
    $siteName = 'Estimation Immobilière';
}
?>
<header class="premium-header">
  <div class="container premium-header__inner">
    <a href="/" class="premium-brand"><?= e($siteName) ?></a>
    <button class="premium-nav-toggle" type="button" aria-expanded="false" aria-label="Ouvrir le menu" data-nav-toggle>
      <span></span><span></span><span></span>
    </button>
    <nav class="premium-nav" data-nav-menu>
      <a href="/">Accueil</a>
      <a href="/estimation">Estimation</a>
      <a href="/financement">Financement</a>
      <a href="/villes">Villes</a>
      <a href="/blog">Blog</a>
      <a href="/contact">Contact</a>
      <a href="/estimation#form-estimation" class="btn btn-gold">Estimer mon bien</a>
    </nav>
  </div>
</header>
