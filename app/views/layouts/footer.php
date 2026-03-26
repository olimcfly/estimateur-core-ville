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

  // Header sticky + menu mobile
  (function() {
    const header = document.querySelector('[data-header]');
    const toggle = document.querySelector('[data-menu-toggle]');
    const overlay = document.querySelector('[data-menu-overlay]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileLinks = document.querySelectorAll('[data-menu-link]');

    if (!header) return;

    function setBodyOffset() {
      document.body.style.paddingTop = header.offsetHeight + 'px';
    }

    function onScroll() {
      header.classList.toggle('site-header--scrolled', window.scrollY > 10);
    }

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
      if (mobileMenu.classList.contains('is-open')) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    overlay.addEventListener('click', closeMenu);
    mobileLinks.forEach((link) => link.addEventListener('click', closeMenu));

    window.addEventListener('resize', function() {
      if (window.innerWidth >= 1024) {
        closeMenu();
      }
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

<!-- Admin presence: banner created dynamically via JS only when admin is active (not in DOM at crawl time) -->
<!-- Admin presence alert banner -->
<div id="admin-presence-alert" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#8B1538,#C41E3A);color:#fff;padding:1rem 2rem;font-family:'DM Sans',sans-serif;box-shadow:0 -4px 20px rgba(0,0,0,0.15);animation:slideUpPresence 0.4s ease-out;">
  <div style="max-width:900px;margin:0 auto;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:0.75rem;flex:1;min-width:200px;">
      <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-user-shield" style="font-size:1.1rem;"></i>
      </div>
      <div>
        <strong id="admin-presence-text" style="font-size:0.95rem;display:block;">L'administrateur travaille actuellement sur cette page.</strong>
        <span style="font-size:0.82rem;opacity:0.9;">Veuillez patienter ou nous contacter pour toute question.</span>
      </div>
    </div>
    <div style="display:flex;gap:0.5rem;flex-shrink:0;">
      <a href="/contact" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;background:rgba(255,255,255,0.2);color:#fff;text-decoration:none;border-radius:6px;font-size:0.85rem;font-weight:600;transition:background 0.2s;">
        <i class="fas fa-envelope"></i> Nous contacter
      </a>
      <button onclick="document.getElementById('admin-presence-alert').style.display='none'" style="background:rgba(255,255,255,0.1);border:none;color:#fff;padding:0.5rem;border-radius:6px;cursor:pointer;font-size:1rem;line-height:1;" title="Fermer">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</div>
<style>
@keyframes slideUpPresence { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
<!-- Admin presence notification: DISABLED on frontend -->
<!-- To re-enable, uncomment the banner HTML and JS below -->

<?php
  $stickyConfig = $config ?? [];
  $stickyCtaLabel = trim((string) ($stickyConfig['cta_label'] ?? 'Voir le prix de mon bien →'));
  $stickyCtaUrl = trim((string) ($stickyConfig['cta_url'] ?? '/estimation#form-estimation'));
  $stickyAccentColor = trim((string) ($stickyConfig['color_accent'] ?? '#0F766E'));
  $stickyAdvisorName = trim((string) ($stickyConfig['advisor_name'] ?? ''));
?>
<div class="sticky-cta sticky-cta--visible" style="--sticky-cta-accent: <?= e($stickyAccentColor) ?>;" aria-hidden="true">
  <div class="sticky-cta__inner">
    <div class="sticky-cta__copy" aria-hidden="true">
      <p class="sticky-cta__label">Estimation gratuite</p>
      <p class="sticky-cta__text">Gratuit · Sans engagement</p>
    </div>
    <a href="<?= e($stickyCtaUrl) ?>" class="sticky-cta__button" aria-label="<?= e($stickyAdvisorName !== '' ? 'Voir le prix de mon bien avec ' . $stickyAdvisorName : 'Voir le prix de mon bien') ?>">
      <?= e($stickyCtaLabel) ?>
    </a>
  </div>
</div>
<!-- ================================================ -->
<!-- MOBILE BOTTOM NAV (APP-LIKE)                     -->
<!-- ================================================ -->
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

<script>
(function() {
  var dismissed = false;
  var alertEl = null;

  function createBanner(name) {
    if (alertEl) return;
    var style = document.createElement('style');
    style.textContent = '@keyframes slideUpPresence{from{transform:translateY(100%);opacity:0}to{transform:translateY(0);opacity:1}}';
    document.head.appendChild(style);

    alertEl = document.createElement('div');
    alertEl.id = 'admin-presence-alert';
    alertEl.style.cssText = 'position:fixed;bottom:0;left:0;right:0;z-index:9999;background:linear-gradient(135deg,#8B1538,#C41E3A);color:#fff;padding:1rem 2rem;font-family:"DM Sans",sans-serif;box-shadow:0 -4px 20px rgba(0,0,0,0.15);animation:slideUpPresence 0.4s ease-out;';
    alertEl.innerHTML = '<div style="max-width:900px;margin:0 auto;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">'
      + '<div style="display:flex;align-items:center;gap:0.75rem;flex:1;min-width:200px;">'
      + '<div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">'
      + '<i class="fas fa-user-shield" style="font-size:1.1rem;"></i></div>'
      + '<div><strong id="admin-presence-text" style="font-size:0.95rem;display:block;"></strong>'
      + '<span style="font-size:0.82rem;opacity:0.9;">Veuillez patienter ou nous contacter pour toute question.</span></div></div>'
      + '<div style="display:flex;gap:0.5rem;flex-shrink:0;">'
      + '<a href="/contact" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;background:rgba(255,255,255,0.2);color:#fff;text-decoration:none;border-radius:6px;font-size:0.85rem;font-weight:600;transition:background 0.2s;">'
      + '<i class="fas fa-envelope"></i> Nous contacter</a>'
      + '<button style="background:rgba(255,255,255,0.1);border:none;color:#fff;padding:0.5rem;border-radius:6px;cursor:pointer;font-size:1rem;line-height:1;" title="Fermer">'
      + '<i class="fas fa-times"></i></button></div></div>';

    document.body.appendChild(alertEl);
    alertEl.querySelector('#admin-presence-text').textContent = name + ' travaille actuellement sur le site.';
    alertEl.querySelector('button').addEventListener('click', function() {
      dismissed = true;
      alertEl.remove();
      alertEl = null;
    });
  }

  function checkPresence() {
    if (dismissed) return;
    fetch('/api/presence/check', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.active) {
          var name = data.admin_name || "L'administrateur";
          if (!alertEl) {
            createBanner(name);
          } else {
            document.getElementById('admin-presence-text').textContent = name + ' travaille actuellement sur le site.';
          }
        } else if (alertEl) {
          alertEl.remove();
          alertEl = null;
        }
      })
      .catch(function() {});
  }

  checkPresence();
  setInterval(checkPresence, 45000);
})();
</script>
<!-- Admin presence JS: DISABLED -->

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
