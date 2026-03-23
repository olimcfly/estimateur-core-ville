-- Migration: Advanced Blog SEO fields
-- Adds focus keyword, SEO scores, silo structure, OG tags, FAQ schema, and content analysis fields

ALTER TABLE articles
    ADD COLUMN focus_keyword VARCHAR(255) NOT NULL DEFAULT '' AFTER awareness_level,
    ADD COLUMN secondary_keywords TEXT DEFAULT NULL AFTER focus_keyword,
    ADD COLUMN seo_score INT UNSIGNED NOT NULL DEFAULT 0 AFTER secondary_keywords,
    ADD COLUMN semantic_score INT UNSIGNED NOT NULL DEFAULT 0 AFTER seo_score,
    ADD COLUMN keyword_density DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER semantic_score,
    ADD COLUMN keyword_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER keyword_density,
    ADD COLUMN word_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER keyword_count,
    ADD COLUMN h1_tag VARCHAR(255) NOT NULL DEFAULT '' AFTER word_count,
    ADD COLUMN og_title VARCHAR(255) NOT NULL DEFAULT '' AFTER h1_tag,
    ADD COLUMN og_description TEXT DEFAULT NULL AFTER og_title,
    ADD COLUMN og_image VARCHAR(500) DEFAULT NULL AFTER og_description,
    ADD COLUMN canonical_url VARCHAR(500) DEFAULT NULL AFTER og_image,
    ADD COLUMN faq_schema LONGTEXT DEFAULT NULL AFTER canonical_url,
    ADD COLUMN internal_links_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER faq_schema,
    ADD COLUMN external_links_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER internal_links_count,
    ADD COLUMN images_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER external_links_count,
    ADD COLUMN images_with_alt INT UNSIGNED NOT NULL DEFAULT 0 AFTER images_count,
    ADD COLUMN reading_time_minutes INT UNSIGNED NOT NULL DEFAULT 0 AFTER images_with_alt,
    ADD COLUMN silo_id INT UNSIGNED DEFAULT NULL AFTER reading_time_minutes,
    ADD COLUMN article_type ENUM('pilier', 'satellite', 'standalone') NOT NULL DEFAULT 'standalone' AFTER silo_id,
    ADD COLUMN target_audience TEXT DEFAULT NULL AFTER article_type,
    ADD COLUMN article_goal TEXT DEFAULT NULL AFTER target_audience,
    ADD COLUMN seo_analysis_json LONGTEXT DEFAULT NULL AFTER article_goal,
    ADD COLUMN published_at DATETIME DEFAULT NULL AFTER status,
    ADD INDEX idx_focus_keyword (focus_keyword),
    ADD INDEX idx_seo_score (seo_score),
    ADD INDEX idx_silo_id (silo_id),
    ADD INDEX idx_article_type (article_type);

-- Silo structure table
CREATE TABLE IF NOT EXISTS article_silos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    pillar_article_id INT UNSIGNED DEFAULT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#8B1538',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_silo_website (website_id),
    INDEX idx_silo_pillar (pillar_article_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO keywords tracking table
CREATE TABLE IF NOT EXISTS article_keywords (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    keyword VARCHAR(255) NOT NULL,
    keyword_type ENUM('focus', 'secondary', 'semantic', 'lsi') NOT NULL DEFAULT 'secondary',
    search_volume INT UNSIGNED DEFAULT NULL,
    difficulty INT UNSIGNED DEFAULT NULL,
    position INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_keyword_article (article_id),
    INDEX idx_keyword_type (keyword_type),
    CONSTRAINT fk_article_keywords_article
        FOREIGN KEY (article_id) REFERENCES articles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
