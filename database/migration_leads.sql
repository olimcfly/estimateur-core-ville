-- Migration: Leads table
-- Creates the main leads table for CRM lead tracking

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
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_website_id (website_id),
    INDEX idx_lead_type (lead_type),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at),
    INDEX idx_partenaire_id (partenaire_id),
    INDEX idx_date_signature (date_signature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
