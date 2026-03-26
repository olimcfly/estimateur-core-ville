<?php
$siteConfig = getSiteConfig();
$colors = $siteConfig['colors'] ?? ($colors ?? []);
$rgbColors = $siteConfig['rgb_colors'] ?? ($rgbColors ?? []);
?>
<head>
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
      --primary-rgb: <?= e((string) ($rgbColors['primary'] ?? '139, 21, 56')) ?>;
      --border-rgb: <?= e((string) ($rgbColors['border'] ?? '232, 223, 215')) ?>;
      --z-sticky-cta: 999;
      --z-header: 1000;
      --z-mobile-overlay: 1050;
      --z-popup-lead: 1100;
      --header-height-desktop: 72px;
      --header-height-mobile: 60px;
      --sticky-cta-height: 64px;
    }
  </style>
</head>
