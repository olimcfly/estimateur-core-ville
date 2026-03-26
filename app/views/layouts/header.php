<!DOCTYPE html>
<html lang="fr">
<head>
  <?php
    $t = $tracking ?? [];
    $_gtmId = !empty($t['gtm_id']) ? $t['gtm_id'] : 'GTM-N8MZSH9C';
  ?>
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','<?= e($_gtmId) ?>');</script>
  <!-- End Google Tag Manager -->

  <?php if (!empty($t['ga4_measurement_id'])): ?>
  <!-- Google Analytics 4 -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($t['ga4_measurement_id']) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= e($t['ga4_measurement_id']) ?>');
    <?php if (!empty($t['google_ads_id'])): ?>
    gtag('config', '<?= e($t['google_ads_id']) ?>');
    <?php endif; ?>
  </script>
  <?php endif; ?>

  <?php if (!empty($t['facebook_pixel_id'])): ?>
  <!-- Facebook / Meta Pixel -->
  <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?= e($t['facebook_pixel_id']) ?>');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= e($t['facebook_pixel_id']) ?>&ev=PageView&noscript=1"/></noscript>
  <?php endif; ?>

  <?php if (!empty($t['microsoft_clarity_id'])): ?>
  <!-- Microsoft Clarity -->
  <script type="text/javascript">
    (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window,document,"clarity","script","<?= e($t['microsoft_clarity_id']) ?>");
  </script>
  <?php endif; ?>

  <?php if (!empty($t['hotjar_id'])): ?>
  <!-- Hotjar -->
  <script>
    (function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
    h._hjSettings={hjid:<?= (int) $t['hotjar_id'] ?>,hjsv:6};
    a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;
    r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['tiktok_pixel_id'])): ?>
  <!-- TikTok Pixel -->
  <script>
    !function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=
    ["page","track","identify","instances","debug","on","off","once","ready","alias",
    "group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=
    function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;
    i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=
    function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,
    ttq.methods[n]);return e};ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
    ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,
    ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",
    o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];
    a.parentNode.insertBefore(o,a)};ttq.load('<?= e($t['tiktok_pixel_id']) ?>');ttq.page();
    }(window,document,'ttq');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['linkedin_partner_id'])): ?>
  <!-- LinkedIn Insight Tag -->
  <script type="text/javascript">
    _linkedin_partner_id = "<?= e($t['linkedin_partner_id']) ?>";
    window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
    window._linkedin_data_partner_ids.push(_linkedin_partner_id);
    (function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};
    window.lintrk.q=[]}var s=document.getElementsByTagName("script")[0];
    var b=document.createElement("script");b.type="text/javascript";b.async=true;
    b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";
    s.parentNode.insertBefore(b,s);})(window.lintrk);
  </script>
  <noscript><img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=<?= e($t['linkedin_partner_id']) ?>&fmt=gif"/></noscript>
  <?php endif; ?>

  <?php if (!empty($t['pinterest_tag_id'])): ?>
  <!-- Pinterest Tag -->
  <script>
    !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(
    Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version="3.0";
    var t=document.createElement("script");t.async=!0,t.src=e;
    var r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}
    ("https://s.pinimg.com/ct/core.js");
    pintrk('load','<?= e($t['pinterest_tag_id']) ?>');pintrk('page');
  </script>
  <noscript><img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?tid=<?= e($t['pinterest_tag_id']) ?>&noscript=1"/></noscript>
  <?php endif; ?>

  <?php if (!empty($t['snapchat_pixel_id'])): ?>
  <!-- Snapchat Pixel -->
  <script type="text/javascript">
    (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?
    a.handleRequest.apply(a,arguments):a.queue.push(arguments)};a.queue=[];
    var s='script';r=t.createElement(s);r.async=!0;r.src=n;
    var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);
    })(window,document,'https://sc-static.net/scevent.min.js');
    snaptr('init','<?= e($t['snapchat_pixel_id']) ?>',{});snaptr('track','PAGE_VIEW');
  </script>
  <?php endif; ?>

  <?php if (!empty($t['custom_head_scripts'])): ?>
  <!-- Custom Head Scripts -->
  <?= $t['custom_head_scripts'] ?>
  <?php endif; ?>
  <?php
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $canonicalPath = (string) parse_url($requestUri, PHP_URL_PATH);
    $canonicalPath = $canonicalPath !== '' ? $canonicalPath : '/';
    if ($canonicalPath !== '/') {
        $canonicalPath = rtrim($canonicalPath, '/');
    }

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $canonicalUrl = $scheme . '://' . $host . $canonicalPath;

    $branding = is_array($branding ?? null) ? $branding : getBrandingConfig();
    $brandSiteName = trim((string) ($branding['site_name'] ?? ''));
    if ($brandSiteName === '') {
        $brandSiteName = 'Estimation Immobilière';
    }

    $brandCityName = trim((string) ($branding['city_name'] ?? ''));
    if ($brandCityName === '') {
        $brandCityName = 'votre ville';
    }

    $brandAreaLabel = trim((string) ($branding['area_label'] ?? ''));
    if ($brandAreaLabel === '') {
        $brandAreaLabel = $brandCityName !== 'votre ville' ? $brandCityName : 'votre secteur';
    }

    $brandBaseUrl = trim((string) ($branding['base_url'] ?? ''));
    if ($brandBaseUrl === '') {
        $brandBaseUrl = $scheme . '://' . $host;
    }
    $brandEmail = trim((string) ($branding['support_email'] ?? ''));
    if ($brandEmail === '') {
        $brandEmail = 'contact@example.test';
    }

    $defaultMetaDescription = sprintf(
        'Estimation immobilière %s - Obtenez votre avis de valeur immobilier gratuit. Données réelles du marché local, résultat en 60 secondes.',
        $brandAreaLabel
    );
    $defaultTitle = sprintf('%s | %s', $brandSiteName, $brandAreaLabel);
    $defaultOgDescription = sprintf(
        'Obtenez votre avis de valeur immobilier gratuit à %s. Résultat en 60 secondes.',
        $brandAreaLabel
    );
    $defaultOgImage = rtrim($brandBaseUrl, '/') . '/favicon.svg';
    $defaultLocality = $brandCityName !== 'votre ville' ? $brandCityName : 'Ville';
    $brandKeywords = [
        'estimation immobilière',
        'avis de valeur',
        'prix immobilier ' . $defaultLocality,
        'marché immobilier local',
    ];
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description" content="<?= htmlspecialchars((string) ($meta_description ?? 'Estimation immobilier Bordeaux et sa métropole - Obtenez votre avis de valeur immobilier gratuit. Données réelles du marché bordelais, résultat en 60 secondes.'), ENT_QUOTES, 'UTF-8') ?>">
  <meta name="theme-color" content="<?= e((string) ($colors['primary'] ?? '#8B1538')) ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="mobile-web-app-capable" content="yes">
  <?php if (!empty($google_site_verification)): ?>
  <meta name="google-site-verification" content="<?= e((string) $google_site_verification) ?>">
  <?php endif; ?>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="canonical" href="<?= e($canonicalUrl) ?>">
  <title><?= isset($page_title) ? $page_title : $defaultTitle ?></title>

  <?php
    $ogTitle = htmlspecialchars((string) ($og_title ?? $page_title ?? $defaultTitle), ENT_QUOTES, 'UTF-8');
    $ogDesc = htmlspecialchars((string) ($og_description ?? $meta_description ?? $defaultOgDescription), ENT_QUOTES, 'UTF-8');
    $ogImg = htmlspecialchars((string) ($og_image ?? $defaultOgImage), ENT_QUOTES, 'UTF-8');
    $ogType = !empty($article) ? 'article' : 'website';
  ?>
  <!-- Open Graph -->
  <meta property="og:type" content="<?= $ogType ?>">
  <meta property="og:title" content="<?= $ogTitle ?>">
  <meta property="og:description" content="<?= $ogDesc ?>">
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="<?= e($brandSiteName) ?>">
  <meta property="og:image" content="<?= $ogImg ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $ogTitle ?>">
  <meta name="twitter:description" content="<?= $ogDesc ?>">
  <meta name="twitter:image" content="<?= $ogImg ?>">

  <!-- Schema.org JSON-LD: LocalBusiness + RealEstateAgent -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "RealEstateAgent",
    "name": "<?= e($brandSiteName) ?>",
    "description": "<?= e('Avis de valeur et estimation immobilière gratuite à ' . $brandAreaLabel . '. Prix au m² et tendances du marché local.') ?>",
    "url": "<?= e($brandBaseUrl) ?>",
    "email": "<?= e($brandEmail) ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?= e($defaultLocality) ?>",
      "addressLocality": "<?= e($defaultLocality) ?>",
      "addressRegion": "<?= e($brandAreaLabel) ?>",
      "postalCode": "",
      "addressCountry": "FR"
    },
    "areaServed": [
      { "@type": "City", "name": "<?= e($defaultLocality) ?>" },
      { "@type": "Place", "name": "<?= e($brandAreaLabel) ?>" }
    ],
    "priceRange": "Gratuit",
    "knowsAbout": ["<?= e($brandKeywords[0]) ?>", "<?= e($brandKeywords[1]) ?>", "<?= e($brandKeywords[2]) ?>", "<?= e($brandKeywords[3]) ?>"]
  }
  </script>

  <!-- Schema.org BreadcrumbList -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "<?= e($brandBaseUrl) ?>"
      },
      <?php if ($canonicalPath !== '/'): ?>
      {
        "@type": "ListItem",
        "position": 2,
        "name": "<?= htmlspecialchars($page_title ?? '', ENT_QUOTES, 'UTF-8') ?>",
        "item": "<?= e($canonicalUrl) ?>"
      }
      <?php endif; ?>
    ]
  }
  </script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- CSS Principal -->
  <link rel="stylesheet" href="/assets/css/app.css">

  <!-- CSS Header Personnalisé -->
  <style>

    :root {
      --bg: <?= e((string) ($colors['bg'] ?? '#faf9f7')) ?>;
      --surface: <?= e((string) ($colors['surface'] ?? '#ffffff')) ?>;
      --text: <?= e((string) ($colors['text'] ?? '#1a1410')) ?>;
      --muted: <?= e((string) ($colors['muted'] ?? '#6b6459')) ?>;
      --primary: <?= e((string) ($colors['primary'] ?? '#8B1538')) ?>;
      --primary-dark: <?= e((string) ($colors['primary_dark'] ?? '#6b0f2d')) ?>;
      --accent: <?= e((string) ($colors['accent'] ?? '#D4AF37')) ?>;
      --accent-light: <?= e((string) ($colors['accent_light'] ?? '#E8C547')) ?>;
      --border: <?= e((string) ($colors['border'] ?? '#e8dfd7')) ?>;
      --success: <?= e((string) ($colors['success'] ?? '#22c55e')) ?>;
      --warning: <?= e((string) ($colors['warning'] ?? '#f97316')) ?>;
      --danger: <?= e((string) ($colors['danger'] ?? '#e24b4a')) ?>;
      --info: <?= e((string) ($colors['info'] ?? '#3b82f6')) ?>;
      --neutral: <?= e((string) ($colors['neutral'] ?? '#000000')) ?>;
      --bg-rgb: <?= e((string) ($rgbColors['bg'] ?? '250, 249, 247')) ?>;
      --border-rgb: <?= e((string) ($rgbColors['border'] ?? '232, 223, 215')) ?>;
      --primary-rgb: <?= e((string) ($rgbColors['primary'] ?? '139, 21, 56')) ?>;
      --accent-rgb: <?= e((string) ($rgbColors['accent'] ?? '212, 175, 55')) ?>;
      --success-rgb: <?= e((string) ($rgbColors['success'] ?? '34, 197, 94')) ?>;
      --warning-rgb: <?= e((string) ($rgbColors['warning'] ?? '249, 115, 22')) ?>;
      --neutral-rgb: <?= e((string) ($rgbColors['neutral'] ?? '0, 0, 0')) ?>;
      --primary-alt: <?= e((string) ($colors['primary_alt'] ?? '#C41E3A')) ?>;
      --text-inverse: <?= e((string) ($colors['text_inverse'] ?? '#ffffff')) ?>;
      --font-display: <?= e((string) ($colors['font_display'] ?? '\'Playfair Display\', serif')) ?>;
      --z-sticky-cta: 999;
      --z-header: 1000;
      --z-mobile-overlay: 1050;
      --z-popup-lead: 1100;
    }

    /* HEADER PREMIUM */
    .site-header {
      position: sticky;
      top: 0;
      z-index: var(--z-header);
      backdrop-filter: blur(12px);
      background: rgba(var(--bg-rgb), 0.95);
      border-bottom: 1px solid rgba(var(--border-rgb), 0.6);
      box-shadow: 0 2px 8px rgba(var(--neutral-rgb), 0.04);
    }

    .header-container {
      width: min(1400px, calc(100% - 2rem));
      margin-inline: auto;
      padding: 0.8rem 0;
    }

    .header-wrapper {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.2rem;
    }

    /* LOGO/BRAND */
    .brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      text-decoration: none;
      margin: 0;
      font-family: var(--font-display);
      font-weight: 700;
      font-size: 1.15rem;
      letter-spacing: -0.02em;
      flex-shrink: 0;
    }

    .brand-icon {
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), var(--primary-alt));
      border-radius: 8px;
      color: var(--text-inverse);
      font-size: 1rem;
      box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
      flex-shrink: 0;
    }

    .brand span {
      color: var(--primary);
    }

    /* NAVIGATION PRINCIPALE */
    .top-nav {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.15rem;
      flex: 1;
      min-width: 0;
    }

    .nav-item {
      position: relative;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.6rem 0.65rem;
      text-decoration: none;
      color: var(--muted);
      font-weight: 500;
      font-size: 0.88rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .nav-link:hover {
      color: var(--primary);
      background: rgba(var(--primary-rgb), 0.05);
    }

    .nav-link.active {
      color: var(--primary);
      background: rgba(var(--primary-rgb), 0.08);
      font-weight: 600;
    }

    .nav-link i {
      font-size: 0.9rem;
    }

    /* DROPDOWN MENU */
    .has-dropdown {
      position: relative;
    }

    .has-dropdown > .nav-link::after {
      content: '';
      display: inline-block;
      width: 0.4rem;
      height: 0.4rem;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      transform: rotate(45deg);
      margin-left: 0.4rem;
      transition: transform 0.2s ease;
    }

    .has-dropdown:hover > .nav-link::after {
      transform: rotate(-135deg);
    }

    .dropdown-menu {
      position: absolute;
      top: calc(100% + 0.5rem);
      left: 0;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(var(--neutral-rgb), 0.1);
      min-width: 220px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s ease;
      list-style: none;
      margin: 0;
      padding: 0.5rem 0;
      z-index: calc(var(--z-header) + 1);
    }

    .has-dropdown:hover .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-menu li {
      margin: 0;
    }

    .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      padding: 0.75rem 1.5rem;
      color: var(--text);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .dropdown-menu a:hover {
      background: rgba(var(--primary-rgb), 0.05);
      border-left-color: var(--primary);
      color: var(--primary);
      padding-left: 1.8rem;
    }

    .dropdown-menu i {
      width: 18px;
      text-align: center;
      color: var(--primary);
    }

    /* CTA & SEARCH */
    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-shrink: 0;
    }

    .search-wrapper {
      position: relative;
      display: none;
    }

    .search-input {
      padding: 0.6rem 1rem 0.6rem 2.5rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.9rem;
      width: 200px;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08);
    }

    .search-icon {
      position: absolute;
      left: 0.8rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      pointer-events: none;
    }

    .btn-cta {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.7rem 1.4rem;
      background: linear-gradient(135deg, var(--primary), var(--primary-alt));
      color: var(--text-inverse);
      text-decoration: none;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.88rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
      white-space: nowrap;
    }

    .btn-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.3);
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    }

    .btn-cta i {
      font-size: 1rem;
    }

    /* TOGGLE MOBILE */
    .menu-toggle {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.6rem;
      z-index: calc(var(--z-mobile-overlay) + 1);
      border-radius: 8px;
      transition: background 0.2s ease;
    }

    .menu-toggle:active {
      background: rgba(var(--primary-rgb), 0.08);
    }

    .menu-toggle span {
      width: 22px;
      height: 2.5px;
      background: var(--text);
      border-radius: 2px;
      transition: all 0.3s ease;
      transform-origin: center;
    }

    .menu-toggle.active span:nth-child(1) {
      transform: rotate(45deg) translate(5px, 5px);
    }

    .menu-toggle.active span:nth-child(2) {
      opacity: 0;
      transform: scaleX(0);
    }

    .menu-toggle.active span:nth-child(3) {
      transform: rotate(-45deg) translate(5px, -5px);
    }

    /* RESPONSIVE */
    @media (max-width: 1023.98px) {
      .top-nav {
        gap: 0;
      }

      .nav-link {
        padding: 0.5rem 0.5rem;
        font-size: 0.82rem;
      }

      .search-wrapper {
        display: none !important;
      }

      .header-wrapper {
        gap: 0.8rem;
      }

      .brand {
        font-size: 1rem;
      }

      .btn-header-cta {
        padding: 0.6rem 1rem;
        font-size: 0.82rem;
      }
    }

    @media (max-width: 767.98px) {
      .menu-toggle {
        display: flex;
      }

      .top-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--surface);
        flex-direction: column;
        gap: 0;
        padding-top: 80px;
        padding-bottom: 2rem;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        z-index: var(--z-mobile-overlay);
      }

      .top-nav.active {
        transform: translateX(0);
      }

      .top-nav > .nav-item {
        width: 100%;
        border-bottom: 1px solid rgba(var(--border-rgb), 0.5);
      }

      .top-nav > .nav-item:last-of-type {
        border-bottom: none;
      }

      .nav-link {
        padding: 1.1rem 1.5rem;
        border-radius: 0;
        justify-content: space-between;
        width: 100%;
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text);
      }

      .nav-link:hover,
      .nav-link:active {
        background: rgba(var(--primary-rgb), 0.06);
        color: var(--primary);
      }

      /* Disable hover-based dropdown on mobile */
      .has-dropdown:hover .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
      }

      .has-dropdown > .nav-link::after {
        width: 0.5rem;
        height: 0.5rem;
        border-right-width: 2.5px;
        border-bottom-width: 2.5px;
        transition: transform 0.3s ease;
      }

      .dropdown-menu {
        position: static;
        opacity: 0;
        visibility: hidden;
        max-height: 0;
        overflow: hidden;
        box-shadow: none;
        border: none;
        border-radius: 0;
        background: rgba(var(--primary-rgb), 0.03);
        border-left: 3px solid var(--primary);
        margin-left: 1.5rem;
        transform: none;
        transition: max-height 0.35s ease, opacity 0.25s ease, visibility 0.25s ease;
        padding: 0;
      }

      .has-dropdown.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        max-height: 500px;
        padding: 0.4rem 0;
      }

      /* Rotate arrow when dropdown is open */
      .has-dropdown.active > .nav-link::after {
        transform: rotate(-135deg);
      }

      .has-dropdown.active > .nav-link {
        color: var(--primary);
        background: rgba(var(--primary-rgb), 0.06);
      }

      .dropdown-menu a {
        padding: 0.85rem 1.2rem 0.85rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--muted);
        border-left: none;
      }

      .dropdown-menu a:hover,
      .dropdown-menu a:active {
        padding-left: 1.5rem;
        background: rgba(var(--primary-rgb), 0.06);
        color: var(--primary);
        border-left-color: transparent;
      }

      .header-actions {
        gap: 0.5rem;
      }

      .btn-cta {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
      }

      .brand {
        font-size: 1rem;
      }

      .brand-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
      }
    }

    @media (max-width: 767.98px) {
      .header-container {
        padding: 0.8rem 0;
      }

      .header-wrapper {
        gap: 0.8rem;
      }

      .brand {
        font-size: 0.85rem;
        gap: 0.4rem;
      }

      .brand-icon {
        width: 32px;
        height: 32px;
      }

      .btn-cta {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
      }

      .btn-cta span {
        display: none;
      }
    }

    /* Header CTA button */
    .btn-header-cta {
      padding: 0.7rem 1.4rem;
      font-size: 0.88rem;
      flex-shrink: 0;
      text-align: center;
      white-space: nowrap;
    }

    .nav-cta-mobile {
      display: none;
    }

    @media (max-width: 767.98px) {
      .btn-header-cta {
        display: none;
      }

      .nav-cta-mobile {
        display: block;
        padding: 1.5rem;
        margin-top: auto;
      }

      .nav-cta-mobile a {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-alt));
        color: var(--text-inverse);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        box-shadow: 0 4px 16px rgba(var(--primary-rgb), 0.3);
        transition: all 0.2s ease;
      }

      .nav-cta-mobile a:active {
        transform: scale(0.98);
        box-shadow: 0 2px 8px rgba(var(--primary-rgb), 0.2);
      }
    }
  </style>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e($_gtmId) ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php if (!empty($t['custom_body_scripts'])): ?>
<!-- Custom Body Scripts -->
<?= $t['custom_body_scripts'] ?>
<?php endif; ?>

<!-- ============================= -->
<!-- HEADER PREMIUM -->
<!-- ============================= -->
<header class="site-header">
  <div class="header-container">
    <div class="header-wrapper">
    <a href="/" class="brand">
      <div class="brand-icon"><i class="fas fa-chart-area"></i></div>
      <?= e($brandSiteName) ?> <span><?= e($brandAreaLabel) ?></span>
    </a>

    <button class="menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <nav class="top-nav" aria-label="Navigation principale">
      <div class="nav-item has-dropdown">
        <a href="/estimation" class="nav-link">Estimation</a>
        <ul class="dropdown-menu" aria-label="Sous-menu estimation">
          <li><a href="/estimation#form-estimation">Estimer mon bien</a></li>
          <li><a href="/estimation#example-result">Voir un exemple</a></li>
          <li><a href="/estimation#how-it-works">Comment ça marche</a></li>
          <li><a href="/estimation#faq">FAQ Estimation</a></li>
        </ul>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/blog" class="nav-link">Blog</a>
        <ul class="dropdown-menu" aria-label="Sous-menu blog">
          <li><a href="/blog">Tous les articles</a></li>
          <li><a href="/blog/vendre-son-bien">Vendre son bien</a></li>
          <li><a href="/blog/marche-immobilier">Marché immobilier</a></li>
          <li><a href="/blog/conseils-astuces">Conseils &amp; astuces</a></li>
          <li><a href="/blog/aspect-juridique">Aspect juridique</a></li>
          <li><a href="/blog/aspects-juridiques">Aspects juridiques</a></li>
        </ul>
      </div>

      <div class="nav-item">
        <a href="/actualites" class="nav-link">Actualités</a>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/services" class="nav-link">Services</a>
        <ul class="dropdown-menu" aria-label="Sous-menu services">
          <li><a href="/services#estimation-detaillee">Estimation détaillée</a></li>
          <li><a href="/services#accompagnement">Accompagnement</a></li>
          <li><a href="/services#conseil-immobilier">Conseil immobilier</a></li>
          <li><a href="/services#marketing-immobilier">Marketing immobilier</a></li>
        </ul>
      </div>

      <div class="nav-item">
        <a href="/a-propos" class="nav-link">À propos</a>
      </div>
      <div class="nav-item">
        <a href="/contact" class="nav-link">Contact</a>
      </div>

      <div class="nav-item has-dropdown">
        <a href="/guides" class="nav-link">Ressources</a>
        <ul class="dropdown-menu" aria-label="Sous-menu ressources">
          <li><a href="/guides">Guides complets</a></li>
          <li><a href="/tools/calculatrice">Calculatrice prix</a></li>
          <li><a href="/quartiers">Quartiers</a></li>
          <li><a href="/newsletter">Newsletter</a></li>
        </ul>
      </div>

      <div class="nav-cta-mobile">
        <a href="/estimation#form-estimation">Estimer mon bien</a>
      </div>
    </nav>

    <a href="/estimation#form-estimation" class="btn btn-header-cta">Estimer mon bien</a>
    </div>
  </div>
</header>

<main>
