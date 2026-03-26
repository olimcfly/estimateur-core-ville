<?php

/**
 * Migration script to add missing tables to an existing database.
 *
 * Safe to run multiple times — uses CREATE TABLE IF NOT EXISTS.
 *
 * Usage: php database/migrate.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

echo "=== Migration : ajout des tables manquantes ===\n\n";

// 1. Test connection
echo "Connexion... ";
try {
    $pdo = Database::connection();
    echo "OK\n\n";
} catch (\Throwable $e) {
    echo "ECHEC : " . $e->getMessage() . "\n";
    exit(1);
}

// Get existing tables
$existingTables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
echo "Tables existantes : " . implode(', ', $existingTables) . "\n\n";

// Define migrations — each entry is [table_name, SQL]
// Order matters: tables with foreign keys must come after their referenced tables.
$migrations = [
    ['articles', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['article_revisions', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['leads', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['estimations', "
        CREATE TABLE IF NOT EXISTS estimations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            ville VARCHAR(120) NOT NULL,
            type_bien VARCHAR(80) NOT NULL,
            surface_m2 DECIMAL(8,2) NOT NULL,
            pieces INT UNSIGNED NOT NULL DEFAULT 3,
            per_sqm_low DECIMAL(10,2) NOT NULL,
            per_sqm_mid DECIMAL(10,2) NOT NULL,
            per_sqm_high DECIMAL(10,2) NOT NULL,
            estimated_low DECIMAL(12,2) NOT NULL,
            estimated_mid DECIMAL(12,2) NOT NULL,
            estimated_high DECIMAL(12,2) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_estimations_website_id (website_id),
            INDEX idx_estimations_ville (ville),
            INDEX idx_estimations_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['partenaires', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['achats', "
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
            INDEX idx_website_id (website_id),
            INDEX idx_lead_id (lead_id),
            INDEX idx_statut (statut),
            INDEX idx_score (score),
            INDEX idx_ville (ville),
            INDEX idx_created_at (created_at),
            INDEX idx_partenaire_id (partenaire_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['admin_users', "
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            name VARCHAR(120) NOT NULL DEFAULT '',
            login_code VARCHAR(255) DEFAULT NULL,
            login_code_expires_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_admin_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['newsletter_subscribers', "
        CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(180) NOT NULL UNIQUE,
            confirmed_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_newsletter_confirmed_at (confirmed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['design_templates', "
        CREATE TABLE IF NOT EXISTS design_templates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL DEFAULT '',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_templates', "
        CREATE TABLE IF NOT EXISTS email_templates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NOT NULL,
            signature TEXT NULL,
            category ENUM('notification', 'client', 'sequence', 'marketing') NOT NULL DEFAULT 'notification',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_logs', "
        CREATE TABLE IF NOT EXISTS email_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            recipient VARCHAR(180) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NULL,
            status ENUM('sent', 'failed') NOT NULL DEFAULT 'sent',
            template_id INT UNSIGNED NULL,
            sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_recipient (recipient),
            INDEX idx_status (status),
            INDEX idx_sent_at (sent_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_sequences', "
        CREATE TABLE IF NOT EXISTS email_sequences (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            persona VARCHAR(50) NULL,
            trigger_event VARCHAR(50) NOT NULL DEFAULT 'lead_created',
            status ENUM('draft', 'active', 'paused') NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_persona (persona)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_sequence_steps', "
        CREATE TABLE IF NOT EXISTS email_sequence_steps (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sequence_id INT UNSIGNED NOT NULL,
            step_order INT UNSIGNED NOT NULL DEFAULT 1,
            delay_days INT UNSIGNED NOT NULL DEFAULT 0,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sequence_order (sequence_id, step_order),
            CONSTRAINT fk_seq_steps_sequence
                FOREIGN KEY (sequence_id) REFERENCES email_sequences(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['lead_personas', "
        CREATE TABLE IF NOT EXISTS lead_personas (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            lead_id INT UNSIGNED NOT NULL UNIQUE,
            neuropersona VARCHAR(50) NULL,
            bant_budget TEXT NULL,
            bant_authority TEXT NULL,
            bant_need TEXT NULL,
            bant_timeline TEXT NULL,
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_neuropersona (neuropersona),
            CONSTRAINT fk_persona_lead
                FOREIGN KEY (lead_id) REFERENCES leads(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['actualites', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['actualites_cron_log', "
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['rss_sources', "
        CREATE TABLE IF NOT EXISTS rss_sources (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            feed_url VARCHAR(500) NOT NULL,
            site_url VARCHAR(500) DEFAULT NULL,
            category VARCHAR(100) NOT NULL DEFAULT 'general',
            zone ENUM('national', 'Bordeaux/Nouvelle-Aquitaine') NOT NULL DEFAULT 'national',
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            last_fetched_at DATETIME DEFAULT NULL,
            last_error TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_rss_sources_url (website_id, feed_url(400)),
            INDEX idx_rss_sources_active (is_active, zone),
            INDEX idx_rss_sources_website (website_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['rss_articles', "
        CREATE TABLE IF NOT EXISTS rss_articles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            rss_source_id INT UNSIGNED NOT NULL,
            guid VARCHAR(500) NOT NULL,
            title VARCHAR(500) NOT NULL,
            link VARCHAR(500) NOT NULL,
            description TEXT DEFAULT NULL,
            content TEXT DEFAULT NULL,
            author VARCHAR(255) DEFAULT NULL,
            pub_date DATETIME DEFAULT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            is_starred TINYINT(1) NOT NULL DEFAULT 0,
            is_used TINYINT(1) NOT NULL DEFAULT 0,
            blog_article_id INT UNSIGNED DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_rss_articles_guid (website_id, guid(400)),
            INDEX idx_rss_articles_source (rss_source_id),
            INDEX idx_rss_articles_pubdate (pub_date),
            INDEX idx_rss_articles_starred (is_starred),
            INDEX idx_rss_articles_used (is_used),
            CONSTRAINT fk_rss_articles_source
                FOREIGN KEY (rss_source_id) REFERENCES rss_sources(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['rss_blog_generation_log', "
        CREATE TABLE IF NOT EXISTS rss_blog_generation_log (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            rss_article_ids TEXT NOT NULL,
            blog_article_id INT UNSIGNED DEFAULT NULL,
            prompt_used LONGTEXT DEFAULT NULL,
            status ENUM('success', 'error') NOT NULL DEFAULT 'success',
            error_message TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_rss_blog_log_website (website_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_drafts', "
        CREATE TABLE IF NOT EXISTS email_drafts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            recipient VARCHAR(500) NULL,
            cc VARCHAR(500) NULL,
            subject VARCHAR(255) NULL,
            body_html LONGTEXT NULL,
            status ENUM('draft', 'scheduled', 'sent', 'failed') NOT NULL DEFAULT 'draft',
            scheduled_at DATETIME NULL,
            sent_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_scheduled (status, scheduled_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['actualite_ai_config', "
        CREATE TABLE IF NOT EXISTS actualite_ai_config (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            website_id INT UNSIGNED NOT NULL,
            config_key VARCHAR(100) NOT NULL,
            config_value TEXT NOT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_actualite_ai_config (website_id, config_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],

    ['email_library', "
        CREATE TABLE IF NOT EXISTS email_library (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL DEFAULT 'autre',
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT NOT NULL,
            tags TEXT DEFAULT NULL,
            is_default TINYINT(1) NOT NULL DEFAULT 0,
            usage_count INT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email_library_category (category),
            INDEX idx_email_library_usage (usage_count)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "],
];

// Run migrations
$created = 0;
$skipped = 0;

foreach ($migrations as [$table, $sql]) {
    $exists = in_array($table, $existingTables, true);
    echo "  {$table} : ";

    if ($exists) {
        echo "existe déjà, ignorée\n";
        $skipped++;
        continue;
    }

    try {
        $pdo->exec($sql);
        echo "CRÉÉE\n";
        $created++;
    } catch (\PDOException $e) {
        echo "ERREUR - " . $e->getMessage() . "\n";
    }
}

// Add new columns to existing tables (safe to run multiple times)
echo "\n  Colonnes additionnelles...\n";
$alterStatements = [
    "ALTER TABLE rss_articles ADD COLUMN IF NOT EXISTS actualite_id INT UNSIGNED DEFAULT NULL AFTER blog_article_id",
    "ALTER TABLE actualites ADD COLUMN IF NOT EXISTS rss_article_ids TEXT DEFAULT NULL AFTER source_results",
];
foreach ($alterStatements as $alterSql) {
    try {
        $pdo->exec($alterSql);
    } catch (\PDOException) {
        // Column may already exist or syntax not supported — ignore
    }
}
echo "  OK\n";

// Seed default admin if table was just created
if (!in_array('admin_users', $existingTables, true)) {
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'contact@estimation-immobilier-bordeaux.fr';
    echo "\n  Ajout de l'admin par défaut ({$adminEmail})... ";
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO admin_users (email, name, created_at) VALUES (:email, :name, NOW())'
    );
    $stmt->execute(['email' => $adminEmail, 'name' => 'Administrateur']);
    echo "OK\n";
}

// Seed default email library templates
if (!in_array('email_library', $existingTables, true)) {
    echo "\n  Ajout des modèles email par défaut... ";
    $libraryTemplates = [
        ['Première prise de contact', 'prospection', 'Votre estimation immobilière gratuite à Bordeaux',
         '<p>Bonjour {{nom}},</p><p>Je me permets de vous contacter suite à votre intérêt pour le marché immobilier bordelais.</p><p>En tant que spécialiste de l\'estimation immobilière à Bordeaux, je serais ravi de vous accompagner dans votre projet. Nous proposons une <strong>estimation gratuite et sans engagement</strong> de votre bien.</p><p>Seriez-vous disponible pour en discuter cette semaine ?</p><p>Bien cordialement</p>',
         'premier contact,prospection,estimation gratuite'],
        ['Relance après estimation', 'relance', 'Suite à votre estimation - Prochaines étapes',
         '<p>Bonjour {{nom}},</p><p>Je reviens vers vous suite à l\'estimation de votre bien situé à {{ville}}.</p><p>Pour rappel, nous avions estimé votre {{type_bien}} à <strong>{{estimation}}</strong>. Le marché bordelais étant actuellement dynamique, c\'est le moment idéal pour concrétiser votre projet.</p><p>Souhaitez-vous que nous planifions un rendez-vous pour discuter de la suite ?</p><p>Cordialement</p>',
         'relance,estimation,suivi'],
        ['Bienvenue nouveau client', 'bienvenue', 'Bienvenue chez Estimation Immobilier Bordeaux !',
         '<p>Bonjour {{nom}},</p><p>Merci de votre confiance ! Nous sommes ravis de vous compter parmi nos clients.</p><p>Voici ce que vous pouvez attendre de nous :</p><ul><li>Un accompagnement personnalisé tout au long de votre projet</li><li>Une connaissance approfondie du marché bordelais</li><li>Des estimations précises basées sur les données du marché</li></ul><p>N\'hésitez pas à me contacter pour toute question.</p><p>À très bientôt !</p>',
         'bienvenue,onboarding,nouveau client'],
        ['Résultat d\'estimation', 'estimation', 'Résultat de l\'estimation de votre bien à {{ville}}',
         '<p>Bonjour {{nom}},</p><p>Suite à notre analyse du marché et des caractéristiques de votre {{type_bien}}, voici le résultat de notre estimation :</p><p style="font-size:1.2em;text-align:center;padding:15px;background:#f8f7f5;border-radius:8px;"><strong>Valeur estimée : {{estimation}}</strong></p><p>Cette estimation prend en compte :</p><ul><li>Les transactions récentes dans votre quartier</li><li>Les caractéristiques spécifiques de votre bien</li><li>Les tendances actuelles du marché bordelais</li></ul><p>Je reste à votre disposition pour en discuter de vive voix.</p><p>Cordialement</p>',
         'estimation,résultat,valeur'],
        ['Suivi après visite', 'suivi', 'Suite à notre rencontre - Votre projet immobilier',
         '<p>Bonjour {{nom}},</p><p>C\'était un plaisir de vous rencontrer aujourd\'hui et de visiter votre {{type_bien}} à {{ville}}.</p><p>Comme convenu, je vous transmettrai une estimation détaillée dans les prochaines 48 heures.</p><p>En attendant, n\'hésitez pas à me contacter si vous avez des questions.</p><p>Bien cordialement</p>',
         'suivi,visite,rendez-vous'],
        ['Proposition partenaire', 'partenaire', 'Proposition de partenariat immobilier à Bordeaux',
         '<p>Bonjour,</p><p>Je me permets de vous contacter car nous développons un réseau de partenaires professionnels dans le secteur immobilier bordelais.</p><p>Notre plateforme <strong>estimation-immobilier-bordeaux.fr</strong> génère un trafic qualifié de propriétaires et acquéreurs potentiels. Un partenariat pourrait être mutuellement bénéfique.</p><p>Seriez-vous ouvert à un échange pour en discuter ?</p><p>Cordialement</p>',
         'partenaire,collaboration,réseau'],
        ['Newsletter marché immobilier', 'marketing', 'Marché immobilier Bordeaux : les tendances du mois',
         '<p>Bonjour {{nom}},</p><p>Voici les dernières tendances du marché immobilier bordelais :</p><h3>📊 Chiffres clés du mois</h3><ul><li>Prix moyen au m² : en légère hausse</li><li>Délai de vente moyen : stable</li><li>Nombre de transactions : en progression</li></ul><h3>🏡 Quartiers à surveiller</h3><p>Les quartiers Chartrons, Bastide et Saint-Michel continuent d\'attirer les acquéreurs.</p><p>Pour une estimation gratuite de votre bien, <a href="https://estimation-immobilier-bordeaux.fr">cliquez ici</a>.</p><p>À bientôt !</p>',
         'newsletter,marché,tendances,marketing'],
        ['Relance douce (pas de réponse)', 'relance', 'Avez-vous encore un projet immobilier à Bordeaux ?',
         '<p>Bonjour {{nom}},</p><p>Je me permets de revenir vers vous car nous avions échangé au sujet de votre projet immobilier.</p><p>Votre situation a peut-être évolué depuis. Si c\'est le cas, je reste à votre entière disposition pour :</p><ul><li>Mettre à jour l\'estimation de votre bien</li><li>Vous informer sur l\'évolution du marché dans votre secteur</li><li>Répondre à vos questions</li></ul><p>N\'hésitez pas à me recontacter quand vous le souhaitez.</p><p>Bien cordialement</p>',
         'relance,douce,rappel,suivi'],
    ];
    $stmt = $pdo->prepare('INSERT INTO email_library (name, category, subject, body_html, tags, is_default) VALUES (:name, :category, :subject, :body_html, :tags, 1)');
    foreach ($libraryTemplates as [$name, $cat, $subj, $body, $tags]) {
        $stmt->execute(['name' => $name, 'category' => $cat, 'subject' => $subj, 'body_html' => $body, 'tags' => $tags]);
    }
    echo "OK (" . count($libraryTemplates) . " modèles ajoutés)\n";
}

// Summary
echo "\n=== Résultat ===\n";
echo "  Tables créées  : {$created}\n";
echo "  Tables ignorées : {$skipped}\n";

// Final verification
$finalTables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
$requiredTables = ['articles', 'article_revisions', 'leads', 'partenaires', 'admin_users', 'newsletter_subscribers', 'design_templates', 'email_templates', 'email_logs', 'email_sequences', 'email_sequence_steps', 'lead_personas'];
$requiredTables = ['articles', 'article_revisions', 'leads', 'partenaires', 'achats', 'admin_users', 'newsletter_subscribers', 'design_templates', 'actualites', 'actualites_cron_log', 'rss_sources', 'rss_articles', 'rss_blog_generation_log', 'actualite_ai_config', 'email_library'];
$missing = array_diff($requiredTables, $finalTables);

if (empty($missing)) {
    echo "\n  Toutes les tables requises sont présentes.\n";
} else {
    echo "\n  ATTENTION — tables encore manquantes : " . implode(', ', $missing) . "\n";
}

echo "\n=== Migration terminée ===\n";
