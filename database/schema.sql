CREATE TABLE IF NOT EXISTS articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255) NOT NULL,
    meta_description TEXT NOT NULL,
    persona VARCHAR(100) NOT NULL,
    awareness_level VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_articles_website_slug (website_id, slug),
    INDEX idx_website_id (website_id),
    INDEX idx_status_created_at (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_revisions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    revision_number INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255) NOT NULL,
    meta_description TEXT NOT NULL,
    persona VARCHAR(100) NOT NULL,
    awareness_level VARCHAR(50) NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    UNIQUE KEY uniq_article_revision (article_id, revision_number),
    INDEX idx_article_created_at (article_id, created_at),
    CONSTRAINT fk_article_revisions_article
        FOREIGN KEY (article_id) REFERENCES articles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_type ENUM('tendance', 'qualifie') NOT NULL DEFAULT 'qualifie',
    nom VARCHAR(120) NULL DEFAULT NULL,
    email VARCHAR(180) NULL DEFAULT NULL,
    telephone VARCHAR(40) NULL DEFAULT NULL,
    adresse VARCHAR(255) NULL DEFAULT NULL,
    ville VARCHAR(120) NOT NULL,
    type_bien VARCHAR(80) NULL,
    surface_m2 DECIMAL(8,2) NULL,
    pieces INT UNSIGNED NULL,
    estimation DECIMAL(12,2) NOT NULL,
    urgence VARCHAR(40) NULL DEFAULT NULL,
    motivation VARCHAR(80) NULL DEFAULT NULL,
    notes TEXT NULL,
    partenaire_id INT UNSIGNED NULL,
    commission_taux DECIMAL(5,2) NULL DEFAULT NULL,
    commission_montant DECIMAL(12,2) NULL DEFAULT NULL,
    assigne_a VARCHAR(180) NULL DEFAULT NULL,
    date_mandat DATE NULL DEFAULT NULL,
    date_compromis DATE NULL DEFAULT NULL,
    date_signature DATE NULL DEFAULT NULL,
    prix_vente DECIMAL(12,2) NULL DEFAULT NULL,
    score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
    statut ENUM(
      'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
      'mandat_simple', 'mandat_exclusif', 'compromis_vente',
      'signe', 'co_signature_partenaire', 'assigne_autre'
    ) NOT NULL DEFAULT 'nouveau',
    created_at DATETIME NOT NULL,
    INDEX idx_website_id (website_id),
    INDEX idx_lead_type (lead_type),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at),
    INDEX idx_partenaire_id (partenaire_id),
    INDEX idx_date_signature (date_signature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS partenaires (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    nom VARCHAR(180) NOT NULL,
    entreprise VARCHAR(255) NULL,
    email VARCHAR(180) NOT NULL,
    telephone VARCHAR(40) NULL,
    specialite VARCHAR(120) NULL,
    zone_geographique VARCHAR(255) NULL,
    commission_defaut DECIMAL(5,2) NULL DEFAULT 3.00,
    statut ENUM('actif', 'inactif', 'prospect') NOT NULL DEFAULT 'actif',
    notes TEXT NULL,
    nb_mandats INT UNSIGNED NOT NULL DEFAULT 0,
    ca_genere DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_website_id (website_id),
    INDEX idx_statut (statut),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS achats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_id INT UNSIGNED NULL,
    nom_acheteur VARCHAR(180) NOT NULL,
    email_acheteur VARCHAR(180) NULL,
    telephone_acheteur VARCHAR(40) NULL,
    adresse_bien VARCHAR(255) NULL,
    ville VARCHAR(120) NOT NULL DEFAULT 'Bordeaux',
    quartier VARCHAR(120) NULL,
    type_bien VARCHAR(80) NULL,
    surface_m2 DECIMAL(8,2) NULL,
    pieces INT UNSIGNED NULL,
    prix_achat DECIMAL(12,2) NULL,
    prix_estime DECIMAL(12,2) NULL,
    type_financement ENUM('comptant', 'credit', 'mixte') NOT NULL DEFAULT 'credit',
    montant_pret DECIMAL(12,2) NULL,
    apport_personnel DECIMAL(12,2) NULL,
    statut ENUM('prospect', 'recherche', 'visite', 'offre', 'negociation', 'compromis', 'financement', 'acte_signe', 'annule') NOT NULL DEFAULT 'prospect',
    score ENUM('chaud', 'tiede', 'froid') NOT NULL DEFAULT 'froid',
    partenaire_id INT UNSIGNED NULL,
    commission_taux DECIMAL(5,2) NULL DEFAULT NULL,
    commission_montant DECIMAL(12,2) NULL DEFAULT NULL,
    date_premiere_visite DATE NULL DEFAULT NULL,
    date_offre DATE NULL DEFAULT NULL,
    date_compromis DATE NULL DEFAULT NULL,
    date_acte DATE NULL DEFAULT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_achats_website_id (website_id),
    INDEX idx_achats_lead_id (lead_id),
    INDEX idx_achats_statut (statut),
    INDEX idx_achats_score (score),
    INDEX idx_achats_ville (ville),
    INDEX idx_achats_created_at (created_at),
    INDEX idx_achats_partenaire_id (partenaire_id),
    CONSTRAINT fk_achats_lead
        FOREIGN KEY (lead_id) REFERENCES leads(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL DEFAULT '',
    role ENUM('superuser', 'admin') NOT NULL DEFAULT 'admin',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    login_code VARCHAR(255) DEFAULT NULL,
    login_code_expires_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_admin_email (email),
    INDEX idx_admin_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    confirmed_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_newsletter_confirmed_at (confirmed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS design_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS actualites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT NOT NULL DEFAULT '',
    meta_title VARCHAR(255) NOT NULL DEFAULT '',
    meta_description TEXT NOT NULL DEFAULT '',
    image_url VARCHAR(500) DEFAULT NULL,
    image_prompt TEXT DEFAULT NULL,
    source_query TEXT DEFAULT NULL,
    source_results LONGTEXT DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    generated_by ENUM('manual', 'ai', 'cron') NOT NULL DEFAULT 'manual',
    published_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_actualites_website_slug (website_id, slug),
    INDEX idx_actualites_website (website_id),
    INDEX idx_actualites_status (status, published_at),
    INDEX idx_actualites_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS actualites_cron_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    query_used TEXT NOT NULL,
    articles_found INT UNSIGNED NOT NULL DEFAULT 0,
    article_published_id INT UNSIGNED DEFAULT NULL,
    status ENUM('success', 'error') NOT NULL DEFAULT 'success',
    error_message TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cron_log_website (website_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_modules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    icon VARCHAR(80) NOT NULL DEFAULT 'fa-puzzle-piece',
    category VARCHAR(60) NOT NULL DEFAULT 'general',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    superuser_only TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_module_active (is_active),
    INDEX idx_module_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL DEFAULT '',
    type ENUM('info', 'success', 'warning', 'error', 'lead', 'system') NOT NULL DEFAULT 'info',
    link VARCHAR(500) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    target_role ENUM('all', 'superuser', 'admin') NOT NULL DEFAULT 'all',
    target_user_id INT UNSIGNED DEFAULT NULL,
    created_by VARCHAR(180) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notif_read (is_read),
    INDEX idx_notif_type (type),
    INDEX idx_notif_target (target_role),
    INDEX idx_notif_created (created_at),
    INDEX idx_notif_user (target_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_presence (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_email VARCHAR(180) NOT NULL UNIQUE,
    admin_name VARCHAR(120) NOT NULL DEFAULT '',
    page_path VARCHAR(500) NOT NULL,
    last_seen_at DATETIME NOT NULL,
    INDEX idx_presence_page (page_path),
    INDEX idx_presence_seen (last_seen_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
