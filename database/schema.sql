-- =============================================================
-- Triggers, procédures stockées et events pour le site immobilier
-- Cible: MySQL 8+
-- =============================================================

-- Important:
-- Ce script suppose l'existence des tables suivantes (colonnes minimales):
-- biens, visite_bien, logs_admin, alertes, notifications, users, messages,
-- recherches_anonymes (ou équivalent), archives_logs_admin.

DELIMITER $$

-- =============================================================
-- TRIGGERS
-- =============================================================

-- 1) Avant INSERT bien :
--    - Génère référence auto (BI-YYYY-XXXX)
--    - Pré-génère slug depuis titre + id (id anticipé via AUTO_INCREMENT)
DROP TRIGGER IF EXISTS trg_biens_before_insert $$
CREATE TRIGGER trg_biens_before_insert
BEFORE INSERT ON biens
FOR EACH ROW
BEGIN
    DECLARE v_next_id BIGINT;
    DECLARE v_slug_base VARCHAR(255);

    -- Référence auto si non fournie
    IF NEW.reference IS NULL OR TRIM(NEW.reference) = '' THEN
        SELECT LPAD(COALESCE(MAX(id), 0) + 1, 4, '0')
          INTO @next_seq
        FROM biens
        WHERE YEAR(COALESCE(date_creation, NOW())) = YEAR(CURDATE());

        SET NEW.reference = CONCAT('BI-', YEAR(CURDATE()), '-', @next_seq);
    END IF;

    -- Pré-slug: titre normalisé
    IF NEW.slug IS NULL OR TRIM(NEW.slug) = '' THEN
        SET v_slug_base = LOWER(TRIM(NEW.titre));
        SET v_slug_base = REGEXP_REPLACE(v_slug_base, '[^a-z0-9]+', '-');
        SET v_slug_base = REGEXP_REPLACE(v_slug_base, '(^-+|-+$)', '');

        IF NEW.id IS NULL OR NEW.id = 0 THEN
            SELECT AUTO_INCREMENT
              INTO v_next_id
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'biens'
            LIMIT 1;
        ELSE
            SET v_next_id = NEW.id;
        END IF;

        SET NEW.slug = CONCAT(v_slug_base, '-', v_next_id);
    END IF;
END $$

-- Trigger complémentaire pour garantir le slug final basé sur l'id réel inséré
DROP TRIGGER IF EXISTS trg_biens_after_insert_slug $$
CREATE TRIGGER trg_biens_after_insert_slug
AFTER INSERT ON biens
FOR EACH ROW
BEGIN
    DECLARE v_slug_base VARCHAR(255);

    SET v_slug_base = LOWER(TRIM(NEW.titre));
    SET v_slug_base = REGEXP_REPLACE(v_slug_base, '[^a-z0-9]+', '-');
    SET v_slug_base = REGEXP_REPLACE(v_slug_base, '(^-+|-+$)', '');

    UPDATE biens
       SET slug = CONCAT(v_slug_base, '-', NEW.id)
     WHERE id = NEW.id
       AND (slug IS NULL OR slug = '' OR slug LIKE CONCAT(v_slug_base, '-%'));
END $$

-- 2) Après INSERT visite_bien : incrémente biens.vues
DROP TRIGGER IF EXISTS trg_visite_bien_after_insert $$
CREATE TRIGGER trg_visite_bien_after_insert
AFTER INSERT ON visite_bien
FOR EACH ROW
BEGIN
    UPDATE biens
       SET vues = COALESCE(vues, 0) + 1
     WHERE id = NEW.bien_id;
END $$

-- 3) Après UPDATE biens (statut -> vendu/loue)
--    - Log dans logs_admin
--    - Désactive alertes liées
DROP TRIGGER IF EXISTS trg_biens_after_update_statut $$
CREATE TRIGGER trg_biens_after_update_statut
AFTER UPDATE ON biens
FOR EACH ROW
BEGIN
    IF OLD.statut <> NEW.statut AND NEW.statut IN ('vendu', 'loue') THEN
        INSERT INTO logs_admin (
            admin_id,
            action,
            cible_type,
            cible_id,
            details,
            created_at
        ) VALUES (
            NULL,
            CONCAT('changement_statut_', NEW.statut),
            'bien',
            NEW.id,
            CONCAT('Bien #', NEW.id, ' passé de ', OLD.statut, ' à ', NEW.statut),
            NOW()
        );

        UPDATE alertes
           SET active = 0,
               updated_at = NOW()
         WHERE active = 1
           AND (
                (bien_id IS NOT NULL AND bien_id = NEW.id)
                OR (bien_id IS NULL
                    AND ville = NEW.ville
                    AND type_bien = NEW.type_bien
                    AND transaction = NEW.transaction)
               );
    END IF;
END $$

-- 4) Avant DELETE user :
--    - Archive ses biens (statut = archive)
--    - Anonymise ses messages
DROP TRIGGER IF EXISTS trg_users_before_delete $$
CREATE TRIGGER trg_users_before_delete
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
    UPDATE biens
       SET statut = 'archive',
           updated_at = NOW()
     WHERE user_id = OLD.id;

    UPDATE messages
       SET user_id = NULL,
           nom = 'Utilisateur supprimé',
           email = CONCAT('anonyme+', OLD.id, '@example.local'),
           telephone = NULL,
           contenu = '[Message anonymisé suite suppression utilisateur]',
           updated_at = NOW()
     WHERE user_id = OLD.id;
END $$

-- =============================================================
-- PROCEDURES STOCKÉES
-- =============================================================

-- 1) Recherche multicritères paginée + total_count
DROP PROCEDURE IF EXISTS sp_recherche_biens $$
CREATE PROCEDURE sp_recherche_biens(
    IN p_type VARCHAR(50),
    IN p_transaction VARCHAR(50),
    IN p_ville VARCHAR(150),
    IN p_prix_min DECIMAL(15,2),
    IN p_prix_max DECIMAL(15,2),
    IN p_surface_min DECIMAL(10,2),
    IN p_surface_max DECIMAL(10,2),
    IN p_nb_pieces INT,
    IN p_page INT,
    IN p_limit INT
)
BEGIN
    DECLARE v_offset INT DEFAULT 0;

    SET p_page = IFNULL(NULLIF(p_page, 0), 1);
    SET p_limit = IFNULL(NULLIF(p_limit, 0), 20);
    SET v_offset = (p_page - 1) * p_limit;

    SELECT
        b.*,
        COUNT(*) OVER() AS total_count
    FROM biens b
    WHERE (p_type IS NULL OR p_type = '' OR b.type_bien = p_type)
      AND (p_transaction IS NULL OR p_transaction = '' OR b.transaction = p_transaction)
      AND (p_ville IS NULL OR p_ville = '' OR b.ville = p_ville)
      AND (p_prix_min IS NULL OR b.prix >= p_prix_min)
      AND (p_prix_max IS NULL OR b.prix <= p_prix_max)
      AND (p_surface_min IS NULL OR b.surface >= p_surface_min)
      AND (p_surface_max IS NULL OR b.surface <= p_surface_max)
      AND (p_nb_pieces IS NULL OR b.nb_pieces >= p_nb_pieces)
      AND b.statut IN ('publie', 'actif')
    ORDER BY b.date_creation DESC, b.id DESC
    LIMIT p_limit OFFSET v_offset;
END $$

-- 2) Biens similaires
DROP PROCEDURE IF EXISTS sp_biens_similaires $$
CREATE PROCEDURE sp_biens_similaires(
    IN p_bien_id BIGINT,
    IN p_limit INT
)
BEGIN
    DECLARE v_ville VARCHAR(150);
    DECLARE v_type VARCHAR(50);
    DECLARE v_prix DECIMAL(15,2);

    SET p_limit = IFNULL(NULLIF(p_limit, 0), 6);

    SELECT ville, type_bien, prix
      INTO v_ville, v_type, v_prix
    FROM biens
    WHERE id = p_bien_id
    LIMIT 1;

    SELECT b.*
    FROM biens b
    WHERE b.id <> p_bien_id
      AND b.ville = v_ville
      AND b.type_bien = v_type
      AND b.prix BETWEEN v_prix * 0.8 AND v_prix * 1.2
      AND b.statut IN ('publie', 'actif')
    ORDER BY ABS(b.prix - v_prix), b.date_creation DESC
    LIMIT p_limit;
END $$

-- 3) Envoi des alertes sur biens récents (<24h)
DROP PROCEDURE IF EXISTS sp_envoyer_alertes $$
CREATE PROCEDURE sp_envoyer_alertes()
BEGIN
    INSERT INTO notifications (
        user_id,
        alerte_id,
        bien_id,
        titre,
        message,
        lu,
        created_at
    )
    SELECT
        a.user_id,
        a.id AS alerte_id,
        b.id AS bien_id,
        'Nouveau bien correspondant à votre alerte',
        CONCAT('Nouveau bien: ', b.titre, ' (', b.ville, ')'),
        0,
        NOW()
    FROM alertes a
    JOIN biens b
      ON (a.type_bien IS NULL OR a.type_bien = '' OR a.type_bien = b.type_bien)
     AND (a.transaction IS NULL OR a.transaction = '' OR a.transaction = b.transaction)
     AND (a.ville IS NULL OR a.ville = '' OR a.ville = b.ville)
     AND (a.prix_min IS NULL OR b.prix >= a.prix_min)
     AND (a.prix_max IS NULL OR b.prix <= a.prix_max)
     AND (a.surface_min IS NULL OR b.surface >= a.surface_min)
     AND (a.surface_max IS NULL OR b.surface <= a.surface_max)
    LEFT JOIN notifications n
      ON n.alerte_id = a.id
     AND n.bien_id = b.id
    WHERE a.active = 1
      AND b.date_creation >= NOW() - INTERVAL 24 HOUR
      AND b.statut IN ('publie', 'actif')
      AND n.id IS NULL;

    UPDATE alertes
       SET derniere_envoi = NOW(),
           updated_at = NOW()
     WHERE active = 1;
END $$

-- 4) Stats dashboard
DROP PROCEDURE IF EXISTS sp_stats_dashboard $$
CREATE PROCEDURE sp_stats_dashboard()
BEGIN
    -- bloc 1: total biens par statut
    SELECT statut, COUNT(*) AS total
    FROM biens
    GROUP BY statut
    ORDER BY total DESC;

    -- bloc 2: visites aujourd'hui / semaine / mois
    SELECT
        SUM(CASE WHEN DATE(v.created_at) = CURDATE() THEN 1 ELSE 0 END) AS visites_aujourdhui,
        SUM(CASE WHEN v.created_at >= (NOW() - INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS visites_semaine,
        SUM(CASE WHEN v.created_at >= (NOW() - INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS visites_mois
    FROM visite_bien v;

    -- bloc 3: messages non traités
    SELECT COUNT(*) AS messages_non_traites
    FROM messages
    WHERE traite = 0;

    -- bloc 4: inscriptions récentes (7 jours)
    SELECT COUNT(*) AS inscriptions_recentes
    FROM users
    WHERE created_at >= NOW() - INTERVAL 7 DAY;

    -- bloc 5: top 5 biens consultés
    SELECT b.id, b.titre, COALESCE(b.vues, 0) AS vues
    FROM biens b
    ORDER BY COALESCE(b.vues, 0) DESC, b.id DESC
    LIMIT 5;
END $$

-- 5) Nettoyage logs
DROP PROCEDURE IF EXISTS sp_nettoyer_logs $$
CREATE PROCEDURE sp_nettoyer_logs(IN p_jours INT)
BEGIN
    SET p_jours = IFNULL(NULLIF(p_jours, 0), 90);

    -- visites anciennes
    DELETE FROM visite_bien
    WHERE created_at < NOW() - INTERVAL p_jours DAY;

    -- recherches anonymes > 30 jours
    DELETE FROM recherches_anonymes
    WHERE created_at < NOW() - INTERVAL 30 DAY;

    -- logs_admin > 1 an vers table d'archive puis purge
    INSERT INTO archives_logs_admin
    SELECT *
    FROM logs_admin
    WHERE created_at < NOW() - INTERVAL 1 YEAR;

    DELETE FROM logs_admin
    WHERE created_at < NOW() - INTERVAL 1 YEAR;
END $$

-- Procédure dédiée à l'événement hebdomadaire newsletter stats
DROP PROCEDURE IF EXISTS sp_stats_hebdo_newsletter $$
CREATE PROCEDURE sp_stats_hebdo_newsletter()
BEGIN
    INSERT INTO logs_admin (admin_id, action, cible_type, cible_id, details, created_at)
    SELECT
        NULL,
        'newsletter_stats_hebdo',
        'systeme',
        NULL,
        JSON_OBJECT(
            'biens_publies_7j', (SELECT COUNT(*) FROM biens WHERE date_creation >= NOW() - INTERVAL 7 DAY),
            'visites_7j', (SELECT COUNT(*) FROM visite_bien WHERE created_at >= NOW() - INTERVAL 7 DAY),
            'inscriptions_7j', (SELECT COUNT(*) FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY)
        ),
        NOW();
END $$

-- =============================================================
-- EVENTS (planification)
-- =============================================================

SET @old_event_scheduler := @@event_scheduler $$
SET GLOBAL event_scheduler = ON $$

DROP EVENT IF EXISTS ev_envoyer_alertes_horaire $$
CREATE EVENT ev_envoyer_alertes_horaire
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    CALL sp_envoyer_alertes();
END $$

DROP EVENT IF EXISTS ev_nettoyage_logs_nuit $$
CREATE EVENT ev_nettoyage_logs_nuit
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURDATE()) + INTERVAL 1 DAY + INTERVAL 2 HOUR)
DO
BEGIN
    CALL sp_nettoyer_logs(90);
END $$

DROP EVENT IF EXISTS ev_stats_hebdo_newsletter $$
CREATE EVENT ev_stats_hebdo_newsletter
ON SCHEDULE EVERY 1 WEEK
STARTS (TIMESTAMP(CURDATE()) + INTERVAL (8 - DAYOFWEEK(CURDATE())) DAY + INTERVAL 8 HOUR)
DO
BEGIN
    CALL sp_stats_hebdo_newsletter();
END $$

DELIMITER ;
