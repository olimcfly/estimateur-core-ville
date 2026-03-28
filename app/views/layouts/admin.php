<?php
// Compute role and module access for sidebar rendering
$_adminCurrentRole = \App\Models\AdminUser::currentRole();
$_adminIsSuperUser = ($_adminCurrentRole === \App\Models\AdminUser::ROLE_SUPERUSER);

// Helper to check module access
$_moduleAccess = function(string $slug) use ($_adminCurrentRole): bool {
    return \App\Models\AdminModule::hasAccess($slug, $_adminCurrentRole);
};

// Notification count
$_notifCount = 0;
try {
    $_notifCount = \App\Models\AdminNotification::countUnread(
        $_adminCurrentRole,
        (int) ($_SESSION['admin_user_id'] ?? 0)
    );
} catch (\Throwable $e) {}

// Banner/toast notifications toggle
$_bannerNotifEnabled = $_moduleAccess('notifications_banner');
$__adminBranding = function_exists('getBrandingConfig') ? getBrandingConfig() : [];
$__adminSiteConfig = function_exists('getSiteConfig') ? getSiteConfig() : [];
$__adminSiteName = trim((string) ($__adminBranding['site_name'] ?? ''));
if ($__adminSiteName === '') {
    $__adminSiteName = 'Plateforme immobilière';
}
$__adminPrimary = (string) ($__adminSiteConfig['colors']['primary'] ?? '#1f6f8b');
$__adminAccent = (string) ($__adminSiteConfig['colors']['accent'] ?? '#22a06b');
$__adminPrimaryDark = (string) ($__adminSiteConfig['colors']['primary_dark'] ?? '#174f64');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Admin - ' . htmlspecialchars($__adminSiteName, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- FontAwesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- CSS Principal -->
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">

  <style>
    :root {
      --admin-sidebar-width: 260px;
      --admin-header-height: 60px;
      --admin-bg: #f4f1ed;
      --admin-sidebar-bg: #1a1410;
      --admin-sidebar-text: #c8c0b8;
      --admin-sidebar-hover: rgba(255,255,255,0.08);
      --admin-sidebar-active: rgba(31, 111, 139, 0.35);
      --admin-primary: <?= htmlspecialchars($__adminPrimary, ENT_QUOTES, 'UTF-8') ?>;
      --admin-accent: <?= htmlspecialchars($__adminAccent, ENT_QUOTES, 'UTF-8') ?>;
      --admin-text: #1a1410;
      --admin-surface: #ffffff;
      --admin-muted: #6b6459;
      --admin-border: #e8dfd7;
      --admin-radius: 8px;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; height: 100%; }

    body {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      color: #1a1410;
      background: var(--admin-bg);
      line-height: 1.6;
      display: flex;
      min-height: 100vh;
    }

    /* ================================ */
    /* SIDEBAR                          */
    /* ================================ */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: var(--admin-sidebar-width);
      background: var(--admin-sidebar-bg);
      color: var(--admin-sidebar-text);
      display: flex;
      flex-direction: column;
      z-index: 1000;
      transition: transform 0.3s ease;
      overflow-y: auto;
    }

    .admin-sidebar-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1.25rem 1.5rem;
      text-decoration: none;
      color: #fff;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      flex-shrink: 0;
    }

    .admin-sidebar-brand-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--admin-primary), <?= htmlspecialchars($__adminPrimaryDark, ENT_QUOTES, 'UTF-8') ?>);
      border-radius: 8px;
      color: #fff;
      font-size: 1rem;
      flex-shrink: 0;
    }

    .admin-sidebar-brand-text {
      font-family: inherit;
      font-weight: 700;
      font-size: 1rem;
      line-height: 1.2;
    }

    .admin-sidebar-brand-text small {
      display: block;
      font-family: 'DM Sans', sans-serif;
      font-weight: 400;
      font-size: 0.7rem;
      color: var(--admin-sidebar-text);
      opacity: 0.7;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-top: 2px;
    }

    /* Sidebar navigation */
    .admin-sidebar-nav {
      flex: 1;
      padding: 1rem 0;
    }

    .admin-sidebar-section {
      padding: 0.5rem 1.5rem 0.4rem;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,0.3);
      margin-top: 0.5rem;
    }

    .admin-sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.7rem 1.5rem;
      color: var(--admin-sidebar-text);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      border-left: 3px solid transparent;
      transition: all 0.15s ease;
    }

    .admin-sidebar-link:hover {
      background: var(--admin-sidebar-hover);
      color: #fff;
    }

    .admin-sidebar-link.active {
      background: var(--admin-sidebar-active);
      color: #fff;
      border-left-color: var(--admin-primary);
    }

    .admin-sidebar-link i {
      width: 20px;
      text-align: center;
      font-size: 0.95rem;
      opacity: 0.8;
    }

    .admin-sidebar-link.active i {
      opacity: 1;
      color: var(--admin-accent);
    }

    .admin-sidebar-link .badge {
      margin-left: auto;
      background: var(--admin-primary);
      color: #fff;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 0.15rem 0.5rem;
      border-radius: 10px;
      min-width: 20px;
      text-align: center;
    }

    /* Sidebar footer */
    .admin-sidebar-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(255,255,255,0.08);
      flex-shrink: 0;
    }

    .admin-sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 0.75rem;
    }

    .admin-sidebar-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--admin-primary), #C41E3A);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.8rem;
      font-weight: 700;
      flex-shrink: 0;
    }

    .admin-sidebar-user-info {
      overflow: hidden;
    }

    .admin-sidebar-user-name {
      font-size: 0.85rem;
      font-weight: 600;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-sidebar-user-email {
      font-size: 0.7rem;
      color: var(--admin-sidebar-text);
      opacity: 0.7;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-sidebar-user-role {
      font-size: 0.65rem;
      color: var(--admin-accent);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .admin-sidebar-logout {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.75rem;
      color: var(--admin-sidebar-text);
      text-decoration: none;
      font-size: 0.8rem;
      border-radius: 6px;
      transition: all 0.15s ease;
    }

    .admin-sidebar-logout:hover {
      background: rgba(226, 75, 74, 0.15);
      color: #e24b4a;
    }

    /* ================================ */
    /* MAIN CONTENT AREA                */
    /* ================================ */
    .admin-main {
      flex: 1;
      margin-left: var(--admin-sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Top bar */
    .admin-topbar {
      position: sticky;
      top: 0;
      z-index: 500;
      height: var(--admin-header-height);
      background: rgba(244, 241, 237, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #e8dfd7;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
    }

    .admin-topbar-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-topbar-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #1a1410;
    }

    .admin-topbar-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-topbar-link {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 0.9rem;
      color: #6b6459;
      text-decoration: none;
      font-size: 0.85rem;
      border-radius: 6px;
      transition: all 0.15s ease;
    }

    .admin-topbar-link:hover {
      background: rgba(139, 21, 56, 0.06);
      color: var(--admin-primary);
    }

    /* Notification bell */
    .admin-notif-bell {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 8px;
      background: none;
      border: none;
      cursor: pointer;
      color: #6b6459;
      font-size: 1.1rem;
      transition: all 0.15s ease;
      text-decoration: none;
    }
    .admin-notif-bell:hover {
      background: rgba(139,21,56,0.06);
      color: var(--admin-primary);
    }
    .admin-notif-badge {
      position: absolute;
      top: 4px;
      right: 2px;
      background: #e24b4a;
      color: #fff;
      font-size: 0.6rem;
      font-weight: 700;
      min-width: 16px;
      height: 16px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 3px;
      line-height: 1;
    }

    /* Notification dropdown */
    .admin-notif-dropdown {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 0.5rem;
      width: 360px;
      max-height: 420px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.15);
      border: 1px solid #e8dfd7;
      overflow: hidden;
      z-index: 600;
    }
    .admin-notif-dropdown.open { display: block; }
    .admin-notif-dropdown-header {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #e8dfd7;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .admin-notif-dropdown-header h4 { margin: 0; font-size: 0.9rem; color: #1a1410; }
    .admin-notif-dropdown-list {
      max-height: 320px;
      overflow-y: auto;
    }
    .admin-notif-item {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #f4f1ed;
      text-decoration: none;
      color: #1a1410;
      transition: background 0.1s;
    }
    .admin-notif-item:hover { background: #faf9f7; }
    .admin-notif-item.unread { background: #fef7f0; }
    .admin-notif-item-icon {
      width: 28px;
      height: 28px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 0.8rem;
    }
    .admin-notif-item-text { flex: 1; min-width: 0; }
    .admin-notif-item-title { font-size: 0.82rem; font-weight: 600; line-height: 1.3; }
    .admin-notif-item-time { font-size: 0.72rem; color: #999; margin-top: 2px; }
    .admin-notif-dropdown-footer {
      padding: 0.6rem 1rem;
      border-top: 1px solid #e8dfd7;
      text-align: center;
    }
    .admin-notif-dropdown-footer a {
      color: var(--admin-primary);
      text-decoration: none;
      font-size: 0.82rem;
      font-weight: 600;
    }

    /* Mobile toggle */
    .admin-sidebar-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      color: #1a1410;
      font-size: 1.25rem;
    }

    /* Admin content */
    .admin-content {
      flex: 1;
      padding: 2rem;
    }

    .admin-content .container {
      width: 100%;
      max-width: 100%;
      margin-inline: 0;
    }

    /* Mobile overlay */
    .admin-sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }

    /* ================================ */
    /* RESPONSIVE                       */
    /* ================================ */
    @media (max-width: 1024px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }

      .admin-sidebar.open {
        transform: translateX(0);
      }

      .admin-sidebar-overlay.open {
        display: block;
      }

      .admin-main {
        margin-left: 0;
      }

      .admin-sidebar-toggle {
        display: block;
      }

      .admin-content {
        padding: 1.25rem;
      }

      .admin-notif-dropdown {
        width: 300px;
        right: -1rem;
      }
    }

    @keyframes slideInRight {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @media (max-width: 640px) {
      .admin-topbar {
        padding: 0 1rem;
      }

      .admin-content {
        padding: 1rem;
      }

      .admin-topbar-title {
        font-size: 1rem;
      }
    }

    /* ================================ */
    /* SHARED ADMIN COMPONENTS          */
    /* ================================ */
    .admin-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
    .admin-page-title { font-size: 1.5rem; font-weight: 700; color: var(--admin-text); margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .admin-page-desc { font-size: 0.9rem; color: var(--admin-muted); margin-top: 0.25rem; }

    .admin-card { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); margin-bottom: 1.5rem; overflow: hidden; }
    .admin-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid var(--admin-border); }
    .admin-card-header h2 { font-size: 1rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .admin-card-body { padding: 1.5rem; }

    .admin-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
    .admin-btn-primary { background: var(--admin-primary); color: #fff; }
    .admin-btn-primary:hover { opacity: 0.9; }
    .admin-btn-secondary { background: var(--admin-bg); color: var(--admin-text); border: 1px solid var(--admin-border); }
    .admin-btn-ghost { background: transparent; color: var(--admin-muted); padding: 0.4rem 0.6rem; }
    .admin-btn-ghost:hover { color: var(--admin-primary); background: rgba(139,21,56,0.06); }
    .admin-btn-danger { background: transparent; color: #dc2626; padding: 0.4rem 0.6rem; }
    .admin-btn-danger:hover { background: rgba(239, 68, 68, 0.1); }
    .admin-btn-sm { padding: 0.35rem 0.5rem; font-size: 0.8rem; }
    .admin-btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .admin-badge { background: var(--admin-bg); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; color: var(--admin-muted); font-weight: 600; }

    .admin-alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
    .admin-alert-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
    .admin-alert-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }

    .admin-status { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .admin-status-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
    .admin-status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

    .admin-table-responsive { overflow-x: auto; }
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); border-bottom: 1px solid var(--admin-border); background: var(--admin-bg); }
    .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-border); font-size: 0.9rem; }
    .admin-table tbody tr:hover { background: rgba(0,0,0,0.02); }

    .admin-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--admin-muted); margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.03em; }
    .admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 0.9rem; font-family: inherit; background: #fff; }
    .admin-input:focus { outline: none; border-color: var(--admin-primary); box-shadow: 0 0 0 3px rgba(139,21,56,0.1); }
    .admin-actions { display: flex; gap: 0.25rem; }

    .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .stat-item { background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: var(--admin-radius); padding: 1rem 1.25rem; text-align: center; }
    .stat-value { display: block; font-size: 1.5rem; font-weight: 700; color: var(--admin-text); }
    .stat-label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-muted); margin-top: 0.15rem; }
    .stat-success .stat-value { color: #16a34a; }
    .stat-ai .stat-value { color: var(--admin-primary); }

    @media (max-width: 768px) {
      .admin-page-header { flex-direction: column; }
    }

    /* Reset app.css .btn overrides so Bootstrap buttons work properly in admin */
    .admin-main .btn {
      background: none;
      box-shadow: none;
      border-radius: 0.375rem;
      padding: 0.375rem 0.75rem;
      font-weight: 400;
      font-size: 1rem;
      position: static;
      overflow: visible;
      transform: none;
      transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .admin-main .btn::after {
      display: none;
    }
    .admin-main .btn:hover {
      transform: none;
      box-shadow: none;
    }
    .admin-main .btn-primary {
      background-color: var(--admin-primary);
      border-color: var(--admin-primary);
      color: #fff;
      font-size: 1rem;
    }
    .admin-main .btn-primary:hover {
      background-color: #6b0f2d;
      border-color: #6b0f2d;
      color: #fff;
    }
    .admin-main .btn-outline-primary {
      color: var(--admin-primary);
      border: 1px solid var(--admin-primary);
      background: transparent;
    }
    .admin-main .btn-outline-primary:hover {
      background-color: var(--admin-primary);
      border-color: var(--admin-primary);
      color: #fff;
    }
    .admin-main .btn-outline-secondary {
      color: #6c757d;
      border: 1px solid #6c757d;
      background: transparent;
    }
    .admin-main .btn-outline-secondary:hover {
      background-color: #6c757d;
      border-color: #6c757d;
      color: #fff;
    }
    .admin-main .btn-outline-info {
      color: #0dcaf0;
      border: 1px solid #0dcaf0;
      background: transparent;
    }
    .admin-main .btn-outline-info:hover {
      background-color: #0dcaf0;
      border-color: #0dcaf0;
      color: #000;
    }
    .admin-main .btn-outline-success {
      color: #198754;
      border: 1px solid #198754;
      background: transparent;
    }
    .admin-main .btn-outline-success:hover {
      background-color: #198754;
      border-color: #198754;
      color: #fff;
    }
    .admin-main .btn-outline-danger {
      color: #dc3545;
      border: 1px solid #dc3545;
      background: transparent;
    }
    .admin-main .btn-outline-danger:hover {
      background-color: #dc3545;
      border-color: #dc3545;
      color: #fff;
    }
    .admin-main .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
      border-radius: 0.25rem;
    }
  </style>
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="admin-sidebar-overlay" id="sidebar-overlay"></div>

<!-- SIDEBAR -->
<aside class="admin-sidebar" id="admin-sidebar">
  <a href="/admin" class="admin-sidebar-brand">
    <span class="admin-sidebar-brand-icon"><i class="fas fa-home"></i></span>
    <span class="admin-sidebar-brand-text">
      Estimation <?= htmlspecialchars((string) site('city', ''), ENT_QUOTES, 'UTF-8') ?>
      <small>Administration</small>
    </span>
  </a>

  <nav class="admin-sidebar-nav">
    <div class="admin-sidebar-section">Principal</div>
    <a href="/admin" class="admin-sidebar-link <?= ($admin_page ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="fas fa-tachometer-alt"></i> Tableau de bord
    </a>
    <?php if ($_moduleAccess('leads')): ?>
    <a href="/admin/leads" class="admin-sidebar-link <?= ($admin_page ?? '') === 'leads' ? 'active' : '' ?>">
      <i class="fas fa-users"></i> Leads
    </a>
    <a href="/admin/funnel" class="admin-sidebar-link <?= ($admin_page ?? '') === 'funnel' ? 'active' : '' ?>">
      <i class="fas fa-filter"></i> Entonnoir de vente
    </a>
    <a href="/admin/pipeline" class="admin-sidebar-link <?= ($admin_page ?? '') === 'pipeline' ? 'active' : '' ?>">
      <i class="fas fa-columns"></i> Pipeline
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('partenaires')): ?>
    <a href="/admin/partenaires" class="admin-sidebar-link <?= ($admin_page ?? '') === 'partenaires' ? 'active' : '' ?>">
      <i class="fas fa-handshake"></i> Partenaires
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('achats')): ?>
    <a href="/admin/achats" class="admin-sidebar-link <?= ($admin_page ?? '') === 'achats' ? 'active' : '' ?>">
      <i class="fas fa-shopping-cart"></i> Achats
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('financement')): ?>
    <a href="/admin/financement" class="admin-sidebar-link <?= ($admin_page ?? '') === 'financement' ? 'active' : '' ?>">
      <i class="fas fa-credit-card"></i> Financement
    </a>
    <?php endif; ?>

    <div class="admin-sidebar-section">Contenu</div>
    <?php if ($_moduleAccess('blog')): ?>
    <a href="/admin/blog" class="admin-sidebar-link <?= ($admin_page ?? '') === 'blog' ? 'active' : '' ?>">
      <i class="fas fa-pen-fancy"></i> Articles Blog
    </a>
    <?php endif; ?>
    <a href="/admin/seo-hub" class="admin-sidebar-link <?= ($admin_page ?? '') === 'seo-hub' ? 'active' : '' ?>">
      <i class="fas fa-chart-line"></i> SEO Hub (GSC)
    </a>
    <?php if ($_moduleAccess('actualites')): ?>
    <a href="/admin/actualites" class="admin-sidebar-link <?= ($admin_page ?? '') === 'actualites' ? 'active' : '' ?>">
      <i class="fas fa-newspaper"></i> Actualites
    </a>
    <?php endif; ?>
    <a href="/admin/rss" class="admin-sidebar-link <?= ($admin_page ?? '') === 'rss' ? 'active' : '' ?>">
      <i class="fas fa-rss"></i> Veille RSS
    </a>
    <?php if ($_moduleAccess('images')): ?>
    <a href="/admin/images" class="admin-sidebar-link <?= ($admin_page ?? '') === 'images' ? 'active' : '' ?>">
      <i class="fas fa-image"></i> Images IA
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('social_images')): ?>
    <a href="/admin/social-images" class="admin-sidebar-link <?= ($admin_page ?? '') === 'social-images' ? 'active' : '' ?>">
      <i class="fas fa-share-alt"></i> Images Sociaux
    </a>
    <?php endif; ?>
    <a href="/admin/gmb" class="admin-sidebar-link <?= str_starts_with($admin_page ?? '', 'gmb') ? 'active' : '' ?>">
      <i class="fas fa-map-marker-alt"></i> Google My Business
    </a>
    <a href="/admin/gmb/guide" class="admin-sidebar-link <?= ($admin_page ?? '') === 'gmb-guide' ? 'active' : '' ?>" style="padding-left: 2.5rem; font-size: 0.85rem;">
      <i class="fas fa-book-open"></i> Guide GMB
    </a>
    <?php if ($_moduleAccess('google_ads')): ?>
    <a href="/admin/google-ads" class="admin-sidebar-link <?= ($admin_page ?? '') === 'google-ads-guide' ? 'active' : '' ?>">
      <i class="fas fa-book"></i> Guide Google Ads
    </a>
    <a href="/admin/google-ads/campaigns" class="admin-sidebar-link <?= ($admin_page ?? '') === 'google-ads-campaigns' ? 'active' : '' ?>">
      <i class="fas fa-magic"></i> Generateur Campagnes
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('gmb')): ?>
    <a href="/admin/gmb" class="admin-sidebar-link <?= ($admin_page ?? '') === 'gmb' ? 'active' : '' ?>">
      <i class="fab fa-google"></i> Google My Business
    </a>
    <?php endif; ?>

    <?php if ($_moduleAccess('emails') || $_moduleAccess('sequences') || $_moduleAccess('mailbox')): ?>
    <div class="admin-sidebar-section">Communication</div>
    <?php if ($_moduleAccess('mailbox')): ?>
    <a href="/admin/mailbox" class="admin-sidebar-link <?= ($admin_page ?? '') === 'mailbox' ? 'active' : '' ?>">
      <i class="fas fa-envelope"></i> Boîte Email
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('emails')): ?>
    <a href="/admin/emails" class="admin-sidebar-link <?= ($admin_page ?? '') === 'emails' ? 'active' : '' ?>">
      <i class="fas fa-envelope-open-text"></i> Emails
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('sequences')): ?>
    <a href="/admin/sequences" class="admin-sidebar-link <?= ($admin_page ?? '') === 'sequences' ? 'active' : '' ?>">
      <i class="fas fa-paper-plane"></i> Sequences
    </a>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ($_moduleAccess('notifications_internes')): ?>
    <div class="admin-sidebar-section">Notifications</div>
    <a href="/admin/notifications" class="admin-sidebar-link <?= ($admin_page ?? '') === 'notifications' ? 'active' : '' ?>">
      <i class="fas fa-inbox"></i> Notifications
      <?php if ($_notifCount > 0): ?>
        <span class="badge"><?= $_notifCount ?></span>
      <?php endif; ?>
    </a>
    <?php endif; ?>

    <div class="admin-sidebar-section">Outils</div>
    <a href="/admin/smtp-api" class="admin-sidebar-link <?= ($admin_page ?? '') === 'smtp-api-management' ? 'active' : '' ?>">
      <i class="fas fa-cogs"></i> SMTP, API & IA
    </a>
    <a href="/admin/api-costs" class="admin-sidebar-link <?= ($admin_page ?? '') === 'api-costs' ? 'active' : '' ?>">
      <i class="fas fa-chart-line"></i> Couts API
    </a>
    <?php if ($_moduleAccess('api_management')): ?>
    <a href="/admin/api-management" class="admin-sidebar-link <?= ($admin_page ?? '') === 'api-management' ? 'active' : '' ?>">
      <i class="fas fa-key"></i> API
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('database')): ?>
    <a href="/admin/database" class="admin-sidebar-link <?= ($admin_page ?? '') === 'database' ? 'active' : '' ?>">
      <i class="fas fa-database"></i> Base de donnees
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('diagnostic')): ?>
    <a href="/admin/diagnostic" class="admin-sidebar-link <?= ($admin_page ?? '') === 'diagnostic' ? 'active' : '' ?>">
      <i class="fas fa-stethoscope"></i> Diagnostic
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('smtp')): ?>
    <a href="/admin/test-smtp" class="admin-sidebar-link <?= ($admin_page ?? '') === 'smtp' ? 'active' : '' ?>">
      <i class="fas fa-envelope"></i> SMTP
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('google_ads')): ?>
    <a href="/admin/google-ads" class="admin-sidebar-link <?= ($admin_page ?? '') === 'google-ads' ? 'active' : '' ?>">
      <i class="fas fa-ad"></i> Google Ads
    </a>
    <?php endif; ?>
    <?php if ($_moduleAccess('gads_campaigns')): ?>
    <a href="/admin/gads-campaigns" class="admin-sidebar-link <?= ($admin_page ?? '') === 'gads-campaigns' ? 'active' : '' ?>">
      <i class="fas fa-bullhorn"></i> Campagnes Ads
    </a>
    <?php endif; ?>
    <?php /* Google Ads moved to Contenu section */ ?>

    <?php if ($_adminIsSuperUser): ?>
    <div class="admin-sidebar-section">Systeme</div>
    <a href="/admin/settings" class="admin-sidebar-link <?= ($admin_page ?? '') === 'settings' ? 'active' : '' ?>">
      <i class="fas fa-cog"></i> Paramètres
    </a>
    <a href="/admin/analytics-settings" class="admin-sidebar-link <?= ($admin_page ?? '') === 'analytics-settings' ? 'active' : '' ?>">
      <i class="fas fa-chart-pie"></i> Analytics & Tracking
    </a>
    <a href="/admin/modules" class="admin-sidebar-link <?= ($admin_page ?? '') === 'modules' ? 'active' : '' ?>">
      <i class="fas fa-puzzle-piece"></i> Modules
    </a>
    <?php if ($_moduleAccess('user_management')): ?>
    <a href="/admin/users" class="admin-sidebar-link <?= ($admin_page ?? '') === 'users' ? 'active' : '' ?>">
      <i class="fas fa-user-shield"></i> Utilisateurs
    </a>
    <?php endif; ?>
    <?php endif; ?>

    <a href="/" class="admin-sidebar-link" target="_blank">
      <i class="fas fa-external-link-alt"></i> Voir le site
    </a>
  </nav>

  <div class="admin-sidebar-footer">
    <div class="admin-sidebar-user">
      <div class="admin-sidebar-avatar" style="<?= $_adminIsSuperUser ? 'background:linear-gradient(135deg,#D4AF37,#B8941F);' : '' ?>">
        <?= strtoupper(mb_substr((string) ($_SESSION['admin_user_name'] ?? 'A'), 0, 1)) ?>
      </div>
      <div class="admin-sidebar-user-info">
        <div class="admin-sidebar-user-name"><?= htmlspecialchars((string) ($_SESSION['admin_user_name'] ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="admin-sidebar-user-role"><?= $_adminIsSuperUser ? 'Super-utilisateur' : 'Administrateur' ?></div>
        <div class="admin-sidebar-user-email"><?= htmlspecialchars((string) ($_SESSION['admin_user_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>
    <a href="/admin/logout" class="admin-sidebar-logout">
      <i class="fas fa-sign-out-alt"></i> Deconnexion
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="admin-main">
  <header class="admin-topbar">
    <div class="admin-topbar-left">
      <button class="admin-sidebar-toggle" id="sidebar-toggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
      </button>
      <span class="admin-topbar-title"><?= htmlspecialchars((string) ($admin_page_title ?? 'Administration'), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <div class="admin-topbar-right">
      <!-- Notification Bell -->
      <?php if ($_moduleAccess('notifications_internes')): ?>
      <div style="position:relative;" id="notif-container">
        <button class="admin-notif-bell" id="notif-bell-btn" aria-label="Notifications" onclick="toggleNotifDropdown()">
          <i class="fas fa-bell"></i>
          <?php if ($_notifCount > 0): ?>
            <span class="admin-notif-badge" id="notif-badge"><?= $_notifCount ?></span>
          <?php endif; ?>
        </button>
        <div class="admin-notif-dropdown" id="notif-dropdown">
          <div class="admin-notif-dropdown-header">
            <h4>Notifications</h4>
            <button onclick="markAllNotifRead()" style="background:none;border:none;color:#3b82f6;cursor:pointer;font-size:0.78rem;">Tout marquer lu</button>
          </div>
          <div class="admin-notif-dropdown-list" id="notif-list">
            <div style="padding:2rem;text-align:center;color:#999;font-size:0.85rem;">Chargement...</div>
          </div>
          <div class="admin-notif-dropdown-footer">
            <a href="/admin/notifications">Voir toutes les notifications</a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($_moduleAccess('api_management')): ?>
      <a href="/admin/api-management" class="admin-topbar-link" title="Parametres API (OpenAI, Perplexity...)">
        <i class="fas fa-key"></i> <span>API</span>
      </a>
      <?php endif; ?>

      <a href="/" class="admin-topbar-link" target="_blank">
        <i class="fas fa-external-link-alt"></i> <span>Voir le site</span>
      </a>
    </div>
  </header>

  <?php if (filter_var($_ENV['DEV_SKIP_AUTH'] ?? $_SERVER['DEV_SKIP_AUTH'] ?? 'false', FILTER_VALIDATE_BOOLEAN)): ?>
  <div style="background:linear-gradient(90deg,#92400e,#d97706);color:#fff;padding:0.5rem 2rem;font-size:0.82rem;font-weight:600;display:flex;align-items:center;gap:0.5rem;">
    <i class="fas fa-exclamation-triangle"></i>
    Mode d&eacute;veloppeur actif &mdash; authentification d&eacute;sactiv&eacute;e (DEV_SKIP_AUTH=true)
    <a href="/admin/diagnostic" style="color:#fff;margin-left:auto;text-decoration:underline;font-weight:400;">G&eacute;rer</a>
  </div>
  <?php endif; ?>

  <div class="admin-content">
    %%ADMIN_CONTENT%%
  </div>
</div>

<script>
(function() {
  var toggle = document.getElementById('sidebar-toggle');
  var sidebar = document.getElementById('admin-sidebar');
  var overlay = document.getElementById('sidebar-overlay');

  if (!toggle || !sidebar || !overlay) return;

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', function() {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

  window.addEventListener('resize', function() {
    if (window.innerWidth > 1024) closeSidebar();
  });
})();

// Admin presence heartbeat - signals that admin is active on this page
(function() {
  var currentPage = window.location.pathname;

  function sendHeartbeat() {
    var fd = new FormData();
    fd.append('page', currentPage);
    fetch('/admin/presence/heartbeat', { method: 'POST', body: fd, credentials: 'same-origin' }).catch(function() {});
  }

  // Send immediately, then every 30 seconds
  sendHeartbeat();
  var heartbeatInterval = setInterval(sendHeartbeat, 30000);

  // Clear presence when leaving the page
  window.addEventListener('beforeunload', function() {
    clearInterval(heartbeatInterval);
    navigator.sendBeacon('/admin/presence/clear', '');
  });
})();

// Notification bell functionality
var _notifDropdownOpen = false;

function toggleNotifDropdown() {
  var dd = document.getElementById('notif-dropdown');
  if (!dd) return;
  _notifDropdownOpen = !_notifDropdownOpen;
  dd.classList.toggle('open', _notifDropdownOpen);
  if (_notifDropdownOpen) loadNotifications();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
  var container = document.getElementById('notif-container');
  if (container && !container.contains(e.target)) {
    var dd = document.getElementById('notif-dropdown');
    if (dd) dd.classList.remove('open');
    _notifDropdownOpen = false;
  }
});

function loadNotifications() {
  fetch('/admin/notifications/fetch', { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) return;
      var list = document.getElementById('notif-list');
      var badge = document.getElementById('notif-badge');

      if (badge) {
        if (data.unread > 0) {
          badge.textContent = data.unread;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }

      if (!data.notifications || data.notifications.length === 0) {
        list.innerHTML = '<div style="padding:2rem;text-align:center;color:#999;font-size:0.85rem;">Aucune notification</div>';
        return;
      }

      var typeColors = { lead: '#8B1538', success: '#22c55e', warning: '#f97316', error: '#e24b4a', system: '#6b6459', info: '#3b82f6' };
      var typeIcons = { lead: 'fa-user-plus', success: 'fa-check-circle', warning: 'fa-exclamation-triangle', error: 'fa-times-circle', system: 'fa-cog', info: 'fa-info-circle' };

      var html = '';
      data.notifications.forEach(function(n) {
        var color = typeColors[n.type] || '#3b82f6';
        var icon = typeIcons[n.type] || 'fa-info-circle';
        var link = n.link || '/admin/notifications';
        html += '<a href="' + link + '" class="admin-notif-item ' + (n.is_read ? '' : 'unread') + '" onclick="markNotifRead(' + n.id + ')">';
        html += '<div class="admin-notif-item-icon" style="background:' + color + '15;color:' + color + ';"><i class="fas ' + icon + '"></i></div>';
        html += '<div class="admin-notif-item-text"><div class="admin-notif-item-title">' + escHtml(n.title) + '</div>';
        html += '<div class="admin-notif-item-time">' + escHtml(n.time_ago) + '</div></div></a>';
      });
      list.innerHTML = html;
    })
    .catch(function() {});
}

function markNotifRead(id) {
  var fd = new FormData();
  fd.append('id', id);
  fetch('/admin/notifications/read', { method: 'POST', body: fd, credentials: 'same-origin' }).catch(function() {});
}

function markAllNotifRead() {
  fetch('/admin/notifications/read-all', { method: 'POST', credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        var badge = document.getElementById('notif-badge');
        if (badge) badge.style.display = 'none';
        loadNotifications();
      }
    });
}

function escHtml(str) {
  var d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

// ─── Banner notifications toggle (controlled from backend modules) ───
var _bannerNotifEnabled = <?= $_bannerNotifEnabled ? 'true' : 'false' ?>;

// ─── Browser notifications permission ───
(function() {
  if (_bannerNotifEnabled && 'Notification' in window && Notification.permission === 'default') {
    setTimeout(function() { Notification.requestPermission(); }, 3000);
  }
})();

// ─── Toast notification container ───
(function() {
  var container = document.createElement('div');
  container.id = 'toast-container';
  container.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;pointer-events:none;';
  document.body.appendChild(container);

  window.showToast = function(title, message, type, link) {
    if (!_bannerNotifEnabled) return;

    var typeColors = { lead: '#8B1538', success: '#22c55e', warning: '#f97316', error: '#e24b4a', system: '#6b6459', info: '#3b82f6' };
    var typeIcons = { lead: 'fa-user-plus', success: 'fa-check-circle', warning: 'fa-exclamation-triangle', error: 'fa-times-circle', system: 'fa-cog', info: 'fa-info-circle' };
    var color = typeColors[type] || '#3b82f6';
    var icon = typeIcons[type] || 'fa-info-circle';

    var toast = document.createElement('div');
    toast.style.cssText = 'pointer-events:auto;background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.15);border-left:4px solid ' + color + ';padding:0.75rem 1rem;display:flex;align-items:flex-start;gap:0.75rem;max-width:380px;animation:slideInRight 0.3s ease;cursor:pointer;';
    toast.innerHTML = '<div style="width:28px;height:28px;border-radius:6px;background:' + color + '15;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas ' + icon + '" style="color:' + color + ';font-size:0.8rem;"></i></div>'
      + '<div style="flex:1;"><div style="font-size:0.85rem;font-weight:600;color:#1a1410;">' + escHtml(title) + '</div>'
      + (message ? '<div style="font-size:0.78rem;color:#6b6459;margin-top:2px;">' + escHtml(message) + '</div>' : '')
      + '</div><button onclick="this.parentElement.remove()" style="background:none;border:none;color:#999;cursor:pointer;font-size:0.9rem;padding:0;">&times;</button>';

    if (link) toast.addEventListener('click', function(e) { if (e.target.tagName !== 'BUTTON') window.location.href = link; });
    container.appendChild(toast);
    setTimeout(function() { if (toast.parentElement) toast.remove(); }, 8000);
  };
})();

// ─── Auto-refresh notifications every 30 seconds with browser + toast alerts ───
var _lastNotifCount = <?= $_notifCount ?>;
setInterval(function() {
  fetch('/admin/notifications/fetch', { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) return;

      // Update banner toggle dynamically from backend
      if (typeof data.banner_enabled !== 'undefined') {
        _bannerNotifEnabled = data.banner_enabled;
      }

      var badge = document.getElementById('notif-badge');
      if (badge) {
        if (data.unread > 0) {
          badge.textContent = data.unread;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }

      // Show toast + browser notification for new notifications (only if banner enabled)
      if (_bannerNotifEnabled && data.unread > _lastNotifCount && data.notifications && data.notifications.length > 0) {
        var newest = data.notifications[0];
        if (!newest.is_read) {
          showToast(newest.title, newest.message, newest.type, newest.link);

          // Browser push notification
          if ('Notification' in window && Notification.permission === 'granted') {
            var n = new Notification(newest.title, {
              body: newest.message || '',
              icon: '/favicon.svg',
              tag: 'notif-' + newest.id
            });
            if (newest.link) n.onclick = function() { window.focus(); window.location.href = newest.link; };
          }
        }
      }
      _lastNotifCount = data.unread;
    })
    .catch(function() {});
}, 30000);
</script>

</body>
</html>
