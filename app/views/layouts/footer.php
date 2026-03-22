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

<footer class="site-footer">
  <div class="container">

    <!-- FOOTER MAIN -->
    <div class="footer-grid">

      <!-- COL 1: BRAND -->
      <div class="footer-column footer-col-brand">
        <a href="/" class="footer-logo-link">
          <span class="footer-logo-icon"><i class="fas fa-home"></i></span>
          <span class="footer-logo-text">Estimation Immobilier <strong>Bordeaux et Métropole</strong></span>
        </a>
        <p class="footer-desc">
          Votre partenaire de confiance pour l'estimation immobilière sur Bordeaux et sa métropole depuis 2020.
          Votre partenaire de confiance pour l'estimation immobilière sur Bordeaux et Métropole depuis 2020.
        </p>
        <div class="footer-social">
          <a href="https://facebook.com/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="Facebook" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://instagram.com/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="Instagram" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="https://linkedin.com/company/estimation-bordeaux" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          <a href="https://twitter.com/estimation_bdx" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="social-icon"><i class="fab fa-x-twitter"></i></a>
        </div>
      </div>

      <!-- COL 2: SERVICES -->
      <div class="footer-column">
        <h4 class="footer-heading">Services</h4>
        <ul class="footer-links">
          <li><a href="/#form-estimation">Estimation en ligne</a></li>
          <li><a href="/processus-estimation">Notre processus</a></li>
          <li><a href="/quartiers">Quartiers de Bordeaux</a></li>
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

      <!-- COL 4: ENTREPRISE -->
      <div class="footer-column">
        <h4 class="footer-heading">Entreprise</h4>
        <ul class="footer-links">
          <li><a href="/a-propos">À propos</a></li>
          <li><a href="/contact">Contact</a></li>
          <li><a href="/mentions-legales">Mentions légales</a></li>
          <li><a href="/politique-confidentialite">Confidentialité</a></li>
          <li><a href="/conditions-utilisation">CGU</a></li>
        </ul>
      </div>

      <!-- COL 5: CONTACT -->
      <div class="footer-column">
        <h4 class="footer-heading">Nous contacter</h4>
        <ul class="footer-contact">
          <li>
            <i class="fas fa-map-marker-alt"></i>
            <span>Bordeaux, 33000<br>Nouvelle-Aquitaine</span>
          </li>
          <li>
            <a href="tel:+33556000000">
              <i class="fas fa-phone"></i>
              <span>05 56 00 00 00</span>
            </a>
          </li>
          <li>
            <a href="mailto:contact@estimation-immobilier-bordeaux.fr">
              <i class="fas fa-envelope"></i>
              <span>contact@estimation-immobilier-bordeaux.fr</span>
            </a>
          </li>
        </ul>
      </div>

    </div>

    <!-- NEWSLETTER -->
    <div class="footer-newsletter-band">
      <div class="footer-newsletter-text">
        <i class="fas fa-envelope-open-text"></i>
        <div>
          <strong>Restez informé</strong>
          <span>Recevez nos analyses du marché de Bordeaux et sa métropole et nos conseils immobiliers.</span>
        </div>
      </div>
      <form class="footer-newsletter-form" method="POST" action="/api/newsletter">
        <input type="email" name="email" placeholder="Votre adresse email" required aria-label="Email pour newsletter">
        <button type="submit">S'inscrire</button>
      </form>
    </div>

    <!-- FOOTER BOTTOM -->
    <div class="footer-bottom">
      <div class="footer-bottom-left">
        <p>&copy; 2026 Estimation Immobilier Bordeaux et Métropole &mdash; SAS OCDM Agency. Tous droits réservés.</p>
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
</script>

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
<script>
(function() {
  var alertEl = document.getElementById('admin-presence-alert');
  var textEl = document.getElementById('admin-presence-text');
  if (!alertEl || !textEl) return;

  var dismissed = false;

  function checkPresence() {
    if (dismissed) return;
    fetch('/api/presence/check', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.active) {
          var name = data.admin_name || "L'administrateur";
          textEl.textContent = name + ' travaille actuellement sur le site.';
          alertEl.style.display = '';
        } else {
          alertEl.style.display = 'none';
        }
      })
      .catch(function() {});
  }

  // Check every 45 seconds
  checkPresence();
  setInterval(checkPresence, 45000);

  // If user manually dismisses, don't show again this session
  alertEl.querySelector('button').addEventListener('click', function() { dismissed = true; });
})();
</script>

</body>
</html>
