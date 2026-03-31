CREATE TABLE IF NOT EXISTS categories_blog (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    couleur VARCHAR(50) NULL,
    icone VARCHAR(100) NULL,
    ordre INT NOT NULL DEFAULT 0,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_blog_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tags_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    extrait VARCHAR(500) NULL,
    contenu LONGTEXT NOT NULL,
    image_cover_url VARCHAR(2048) NULL,
    auteur_id BIGINT UNSIGNED NOT NULL,
    categorie_id BIGINT UNSIGNED NOT NULL,
    statut ENUM('brouillon', 'publie', 'archive') NOT NULL DEFAULT 'brouillon',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    vues INT UNSIGNED NOT NULL DEFAULT 0,
    temps_lecture SMALLINT UNSIGNED NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    og_image_url VARCHAR(2048) NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_articles_slug (slug),
    KEY idx_articles_statut_published_at (statut, published_at),
    KEY idx_articles_auteur_id_statut (auteur_id, statut),
    KEY idx_articles_categorie_id_statut (categorie_id, statut),
    CONSTRAINT fk_articles_auteur_id_users
        FOREIGN KEY (auteur_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_articles_categorie_id_categories_blog
        FOREIGN KEY (categorie_id) REFERENCES categories_blog (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles_tags (
    article_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    CONSTRAINT fk_articles_tags_article_id_articles
        FOREIGN KEY (article_id) REFERENCES articles (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_articles_tags_tag_id_tags
        FOREIGN KEY (tag_id) REFERENCES tags (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commentaires (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    nom VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    contenu TEXT NOT NULL,
    statut ENUM('en_attente', 'approuve', 'spam') NOT NULL DEFAULT 'en_attente',
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_commentaires_article_id_statut (article_id, statut),
    CONSTRAINT fk_commentaires_article_id_articles
        FOREIGN KEY (article_id) REFERENCES articles (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_commentaires_parent_id_commentaires
        FOREIGN KEY (parent_id) REFERENCES commentaires (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_commentaires_user_id_users
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    nom VARCHAR(255) NULL,
    prenom VARCHAR(255) NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    token_desabo VARCHAR(255) NOT NULL,
    preferences JSON NULL,
    confirmed TINYINT(1) NOT NULL DEFAULT 0,
    confirmed_at TIMESTAMP NULL,
    source VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_newsletter_email (email),
    UNIQUE KEY uq_newsletter_token_desabo (token_desabo),
    KEY idx_newsletter_actif_confirmed (actif, confirmed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages_seo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_type ENUM('accueil', 'ville', 'type_bien', 'blog') NOT NULL,
    identifiant VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    og_title VARCHAR(255) NULL,
    og_description VARCHAR(500) NULL,
    og_image VARCHAR(2048) NULL,
    h1_override VARCHAR(255) NULL,
    contenu_intro TEXT NULL,
    contenu_bas_page TEXT NULL,
    schema_json JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pages_seo_page_type_identifiant (page_type, identifiant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
