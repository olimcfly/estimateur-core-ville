-- 003_create_interactions_alertes_estimations.sql
-- Tables: favoris, alertes, messages, conversations, estimations, contacts_agents

CREATE TABLE IF NOT EXISTS favoris (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    bien_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favoris_user_bien (user_id, bien_id),
    CONSTRAINT fk_favoris_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favoris_bien FOREIGN KEY (bien_id) REFERENCES biens(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alertes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    nom_alerte VARCHAR(255) NOT NULL,
    type_transaction VARCHAR(50) NULL,
    type_bien VARCHAR(50) NULL,
    ville VARCHAR(150) NULL,
    rayon_km DECIMAL(6,2) NULL,
    prix_min DECIMAL(12,2) NULL,
    prix_max DECIMAL(12,2) NULL,
    surface_min DECIMAL(10,2) NULL,
    surface_max DECIMAL(10,2) NULL,
    nb_pieces_min SMALLINT UNSIGNED NULL,
    frequence ENUM('immediat', 'quotidien', 'hebdo') NOT NULL DEFAULT 'quotidien',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    derniere_envoi DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alertes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_alertes_user_actif (user_id, actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediteur_id BIGINT UNSIGNED NULL,
    bien_id BIGINT UNSIGNED NULL,
    nom VARCHAR(150) NULL,
    email VARCHAR(190) NULL,
    telephone VARCHAR(30) NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    statut ENUM('nouveau', 'lu', 'en_cours', 'traite', 'archive') NOT NULL DEFAULT 'nouveau',
    agent_id BIGINT UNSIGNED NULL,
    note_interne TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_expediteur FOREIGN KEY (expediteur_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_messages_bien FOREIGN KEY (bien_id) REFERENCES biens(id) ON DELETE SET NULL,
    CONSTRAINT fk_messages_agent FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_messages_statut_agent (statut, agent_id),
    INDEX idx_messages_bien_created (bien_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    auteur_id BIGINT UNSIGNED NOT NULL,
    contenu TEXT NOT NULL,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_conversations_message FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    CONSTRAINT fk_conversations_auteur FOREIGN KEY (auteur_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversations_message_created (message_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS estimations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    bien_id BIGINT UNSIGNED NULL,
    type_bien VARCHAR(50) NULL,
    transaction VARCHAR(50) NULL,
    adresse VARCHAR(255) NULL,
    ville VARCHAR(150) NOT NULL,
    code_postal VARCHAR(12) NULL,
    surface DECIMAL(10,2) NULL,
    nb_pieces SMALLINT UNSIGNED NULL,
    nb_chambres SMALLINT UNSIGNED NULL,
    etage SMALLINT NULL,
    annee_construction SMALLINT UNSIGNED NULL,
    etat ENUM('excellent', 'bon', 'moyen', 'travaux') NULL,
    equipements JSON NULL,
    prix_estime_min DECIMAL(12,2) NULL,
    prix_estime_max DECIMAL(12,2) NULL,
    prix_estime_moyen DECIMAL(12,2) NULL,
    prix_m2_quartier DECIMAL(10,2) NULL,
    rapport_url VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_estimations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_estimations_bien FOREIGN KEY (bien_id) REFERENCES biens(id) ON DELETE SET NULL,
    INDEX idx_estimations_ville_created (ville, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contacts_agents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agent_id BIGINT UNSIGNED NOT NULL,
    bien_id BIGINT UNSIGNED NULL,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    telephone VARCHAR(30) NULL,
    message TEXT NOT NULL,
    source ENUM('fiche_bien', 'page_agent', 'formulaire_contact') NOT NULL,
    traite TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contacts_agents_agent FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_contacts_agents_bien FOREIGN KEY (bien_id) REFERENCES biens(id) ON DELETE SET NULL,
    INDEX idx_contacts_agents_agent_traite (agent_id, traite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
