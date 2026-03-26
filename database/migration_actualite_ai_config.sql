-- Migration: Actualite AI configuration for RSS-based article generation
-- Safe to run multiple times — uses CREATE TABLE IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS actualite_ai_config (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    config_key VARCHAR(100) NOT NULL,
    config_value TEXT NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_actualite_ai_config (website_id, config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default configuration values
INSERT IGNORE INTO actualite_ai_config (website_id, config_key, config_value) VALUES
(1, 'zone_priority', 'local_first'),
(1, 'exclude_agencies', '1'),
(1, 'exclude_keywords', 'annonce,vente appartement,location meublée,agence immobilière,programme neuf promoteur'),
(1, 'require_keywords', ''),
(1, 'max_article_age_days', '7'),
(1, 'min_relevance_score', '6'),
(1, 'article_tone', 'journalistique'),
(1, 'article_length', '800-1200'),
(1, 'seo_focus', ''),
(1, 'local_angle', ''),
(1, 'cta_style', 'soft'),
(1, 'source_citation', '1'),
(1, 'auto_publish', '0'),
(1, 'generation_model', 'anthropic');

-- Add actualite_id column to rss_articles for tracking which RSS articles were used for actualites
ALTER TABLE rss_articles ADD COLUMN IF NOT EXISTS actualite_id INT UNSIGNED DEFAULT NULL AFTER blog_article_id;
ALTER TABLE rss_articles ADD INDEX IF NOT EXISTS idx_rss_articles_actualite (actualite_id);

-- Add rss_article_ids to actualites table
ALTER TABLE actualites ADD COLUMN IF NOT EXISTS rss_article_ids TEXT DEFAULT NULL AFTER source_results;
