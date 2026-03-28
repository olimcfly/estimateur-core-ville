<!-- ================================================ -->
<!-- MOBILE BOTTOM NAV (APP-LIKE)                     -->
<!-- ================================================ -->
<?php
  $contactPhone = function_exists('getContactPhone') ? getContactPhone() : ['display' => '', 'href' => ''];
  $isEstimationPage = str_starts_with(($canonicalPath ?? ''), '/estimation');
  $smartCtaHref = '/estimation#form-estimation';
  $smartCtaLabel = 'Estimation gratuite';
  $smartCtaIcon = 'fa-chart-line';

  if (!$isEstimationPage && $contactPhone['href'] !== '' && $contactPhone['display'] !== '') {
      $smartCtaHref = (string) $contactPhone['href'];
      $smartCtaLabel = 'Appeler le conseiller';
      $smartCtaIcon = 'fa-phone';
  }
?>
<a
  href="<?= e($smartCtaHref) ?>"
  class="sticky-cta"
  data-smart-cta
  aria-hidden="true"
  aria-label="<?= e($smartCtaLabel) ?>"
>
  <i class="fas <?= e($smartCtaIcon) ?>" aria-hidden="true"></i>
  <span><?= e($smartCtaLabel) ?></span>
</a>

<nav class="mobile-bottom-nav" aria-label="Navigation mobile">
  <div class="mobile-bottom-nav__inner">
    <a href="/" class="mobile-bottom-nav__item <?= ($canonicalPath ?? '/') === '/' ? 'active' : '' ?>" aria-label="Accueil">
      <i class="fas fa-home"></i>
      <span>Accueil</span>
    </a>
    <a href="/blog" class="mobile-bottom-nav__item <?= str_starts_with(($canonicalPath ?? ''), '/blog') ? 'active' : '' ?>" aria-label="Blog">
      <i class="fas fa-newspaper"></i>
      <span>Blog</span>
    </a>
    <a href="/estimation#form-estimation" class="mobile-bottom-nav__cta" aria-label="Estimer mon bien">
      <span class="mobile-bottom-nav__cta-icon"><i class="fas fa-chart-line"></i></span>
      <span>Estimer</span>
    </a>
    <a href="/services" class="mobile-bottom-nav__item <?= str_starts_with(($canonicalPath ?? ''), '/services') ? 'active' : '' ?>" aria-label="Services">
      <i class="fas fa-concierge-bell"></i>
      <span>Services</span>
    </a>
    <a href="/contact" class="mobile-bottom-nav__item <?= str_starts_with(($canonicalPath ?? ''), '/contact') ? 'active' : '' ?>" aria-label="Contact">
      <i class="fas fa-envelope"></i>
      <span>Contact</span>
    </a>
  </div>
</nav>
