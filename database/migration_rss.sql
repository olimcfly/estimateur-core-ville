-- Migration: RSS feed sources and articles for content curation
-- Safe to run multiple times — uses CREATE TABLE IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS rss_sources (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    feed_url VARCHAR(500) NOT NULL,
    site_url VARCHAR(500) DEFAULT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'general',
    zone VARCHAR(120) NOT NULL DEFAULT 'national',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_fetched_at DATETIME DEFAULT NULL,
    last_error TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rss_sources_url (website_id, feed_url(400)),
    INDEX idx_rss_sources_active (is_active, zone),
    INDEX idx_rss_sources_website (website_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rss_articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    rss_source_id INT UNSIGNED NOT NULL,
    guid VARCHAR(500) NOT NULL,
    title VARCHAR(500) NOT NULL,
    link VARCHAR(500) NOT NULL,
    description TEXT DEFAULT NULL,
    content TEXT DEFAULT NULL,
    author VARCHAR(255) DEFAULT NULL,
    pub_date DATETIME DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    is_starred TINYINT(1) NOT NULL DEFAULT 0,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    blog_article_id INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rss_articles_guid (website_id, guid(400)),
    INDEX idx_rss_articles_source (rss_source_id),
    INDEX idx_rss_articles_pubdate (pub_date),
    INDEX idx_rss_articles_starred (is_starred),
    INDEX idx_rss_articles_used (is_used),
    CONSTRAINT fk_rss_articles_source
        FOREIGN KEY (rss_source_id) REFERENCES rss_sources(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rss_blog_generation_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    rss_article_ids TEXT NOT NULL COMMENT 'JSON array of rss_article IDs used',
    blog_article_id INT UNSIGNED DEFAULT NULL,
    prompt_used LONGTEXT DEFAULT NULL,
    status ENUM('success', 'error') NOT NULL DEFAULT 'success',
    error_message TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rss_blog_log_website (website_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
