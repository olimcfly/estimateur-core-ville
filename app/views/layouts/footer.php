<?php
  $branding = is_array($branding ?? null) ? $branding : getBrandingConfig();
  $settingsMap = getSettingsMap();

  $siteName = trim((string) ($branding['site_name'] ?? ''));
  if ($siteName === '') {
      $siteName = 'Estimation Immobilière';
  }

  $cityName = trim((string) ($branding['city_name'] ?? ''));
  if ($cityName === '') {
      $cityName = 'Votre ville';
  }

  $areaLabel = trim((string) ($branding['area_label'] ?? ''));
  if ($areaLabel === '') {
      $areaLabel = $cityName !== 'Votre ville' ? $cityName : 'Votre secteur';
  }

  $supportEmail = trim((string) ($branding['support_email'] ?? ''));
  if ($supportEmail === '') {
      $supportEmail = 'contact@localhost';
  }

  $footerSocialLinks = [
      'facebook' => trim((string) ($settingsMap['social_facebook_url'] ?? '')),
      'instagram' => trim((string) ($settingsMap['social_instagram_url'] ?? '')),
      'linkedin' => trim((string) ($settingsMap['social_linkedin_url'] ?? '')),
      'x' => trim((string) ($settingsMap['social_x_url'] ?? '')),
  ];

  $footerTagline = trim((string) ($settingsMap['footer_tagline'] ?? ''));
  if ($footerTagline === '') {
      $footerTagline = sprintf(
          'Votre partenaire de confiance pour l\'estimation immobilière sur %s.',
          $areaLabel
      );
  }

  $footerNewsletterText = trim((string) ($settingsMap['footer_newsletter_text'] ?? ''));
  if ($footerNewsletterText === '') {
      $footerNewsletterText = sprintf(
          'Recevez nos analyses du marché local à %s et nos conseils immobiliers.',
          $areaLabel
      );
  }

  $footerAddressLine1 = trim((string) ($settingsMap['footer_address_line_1'] ?? $cityName));
  $footerAddressLine2 = trim((string) ($settingsMap['footer_address_line_2'] ?? $areaLabel));

  $footerLegalEntity = trim((string) ($settingsMap['footer_legal_entity'] ?? ''));
  if ($footerLegalEntity === '') {
      $footerLegalEntity = 'Éditeur local';
  }

  $config = $config ?? getSiteConfig();
  $siteName = (string) ($config['site_name'] ?? $siteName);
  $services = is_array($config['footer_services'] ?? null) ? $config['footer_services'] : [];
  $resources = is_array($config['footer_resources'] ?? null) ? $config['footer_resources'] : [];
  $legalLinks = is_array($config['footer_legal'] ?? null) ? $config['footer_legal'] : [];
  $newsletterAction = (string) ($config['newsletter_action_url'] ?? '/api/newsletter');
?>

<section class="footer-cta-band">
  <div class="container">
    <div class="footer-cta-inner">
      <div class="footer-cta-text">
        <h3>Estimez votre bien immobilier à <?= e($areaLabel) ?></h3>
        <p>Algorithme IA + expertise locale pour une estimation fiable en quelques minutes.</p>
      </div>
      <a href="/#form-estimation" class="btn-footer-cta">
        <i class="fas fa-chart-line"></i> Estimer mon bien
      </a>
    </div>
  </div>
</section>

<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <section class="footer-column footer-col-brand">
        <a href="/" class="footer-logo-link">
          <span class="footer-logo-icon"><i class="fas fa-home"></i></span>
          <span class="footer-logo-text"><?= e($siteName) ?> <strong><?= e($areaLabel) ?></strong></span>
        </a>
        <p class="footer-desc"><?= e($footerTagline) ?></p>
        <div class="footer-social">
          <?php if ($footerSocialLinks['facebook'] !== ''): ?><a href="<?= e($footerSocialLinks['facebook']) ?>" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
          <?php if ($footerSocialLinks['instagram'] !== ''): ?><a href="<?= e($footerSocialLinks['instagram']) ?>" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a><?php endif; ?>
          <?php if ($footerSocialLinks['linkedin'] !== ''): ?><a href="<?= e($footerSocialLinks['linkedin']) ?>" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="social-icon"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
          <?php if ($footerSocialLinks['x'] !== ''): ?><a href="<?= e($footerSocialLinks['x']) ?>" target="_blank" rel="noopener noreferrer" title="X" class="social-icon"><i class="fab fa-x-twitter"></i></a><?php endif; ?>
        </div>
      </section>

      <section class="footer-column" data-accordion>
        <button class="footer-heading site-footer__accordion-toggle" type="button" aria-expanded="false">Services</button>
        <ul class="footer-links" data-accordion-panel>
          <li><a href="/#form-estimation">Estimation en ligne</a></li>
          <li><a href="/processus-estimation">Notre processus</a></li>
          <li><a href="/quartiers">Quartiers</a></li>
          <li><a href="/#how-it-works">Comment ça marche</a></li>
          <?php foreach ($services as $item):
            $label = (string) ($item['label'] ?? 'Service');
            $url = (string) ($item['url'] ?? '#');
          ?>
            <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="footer-column" data-accordion>
        <button class="footer-heading site-footer__accordion-toggle" type="button" aria-expanded="false">Ressources</button>
        <ul class="footer-links" data-accordion-panel>
          <li><a href="/blog">Blog & actualités</a></li>
          <li><a href="/guides">Guides immobiliers</a></li>
          <li><a href="/#faq">FAQ</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
          <?php foreach ($resources as $item):
            $label = (string) ($item['label'] ?? 'Ressource');
            $url = (string) ($item['url'] ?? '#');
          ?>
            <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="footer-column" data-accordion>
        <button class="footer-heading site-footer__accordion-toggle" type="button" aria-expanded="false">Informations</button>
        <ul class="footer-links" data-accordion-panel>
          <li><a href="/contact">Nous contacter</a></li>
          <li><a href="mailto:<?= e($supportEmail) ?>"><?= e($supportEmail) ?></a></li>
          <li><span><?= e($footerAddressLine1) ?>, <?= e($footerAddressLine2) ?></span></li>
          <?php foreach ($legalLinks as $item):
            $label = (string) ($item['label'] ?? 'Lien légal');
            $url = (string) ($item['url'] ?? '#');
          ?>
            <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>
    </div>

    <div class="footer-newsletter-band">
      <div class="footer-newsletter-text">
        <i class="fas fa-envelope-open-text"></i>
        <div>
          <strong>Restez informé</strong>
          <span><?= e($footerNewsletterText) ?></span>
        </div>
      </div>
      <form action="<?= e($newsletterAction) ?>" method="post" class="footer-newsletter-form" data-newsletter-form>
        <input type="email" name="email" placeholder="Votre e-mail" required>
        <button type="submit" class="btn-footer-cta">S'inscrire</button>
      </form>
      <p class="footer-newsletter-feedback" data-newsletter-feedback aria-live="polite"></p>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-left">
        <p>&copy; <?= date('Y') ?> <?= e($siteName) ?> &mdash; <?= e($footerLegalEntity) ?>. Tous droits réservés.</p>
      </div>
      <div class="footer-bottom-right">
        <div class="footer-trust">
          <span class="trust-badge"><i class="fas fa-lock"></i> SSL</span>
          <span class="trust-badge"><i class="fas fa-shield-alt"></i> RGPD</span>
          <span class="trust-badge"><i class="fas fa-check-circle"></i> Vérifié</span>
        </div>
        <a href="#top" class="back-to-top" aria-label="Retour en haut">
          <i class="fas fa-chevron-up"></i>
        </a>
      </div>
    </div>
  </div>
</footer>

<script>
  document.querySelectorAll('img[data-address][data-bedrooms]').forEach((propertyImage) => {
    const address = (propertyImage.dataset.address || '').trim();
    const bedrooms = (propertyImage.dataset.bedrooms || '').trim();
    if (!address || !bedrooms) return;
    propertyImage.alt = `${address} - ${bedrooms} pièces`;
  });

  (function() {
    const header = document.querySelector('[data-header]');
    const toggle = document.querySelector('[data-menu-toggle]');
    const overlay = document.querySelector('[data-menu-overlay]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileLinks = document.querySelectorAll('[data-menu-link]');

    if (!header) return;

    function setBodyOffset() { document.body.style.paddingTop = header.offsetHeight + 'px'; }
    function onScroll() { header.classList.toggle('site-header--scrolled', window.scrollY > 10); }

    setBodyOffset();
    onScroll();
    window.addEventListener('resize', setBodyOffset);
    window.addEventListener('scroll', onScroll, { passive: true });

    if (!toggle || !overlay || !mobileMenu) return;

    function closeMenu() {
      toggle.classList.remove('is-active');
      overlay.classList.remove('is-open');
      mobileMenu.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Ouvrir le menu');
      document.body.style.overflow = '';
    }

    function openMenu() {
      toggle.classList.add('is-active');
      overlay.classList.add('is-open');
      mobileMenu.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Fermer le menu');
      document.body.style.overflow = 'hidden';
    }

    toggle.addEventListener('click', function() {
      if (mobileMenu.classList.contains('is-open')) closeMenu(); else openMenu();
    });

    overlay.addEventListener('click', closeMenu);
    mobileLinks.forEach((link) => link.addEventListener('click', closeMenu));

    window.addEventListener('resize', function() {
      if (window.innerWidth >= 1024) closeMenu();
    });
  })();

  (function() {
    const mq = window.matchMedia('(max-width: 767px)');
    const accordionItems = document.querySelectorAll('[data-accordion]');
    const newsletterForms = document.querySelectorAll('[data-newsletter-form]');

    function setAccordionState() {
      accordionItems.forEach((item) => {
        const toggle = item.querySelector('.site-footer__accordion-toggle');
        const panel = item.querySelector('[data-accordion-panel]');
        if (!toggle || !panel) return;
        if (mq.matches) {
          const expanded = toggle.getAttribute('aria-expanded') === 'true';
          panel.hidden = !expanded;
          toggle.disabled = false;
        } else {
          panel.hidden = false;
          toggle.setAttribute('aria-expanded', 'true');
          toggle.disabled = true;
        }
      });
    }

    accordionItems.forEach((item) => {
      const toggle = item.querySelector('.site-footer__accordion-toggle');
      const panel = item.querySelector('[data-accordion-panel]');
      if (!toggle || !panel) return;
      toggle.addEventListener('click', () => {
        if (!mq.matches) return;
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isExpanded));
        panel.hidden = isExpanded;
      });
    });

    newsletterForms.forEach((form) => {
      const feedback = form.parentElement ? form.parentElement.querySelector('[data-newsletter-feedback]') : null;
      form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (feedback) feedback.textContent = 'Inscription en cours...';
        try {
          const response = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
          });
          if (!response.ok) throw new Error('Newsletter error');
          form.reset();
          if (feedback) feedback.textContent = 'Merci, votre inscription est confirmée.';
        } catch (error) {
          if (feedback) feedback.textContent = 'Une erreur est survenue. Veuillez réessayer.';
        }
      });
    });

    setAccordionState();
    if (typeof mq.addEventListener === 'function') mq.addEventListener('change', setAccordionState);
    else window.addEventListener('resize', setAccordionState);
  })();
</script>

<?php include __DIR__ . '/sticky-cta.php'; ?>

<script>
(function() {
  var stickyCta = document.querySelector('.sticky-cta');
  if (!stickyCta) return;

  var mobileQuery = window.matchMedia('(max-width: 767px)');
  var estimationForm = document.getElementById('form-estimation');

  function toggleSticky(forceVisible) {
    stickyCta.classList.toggle('sticky-cta--visible', !!forceVisible);
    stickyCta.setAttribute('aria-hidden', forceVisible ? 'false' : 'true');
  }

  function refreshForViewport() {
    if (!mobileQuery.matches) {
      toggleSticky(false);
      return;
    }

    if (!estimationForm) {
      toggleSticky(true);
    }
  }

  refreshForViewport();

  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', refreshForViewport);
  } else if (typeof mobileQuery.addListener === 'function') {
    mobileQuery.addListener(refreshForViewport);
  }

  if (!estimationForm || typeof IntersectionObserver !== 'function') {
    return;
  }

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (!mobileQuery.matches) {
        toggleSticky(false);
        return;
      }

      toggleSticky(!entry.isIntersecting);
    });
  }, {
    threshold: 0.1
  });

  observer.observe(estimationForm);
})();
</script>

</body>
</html>
