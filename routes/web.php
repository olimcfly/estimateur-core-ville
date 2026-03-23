<?php

declare(strict_types=1);

use App\Controllers\ActualiteController;
use App\Controllers\AdminActualiteController;
use App\Controllers\AdminBlogController;
use App\Controllers\AdminAchatController;
use App\Controllers\AdminController;
use App\Controllers\AdminDashboardController;
use App\Controllers\AdminDatabaseController;
use App\Controllers\AdminEmailController;
use App\Controllers\AdminImageController;
use App\Controllers\AdminLeadController;
use App\Controllers\AdminPartenaireController;
use App\Controllers\AdminSequenceController;
use App\Controllers\AdminDiagnosticController;
use App\Controllers\AdminApiController;
use App\Controllers\AdminApiCostsController;
use App\Controllers\AdminSmtpApiController;
use App\Controllers\AdminModuleController;
use App\Controllers\AdminMailboxController;
use App\Controllers\AdminNotificationController;
use App\Controllers\AdminSocialImageController;
use App\Controllers\AdminAnalyticsSettingsController;
use App\Controllers\AdminSettingsController;
use App\Controllers\AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\EstimationController;
use App\Controllers\PageController;
use App\Controllers\LandingPageController;
use App\Controllers\AdminFinancementController;
use App\Controllers\AdminGoogleAdsCampaignController;
use App\Controllers\AdminRssController;
use App\Controllers\AdminSeoHubController;
use App\Controllers\ToolController;

$router->get('/', [PageController::class, 'home']);
$router->get('/estimation', [EstimationController::class, 'index']);
$router->get('/leads', [EstimationController::class, 'leads']);
$router->post('/estimation', [EstimationController::class, 'estimate']);
$router->post('/api/estimation', [EstimationController::class, 'apiEstimate']);
$router->post('/lead', [EstimationController::class, 'storeLead']);

// Auth routes
$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);
$router->get('/admin/diagnostic', [AdminDiagnosticController::class, 'index']);
$router->get('/admin/diagnostic/database', [AdminDiagnosticController::class, 'databaseDiagnostic']);
$router->get('/admin/test-smtp', [AuthController::class, 'testSmtp']);
$router->post('/admin/test-smtp/save', [AuthController::class, 'testSmtpSave']);
$router->post('/admin/test-smtp/reset', [AuthController::class, 'testSmtpReset']);
$router->post('/admin/test-smtp/run', [AuthController::class, 'testSmtpRun']);
$router->post('/admin/test-smtp/send', [AuthController::class, 'testSmtpSendEmail']);
$router->post('/admin/dev-skip-auth/toggle', [AuthController::class, 'toggleDevSkipAuth']);
$router->post('/admin/presence/heartbeat', [AuthController::class, 'presenceHeartbeat']);
$router->post('/admin/presence/clear', [AuthController::class, 'presenceClear']);
$router->get('/api/presence/check', [AuthController::class, 'presenceCheck']);

// Protected admin routes
$router->get('/admin', [AdminDashboardController::class, 'index']);
$router->get('/admin/leads', [AdminLeadController::class, 'index']);
$router->post('/admin/leads/create-table', [AdminLeadController::class, 'createTable']);
$router->get('/admin/leads/ajax-detail', [AdminLeadController::class, 'ajaxDetail']);
$router->get('/admin/leads/export-csv', [AdminLeadController::class, 'exportCsv']);
$router->get('/admin/leads/{id}', [AdminLeadController::class, 'show']);
$router->get('/admin/leads/edit/{id}', [AdminLeadController::class, 'edit']);
$router->post('/admin/leads/update/{id}', [AdminLeadController::class, 'update']);
$router->post('/admin/leads/statut/{id}', [AdminLeadController::class, 'updateStatut']);
$router->post('/admin/leads/note/{id}', [AdminLeadController::class, 'addNote']);
$router->post('/admin/leads/note/delete/{id}', [AdminLeadController::class, 'deleteNote']);
$router->post('/admin/leads/update-inline', [AdminLeadController::class, 'quickUpdate']);
$router->post('/admin/leads/delete/{id}', [AdminLeadController::class, 'delete']);
$router->post('/admin/leads/bulk-action', [AdminLeadController::class, 'bulkAction']);
$router->post('/admin/leads/create-tables', [AdminLeadController::class, 'createTables']);

// Admin funnel, pipeline & portfolio
$router->get('/admin/funnel', [AdminDashboardController::class, 'funnel']);
$router->post('/admin/funnel/create-table', [AdminDashboardController::class, 'createLeadsTable']);
$router->get('/admin/pipeline', [EstimationController::class, 'pipeline']);
$router->get('/admin/portfolio', [AdminDashboardController::class, 'portfolio']);
$router->post('/admin/portfolio/commission', [AdminDashboardController::class, 'updateCommissionRate']);

// Admin achats routes
$router->get('/admin/achats', [AdminAchatController::class, 'index']);
$router->get('/admin/achats/edit', [AdminAchatController::class, 'edit']);
$router->post('/admin/achats/save', [AdminAchatController::class, 'save']);
$router->post('/admin/achats/delete', [AdminAchatController::class, 'delete']);
$router->post('/admin/achats/create-table', [AdminAchatController::class, 'createTable']);

// Admin financement routes (demandes de financement visiteurs — 2L Courtage)
$router->get('/admin/financement', [AdminFinancementController::class, 'index']);
$router->get('/admin/financement/edit', [AdminFinancementController::class, 'edit']);
$router->post('/admin/financement/save', [AdminFinancementController::class, 'save']);
$router->post('/admin/financement/delete', [AdminFinancementController::class, 'delete']);
$router->post('/admin/financement/transmettre', [AdminFinancementController::class, 'transmettre']);
$router->get('/admin/financement/export-courtier', [AdminFinancementController::class, 'exportCourtier']);
$router->post('/admin/financement/create-table', [AdminFinancementController::class, 'createTable']);

// Admin partenaires routes
$router->get('/admin/partenaires', [AdminPartenaireController::class, 'index']);
$router->get('/admin/partenaires/edit', [AdminPartenaireController::class, 'edit']);
$router->post('/admin/partenaires/save', [AdminPartenaireController::class, 'save']);
$router->post('/admin/partenaires/delete', [AdminPartenaireController::class, 'delete']);

// Admin social images routes
$router->get('/admin/social-images', [AdminSocialImageController::class, 'index']);
$router->get('/admin/social-images/history', [AdminSocialImageController::class, 'history']);
$router->post('/admin/social-images/save', [AdminSocialImageController::class, 'save']);
$router->post('/admin/social-images/delete', [AdminSocialImageController::class, 'delete']);

$router->get('/services', [PageController::class, 'services']);
$router->get('/about', [PageController::class, 'about']);
$router->get('/a-propos', [PageController::class, 'aPropos']);
$router->get('/processus-estimation', [PageController::class, 'processusEstimation']);
$router->get('/quartiers', [PageController::class, 'quartiers']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/newsletter', [PageController::class, 'newsletter']);
$router->post('/newsletter', [PageController::class, 'newsletterSubscribe']);
$router->get('/newsletter/confirm', [PageController::class, 'newsletterConfirm']);
$router->get('/exemples-estimation', [PageController::class, 'exemplesEstimation']);
$router->get('/guides', [PageController::class, 'guides']);
$router->post('/contact', [PageController::class, 'contactSubmit']);
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/marche-immobilier', [BlogController::class, 'category']);
$router->get('/blog/vendre-son-bien', [BlogController::class, 'category']);
$router->get('/blog/conseils-astuces', [BlogController::class, 'category']);
$router->get('/blog/aspect-juridique', [BlogController::class, 'category']);
$router->get('/blog/aspects-juridiques', [BlogController::class, 'category']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

// Actualités (news) routes
$router->get('/actualites', [ActualiteController::class, 'index']);
$router->get('/actualites/{slug}', [ActualiteController::class, 'show']);

$router->get('/mentions-legales', [PageController::class, 'mentionsLegales']);
$router->get('/politique-confidentialite', [PageController::class, 'politiqueConfidentialite']);
$router->get('/conditions-utilisation', [PageController::class, 'conditionsUtilisation']);
$router->get('/rgpd', [PageController::class, 'rgpd']);

$router->get('/tools/calculatrice', [ToolController::class, 'calculatrice']);

// Admin blog routes
$router->get('/admin/blog', [AdminBlogController::class, 'index']);
$router->get('/admin/blog/create', [AdminBlogController::class, 'create']);
$router->get('/admin/blog/wizard', [AdminBlogController::class, 'wizard']);
$router->post('/admin/blog/wizard/generate', [AdminBlogController::class, 'wizardGenerate']);
$router->post('/admin/blog/store', [AdminBlogController::class, 'store']);
$router->get('/admin/blog/edit/{id}', [AdminBlogController::class, 'edit']);
$router->post('/admin/blog/update/{id}', [AdminBlogController::class, 'update']);
$router->post('/admin/blog/delete/{id}', [AdminBlogController::class, 'delete']);
$router->post('/admin/blog/generate', [AdminBlogController::class, 'generate']);
$router->post('/admin/blog/restore/{id}/{revisionId}', [AdminBlogController::class, 'restoreRevision']);
$router->post('/admin/blog/api/analyze', [AdminBlogController::class, 'analyzeApi']);
$router->post('/admin/blog/api/check-indexing', [AdminBlogController::class, 'checkIndexing']);
$router->get('/admin/blog/silos', [AdminBlogController::class, 'silos']);
$router->post('/admin/blog/silos/create', [AdminBlogController::class, 'createSilo']);
$router->post('/admin/blog/silos/delete/{id}', [AdminBlogController::class, 'deleteSilo']);
$router->get('/admin/blog/seo-guide', [AdminBlogController::class, 'seoGuide']);
$router->get('/admin/blog/ideas', [AdminBlogController::class, 'ideas']);
$router->post('/admin/blog/api/ai-suggest', [AdminBlogController::class, 'aiSuggest']);

// Admin actualités routes
$router->get('/admin/actualites', [AdminActualiteController::class, 'index']);
$router->get('/admin/actualites/create', [AdminActualiteController::class, 'create']);
$router->post('/admin/actualites/store', [AdminActualiteController::class, 'store']);
$router->get('/admin/actualites/edit/{id}', [AdminActualiteController::class, 'edit']);
$router->post('/admin/actualites/update/{id}', [AdminActualiteController::class, 'update']);
$router->post('/admin/actualites/delete/{id}', [AdminActualiteController::class, 'delete']);
$router->post('/admin/actualites/search', [AdminActualiteController::class, 'search']);
$router->post('/admin/actualites/generate', [AdminActualiteController::class, 'generate']);
$router->post('/admin/actualites/generate-rss', [AdminActualiteController::class, 'generateFromRss']);
$router->post('/admin/actualites/save-ai-config', [AdminActualiteController::class, 'saveAiConfig']);

// Admin AI image generation routes
$router->get('/admin/images', [AdminImageController::class, 'index']);
$router->post('/admin/images/generate', [AdminImageController::class, 'generate']);
$router->post('/admin/images/delete', [AdminImageController::class, 'delete']);
$router->post('/admin/api/images/generate', [AdminImageController::class, 'apiGenerate']);
$router->get('/admin/api/images/seo-prompt', [AdminImageController::class, 'apiSeoPrompt']);

// Admin database management routes
$router->get('/admin/database', [AdminDatabaseController::class, 'index']);
$router->post('/admin/database', [AdminDatabaseController::class, 'index']);

// Admin email template routes
$router->get('/admin/emails', [AdminEmailController::class, 'index']);
$router->get('/admin/emails/edit', [AdminEmailController::class, 'edit']);
$router->post('/admin/emails/save', [AdminEmailController::class, 'save']);
$router->post('/admin/emails/delete', [AdminEmailController::class, 'delete']);
$router->post('/admin/emails/send-test', [AdminEmailController::class, 'sendTest']);
$router->post('/admin/emails/ai-generate', [AdminEmailController::class, 'aiGenerate']);

// Admin email sequence routes
$router->get('/admin/sequences', [AdminSequenceController::class, 'index']);
$router->get('/admin/sequences/edit', [AdminSequenceController::class, 'edit']);
$router->post('/admin/sequences/save', [AdminSequenceController::class, 'save']);
$router->post('/admin/sequences/delete', [AdminSequenceController::class, 'delete']);
$router->post('/admin/sequences/save-persona', [AdminSequenceController::class, 'savePersona']);
$router->get('/admin/sequences/article-suggestions', [AdminSequenceController::class, 'articleSuggestions']);

// Google Ads Landing Pages (capture pages — no navigation)
$router->get('/lp/estimation-bordeaux', [LandingPageController::class, 'estimationBordeaux']);
$router->get('/lp/vendre-maison-bordeaux', [LandingPageController::class, 'vendreMaisonBordeaux']);
$router->get('/lp/avis-valeur-gratuit', [LandingPageController::class, 'avisValeurGratuit']);
$router->post('/lp/submit', [LandingPageController::class, 'submitLead']);

// Admin: Google Ads guide & campaign generator
$router->get('/admin/google-ads', [LandingPageController::class, 'guide']);
$router->get('/admin/google-ads/campaigns', [LandingPageController::class, 'campaigns']);

// Admin: Google Ads Campaign Manager
$router->get('/admin/gads-campaigns', [AdminGoogleAdsCampaignController::class, 'index']);
$router->get('/admin/gads-campaigns/wizard', [AdminGoogleAdsCampaignController::class, 'wizard']);
$router->get('/admin/gads-campaigns/preview', [AdminGoogleAdsCampaignController::class, 'preview']);
$router->get('/admin/gads-campaigns/export', [AdminGoogleAdsCampaignController::class, 'apiExport']);
$router->post('/admin/gads-campaigns/api/generate', [AdminGoogleAdsCampaignController::class, 'apiGenerate']);
$router->post('/admin/gads-campaigns/api/save', [AdminGoogleAdsCampaignController::class, 'apiSave']);
$router->post('/admin/gads-campaigns/api/delete', [AdminGoogleAdsCampaignController::class, 'apiDelete']);
$router->post('/admin/gads-campaigns/api/status', [AdminGoogleAdsCampaignController::class, 'apiStatus']);

// Admin RSS feed management routes
$router->get('/admin/rss', [AdminRssController::class, 'index']);
$router->get('/admin/rss/sources', [AdminRssController::class, 'sources']);
$router->post('/admin/rss/sources/add', [AdminRssController::class, 'addSource']);
$router->post('/admin/rss/sources/delete/{id}', [AdminRssController::class, 'deleteSource']);
$router->post('/admin/rss/sources/toggle/{id}', [AdminRssController::class, 'toggleSource']);
$router->post('/admin/rss/sources/fetch/{id}', [AdminRssController::class, 'fetchOne']);
$router->post('/admin/rss/fetch-all', [AdminRssController::class, 'fetchAll']);
$router->post('/admin/rss/toggle-star/{id}', [AdminRssController::class, 'toggleStar']);
$router->post('/admin/rss/generate', [AdminRssController::class, 'generate']);
$router->post('/admin/rss/seed', [AdminRssController::class, 'seed']);

// Admin SEO Hub (Google Search Console)
$router->get('/admin/seo-hub', [AdminSeoHubController::class, 'index']);
$router->get('/admin/seo-hub/connect', [AdminSeoHubController::class, 'connect']);
$router->get('/admin/seo-hub/callback', [AdminSeoHubController::class, 'callback']);
$router->get('/admin/seo-hub/select-site', [AdminSeoHubController::class, 'selectSite']);
$router->post('/admin/seo-hub/confirm-site', [AdminSeoHubController::class, 'confirmSite']);
$router->post('/admin/seo-hub/refresh', [AdminSeoHubController::class, 'refresh']);
$router->post('/admin/seo-hub/disconnect', [AdminSeoHubController::class, 'disconnect']);
$router->get('/admin/seo-hub/api/keywords', [AdminSeoHubController::class, 'apiKeywords']);
$router->get('/admin/seo-hub/api/keywords-by-page', [AdminSeoHubController::class, 'apiKeywordsByPage']);

// Admin: Installateur (copie fichiers admin vers un autre site)
$router->get('/admin/installer', [AdminDashboardController::class, 'installer']);
$router->post('/admin/installer', [AdminDashboardController::class, 'installer']);

// Admin unified SMTP/API/IA management module
$router->get('/admin/smtp-api', [AdminSmtpApiController::class, 'index']);
$router->post('/admin/smtp-api/create-table', [AdminSmtpApiController::class, 'createTable']);
$router->post('/admin/smtp-api/seed-data', [AdminSmtpApiController::class, 'seedSampleData']);
$router->get('/admin/smtp-api/usage-stats', [AdminSmtpApiController::class, 'apiUsageStats']);

// Admin API costs dashboard
$router->get('/admin/api-costs', [AdminApiCostsController::class, 'index']);
$router->get('/admin/api-costs/stats', [AdminApiCostsController::class, 'apiStats']);

// Admin API management routes
$router->get('/admin/api-management', [AdminApiController::class, 'index']);
$router->post('/admin/api/test/{apiKey}', [AdminApiController::class, 'testApi']);
$router->post('/admin/api/save-keys', [AdminApiController::class, 'saveKeys']);
$router->post('/admin/api/register-claude', [AdminApiController::class, 'registerClaude']);

// Admin settings routes
$router->get('/admin/settings', [AdminSettingsController::class, 'index']);
$router->post('/admin/settings/save', [AdminSettingsController::class, 'save']);

// Admin analytics & tracking settings routes
$router->get('/admin/analytics-settings', [AdminAnalyticsSettingsController::class, 'index']);
$router->post('/admin/analytics-settings/save', [AdminAnalyticsSettingsController::class, 'save']);
$router->get('/admin/analytics-settings/status', [AdminAnalyticsSettingsController::class, 'status']);

// Admin module management routes (superuser only)
$router->get('/admin/modules', [AdminModuleController::class, 'index']);
$router->post('/admin/modules/toggle', [AdminModuleController::class, 'toggle']);
$router->post('/admin/modules/update', [AdminModuleController::class, 'update']);
$router->post('/admin/modules/seed', [AdminModuleController::class, 'seedModules']);

// Admin user management routes (superuser only)
$router->get('/admin/users', [AdminUserController::class, 'index']);
$router->post('/admin/users/create', [AdminUserController::class, 'create']);
$router->post('/admin/users/update', [AdminUserController::class, 'update']);
$router->post('/admin/users/delete', [AdminUserController::class, 'delete']);
$router->get('/admin/users/modules/{id}', [AdminUserController::class, 'userModules']);
$router->post('/admin/users/modules/save', [AdminUserController::class, 'saveUserModules']);

// Admin mailbox (email client) routes
$router->get('/admin/mailbox', [AdminMailboxController::class, 'index']);
$router->get('/admin/mailbox/read', [AdminMailboxController::class, 'read']);
$router->get('/admin/mailbox/compose', [AdminMailboxController::class, 'compose']);
$router->post('/admin/mailbox/send', [AdminMailboxController::class, 'send']);
$router->post('/admin/mailbox/delete', [AdminMailboxController::class, 'delete']);
$router->get('/admin/mailbox/unread-count', [AdminMailboxController::class, 'unreadCount']);
$router->post('/admin/mailbox/ai-assist', [AdminMailboxController::class, 'aiAssist']);
$router->get('/admin/mailbox/email-library', [AdminMailboxController::class, 'emailLibrary']);
$router->post('/admin/mailbox/email-library/save', [AdminMailboxController::class, 'emailLibrarySave']);
$router->post('/admin/mailbox/email-library/delete', [AdminMailboxController::class, 'emailLibraryDelete']);
$router->post('/admin/mailbox/email-library/use', [AdminMailboxController::class, 'emailLibraryUse']);

// Admin internal notifications routes
$router->get('/admin/notifications', [AdminNotificationController::class, 'index']);
$router->get('/admin/notifications/fetch', [AdminNotificationController::class, 'fetch']);
$router->post('/admin/notifications/read', [AdminNotificationController::class, 'markRead']);
$router->post('/admin/notifications/read-all', [AdminNotificationController::class, 'markAllRead']);
$router->post('/admin/notifications/delete', [AdminNotificationController::class, 'delete']);
$router->post('/admin/notifications/cleanup', [AdminNotificationController::class, 'cleanup']);
