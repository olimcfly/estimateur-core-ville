</main>

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
?>

<!-- ================================================ -->
<!-- FOOTER PRO -->
<!-- ================================================ -->

<!-- PRE-FOOTER CTA -->
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

<?php
  $config = $config ?? getSiteConfig();
  $siteName = (string) ($config['site_name'] ?? 'Estimation Immobilier');
  $siteLogo = (string) ($config['site_logo'] ?? '/favicon.svg');
  $city = (string) ($config['ville'] ?? 'votre ville');
  $advisorName = (string) ($config['advisor_name'] ?? 'Conseiller immobilier');
  $advisorPhone = (string) ($config['advisor_phone'] ?? '');
  $advisorEmail = (string) ($config['advisor_email'] ?? '');
  $advisorPhoto = (string) ($config['advisor_photo'] ?? '/favicon.svg');
  $services = is_array($config['footer_services'] ?? null) ? $config['footer_services'] : [];
  $resources = is_array($config['footer_resources'] ?? null) ? $config['footer_resources'] : [];
  $legalLinks = is_array($config['footer_legal'] ?? null) ? $config['footer_legal'] : [];
  $socialLinks = is_array($config['social_links'] ?? null) ? $config['social_links'] : [];
  $accentColor = (string) ($config['color_accent'] ?? '#D4AF37');
  $newsletterAction = (string) ($config['newsletter_action_url'] ?? '/api/newsletter');
  $currentYear = (int) date('Y');
?>

      <!-- COL 1: BRAND -->
      <div class="footer-column footer-col-brand">
        <a href="/" class="footer-logo-link">
          <span class="footer-logo-icon"><i class="fas fa-home"></i></span>
          <span class="footer-logo-text"><?= e($siteName) ?> <strong><?= e($areaLabel) ?></strong></span>
        </a>
        <p class="footer-desc">
          <?= e($footerTagline) ?>
        </p>
        <div class="footer-social">
          <?php if ($footerSocialLinks['facebook'] !== ''): ?>
          <a href="<?= e($footerSocialLinks['facebook']) ?>" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <?php endif; ?>
          <?php if ($footerSocialLinks['instagram'] !== ''): ?>
          <a href="<?= e($footerSocialLinks['instagram']) ?>" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
          <?php if ($footerSocialLinks['linkedin'] !== ''): ?>
          <a href="<?= e($footerSocialLinks['linkedin']) ?>" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <?php endif; ?>
          <?php if ($footerSocialLinks['x'] !== ''): ?>
          <a href="<?= e($footerSocialLinks['x']) ?>" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="social-icon"><i class="fab fa-x-twitter"></i></a>
          <?php endif; ?>
        </div>
      </div>

      <!-- COL 2: SERVICES -->
      <div class="footer-column">
        <h4 class="footer-heading">Services</h4>
        <ul class="footer-links">
          <li><a href="/#form-estimation">Estimation en ligne</a></li>
          <li><a href="/processus-estimation">Notre processus</a></li>
          <li><a href="/quartiers">Quartiers</a></li>
          <li><a href="/#how-it-works">Comment ça marche</a></li>
          <li><a href="/#example-result">Voir un exemple</a></li>
        </ul>
      </div>

      <!-- COL 3: RESSOURCES -->
      <div class="footer-column">
        <h4 class="footer-heading">Ressources</h4>
        <ul class="footer-links">
          <li><a href="/blog">Blog & actualités</a></li>
          <li><a href="/guides">Guides immobiliers</a></li>
          <li><a href="/#faq">FAQ</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
        </ul>
      </div>

      <section class="site-footer__column site-footer__column--services" data-accordion>
        <button class="site-footer__heading site-footer__accordion-toggle" type="button" aria-expanded="false">Nos services</button>
        <ul class="site-footer__links" data-accordion-panel>
          <?php foreach ($services as $item):
            $label = (string) ($item['label'] ?? 'Service');
            $url = (string) ($item['url'] ?? '#');
          ?>
            <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <!-- COL 5: CONTACT -->
      <div class="footer-column">
        <h4 class="footer-heading">Nous contacter</h4>
        <ul class="footer-contact">
          <li>
            <i class="fas fa-map-marker-alt"></i>
            <span><?= e($footerAddressLine1) ?><br><?= e($footerAddressLine2) ?></span>
          </li>
          <li>
            <a href="/contact">
              <i class="fas fa-comment-dots"></i>
              <span>Nous contacter</span>
            </a>
          </li>
          <li>
            <a href="mailto:<?= e($supportEmail) ?>">
              <i class="fas fa-envelope"></i>
              <span><?= e($supportEmail) ?></span>
            </a>
          </li>
        </ul>
      </section>

    <!-- NEWSLETTER -->
    <div class="footer-newsletter-band">
      <div class="footer-newsletter-text">
        <i class="fas fa-envelope-open-text"></i>
        <div>
          <strong>Restez informé</strong>
          <span><?= e($footerNewsletterText) ?></span>
        </div>
      </section>
    </div>
  </div>

    <!-- FOOTER BOTTOM -->
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
  // Property image alt text
  document.querySelectorAll('img[data-address][data-bedrooms]').forEach((propertyImage) => {
    const address = (propertyImage.dataset.address || '').trim();
    const bedrooms = (propertyImage.dataset.bedrooms || '').trim();
    if (!address || !bedrooms) return;
    propertyImage.alt = `${address} - ${bedrooms} pièces`;
  });

  // Mobile menu toggle
  (function() {
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.top-nav');
    if (!toggle || !nav) return;

    function closeMenu() {
      nav.classList.remove('active');
      toggle.classList.remove('active');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Ouvrir le menu');
      document.body.style.overflow = '';
      document.querySelectorAll('.has-dropdown').forEach(function(d) {
        d.classList.remove('active');
      });
    }

    toggle.addEventListener('click', function() {
      const isOpen = nav.classList.toggle('active');
      toggle.classList.toggle('active');
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Mobile dropdown toggles (touch-friendly)
    document.querySelectorAll('.has-dropdown > .nav-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (window.innerWidth < 768) {
          e.preventDefault();
          e.stopPropagation();
          var parent = this.parentElement;
          // Close other dropdowns
          document.querySelectorAll('.has-dropdown').forEach(function(d) {
            if (d !== parent) d.classList.remove('active');
          });
          parent.classList.toggle('active');
        }
      });
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth >= 768) {
        closeMenu();
      }
    });

    // Close menu when clicking a dropdown sub-link or regular nav link (mobile)
    nav.querySelectorAll('.dropdown-menu a, .nav-item:not(.has-dropdown) .nav-link, .nav-cta-mobile a').forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth < 768) {
          closeMenu();
        }
      });
    });
  })();

  // Footer accordion (mobile) + newsletter AJAX submit
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

          if (!response.ok) {
            throw new Error('Newsletter error');
          }

          form.reset();
          if (feedback) feedback.textContent = 'Merci, votre inscription est confirmée.';
        } catch (error) {
          if (feedback) feedback.textContent = 'Une erreur est survenue. Veuillez réessayer.';
        }
      });
    });

    setAccordionState();
    if (typeof mq.addEventListener === 'function') {
      mq.addEventListener('change', setAccordionState);
    } else {
      window.addEventListener('resize', setAccordionState);
    }
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
