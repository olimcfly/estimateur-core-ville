<?php
$branding = getBrandingConfig();
$city = trim((string) ($branding['city_name'] ?? site('city', 'votre ville')));
$siteName = trim((string) ($branding['site_name'] ?? 'Estimation Immobilière'));
$email = trim((string) ($branding['support_email'] ?? 'contact@example.test'));
?>
<footer class="premium-footer">
  <div class="container premium-footer__grid">
    <section>
      <h3><?= e($siteName) ?></h3>
      <p>Nous aidons les propriétaires vendeurs à positionner leur bien au juste prix pour vendre plus vite et plus sereinement.</p>
    </section>
    <section>
      <h4>Navigation</h4>
      <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/estimation">Estimation</a></li>
        <li><a href="/financement">Financement</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </section>
    <section>
      <h4>Villes couvertes</h4>
      <ul>
        <li><a href="/ville/toulon">Toulon</a></li>
        <li><a href="/ville/hyeres">Hyères</a></li>
        <li><a href="/ville/la-seyne-sur-mer">La Seyne-sur-Mer</a></li>
        <li><a href="/ville/sanary-sur-mer">Sanary-sur-Mer</a></li>
      </ul>
    </section>
    <section>
      <h4>Contact & légal</h4>
      <ul>
        <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
        <li><a href="/mentions-legales">Mentions légales</a></li>
        <li><a href="/politique-confidentialite">Confidentialité</a></li>
        <li><a href="/rgpd">RGPD</a></li>
      </ul>
    </section>
  </div>
  <div class="premium-footer__bottom">
    © <?= date('Y') ?> <?= e($siteName) ?> · <?= e($city) ?>
  </div>
</footer>
