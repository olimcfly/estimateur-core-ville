-- Schéma MySQL 8+ pour plateforme immobilière
-- Encodage global
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS estimation_immobilier
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE estimation_immobilier;

-- =====================================================
-- TABLE: users
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire utilisateur',
  email VARCHAR(255) NOT NULL COMMENT 'Email de connexion (unique)',
  password_hash VARCHAR(255) NOT NULL COMMENT 'Hash sécurisé du mot de passe',
  nom VARCHAR(120) NOT NULL COMMENT 'Nom de famille',
  prenom VARCHAR(120) NOT NULL COMMENT 'Prénom',
  telephone VARCHAR(30) NULL COMMENT 'Téléphone au format international',
  avatar_url VARCHAR(500) NULL COMMENT 'URL de la photo de profil',
  role ENUM('admin','agent','client') NOT NULL DEFAULT 'client' COMMENT 'Rôle applicatif',
  email_verified TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si email vérifié',
  actif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 si compte actif',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  last_login DATETIME NULL COMMENT 'Dernière connexion',
  PRIMARY KEY (id),
  UNIQUE KEY uk_users_email (email),
  KEY idx_users_role_actif (role, actif),
  KEY idx_users_created_at (created_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Utilisateurs de la plateforme';

-- =====================================================
-- TABLE: biens
-- =====================================================
CREATE TABLE IF NOT EXISTS biens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire bien immobilier',
  reference VARCHAR(20) NOT NULL COMMENT 'Référence métier format BI-YYYY-XXXX',
  titre VARCHAR(255) NOT NULL COMMENT 'Titre commercial de l’annonce',
  slug VARCHAR(255) NOT NULL COMMENT 'Slug SEO unique',
  description TEXT NULL COMMENT 'Description détaillée du bien',
  type ENUM('appartement','maison','terrain','local','bureau','villa') NOT NULL COMMENT 'Type de bien',
  transaction ENUM('vente','location') NOT NULL COMMENT 'Type de transaction',
  statut ENUM('actif','vendu','loue','suspendu','archive') NOT NULL DEFAULT 'actif' COMMENT 'Statut de publication/commercialisation',
  prix DECIMAL(12,2) NOT NULL COMMENT 'Prix de vente ou loyer mensuel',
  prix_negociable TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si le prix est négociable',
  surface DECIMAL(10,2) NULL COMMENT 'Surface habitable en m²',
  surface_terrain DECIMAL(10,2) NULL COMMENT 'Surface terrain en m²',
  nb_pieces SMALLINT UNSIGNED NULL COMMENT 'Nombre total de pièces',
  nb_chambres SMALLINT UNSIGNED NULL COMMENT 'Nombre de chambres',
  nb_sdb SMALLINT UNSIGNED NULL COMMENT 'Nombre de salles de bain',
  etage SMALLINT NULL COMMENT 'Étage du bien',
  nb_etages_total SMALLINT UNSIGNED NULL COMMENT 'Nombre total d’étages de l’immeuble',
  annee_construction YEAR NULL COMMENT 'Année de construction',
  dpe ENUM('A','B','C','D','E','F','G') NULL COMMENT 'Diagnostic de performance énergétique',
  ges ENUM('A','B','C','D','E','F','G') NULL COMMENT 'Émission de gaz à effet de serre',
  ville VARCHAR(120) NOT NULL COMMENT 'Ville du bien',
  code_postal VARCHAR(12) NOT NULL COMMENT 'Code postal',
  adresse VARCHAR(255) NULL COMMENT 'Adresse postale',
  quartier VARCHAR(120) NULL COMMENT 'Quartier/localisation secondaire',
  latitude DECIMAL(10,7) NULL COMMENT 'Latitude WGS84',
  longitude DECIMAL(10,7) NULL COMMENT 'Longitude WGS84',
  charges_mensuelles DECIMAL(10,2) NULL COMMENT 'Charges mensuelles',
  taxe_fonciere DECIMAL(10,2) NULL COMMENT 'Taxe foncière annuelle',
  disponible_le DATE NULL COMMENT 'Date de disponibilité',
  agent_id BIGINT UNSIGNED NULL COMMENT 'Agent responsable du bien',
  vues INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Compteur de vues',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de mise à jour',
  PRIMARY KEY (id),
  UNIQUE KEY uk_biens_reference (reference),
  UNIQUE KEY uk_biens_slug (slug),
  KEY idx_biens_agent_id (agent_id),
  KEY idx_biens_type_transaction_statut_prix (type, transaction, statut, prix),
  KEY idx_biens_ville_code_postal_quartier (ville, code_postal, quartier),
  KEY idx_biens_surface_pieces_chambres (surface, nb_pieces, nb_chambres),
  KEY idx_biens_statut_disponible (statut, disponible_le),
  KEY idx_biens_geo (latitude, longitude),
  KEY idx_biens_created_at (created_at),
  FULLTEXT KEY ft_biens_recherche (titre, description, ville, quartier),
  CONSTRAINT fk_biens_agent
    FOREIGN KEY (agent_id) REFERENCES users(id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catalogue des biens immobiliers';

-- =====================================================
-- TABLE: biens_photos
-- =====================================================
CREATE TABLE IF NOT EXISTS biens_photos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire photo',
  bien_id BIGINT UNSIGNED NOT NULL COMMENT 'Bien associé',
  url VARCHAR(500) NOT NULL COMMENT 'URL image originale',
  url_thumbnail VARCHAR(500) NULL COMMENT 'URL miniature optimisée',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Ordre d’affichage',
  is_principale TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si photo principale',
  alt_text VARCHAR(255) NULL COMMENT 'Texte alternatif SEO/accessibilité',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d’ajout',
  PRIMARY KEY (id),
  KEY idx_biens_photos_bien_ordre (bien_id, ordre),
  KEY idx_biens_photos_bien_principale (bien_id, is_principale),
  CONSTRAINT fk_biens_photos_bien
    FOREIGN KEY (bien_id) REFERENCES biens(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Photos associées aux biens';

-- =====================================================
-- TABLE: biens_equipements
-- =====================================================
CREATE TABLE IF NOT EXISTS biens_equipements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire équipement',
  bien_id BIGINT UNSIGNED NOT NULL COMMENT 'Bien associé',
  categorie ENUM('interieur','exterieur','securite','chauffage') NOT NULL COMMENT 'Catégorie de l’équipement',
  nom VARCHAR(120) NOT NULL COMMENT 'Nom de l’équipement (parking, piscine, gardien, etc.)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_bien_equipement_unique (bien_id, categorie, nom),
  KEY idx_biens_equipements_categorie_nom (categorie, nom),
  CONSTRAINT fk_biens_equipements_bien
    FOREIGN KEY (bien_id) REFERENCES biens(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Équipements et prestations des biens';

-- =====================================================
-- TABLE: villes
-- =====================================================
CREATE TABLE IF NOT EXISTS villes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Clé primaire ville',
  nom VARCHAR(120) NOT NULL COMMENT 'Nom de la ville',
  slug VARCHAR(160) NOT NULL COMMENT 'Slug SEO unique',
  departement VARCHAR(120) NULL COMMENT 'Département administratif',
  region VARCHAR(120) NULL COMMENT 'Région administrative',
  code_postal VARCHAR(12) NOT NULL COMMENT 'Code postal principal',
  latitude DECIMAL(10,7) NULL COMMENT 'Latitude du centre-ville',
  longitude DECIMAL(10,7) NULL COMMENT 'Longitude du centre-ville',
  description TEXT NULL COMMENT 'Description marketing/SEO',
  image_url VARCHAR(500) NULL COMMENT 'Image de couverture de la ville',
  actif TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 si visible sur le site',
  ordre_affichage INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Poids de tri pour les listings',
  meta_title VARCHAR(255) NULL COMMENT 'Balise title SEO',
  meta_description VARCHAR(320) NULL COMMENT 'Meta description SEO',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  PRIMARY KEY (id),
  UNIQUE KEY uk_villes_slug (slug),
  KEY idx_villes_nom (nom),
  KEY idx_villes_code_postal (code_postal),
  KEY idx_villes_region_departement (region, departement),
  KEY idx_villes_actif_ordre (actif, ordre_affichage),
  KEY idx_villes_geo (latitude, longitude)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Référentiel des villes';

-- =====================================================
-- TRIGGER: auto-génération de la référence BI-YYYY-XXXX
-- =====================================================
DROP TRIGGER IF EXISTS trg_biens_generate_reference;
DELIMITER $$
CREATE TRIGGER trg_biens_generate_reference
BEFORE INSERT ON biens
FOR EACH ROW
BEGIN
  DECLARE v_year CHAR(4);
  DECLARE v_next INT;

  IF NEW.reference IS NULL OR NEW.reference = '' THEN
    SET v_year = DATE_FORMAT(COALESCE(NEW.created_at, CURRENT_TIMESTAMP), '%Y');

    SELECT COALESCE(MAX(CAST(RIGHT(reference, 4) AS UNSIGNED)), 0) + 1
      INTO v_next
    FROM biens
    WHERE reference LIKE CONCAT('BI-', v_year, '-%');

    SET NEW.reference = CONCAT('BI-', v_year, '-', LPAD(v_next, 4, '0'));
  END IF;
END$$
DELIMITER ;
