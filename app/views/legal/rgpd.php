<?php
$_contactEmail = \App\Core\Config::get('mail.admin_email') ?: \App\Core\Config::get('mail.from') ?: ('contact@' . (site('domain', '') ?: 'example.test'));
?>
<section class="section page-hero">
  <div class="container">
    <div class="page-hero-inner card">
      <p class="eyebrow"><i class="fas fa-shield-alt"></i> Conformité</p>
      <h1>RGPD & cookies</h1>
      <p class="lead">Informations sur le traitement de vos données et l'usage des cookies sur notre plateforme.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container card" style="display: grid; gap: 1.25rem;">
    <h2>Base légale</h2>
    <p>Le traitement repose sur l'exécution de votre demande, votre consentement (si requis), et notre intérêt légitime à améliorer nos services.</p>

    <h2>Cookies</h2>
    <p>Des cookies techniques peuvent être utilisés pour le bon fonctionnement du site. Les cookies de mesure d'audience sont paramétrés de manière respectueuse de la vie privée.</p>

    <h2>Exercer vos droits</h2>
    <p>Pour toute demande relative à vos données personnelles, contactez-nous à <a href="mailto:<?= htmlspecialchars($_contactEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($_contactEmail, ENT_QUOTES, 'UTF-8') ?></a>.</p>

    <h2>Réclamation</h2>
    <p>Vous pouvez introduire une réclamation auprès de l'autorité de contrôle compétente, notamment la CNIL (France).</p>
  </div>
</section>
