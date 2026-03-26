</main>

<!-- ================================================ -->
<!-- FOOTER PRO -->
<!-- ================================================ -->

<!-- PRE-FOOTER CTA -->
<section class="footer-cta-band">
  <div class="container">
    <div class="footer-cta-inner">
      <div class="footer-cta-text">
        <h3>Estimez votre bien immobilier à Bordeaux et sa métropole</h3>
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

<footer class="site-footer" style="--color-accent: <?= e($accentColor) ?>;">
  <div class="container">
    <div class="site-footer__grid">
      <section class="site-footer__identity">
        <a href="/" class="site-footer__brand" aria-label="<?= e($siteName) ?>">
          <img src="<?= e($siteLogo) ?>" alt="Logo <?= e($siteName) ?>" class="site-footer__logo">
          <span class="site-footer__site-name"><?= e($siteName) ?></span>
        </a>
        <p class="site-footer__tagline">Votre spécialiste de l'estimation immobilière à <?= e($city) ?></p>
        <div class="site-footer__social" aria-label="Réseaux sociaux">
          <?php foreach ($socialLinks as $social):
            $platform = (string) ($social['platform'] ?? 'Réseau social');
            $url = (string) ($social['url'] ?? '#');
            $icon = (string) ($social['icon'] ?? 'fas fa-share-alt');
            $hasInlineIcon = str_contains($icon, '<svg');
          ?>
            <a href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer" class="site-footer__social-link" aria-label="<?= e($platform) ?>">
              <?php if ($hasInlineIcon): ?>
                <?= $icon ?>
              <?php else: ?>
                <i class="<?= e($icon) ?>" aria-hidden="true"></i>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="site-footer__trust" aria-label="Badges de confiance">
          <span class="site-footer__badge"><?= '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 17a7 7 0 1 1 7-7 7 7 0 0 1-7 7Zm-1-5.5 5-5-1.4-1.4L11 10.7 9.4 9.1 8 10.5Z"/></svg>' ?>SSL</span>
          <span class="site-footer__badge"><?= '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5Zm0 17c-3-1.2-5-4.6-5-8V7.2l5-1.9 5 1.9V11c0 3.4-2 6.8-5 8Zm-1.2-4.6 4.7-4.7-1.4-1.4-3.3 3.3-1.4-1.4-1.4 1.4Z"/></svg>' ?>RGPD</span>
          <span class="site-footer__badge"><?= '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 2 7l10 15L22 7Zm-1 13-4-6 1.7-1.1 2.4 3.6 4.5-5.4L17.4 7Z"/></svg>' ?>Vérifié</span>
        </div>
      </section>

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

      <section class="site-footer__column site-footer__column--resources" data-accordion>
        <button class="site-footer__heading site-footer__accordion-toggle" type="button" aria-expanded="false">Ressources</button>
        <ul class="site-footer__links" data-accordion-panel>
          <?php foreach ($resources as $item):
            $label = (string) ($item['label'] ?? 'Ressource');
            $url = (string) ($item['url'] ?? '#');
          ?>
            <li><a href="<?= e($url) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>

      <section class="site-footer__column site-footer__column--contact" data-accordion>
        <button class="site-footer__heading site-footer__accordion-toggle" type="button" aria-expanded="false">Nous contacter</button>
        <div class="site-footer__contact" data-accordion-panel>
          <div class="site-footer__advisor">
            <img src="<?= e($advisorPhoto) ?>" alt="<?= e($advisorName) ?>" class="site-footer__advisor-photo">
            <strong><?= e($advisorName) ?></strong>
          </div>
          <?php if ($advisorPhone !== ''): ?>
            <a class="site-footer__contact-link" href="tel:<?= e(preg_replace('/\s+/', '', $advisorPhone) ?? $advisorPhone) ?>"><?= e($advisorPhone) ?></a>
          <?php endif; ?>
          <?php if ($advisorEmail !== ''): ?>
            <a class="site-footer__contact-link" href="mailto:<?= e($advisorEmail) ?>"><?= e($advisorEmail) ?></a>
          <?php endif; ?>
          <p class="site-footer__address"><?= e($city) ?>, France</p>
          <form class="site-footer__newsletter" method="POST" action="<?= e($newsletterAction) ?>" data-newsletter-form>
            <label class="sr-only" for="footer-newsletter-email">Adresse email</label>
            <input id="footer-newsletter-email" type="email" name="email" required placeholder="Votre email">
            <button type="submit">S'inscrire</button>
          </form>
          <p class="site-footer__newsletter-feedback" data-newsletter-feedback aria-live="polite"></p>
        </div>
      </section>
    </div>
  </div>

  <div class="site-footer__bottom">
    <div class="container site-footer__bottom-inner">
      <p>© <?= e((string) $currentYear) ?> <?= e($siteName) ?> — SAS OCDM Agency</p>
      <nav class="site-footer__legal" aria-label="Liens légaux">
        <?php foreach ($legalLinks as $item):
          $label = (string) ($item['label'] ?? 'Lien légal');
          $url = (string) ($item['url'] ?? '#');
        ?>
          <a href="<?= e($url) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>
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
        if (window.innerWidth <= 768) {
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
      if (window.innerWidth > 768) {
        closeMenu();
      }
    });

    // Close menu when clicking a dropdown sub-link or regular nav link (mobile)
    nav.querySelectorAll('.dropdown-menu a, .nav-item:not(.has-dropdown) .nav-link, .nav-cta-mobile a').forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
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
