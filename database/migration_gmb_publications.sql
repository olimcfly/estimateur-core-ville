-- Migration: Create GMB publications tables
-- Date: 2026-03-23
-- Description: Tables pour la gestion des publications Google My Business

CREATE TABLE IF NOT EXISTS gmb_publications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    article_id INT UNSIGNED NULL DEFAULT NULL,
    actualite_id INT UNSIGNED NULL DEFAULT NULL,
    post_type ENUM('update','event','offer','product') NOT NULL DEFAULT 'update',
    title VARCHAR(58) NULL DEFAULT NULL COMMENT 'Requis pour event/offer/product, max 58 car',
    content TEXT NOT NULL COMMENT 'Texte de la publication, max 1500 car',
    cta_type ENUM('book','order_online','buy','learn_more','sign_up','get_offer','call_now') NULL DEFAULT NULL,
    cta_url VARCHAR(500) NULL DEFAULT NULL,
    image_path VARCHAR(255) NULL DEFAULT NULL,
    event_start DATETIME NULL DEFAULT NULL,
    event_end DATETIME NULL DEFAULT NULL,
    offer_code VARCHAR(50) NULL DEFAULT NULL,
    offer_terms TEXT NULL DEFAULT NULL,
    status ENUM('draft','scheduled','notified','published','expired') NOT NULL DEFAULT 'draft',
    scheduled_at DATETIME NULL DEFAULT NULL,
    notified_at DATETIME NULL DEFAULT NULL,
    published_at DATETIME NULL DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gmb_website_id (website_id),
    INDEX idx_gmb_article_id (article_id),
    INDEX idx_gmb_actualite_id (actualite_id),
    INDEX idx_gmb_status (status),
    INDEX idx_gmb_scheduled_at (scheduled_at),
    INDEX idx_gmb_post_type (post_type),
    INDEX idx_gmb_status_scheduled (status, scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gmb_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL DEFAULT NULL,
    UNIQUE KEY uq_gmb_settings_website_key (website_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings insert (safe: uses IGNORE to skip if already exists)
-- setting_key options:
--   default_cta_type    : CTA par defaut (learn_more)
--   default_cta_url     : URL CTA par defaut
--   notification_email  : Email pour les notifications
--   notification_hour   : Heure d'envoi des notifications (8)
--   auto_generate       : Generation auto depuis articles (1=oui, 0=non)
--   posting_days        : Jours de publication (1,3,5 = lun,mer,ven)
--   gmb_profile_url     : URL de la fiche Google My Business
