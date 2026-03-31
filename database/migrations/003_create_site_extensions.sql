-- Extension du schéma SQL pour le site immobilier
-- Tables: visites_biens, recherches, notifications, push_subscriptions,
--         config_site, logs_admin
-- Vues: v_biens_complets, v_stats_dashboard, v_articles_publies

CREATE TABLE IF NOT EXISTS visites_biens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bien_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(512) NULL,
    referrer VARCHAR(1024) NULL,
    utm_source VARCHAR(100) NULL,
    utm_medium VARCHAR(100) NULL,
    utm_campaign VARCHAR(150) NULL,
    duree_secondes INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visites_biens_bien
        FOREIGN KEY (bien_id) REFERENCES biens(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_visites_biens_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    INDEX idx_visites_biens_bien_created_at (bien_id, created_at),
    INDEX idx_visites_biens_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS recherches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    `query` VARCHAR(255) NOT NULL,
    filtres JSON NULL,
    nb_resultats INT UNSIGNED NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recherches_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    INDEX idx_recherches_created_at (created_at),
    INDEX idx_recherches_user_created_at (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('alerte_bien', 'message', 'estimation', 'systeme', 'promo') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lien_url VARCHAR(2048) NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    lu_le DATETIME NULL,
    bien_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_notifications_bien
        FOREIGN KEY (bien_id) REFERENCES biens(id)
        ON DELETE SET NULL,
    INDEX idx_notifications_user_lu_created_at (user_id, lu, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS push_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    endpoint TEXT NOT NULL,
    endpoint_hash CHAR(64) GENERATED ALWAYS AS (SHA2(endpoint, 256)) STORED,
    p256dh_key VARCHAR(255) NOT NULL,
    auth_key VARCHAR(255) NOT NULL,
    user_agent VARCHAR(512) NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    topics JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_used DATETIME NULL,
    CONSTRAINT fk_push_subscriptions_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL,
    UNIQUE KEY uq_push_subscriptions_endpoint_hash (endpoint_hash),
    INDEX idx_push_subscriptions_user_id (user_id),
    INDEX idx_push_subscriptions_actif_last_used (actif, last_used)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS config_site (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(100) NOT NULL,
    valeur TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'json') NOT NULL DEFAULT 'string',
    description VARCHAR(255) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    CONSTRAINT fk_config_site_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL,
    UNIQUE KEY uq_config_site_cle (cle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO config_site (cle, valeur, type, description)
VALUES
    ('site_nom', 'ImmoSite', 'string', 'Nom du site immobilier'),
    ('site_email', 'contact@...', 'string', 'Adresse email de contact du site'),
    ('commission_vente', '3', 'integer', 'Commission vente par défaut (en %)'),
    ('alertes_actives', '1', 'boolean', 'Activation globale des alertes'),
    ('maintenance_mode', '0', 'boolean', 'Active/Désactive le mode maintenance'),
    ('nb_biens_homepage', '6', 'integer', 'Nombre de biens affichés sur la page d\'accueil'),
    ('google_maps_key', '', 'string', 'Clé API Google Maps'),
    ('smtp_host', '', 'string', 'Serveur SMTP sortant')
ON DUPLICATE KEY UPDATE
    valeur = VALUES(valeur),
    type = VALUES(type),
    description = VALUES(description),
    updated_at = CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS logs_admin (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id BIGINT UNSIGNED NOT NULL,
    action ENUM('create', 'update', 'delete', 'login', 'export', 'import') NOT NULL,
    table_cible VARCHAR(100) NOT NULL,
    id_cible BIGINT UNSIGNED NULL,
    anciennes_valeurs JSON NULL,
    nouvelles_valeurs JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_admin_admin
        FOREIGN KEY (admin_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_logs_admin_admin_created_at (admin_id, created_at),
    INDEX idx_logs_admin_table_id_cible (table_cible, id_cible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW v_biens_complets AS
SELECT
    b.*,
    u.id AS agent_id,
    u.nom AS agent_nom,
    u.email AS agent_email,
    COUNT(DISTINCT p.id) AS nb_photos,
    COUNT(DISTINCT f.id) AS nb_favoris
FROM biens b
LEFT JOIN users u ON u.id = b.agent_id
LEFT JOIN photos_biens p ON p.bien_id = b.id
LEFT JOIN favoris f ON f.bien_id = b.id
GROUP BY b.id, u.id, u.nom, u.email;

CREATE OR REPLACE VIEW v_stats_dashboard AS
SELECT
    (SELECT COUNT(*) FROM biens) AS total_biens,
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM leads) AS total_leads,
    (SELECT COUNT(*) FROM recherches WHERE DATE(created_at) = CURRENT_DATE()) AS recherches_jour,
    (SELECT COUNT(*) FROM visites_biens WHERE DATE(created_at) = CURRENT_DATE()) AS visites_jour,
    (SELECT COUNT(*) FROM notifications WHERE lu = 0) AS notifications_non_lues,
    (SELECT COUNT(*) FROM articles WHERE statut = 'publie') AS articles_publies;

CREATE OR REPLACE VIEW v_articles_publies AS
SELECT
    a.id,
    a.titre,
    a.slug,
    a.extrait,
    a.contenu,
    a.created_at,
    a.updated_at,
    a.publie_le,
    u.id AS auteur_id,
    u.nom AS auteur_nom,
    c.id AS categorie_id,
    c.nom AS categorie_nom,
    COUNT(DISTINCT com.id) AS nb_commentaires
FROM articles a
LEFT JOIN users u ON u.id = a.auteur_id
LEFT JOIN categories_articles c ON c.id = a.categorie_id
LEFT JOIN commentaires_articles com ON com.article_id = a.id AND com.statut = 'approuve'
WHERE a.statut = 'publie'
GROUP BY
    a.id,
    a.titre,
    a.slug,
    a.extrait,
    a.contenu,
    a.created_at,
    a.updated_at,
    a.publie_le,
    u.id,
    u.nom,
    c.id,
    c.nom;
