<?php
$_contactEmail = \App\Core\Config::get('mail.admin_email') ?: \App\Core\Config::get('mail.from') ?: ('contact@' . (site('domain', '') ?: 'example.test'));
$_siteAddress  = site('city', '') !== '' ? (site('city', '') . ', France') : 'France';
$_siteName     = 'Estimation Immobilier ' . site('city', '');
?>
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-gavel"></i> Informations légales</p>
      <h1>Mentions légales</h1>
      <p class="lead">Informations éditoriales et juridiques relatives au site <?= htmlspecialchars($_siteName, ENT_QUOTES, 'UTF-8') ?>.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container card" style="display: grid; gap: 1.25rem;">
    <h2>Éditeur du site</h2>
    <p><?= htmlspecialchars($_siteName, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($_siteAddress, ENT_QUOTES, 'UTF-8') ?>.</p>

    <h2>Directeur de publication</h2>
    <p>Direction <?= htmlspecialchars($_siteName, ENT_QUOTES, 'UTF-8') ?>.</p>

    <h2>Hébergement</h2>
    <p>Hébergement technique assuré par un prestataire cloud situé dans l'Union européenne.</p>

    <h2>Contact</h2>
    <p>Email : <a href="mailto:<?= htmlspecialchars($_contactEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($_contactEmail, ENT_QUOTES, 'UTF-8') ?></a>.</p>
  </div>
</section>
