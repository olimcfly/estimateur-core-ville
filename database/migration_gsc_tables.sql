-- Google Search Console integration tables
-- Run this migration to create GSC tables (also auto-created by the service)

CREATE TABLE IF NOT EXISTS gsc_connections (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    site_url VARCHAR(500) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_gsc_website (website_id),
    INDEX idx_gsc_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gsc_keywords_cache (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    keyword VARCHAR(500) NOT NULL,
    clicks INT UNSIGNED NOT NULL DEFAULT 0,
    impressions INT UNSIGNED NOT NULL DEFAULT 0,
    ctr DECIMAL(6,4) NOT NULL DEFAULT 0.0000,
    position DECIMAL(5,1) NOT NULL DEFAULT 0.0,
    fetched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_gsc_kw_website (website_id),
    INDEX idx_gsc_kw_clicks (website_id, clicks DESC),
    INDEX idx_gsc_kw_impressions (website_id, impressions DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
