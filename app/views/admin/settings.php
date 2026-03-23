<div style="max-width:800px;">
  <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:0.5rem;">
    <i class="fas fa-cog" style="color:var(--admin-primary,#8B1538);margin-right:0.5rem;"></i>
    Paramètres du site
  </h1>
  <p style="color:#6b6459;margin-bottom:2rem;">Configurez les paramètres généraux de votre site.</p>

  <?php if (!empty($success)): ?>
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:1rem 1.25rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
    <i class="fas fa-check-circle"></i>
    Paramètres sauvegardés avec succès.
  </div>
  <?php endif; ?>

  <div style="background:#fff;border-radius:12px;border:1px solid #e8dfd7;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
    <h2 style="font-size:1.15rem;font-weight:700;margin:0 0 0.25rem;display:flex;align-items:center;gap:0.5rem;">
      <i class="fab fa-google" style="color:#4285F4;"></i>
      Google Site Verification
    </h2>
    <p style="color:#6b6459;font-size:0.9rem;margin:0 0 1.5rem;">
      Collez le code <code>content</code> de votre balise meta Google Search Console pour vérifier la propriété de votre site.
    </p>

    <form method="post" action="/admin/settings/save">
      <label for="google_site_verification" style="display:block;font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;">
        Code de vérification Google
      </label>
      <input
        type="text"
        id="google_site_verification"
        name="google_site_verification"
        value="<?= htmlspecialchars((string) ($google_site_verification ?? ''), ENT_QUOTES, 'UTF-8') ?>"
        placeholder="Ex: eUCMXYnNpyWP-GKzOuOitF2ylAnBLRvIl1sjIqTKWXA"
        style="width:100%;padding:0.75rem 1rem;border:1px solid #e8dfd7;border-radius:8px;font-size:0.95rem;font-family:monospace;transition:border-color 0.2s;"
        onfocus="this.style.borderColor='#8B1538';this.style.boxShadow='0 0 0 3px rgba(139,21,56,0.08)'"
        onblur="this.style.borderColor='#e8dfd7';this.style.boxShadow='none'"
      >
      <p style="color:#9ca3af;font-size:0.8rem;margin:0.5rem 0 0;">
        Depuis Google Search Console &rarr; Paramètres &rarr; Validation de la propriété &rarr; Balise HTML.
        Copiez uniquement la valeur du <code>content</code>, pas la balise entière.
      </p>

      <?php if (!empty($google_site_verification)): ?>
      <div style="margin-top:1rem;padding:0.75rem 1rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;font-size:0.85rem;color:#0369a1;">
        <i class="fas fa-code" style="margin-right:0.4rem;"></i>
        Balise générée :
        <code style="display:block;margin-top:0.4rem;word-break:break-all;">&lt;meta name="google-site-verification" content="<?= htmlspecialchars((string) $google_site_verification, ENT_QUOTES, 'UTF-8') ?>" /&gt;</code>
      </div>
      <?php endif; ?>

      <div style="margin-top:1.5rem;display:flex;gap:1rem;align-items:center;">
        <button type="submit" style="padding:0.75rem 2rem;background:linear-gradient(135deg,#8B1538,#C41E3A);color:#fff;border:none;border-radius:8px;font-weight:600;font-size:0.95rem;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 12px rgba(139,21,56,0.2);"
          onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(139,21,56,0.3)'"
          onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(139,21,56,0.2)'"
        >
          <i class="fas fa-save" style="margin-right:0.4rem;"></i>
          Sauvegarder
        </button>
      </div>
    </form>
  </div>
</div>
