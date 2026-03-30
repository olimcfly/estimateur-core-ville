#!/bin/bash

ROOT="estimateur-core-ville"

# ─── CRÉATION RACINE 
──────────────────────────────────────────────────────────
mkdir -p $ROOT
cd $ROOT

# ─── CONFIG 
───────────────────────────────────────────────────────────────────
mkdir -p config
touch config/app.php config/database.php 
config/mail.php config/ai.php \
      config/scoring.php config/seo.php 
config/villes.php

# ─── DATA 
─────────────────────────────────────────────────────────────────────
mkdir -p data/villes data/seeds data/fallback
touch data/villes/_template.json 
data/villes/paris.json \
      data/villes/lyon.json 
data/villes/bordeaux.json
touch data/seeds/villes.sql 
data/seeds/quartiers.sql \
      data/seeds/prix_historique.sql
touch data/fallback/prix_m2_default.json

# ─── DATABASE 
─────────────────────────────────────────────────────────────────
mkdir -p database/migrations
touch database/schema.sql
touch database/migrations/001_create_villes.sql \
      database/migrations/002_create_leads.sql \
      
database/migrations/003_create_estimations.sql \
      database/migrations/004_create_articles.sql \
      database/migrations/005_create_quartiers.sql 
\
      
database/migrations/006_add_ville_to_leads.sql

# ─── SRC / CORE 
───────────────────────────────────────────────────────────────
mkdir -p src/Core
touch src/Core/Router.php src/Core/Controller.php 
src/Core/Model.php \
      src/Core/Database.php src/Core/Config.php 
src/Core/Request.php \
      src/Core/Response.php src/Core/Session.php 
src/Core/Cache.php \
      src/Core/Logger.php src/Core/Validator.php

# ─── SRC / MIDDLEWARE 
─────────────────────────────────────────────────────────
mkdir -p src/Middleware
touch src/Middleware/AuthMiddleware.php 
src/Middleware/CsrfMiddleware.php \
      src/Middleware/MaintenanceMiddleware.php \
      src/Middleware/RateLimitMiddleware.php \
      src/Middleware/VilleMiddleware.php

# ─── SRC / MODELS 
─────────────────────────────────────────────────────────────
mkdir -p src/Models
touch src/Models/VilleModel.php 
src/Models/LeadModel.php \
      src/Models/EstimationModel.php 
src/Models/ArticleModel.php \
      src/Models/QuartierModel.php 
src/Models/PrixHistoriqueModel.php \
      src/Models/UserModel.php

# ─── SRC / CONTROLLERS 
────────────────────────────────────────────────────────
mkdir -p src/Controllers/Public 
src/Controllers/Admin src/Controllers/Api

touch src/Controllers/Public/HomeController.php \
      src/Controllers/Public/VilleController.php \
      
src/Controllers/Public/EstimationController.php \
      src/Controllers/Public/PrixController.php \
      src/Controllers/Public/BlogController.php \
      src/Controllers/Public/ContactController.php 
\
      src/Controllers/Public/ErrorController.php

touch src/Controllers/Admin/DashboardController.php 
\
      src/Controllers/Admin/LeadAdminController.php 
\
      
src/Controllers/Admin/VilleAdminController.php \
      
src/Controllers/Admin/ArticleAdminController.php \
      src/Controllers/Admin/AuthController.php

touch 
src/Controllers/Api/EstimationApiController.php \
      src/Controllers/Api/PrixApiController.php \
      src/Controllers/Api/LeadApiController.php

# ─── SRC / SERVICES 
───────────────────────────────────────────────────────────
mkdir -p src/Services/AI
touch src/Services/EstimationService.php 
src/Services/ScoringService.php \
      src/Services/LeadService.php 
src/Services/VilleService.php \
      src/Services/MailService.php 
src/Services/CacheService.php
touch src/Services/AI/PerplexityService.php 
src/Services/AI/OpenAIService.php

# ─── SRC / FEATURES 
───────────────────────────────────────────────────────────
mkdir -p src/Features/Estimation src/Features/Lead 
\
         src/Features/Blog src/Features/Prix

touch src/Features/Estimation/EstimationFeature.php 
\
      src/Features/Estimation/EstimationRequest.php 
\
      src/Features/Estimation/EstimationResult.php

touch src/Features/Lead/LeadFeature.php \
      src/Features/Lead/LeadRequest.php \
      src/Features/Lead/LeadNotifier.php

touch src/Features/Blog/BlogFeature.php \
      src/Features/Blog/ArticleGenerator.php \
      src/Features/Blog/ArticlePublisher.php

touch src/Features/Prix/PrixFeature.php \
      src/Features/Prix/PrixFetcher.php \
      src/Features/Prix/PrixCache.php

# ─── SRC / SEO 
────────────────────────────────────────────────────────────────
mkdir -p src/Seo
touch src/Seo/SeoManager.php 
src/Seo/MetaBuilder.php \
      src/Seo/SchemaBuilder.php 
src/Seo/SitemapGenerator.php \
      src/Seo/CanonicalResolver.php

# ─── ROUTES 
───────────────────────────────────────────────────────────────────
mkdir -p routes
touch routes/web.php routes/admin.php 
routes/api.php

# ─── TEMPLATES / LAYOUTS 
──────────────────────────────────────────────────────
mkdir -p templates/layouts
touch templates/layouts/main.php 
templates/layouts/admin.php \
      templates/layouts/minimal.php

# ─── TEMPLATES / COMPONENTS 
───────────────────────────────────────────────────
mkdir -p templates/components/forms 
templates/components/ui \
         templates/components/seo 
templates/components/estimation \
         templates/components/ville 
templates/components/blog

touch 
templates/components/forms/estimation-form.php \
      templates/components/forms/lead-capture.php \
      templates/components/forms/contact-form.php \
      templates/components/forms/login-form.php

touch templates/components/ui/alert.php \
      templates/components/ui/badge.php \
      templates/components/ui/breadcrumb.php \
      templates/components/ui/card.php \
      templates/components/ui/modal.php \
      templates/components/ui/pagination.php \
      templates/components/ui/spinner.php

touch templates/components/seo/head-meta.php \
      templates/components/seo/schema-org.php \
      templates/components/seo/breadcrumb-seo.php

touch 
templates/components/estimation/price-result.php \
      
templates/components/estimation/price-gauge.php \
      
templates/components/estimation/estimation-cta.php

touch templates/components/ville/ville-hero.php \
      templates/components/ville/prix-m2-widget.php 
\
      templates/components/ville/quartiers-list.php 
\
      templates/components/ville/ville-stats.php

touch templates/components/blog/article-card.php \
      templates/components/blog/articles-grid.php

# ─── TEMPLATES / PAGES 
────────────────────────────────────────────────────────
mkdir -p templates/pages/public 
templates/pages/errors \
         templates/pages/admin/leads \
         templates/pages/admin/villes \
         templates/pages/admin/articles

touch templates/pages/public/home.php \
      templates/pages/public/localite.php \
      templates/pages/public/estimation.php \
      templates/pages/public/result.php \
      templates/pages/public/prix-marche.php \
      templates/pages/public/quartiers.php \
      templates/pages/public/blog.php \
      templates/pages/public/article.php \
      templates/pages/public/contact.php \
      templates/pages/public/mentions-legales.php \
      templates/pages/public/confidentialite.php

touch templates/pages/admin/login.php \
      templates/pages/admin/dashboard.php \
      templates/pages/admin/settings.php

touch templates/pages/admin/leads/index.php \
      templates/pages/admin/leads/show.php \
      templates/pages/admin/leads/export.php

touch templates/pages/admin/villes/index.php \
      templates/pages/admin/villes/create.php \
      templates/pages/admin/villes/edit.php

touch templates/pages/admin/articles/index.php \
      templates/pages/admin/articles/edit.php

touch templates/pages/errors/404.php \
      templates/pages/errors/500.php \
      templates/pages/errors/maintenance.php

# ─── PUBLIC 
───────────────────────────────────────────────────────────────────
mkdir -p public/assets/css public/assets/js \
         public/assets/images/villes

touch public/index.php public/.htaccess 
public/robots.txt
touch public/assets/css/app.css 
public/assets/css/admin.css \
      public/assets/css/critical.css
touch public/assets/js/app.js 
public/assets/js/estimation.js \
      public/assets/js/admin.js
touch public/assets/images/og-default.jpg \
      public/assets/images/villes/paris.jpg \
      public/assets/images/villes/lyon.jpg

# ─── STYLES 
───────────────────────────────────────────────────────────────────
mkdir -p styles/base styles/components styles/pages

touch styles/base/_reset.css 
styles/base/_typography.css \
      styles/base/_variables.css

touch styles/components/_forms.css 
styles/components/_cards.css \
      styles/components/_nav.css 
styles/components/_buttons.css \
      styles/components/_badges.css

touch styles/pages/_home.css 
styles/pages/_estimation.css \
      styles/pages/_localite.css 
styles/pages/_admin.css

touch styles/main.css

# ─── ADMIN 
────────────────────────────────────────────────────────────────────
mkdir -p admin/google-ads

# ─── CRON 
─────────────────────────────────────────────────────────────────────
mkdir -p cron
touch cron/update-prix.php 
cron/generate-articles.php cron/cleanup-logs.php

# ─── TOOLS 
────────────────────────────────────────────────────────────────────
mkdir -p tools
touch tools/create-ville.php 
tools/generate-sitemap.php \
      tools/seed-database.php

# ─── TESTS 
────────────────────────────────────────────────────────────────────
mkdir -p tests/Unit tests/Integration
touch tests/Unit/EstimationServiceTest.php \
      tests/Unit/ScoringServiceTest.php \
      tests/Unit/ValidatorTest.php
touch tests/Integration/EstimationFlowTest.php \
      tests/Integration/LeadCaptureTest.php
touch tests/bootstrap.php

# ─── DOCS 
─────────────────────────────────────────────────────────────────────
mkdir -p docs
touch docs/ARCHITECTURE.md docs/AJOUTER_VILLE.md \
      docs/API.md docs/DEPLOIEMENT.md

# ─── LOGS 
─────────────────────────────────────────────────────────────────────
mkdir -p logs
touch logs/.gitkeep

# ─── RACINE 
───────────────────────────────────────────────────────────────────
touch .env.example .gitignore .htaccess \
      composer.json composer.lock phpunit.xml 
README.md

echo ""
echo "✅ Arborescence créée avec succès dans 
./$ROOT"
echo ""
find . -type f | sort

