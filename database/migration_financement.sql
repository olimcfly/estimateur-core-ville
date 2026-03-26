-- Migration: Create demandes_financement table
-- Date: 2026-03-23
-- Module: Demandes de financement visiteurs (partenaire 2L Courtage)

CREATE TABLE IF NOT EXISTS demandes_financement (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    website_id INT UNSIGNED NOT NULL,
    lead_id INT UNSIGNED NULL,

    -- Identite du demandeur
    nom VARCHAR(180) NOT NULL,
    prenom VARCHAR(180) NULL,
    email VARCHAR(180) NOT NULL,
    telephone VARCHAR(40) NULL,

    -- Situation professionnelle
    situation_pro ENUM('salarie_cdi', 'salarie_cdd', 'fonctionnaire', 'independant', 'profession_liberale', 'retraite', 'autre') NULL,
    revenus_mensuels DECIMAL(10,2) NULL,
    co_emprunteur TINYINT(1) NOT NULL DEFAULT 0,
    revenus_co_emprunteur DECIMAL(10,2) NULL,

    -- Projet immobilier
    type_projet ENUM('achat_residence_principale', 'achat_secondaire', 'investissement_locatif', 'rachat_credit', 'renogociation', 'autre') NOT NULL DEFAULT 'achat_residence_principale',
    montant_projet DECIMAL(12,2) NULL,
    apport_personnel DECIMAL(12,2) NULL,
    montant_pret_souhaite DECIMAL(12,2) NULL,
    duree_souhaitee_mois INT UNSIGNED NULL,

    -- Bien concerne
    type_bien VARCHAR(80) NULL,
    ville VARCHAR(120) NULL DEFAULT 'Ville à configurer',
    quartier VARCHAR(120) NULL,

    -- Gestion de la demande
    statut ENUM('nouvelle', 'contactee', 'en_cours', 'transmise_courtier', 'acceptee', 'refusee', 'annulee') NOT NULL DEFAULT 'nouvelle',
    date_transmission DATE NULL DEFAULT NULL,
    courtier_reference VARCHAR(255) NULL DEFAULT '2L Courtage',
    notes_internes TEXT NULL,
    notes_courtier TEXT NULL,

    -- Tracking
    source VARCHAR(80) NULL DEFAULT 'site_web',
    utm_source VARCHAR(120) NULL,
    utm_medium VARCHAR(120) NULL,
    utm_campaign VARCHAR(120) NULL,
    ip_address VARCHAR(45) NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_website_id (website_id),
    INDEX idx_lead_id (lead_id),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at),
    INDEX idx_date_transmission (date_transmission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
