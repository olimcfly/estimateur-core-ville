-- ============================================
-- SCHEMA MAÎTRE
-- MySQL · utf8mb4 · InnoDB
-- ============================================

-- Désactiver les FK pendant l'import
SET FOREIGN_KEY_CHECKS = 0;

SOURCE migrations/001_create_users_and_agencies.sql;
SOURCE migrations/002_create_biens_and_photos.sql;
SOURCE migrations/003_create_blog_and_seo_tables.sql;
SOURCE migrations/004_create_interactions_alertes_estimations.sql;
SOURCE migrations/005_create_site_extensions.sql;

-- Réactiver les FK
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- FIN DU SCHEMA
-- Version : 1.0.0
-- Tables  : 30
-- Vues    : 3
-- ============================================
