<?php
$s = $settings ?? [];
$currentTab = $tab ?? 'google';
?>

<style>
  .analytics-tabs {
    display: flex;
    gap: 0.25rem;
    border-bottom: 2px solid #e8dfd7;
    margin-bottom: 2rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .analytics-tab {
    padding: 0.75rem 1.25rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b6459;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .analytics-tab:hover { color: #8B1538; }
  .analytics-tab.active {
    color: #8B1538;
    border-bottom-color: #8B1538;
  }
  .analytics-tab i { font-size: 1rem; }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  .tracking-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e8dfd7;
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  }
  .tracking-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.25rem;
  }
  .tracking-card-header h3 {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0;
  }
  .tracking-card-header .icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #fff;
    flex-shrink: 0;
  }
  .tracking-card p.desc {
    color: #6b6459;
    font-size: 0.85rem;
    margin: 0.25rem 0 1.25rem;
    line-height: 1.5;
  }
  .field-group { margin-bottom: 1.25rem; }
  .field-group:last-child { margin-bottom: 0; }
  .field-label {
    display: block;
    font-weight: 600;
    font-size: 0.88rem;
    margin-bottom: 0.4rem;
    color: #1a1410;
  }
  .field-hint {
    color: #9ca3af;
    font-size: 0.78rem;
    margin: 0.35rem 0 0;
  }
  .field-input {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 1px solid #e8dfd7;
    border-radius: 8px;
    font-size: 0.92rem;
    font-family: 'DM Sans', sans-serif;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .field-input:focus {
    outline: none;
    border-color: #8B1538;
    box-shadow: 0 0 0 3px rgba(139,21,56,0.08);
  }
  .field-input-mono {
    font-family: 'SF Mono', 'Fira Code', monospace;
    font-size: 0.88rem;
  }
  textarea.field-input {
    min-height: 100px;
    resize: vertical;
    font-family: 'SF Mono', 'Fira Code', monospace;
    font-size: 0.82rem;
    line-height: 1.5;
  }
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-left: 0.5rem;
  }
  .status-dot.active { background: #22c55e; }
  .status-dot.inactive { background: #d1d5db; }

  .btn-save-analytics {
    padding: 0.75rem 2rem;
    background: linear-gradient(135deg, #8B1538, #C41E3A);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(139,21,56,0.2);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  .btn-save-analytics:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(139,21,56,0.3);
  }

  .overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  .overview-card {
    background: #fff;
    border: 1px solid #e8dfd7;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .overview-card .oc-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: #fff;
    flex-shrink: 0;
  }
  .overview-card .oc-text { font-size: 0.85rem; font-weight: 600; color: #1a1410; }
  .overview-card .oc-status { font-size: 0.75rem; color: #6b6459; }
  .oc-active { color: #16a34a !important; font-weight: 600; }
  .oc-inactive { color: #9ca3af !important; }
</style>

<div style="max-width:900px;">
  <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:0.5rem;">
    <i class="fas fa-chart-pie" style="color:var(--admin-primary,#8B1538);margin-right:0.5rem;"></i>
    Analytics & Tracking
  </h1>
  <p style="color:#6b6459;margin-bottom:1.5rem;">Configurez vos pixels, tags et scripts de suivi pour mesurer les performances de votre site.</p>

  <?php if (!empty($success)): ?>
  <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:1rem 1.25rem;border-radius:10px;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
    <i class="fas fa-check-circle"></i>
    Paramètres de tracking sauvegardés avec succès.
  </div>
  <?php endif; ?>

  <!-- Overview Cards -->
  <div class="overview-grid">
    <div class="overview-card">
      <div class="oc-icon" style="background:#4285F4;"><i class="fab fa-google"></i></div>
      <div>
        <div class="oc-text">Google Tag Manager</div>
        <div class="oc-status <?= !empty($s['gtm_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['gtm_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
    <div class="overview-card">
      <div class="oc-icon" style="background:#E37400;"><i class="fas fa-chart-bar"></i></div>
      <div>
        <div class="oc-text">Google Analytics 4</div>
        <div class="oc-status <?= !empty($s['ga4_measurement_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['ga4_measurement_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
    <div class="overview-card">
      <div class="oc-icon" style="background:#1877F2;"><i class="fab fa-facebook-f"></i></div>
      <div>
        <div class="oc-text">Facebook Pixel</div>
        <div class="oc-status <?= !empty($s['facebook_pixel_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['facebook_pixel_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
    <div class="overview-card">
      <div class="oc-icon" style="background:#F25022;"><i class="fab fa-microsoft"></i></div>
      <div>
        <div class="oc-text">Microsoft Clarity</div>
        <div class="oc-status <?= !empty($s['microsoft_clarity_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['microsoft_clarity_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
    <div class="overview-card">
      <div class="oc-icon" style="background:#FF3C00;"><i class="fas fa-fire"></i></div>
      <div>
        <div class="oc-text">Hotjar</div>
        <div class="oc-status <?= !empty($s['hotjar_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['hotjar_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
    <div class="overview-card">
      <div class="oc-icon" style="background:#34A853;"><i class="fas fa-ad"></i></div>
      <div>
        <div class="oc-text">Google Ads</div>
        <div class="oc-status <?= !empty($s['google_ads_id']) ? 'oc-active' : 'oc-inactive' ?>">
          <?= !empty($s['google_ads_id']) ? 'Actif' : 'Non configuré' ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="analytics-tabs">
    <button class="analytics-tab <?= $currentTab === 'google' ? 'active' : '' ?>" data-tab="google">
      <i class="fab fa-google"></i> Google
    </button>
    <button class="analytics-tab <?= $currentTab === 'social' ? 'active' : '' ?>" data-tab="social">
      <i class="fab fa-facebook"></i> Réseaux sociaux
    </button>
    <button class="analytics-tab <?= $currentTab === 'ux' ? 'active' : '' ?>" data-tab="ux">
      <i class="fas fa-mouse-pointer"></i> UX & Heatmaps
    </button>
    <button class="analytics-tab <?= $currentTab === 'custom' ? 'active' : '' ?>" data-tab="custom">
      <i class="fas fa-code"></i> Scripts personnalisés
    </button>
  </div>

  <form method="post" action="/admin/analytics-settings/save">
    <input type="hidden" name="_tab" id="current-tab" value="<?= htmlspecialchars($currentTab, ENT_QUOTES, 'UTF-8') ?>">

    <!-- ====== TAB: GOOGLE ====== -->
    <div class="tab-panel <?= $currentTab === 'google' ? 'active' : '' ?>" id="tab-google">

      <!-- GTM -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#4285F4;"><i class="fab fa-google"></i></div>
          <h3>Google Tag Manager
            <span class="status-dot <?= !empty($s['gtm_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Google Tag Manager centralise tous vos tags de suivi (Analytics, Ads, remarketing, etc.) dans un seul conteneur.
          Insérez votre ID de conteneur GTM pour activer l'injection automatique dans le &lt;head&gt; et le &lt;body&gt;.
        </p>
        <div class="field-group">
          <label class="field-label" for="gtm_id">GTM Container ID</label>
          <input type="text" id="gtm_id" name="gtm_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['gtm_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="GTM-XXXXXXX">
          <p class="field-hint">Format : GTM-XXXXXXX. Depuis tagmanager.google.com &rarr; Admin &rarr; Container Settings.</p>
        </div>
      </div>

      <!-- GA4 -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#E37400;"><i class="fas fa-chart-bar"></i></div>
          <h3>Google Analytics 4
            <span class="status-dot <?= !empty($s['ga4_measurement_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          GA4 mesure les événements, le trafic, les conversions et le comportement des utilisateurs.
          Si vous utilisez GTM, il est recommandé de configurer GA4 via GTM plutôt qu'ici (sauf si vous n'utilisez pas GTM).
        </p>
        <div class="field-group">
          <label class="field-label" for="ga4_measurement_id">Measurement ID (GA4)</label>
          <input type="text" id="ga4_measurement_id" name="ga4_measurement_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['ga4_measurement_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="G-XXXXXXXXXX">
          <p class="field-hint">Format : G-XXXXXXXXXX. Depuis analytics.google.com &rarr; Admin &rarr; Data Streams.</p>
        </div>
      </div>

      <!-- Google Ads -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#34A853;"><i class="fas fa-ad"></i></div>
          <h3>Google Ads Conversion Tracking
            <span class="status-dot <?= !empty($s['google_ads_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Suivi des conversions Google Ads pour mesurer le ROI de vos campagnes publicitaires.
        </p>
        <div class="field-group">
          <label class="field-label" for="google_ads_id">Google Ads ID</label>
          <input type="text" id="google_ads_id" name="google_ads_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['google_ads_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="AW-XXXXXXXXX">
          <p class="field-hint">Format : AW-XXXXXXXXX. Depuis ads.google.com &rarr; Outils &rarr; Conversions.</p>
        </div>
        <div class="field-group">
          <label class="field-label" for="google_ads_conversion_label">Conversion Label (optionnel)</label>
          <input type="text" id="google_ads_conversion_label" name="google_ads_conversion_label" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['google_ads_conversion_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="AbCdEfGhIjKlMn">
          <p class="field-hint">Label de la conversion principale (formulaire d'estimation soumis).</p>
        </div>
      </div>
    </div>

    <!-- ====== TAB: SOCIAL ====== -->
    <div class="tab-panel <?= $currentTab === 'social' ? 'active' : '' ?>" id="tab-social">

      <!-- Facebook Pixel -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#1877F2;"><i class="fab fa-facebook-f"></i></div>
          <h3>Facebook / Meta Pixel
            <span class="status-dot <?= !empty($s['facebook_pixel_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Le Meta Pixel suit les visiteurs, mesure les conversions publicitaires Facebook/Instagram et permet le remarketing.
        </p>
        <div class="field-group">
          <label class="field-label" for="facebook_pixel_id">Pixel ID</label>
          <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['facebook_pixel_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="123456789012345">
          <p class="field-hint">Depuis business.facebook.com &rarr; Events Manager &rarr; Data Sources.</p>
        </div>
        <div class="field-group">
          <label class="field-label" for="facebook_conversions_api_token">Conversions API Token (optionnel)</label>
          <input type="text" id="facebook_conversions_api_token" name="facebook_conversions_api_token" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['facebook_conversions_api_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="EAAxxxxxxx...">
          <p class="field-hint">Token d'accès pour l'API Conversions serveur-side (améliore la précision du tracking).</p>
        </div>
      </div>

      <!-- TikTok Pixel -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#000;"><i class="fab fa-tiktok"></i></div>
          <h3>TikTok Pixel
            <span class="status-dot <?= !empty($s['tiktok_pixel_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Suivi des conversions TikTok Ads et remarketing.
        </p>
        <div class="field-group">
          <label class="field-label" for="tiktok_pixel_id">TikTok Pixel ID</label>
          <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['tiktok_pixel_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="CXXXXXXXXXXXXXXXXX">
          <p class="field-hint">Depuis TikTok Ads Manager &rarr; Assets &rarr; Events.</p>
        </div>
      </div>

      <!-- LinkedIn -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#0A66C2;"><i class="fab fa-linkedin-in"></i></div>
          <h3>LinkedIn Insight Tag
            <span class="status-dot <?= !empty($s['linkedin_partner_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Suivi des conversions LinkedIn Ads et audiences de remarketing B2B.
        </p>
        <div class="field-group">
          <label class="field-label" for="linkedin_partner_id">Partner ID</label>
          <input type="text" id="linkedin_partner_id" name="linkedin_partner_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['linkedin_partner_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="123456">
          <p class="field-hint">Depuis LinkedIn Campaign Manager &rarr; Analyze &rarr; Insight Tag.</p>
        </div>
      </div>

      <!-- Pinterest -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#E60023;"><i class="fab fa-pinterest-p"></i></div>
          <h3>Pinterest Tag
            <span class="status-dot <?= !empty($s['pinterest_tag_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Suivi des conversions Pinterest Ads.
        </p>
        <div class="field-group">
          <label class="field-label" for="pinterest_tag_id">Pinterest Tag ID</label>
          <input type="text" id="pinterest_tag_id" name="pinterest_tag_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['pinterest_tag_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="1234567890123">
          <p class="field-hint">Depuis Pinterest Ads Manager &rarr; Conversions &rarr; Tag Manager.</p>
        </div>
      </div>

      <!-- Snapchat -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#FFFC00; color:#000;"><i class="fab fa-snapchat-ghost"></i></div>
          <h3>Snapchat Pixel
            <span class="status-dot <?= !empty($s['snapchat_pixel_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Suivi des conversions Snapchat Ads.
        </p>
        <div class="field-group">
          <label class="field-label" for="snapchat_pixel_id">Snap Pixel ID</label>
          <input type="text" id="snapchat_pixel_id" name="snapchat_pixel_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['snapchat_pixel_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
          <p class="field-hint">Depuis Snapchat Ads Manager &rarr; Events Manager.</p>
        </div>
      </div>
    </div>

    <!-- ====== TAB: UX ====== -->
    <div class="tab-panel <?= $currentTab === 'ux' ? 'active' : '' ?>" id="tab-ux">

      <!-- Microsoft Clarity -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#F25022;"><i class="fab fa-microsoft"></i></div>
          <h3>Microsoft Clarity
            <span class="status-dot <?= !empty($s['microsoft_clarity_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Heatmaps, enregistrements de sessions et analyses de comportement gratuits par Microsoft.
          Outil gratuit et sans limite de trafic.
        </p>
        <div class="field-group">
          <label class="field-label" for="microsoft_clarity_id">Clarity Project ID</label>
          <input type="text" id="microsoft_clarity_id" name="microsoft_clarity_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['microsoft_clarity_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="abcdefghij">
          <p class="field-hint">Depuis clarity.microsoft.com &rarr; Settings &rarr; Setup.</p>
        </div>
      </div>

      <!-- Hotjar -->
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#FF3C00;"><i class="fas fa-fire"></i></div>
          <h3>Hotjar
            <span class="status-dot <?= !empty($s['hotjar_id']) ? 'active' : 'inactive' ?>"></span>
          </h3>
        </div>
        <p class="desc">
          Heatmaps, enregistrements, sondages et funnels de conversion.
        </p>
        <div class="field-group">
          <label class="field-label" for="hotjar_id">Hotjar Site ID</label>
          <input type="text" id="hotjar_id" name="hotjar_id" class="field-input field-input-mono"
                 value="<?= htmlspecialchars($s['hotjar_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                 placeholder="1234567">
          <p class="field-hint">Depuis hotjar.com &rarr; Sites & Organizations &rarr; Site ID.</p>
        </div>
      </div>
    </div>

    <!-- ====== TAB: CUSTOM ====== -->
    <div class="tab-panel <?= $currentTab === 'custom' ? 'active' : '' ?>" id="tab-custom">
      <div class="tracking-card">
        <div class="tracking-card-header">
          <div class="icon-circle" style="background:#6b6459;"><i class="fas fa-code"></i></div>
          <h3>Scripts personnalisés</h3>
        </div>
        <p class="desc">
          Injectez du code personnalisé dans le &lt;head&gt; ou le &lt;body&gt; de toutes les pages.
          Utile pour des pixels tiers, du tracking personnalisé ou des outils non listés ci-dessus.
        </p>
        <div class="field-group">
          <label class="field-label" for="custom_head_scripts">Scripts dans le &lt;head&gt;</label>
          <textarea id="custom_head_scripts" name="custom_head_scripts" class="field-input"
                    placeholder="<!-- Collez vos scripts head ici -->"><?= htmlspecialchars($s['custom_head_scripts'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          <p class="field-hint">Injecté juste avant la fermeture &lt;/head&gt;. Accepte &lt;script&gt;, &lt;meta&gt;, &lt;link&gt;, etc.</p>
        </div>
        <div class="field-group">
          <label class="field-label" for="custom_body_scripts">Scripts dans le &lt;body&gt;</label>
          <textarea id="custom_body_scripts" name="custom_body_scripts" class="field-input"
                    placeholder="<!-- Collez vos scripts body ici -->"><?= htmlspecialchars($s['custom_body_scripts'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          <p class="field-hint">Injecté juste après l'ouverture &lt;body&gt;. Utile pour les balises &lt;noscript&gt;.</p>
        </div>
      </div>

      <div style="background:#fefce8;border:1px solid #fde68a;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
          <i class="fas fa-exclamation-triangle" style="color:#d97706;margin-top:0.2rem;"></i>
          <div>
            <strong style="color:#92400e;font-size:0.9rem;">Attention</strong>
            <p style="color:#78350f;font-size:0.85rem;margin:0.25rem 0 0;line-height:1.5;">
              Les scripts personnalisés sont injectés sans échappement. Assurez-vous que le code provient d'une source de confiance pour éviter les failles de sécurité (XSS).
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div style="margin-top:1rem;display:flex;gap:1rem;align-items:center;">
      <button type="submit" class="btn-save-analytics">
        <i class="fas fa-save"></i>
        Sauvegarder les paramètres
      </button>
    </div>
  </form>
</div>

<script>
(function() {
  var tabs = document.querySelectorAll('.analytics-tab');
  var panels = document.querySelectorAll('.tab-panel');
  var hiddenTab = document.getElementById('current-tab');

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      var target = this.getAttribute('data-tab');

      tabs.forEach(function(t) { t.classList.remove('active'); });
      panels.forEach(function(p) { p.classList.remove('active'); });

      this.classList.add('active');
      document.getElementById('tab-' + target).classList.add('active');
      hiddenTab.value = target;

      // Update URL without reload
      var url = new URL(window.location);
      url.searchParams.set('tab', target);
      url.searchParams.delete('success');
      history.replaceState(null, '', url);
    });
  });
})();
</script>
