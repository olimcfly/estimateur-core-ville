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
  <meta name="description" content="<?= htmlspecialchars((string) ($meta_description ?? ('Estimation immobilier ' . site('city', 'local') . ' — Obtenez votre avis de valeur immobilier gratuit. Données réelles du marché local, résultat en 60 secondes.')), ENT_QUOTES, 'UTF-8') ?>">
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
  <link rel="stylesheet" href="/assets/css/shell.css">

  <?php
    $config = getSiteConfig();
    $siteLogo = (string) ($config['site_logo'] ?? '');
    $siteName = (string) ($config['site_name'] ?? '');
    if ($siteName === '') {
        $siteName = (string) ($branding['site_name'] ?? 'Estimation Immobilière');
    }
    $ctaLabel = trim((string) ($config['cta_label'] ?? ''));
    if ($ctaLabel === '') {
        $ctaLabel = 'Estimer mon bien';
    }
    $ctaUrl = trim((string) ($config['cta_url'] ?? '/estimation#form-estimation'));
    if ($ctaUrl === '') {
        $ctaUrl = '/estimation#form-estimation';
    }
    $navLinks = is_array($config['nav_links'] ?? null) ? $config['nav_links'] : [];
    if ($navLinks === []) {
        $navLinks = [
            ['label' => 'Accueil', 'url' => '/'],
            ['label' => 'Estimation', 'url' => '/estimation'],
            ['label' => 'Services', 'url' => '/services'],
            ['label' => 'Quartiers', 'url' => '/quartiers'],
            ['label' => 'Contact', 'url' => '/contact'],
        ];
    }
    $contactPhone = getContactPhone();
    $accentColor = (string) ($config['color_accent'] ?? ($colors['accent'] ?? '#D4AF37'));
  ?>
  <!-- Runtime design tokens (white-label) -->
  <style>
    :root {
      --bg: <?= e((string) ($colors['bg'] ?? '#faf9f7')) ?>;
      --surface: <?= e((string) ($colors['surface'] ?? '#ffffff')) ?>;
      --text: <?= e((string) ($colors['text'] ?? '#1a1410')) ?>;
      --muted: <?= e((string) ($colors['muted'] ?? '#6b6459')) ?>;
      --primary: <?= e((string) ($colors['primary'] ?? '#8B1538')) ?>;
      --primary-dark: <?= e((string) ($colors['primary_dark'] ?? '#6b0f2d')) ?>;
      --accent: <?= e((string) ($colors['accent'] ?? '#D4AF37')) ?>;
      --color-accent: <?= e($accentColor) ?>;
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
<!-- HEADER -->
<!-- ============================= -->
<header class="site-header" data-header>
  <div class="site-header__container">
    <div class="site-header__inner">
      <a href="/" class="site-header__brand" aria-label="<?= e($siteName) ?>">
        <?php if ($siteLogo !== ''): ?>
          <img src="<?= e($siteLogo) ?>" alt="<?= e($siteName) ?>" class="site-header__logo">
        <?php endif; ?>
        <span class="site-header__brand-text"><?= e($siteName) ?></span>
      </a>

      <nav class="site-header__nav" role="navigation" aria-label="Navigation principale">
        <?php foreach ($navLinks as $link): ?>
          <?php
            $label = (string) ($link['label'] ?? '');
            $url = (string) ($link['url'] ?? '#');
            $active = !empty($link['active']);
          ?>
          <a href="<?= e($url) ?>" class="site-header__nav-link<?= $active ? ' site-header__nav-link--active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>

      <div class="site-header__actions">
        <?php if ($contactPhone['display'] !== '' && $contactPhone['href'] !== ''): ?>
          <a href="<?= e($contactPhone['href']) ?>" class="site-header__phone site-header__phone--desktop" aria-label="Appeler <?= e($contactPhone['display']) ?>">
            <i class="fas fa-phone"></i>
            <span><?= e($contactPhone['display']) ?></span>
          </a>
          <a href="<?= e($contactPhone['href']) ?>" class="site-header__phone site-header__phone--mobile" aria-label="Appeler maintenant">
            <i class="fas fa-phone"></i>
          </a>
        <?php endif; ?>
        <a href="<?= e($ctaUrl) ?>" class="site-header__cta site-header__cta--mobile" aria-label="Estimer mon bien">
          Estimer
        </a>
        <a href="<?= e($ctaUrl) ?>" class="site-header__cta site-header__cta--desktop"><?= e($ctaLabel) ?></a>
        <button type="button" class="site-header__toggle" aria-label="Ouvrir le menu" aria-expanded="false" data-menu-toggle>
          <span class="site-header__toggle-line"></span>
          <span class="site-header__toggle-line"></span>
          <span class="site-header__toggle-line"></span>
        </button>
      </div>
    </div>
  </div>

  <div class="site-header__overlay" data-menu-overlay></div>

  <div class="site-header__mobile-panel" data-mobile-menu>
    <nav class="site-header__mobile-nav" role="navigation" aria-label="Navigation mobile">
      <?php foreach ($navLinks as $link): ?>
        <?php
          $label = (string) ($link['label'] ?? '');
          $url = (string) ($link['url'] ?? '#');
          $active = !empty($link['active']);
        ?>
        <a href="<?= e($url) ?>" class="site-header__mobile-link<?= $active ? ' site-header__mobile-link--active' : '' ?>" data-menu-link><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <?php if ($contactPhone['display'] !== '' && $contactPhone['href'] !== ''): ?>
      <a href="<?= e($contactPhone['href']) ?>" class="site-header__mobile-link" data-menu-link>
        <i class="fas fa-phone"></i> <?= e($contactPhone['display']) ?>
      </a>
    <?php endif; ?>

    <a href="<?= e($ctaUrl) ?>" class="site-header__cta site-header__mobile-cta" data-menu-link><?= e($ctaLabel) ?></a>
  </div>
</header>
