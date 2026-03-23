<?php

/**
 * Migration: Create GMB publications tables and default settings.
 * Safe to run multiple times (uses IF NOT EXISTS / INSERT IGNORE).
 *
 * Usage: php database/migrate-gmb.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

echo "=== Migration: GMB Publications ===\n\n";

// 1. Connexion DB
try {
    $pdo = Database::connection();
    echo "1. Connexion DB... OK\n";
} catch (\Throwable $e) {
    echo "ERREUR: Impossible de se connecter à la base de données.\n";
    echo "   " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Exécuter le fichier SQL de migration
try {
    $sqlFile = __DIR__ . '/migration_gmb_publications.sql';
    if (!file_exists($sqlFile)) {
        echo "ERREUR: Fichier SQL introuvable: {$sqlFile}\n";
        exit(1);
    }

    $sql = file_get_contents($sqlFile);

    // Filtrer les commentaires et lignes vides, exécuter chaque statement
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($s) {
            $s = trim($s);
            return $s !== '' && !str_starts_with($s, '--');
        }
    );

    foreach ($statements as $statement) {
        // Ignorer les lignes qui ne sont que des commentaires
        $lines = array_filter(explode("\n", $statement), function ($line) {
            $line = trim($line);
            return $line !== '' && !str_starts_with($line, '--');
        });
        if (empty($lines)) {
            continue;
        }
        $pdo->exec($statement);
    }

    echo "2. Tables gmb_publications et gmb_settings... créées (ou déjà existantes).\n";
} catch (\Throwable $e) {
    echo "ERREUR lors de l'exécution du SQL: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Vérifier que les tables existent
try {
    $tables = $pdo->query("SHOW TABLES LIKE 'gmb_%'")->fetchAll(\PDO::FETCH_COLUMN);
    $expected = ['gmb_publications', 'gmb_settings'];
    foreach ($expected as $table) {
        if (in_array($table, $tables, true)) {
            echo "3. Table '{$table}'... OK\n";
        } else {
            echo "3. Table '{$table}'... MANQUANTE !\n";
        }
    }
} catch (\Throwable $e) {
    echo "ERREUR vérification tables: " . $e->getMessage() . "\n";
}

// 4. Vérifier les index sur gmb_publications
try {
    $indexes = $pdo->query("SHOW INDEX FROM gmb_publications")->fetchAll(\PDO::FETCH_ASSOC);
    $indexNames = array_unique(array_column($indexes, 'Key_name'));
    $expectedIndexes = [
        'idx_gmb_website_id',
        'idx_gmb_article_id',
        'idx_gmb_actualite_id',
        'idx_gmb_status',
        'idx_gmb_scheduled_at',
        'idx_gmb_post_type',
        'idx_gmb_status_scheduled',
    ];
    foreach ($expectedIndexes as $idx) {
        if (in_array($idx, $indexNames, true)) {
            echo "4. Index '{$idx}'... OK\n";
        } else {
            echo "4. Index '{$idx}'... MANQUANT !\n";
        }
    }
} catch (\Throwable $e) {
    echo "ERREUR vérification index: " . $e->getMessage() . "\n";
}

// 5. Insérer les settings par défaut
try {
    $defaults = [
        'default_cta_type'   => 'learn_more',
        'default_cta_url'    => '',
        'notification_email' => '',
        'notification_hour'  => '8',
        'auto_generate'      => '1',
        'posting_days'       => '1,3,5',
        'gmb_profile_url'    => '',
    ];

    // Récupérer le website_id depuis la config ou la table websites
    $websiteId = null;
    try {
        $row = $pdo->query("SELECT id FROM websites LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        $websiteId = $row['id'] ?? null;
    } catch (\Throwable $e) {
        // Table websites n'existe peut-être pas
    }

    if ($websiteId) {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO gmb_settings (website_id, setting_key, setting_value) VALUES (?, ?, ?)"
        );
        foreach ($defaults as $key => $value) {
            $stmt->execute([$websiteId, $key, $value]);
        }
        echo "5. Settings par défaut... insérés (website_id={$websiteId}).\n";
    } else {
        echo "5. Settings par défaut... ignorés (aucun website trouvé, à insérer manuellement).\n";
    }
} catch (\Throwable $e) {
    echo "ERREUR insertion settings: " . $e->getMessage() . "\n";
}

// 6. Vérifier la structure finale
try {
    $columns = $pdo->query("SHOW COLUMNS FROM gmb_publications")->fetchAll(\PDO::FETCH_ASSOC);
    echo "\n6. Structure gmb_publications (" . count($columns) . " colonnes):\n";
    foreach ($columns as $col) {
        $null = $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $col['Default'] !== null ? " DEFAULT '{$col['Default']}'" : '';
        echo "   - {$col['Field']} ({$col['Type']}) {$null}{$default}\n";
    }
} catch (\Throwable $e) {
    echo "ERREUR vérification structure: " . $e->getMessage() . "\n";
}

echo "\n=== Migration GMB terminée ===\n";
