<!DOCTYPE html>
<html lang="fr">
<head>
  <?php
    $branding = is_array($branding ?? null) ? $branding : getBrandingConfig();
    $siteName = trim((string) ($branding['site_name'] ?? 'Estimation Immobilière Premium'));
    $cityName = trim((string) ($branding['city_name'] ?? site('city', 'votre ville')));
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $canonicalPath = (string) parse_url($requestUri, PHP_URL_PATH);
    $canonicalPath = $canonicalPath !== '' ? $canonicalPath : '/';
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $canonicalUrl = $scheme . '://' . $host . $canonicalPath;
    $metaDescription = (string) ($meta_description ?? ('Estimez votre bien à ' . $cityName . ' et activez votre stratégie de vente avec un accompagnement premium.'));
    $title = (string) ($page_title ?? ($siteName . ' | Estimation immobilière vendeurs'));
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($metaDescription) ?>">
  <link rel="canonical" href="<?= e($canonicalUrl) ?>">
  <title><?= e($title) ?></title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($metaDescription) ?>">
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta property="og:locale" content="fr_FR">

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="stylesheet" href="/assets/css/immobilier-premium.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="premium-main">
