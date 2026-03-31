## Analyse du fichier ✅

Fichier très bien structuré. Plusieurs incohérences avec les fichiers précédents détectées :

---

## ⚠️ Problèmes critiques

### 1. Références à des tables inexistantes
```sql
-- v_stats_dashboard référence :
FROM leads              -- ❌ table jamais créée

-- v_articles_publies référence :
FROM categories_articles    -- ❌ c'est categories_blog (fichier 003)
FROM commentaires_articles  -- ❌ c'est commentaires (fichier 003)
a.publie_le                 -- ❌ c'est published_at (fichier 003)
```

### 2. `v_biens_complets` — `b.*` avec GROUP BY
```sql
-- MySQL strict mode : b.* interdit sans lister toutes les colonnes
-- dans le GROUP BY ou utiliser ANY_VALUE()
-- ❌ Échouera en sql_mode=ONLY_FULL_GROUP_BY
```

### 3. `v_stats_dashboard` — `notifications_non_lues` global
```sql
-- Pas de filtre user_id → toutes les notifications
-- Inutile pour un dashboard (valeur trop large)
```

### 4. `logs_admin` — `ON DELETE CASCADE` risqué
```sql
-- Si un admin est supprimé → perte de tous les logs
-- ❌ Les logs doivent être conservés pour audit
ON DELETE RESTRICT  -- ou SET NULL + admin_id nullable
```

### 5. `push_subscriptions` — `topics JSON` sans doc
```sql
-- Structure non documentée
-- Suggestion :
-- ["alerte_bien", "message", "promo"]
```

---

## 📄 Fichier corrigé complet

```sql
-- ============================================
-- 005 - TRACKING, CONFIG, LOGS & VUES
-- Tables: visites_biens, recherches, notifications,
--         push_subscriptions, config_site, logs_admin
-- Vues:   v_biens_complets, v_stats_dashboard,
--         v_articles_publies
-- ============================================

-- -----------------------------------------------
-- VISITES BIENS (analytics)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS visites_biens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bien_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,                          -- NULL si anonyme

    -- Tracking
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(512) NULL,
    referrer VARCHAR(1024) NULL,
    utm_source VARCHAR(100) NULL,
    utm_medium VARCHAR(100) NULL,
    utm_campaign VARCHAR(150) NULL,
    duree_secondes INT UNSIGNED NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_visites_biens_bien_created_at (bien_id, created_at),
    INDEX idx_visites_biens_created_at (created_at),
    INDEX idx_visites_biens_user_id (user_id),             -- ✅ ajouté

    CONSTRAINT fk_visites_biens_bien
        FOREIGN KEY (bien_id) REFERENCES biens(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_visites_biens_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- RECHERCHES (analytics requêtes)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS recherches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    query VARCHAR(255) NOT NULL,
    filtres JSON NULL,
    -- Structure attendue :
    -- {
    --   "type_bien": "appartement",
    --   "ville": "Paris",
    --   "prix_max": 500000,
    --   "surface_min": 60
    -- }
    nb_resultats INT UNSIGNED NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_recherches_created_at (created_at),
    INDEX idx_recherches_user_created_at (user_id, created_at),
    INDEX idx_recherches_nb_resultats (nb_resultats),      -- ✅ requêtes sans résultat

    CONSTRAINT fk_recherches_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- NOTIFICATIONS
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM(
        'alerte_bien',
        'message',
        'estimation',
        'systeme',
        'promo'
    ) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lien_url VARCHAR(2048) NULL,

    -- État de lecture
    lu TINYINT(1) NOT NULL DEFAULT 0,
    lu_le DATETIME NULL,

    -- Référence optionnelle
    bien_id BIGINT UNSIGNED NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notifications_user_lu_created (user_id, lu, created_at),
    INDEX idx_notifications_bien_id (bien_id),             -- ✅ ajouté

    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_notifications_bien
        FOREIGN KEY (bien_id) REFERENCES biens(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PUSH SUBSCRIPTIONS (Web Push API)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS push_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,                          -- NULL si non connecté
    endpoint TEXT NOT NULL,
    endpoint_hash CHAR(64) GENERATED ALWAYS AS
        (SHA2(endpoint, 256)) STORED,
    p256dh_key VARCHAR(255) NOT NULL,
    auth_key VARCHAR(255) NOT NULL,
    user_agent VARCHAR(512) NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    topics JSON NULL,
    -- Structure attendue :
    -- ["alerte_bien", "message", "promo", "systeme"]
    last_used DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_push_endpoint_hash (endpoint_hash),
    INDEX idx_push_user_id (user_id),
    INDEX idx_push_actif_last_used (actif, last_used),

    CONSTRAINT fk_push_subscriptions_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- CONFIG SITE (clé/valeur)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS config_site (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cle VARCHAR(100) NOT NULL,
    valeur TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'json')
        NOT NULL DEFAULT 'string',
    groupe VARCHAR(100) NULL,                              -- ✅ regroupement: 'smtp','seo','general'
    description VARCHAR(255) NULL,
    is_secret TINYINT(1) NOT NULL DEFAULT 0,               -- ✅ masquer dans les logs
    updated_by BIGINT UNSIGNED NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_config_site_cle (cle),
    INDEX idx_config_site_groupe (groupe),                 -- ✅ filtrer par groupe

    CONSTRAINT fk_config_site_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valeurs par défaut
INSERT INTO config_site
    (cle, valeur, type, groupe, description, is_secret)
VALUES
    -- Général
    ('site_nom',          'ImmoSite',  'string',  'general',
     'Nom du site immobilier', 0),
    ('site_email',        'contact@immosite.fr', 'string', 'general',
     'Adresse email de contact', 0),
    ('nb_biens_homepage', '6',         'integer', 'general',
     'Biens affichés en homepage', 0),
    ('maintenance_mode',  '0',         'boolean', 'general',
     'Mode maintenance actif', 0),

    -- Business
    ('commission_vente',  '3',         'integer', 'business',
     'Commission vente par défaut (%)', 0),
    ('alertes_actives',   '1',         'boolean', 'business',
     'Activation globale des alertes email', 0),

    -- API (secrets)
    ('google_maps_key',   '',          'string',  'api',
     'Clé API Google Maps', 1),            -- ✅ is_secret = 1

    -- SMTP
    ('smtp_host',         '',          'string',  'smtp',
     'Serveur SMTP sortant', 0),
    ('smtp_port',         '587',       'integer', 'smtp',    -- ✅ ajouté
     'Port SMTP', 0),
    ('smtp_user',         '',          'string',  'smtp',
     'Utilisateur SMTP', 0),
    ('smtp_password',     '',          'string',  'smtp',
     'Mot de passe SMTP', 1)                -- ✅ is_secret = 1

ON DUPLICATE KEY UPDATE
    valeur      = VALUES(valeur),
    type        = VALUES(type),
    groupe      = VALUES(groupe),
    description = VALUES(description),
    updated_at  = CURRENT_TIMESTAMP;

-- -----------------------------------------------
-- LOGS ADMIN (audit trail)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS logs_admin (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id BIGINT UNSIGNED NULL,                         -- ✅ NULL si compte supprimé
    admin_email VARCHAR(190) NULL,                         -- ✅ snapshot email au moment du log
    action ENUM(
        'create', 'update', 'delete',
        'login', 'logout',                                 -- ✅ ajouté logout
        'export', 'import',
        'config_change'                                    -- ✅ ajouté pour config_site
    ) NOT NULL,
    table_cible VARCHAR(100) NOT NULL,
    id_cible BIGINT UNSIGNED NULL,
    anciennes_valeurs JSON NULL,
    nouvelles_valeurs JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_logs_admin_admin_created (admin_id, created_at),
    INDEX idx_logs_admin_table_id (table_cible, id_cible),
    INDEX idx_logs_admin_action_created (action, created_at), -- ✅ filtrer par action

    CONSTRAINT fk_logs_admin_admin
        FOREIGN KEY (admin_id) REFERENCES users(id)
        ON DELETE SET NULL                                 -- ✅ RESTRICT → SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- VUES
-- ============================================

-- -----------------------------------------------
-- VUE : Biens complets avec agent et compteurs
-- -----------------------------------------------
CREATE OR REPLACE VIEW v_biens_complets AS
SELECT
    -- Bien (colonnes explicites pour éviter ONLY_FULL_GROUP_BY) ✅
    b.id,
    b.titre,
    b.slug,
    b.description,
    b.type_bien,
    b.type_transaction,
    b.statut,
    b.prix,
    b.surface,
    b.nb_pieces,
    b.nb_chambres,
    b.adresse,
    b.ville,
    b.code_postal,
    b.latitude,
    b.longitude,
    b.featured,
    b.vues,
    b.published_at,
    b.created_at,
    b.updated_at,

    -- Agent
    u.id        AS agent_id,
    u.nom       AS agent_nom,
    u.email     AS agent_email,
    u.telephone AS agent_telephone,

    -- Compteurs
    COUNT(DISTINCT p.id) AS nb_photos,
    COUNT(DISTINCT f.id) AS nb_favoris

FROM biens b
LEFT JOIN users          u ON u.id = b.agent_id
LEFT JOIN photos_biens   p ON p.bien_id = b.id
LEFT JOIN favoris        f ON f.bien_id = b.id
GROUP BY
    b.id, b.titre, b.slug, b.description,
    b.type_bien, b.type_transaction, b.statut,
    b.prix, b.surface, b.nb_pieces, b.nb_chambres,
    b.adresse, b.ville, b.code_postal,
    b.latitude, b.longitude,
    b.featured, b.vues, b.published_at,
    b.created_at, b.updated_at,
    u.id, u.nom, u.email, u.telephone;