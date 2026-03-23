<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadActivity;
use App\Models\Partenaire;

final class AdminLeadController
{
    private const REQUIRED_TABLES = [
        'leads' => [
            'id', 'website_id', 'lead_type', 'nom', 'email', 'telephone',
            'adresse', 'ville', 'type_bien', 'surface_m2', 'pieces',
            'estimation', 'urgence', 'motivation', 'notes',
            'partenaire_id', 'commission_taux', 'commission_montant',
            'assigne_a', 'date_mandat', 'date_compromis', 'date_signature',
            'prix_vente', 'score', 'statut', 'neuropersona', 'niveau_conscience',
            'created_at',
        ],
        'lead_notes' => ['id', 'lead_id', 'content', 'author', 'created_at'],
        'lead_activities' => ['id', 'lead_id', 'activity_type', 'description', 'created_at'],
    ];

    public function createTable(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        try {
            $pdo = Database::connection();
            $sql = file_get_contents(dirname(__DIR__, 2) . '/database/migration_leads.sql');
            if ($sql === false) {
                throw new \RuntimeException('Fichier de migration introuvable.');
            }

            $sql = preg_replace('/--.*$/m', '', $sql);
            $sql = trim($sql);

            if ($sql !== '') {
                $pdo->exec($sql);
            }

            $_SESSION['leads_flash'] = ['type' => 'success', 'message' => 'Table "leads" creee avec succes ! La page est maintenant fonctionnelle.'];
        } catch (\Throwable $e) {
            $_SESSION['leads_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/leads');
        exit;
    }

    public function index(): void
    {
        AuthController::requireAuth();

        $leads = [];
        $dbError = null;
        $tableExists = false;

        try {
            $tableExists = Database::tableExists('leads');
        } catch (\Throwable $e) {
            $dbError = 'Base de données indisponible : les leads ne peuvent pas être chargés.';
        }

        if ($tableExists) {
        try {
            $leadModel = new Lead();
            $scoreFilter = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
            $typeFilter = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
            $statutFilter = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;

                $leads = $leadModel->findAllLeads();

                if ($typeFilter !== null && in_array($typeFilter, ['tendance', 'qualifie'], true)) {
                    $leads = array_filter($leads, fn($l) => ($l['lead_type'] ?? '') === $typeFilter);
                    $leads = array_values($leads);
                }
                if ($scoreFilter !== null && in_array($scoreFilter, ['chaud', 'tiede', 'froid'], true)) {
                    $leads = array_filter($leads, fn($l) => ($l['score'] ?? '') === $scoreFilter);
                    $leads = array_values($leads);
                }
                if ($statutFilter !== null) {
                    $leads = array_filter($leads, fn($l) => ($l['statut'] ?? '') === $statutFilter);
                    $leads = array_values($leads);
                }
            } catch (\Throwable $e) {
                $dbError = 'Erreur lors du chargement des leads : ' . $e->getMessage();
            }
        } // end if ($tableExists)

        View::renderAdmin('admin/leads', [
            'page_title' => 'Leads - Admin CRM',
            'admin_page_title' => 'Leads',
            'admin_page' => 'leads',
            'breadcrumb' => 'Leads',
            'leads' => $leads,
            'leadCount' => count($leads),
            'dbError' => $dbError,
            'tableExists' => $tableExists,
        ]);
    }

    public function show(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $lead = $leadModel->findById($id);
        if ($lead === null) {
            header('Location: /admin/leads');
            exit;
        }

        $notes = [];
        $activities = [];
        $partenaire = null;

        try {
            $noteModel = new LeadNote();
            $notes = $noteModel->findByLeadId($id);
        } catch (\Throwable) {
        }

        try {
            $activityModel = new LeadActivity();
            $activities = $activityModel->findByLeadId($id);
        } catch (\Throwable) {
        }

        if (!empty($lead['partenaire_id'])) {
            try {
                $partenaireModel = new Partenaire();
                $partenaire = $partenaireModel->findById((int) $lead['partenaire_id']);
            } catch (\Throwable) {
            }
        }

        View::renderAdmin('admin/lead-detail', [
            'page_title' => 'Lead #' . $id . ' - Admin CRM',
            'admin_page' => 'leads',
            'breadcrumb' => 'Lead #' . $id,
            'lead' => $lead,
            'notes' => $notes,
            'activities' => $activities,
            'partenaire' => $partenaire,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $lead = $leadModel->findById($id);
        if ($lead === null) {
            header('Location: /admin/leads');
            exit;
        }

        $partenaires = [];
        try {
            $partenaireModel = new Partenaire();
            $partenaires = $partenaireModel->findActifs();
        } catch (\Throwable) {
        }

        View::renderAdmin('admin/lead-edit', [
            'page_title' => 'Modifier Lead #' . $id,
            'admin_page' => 'leads',
            'breadcrumb' => 'Modifier Lead #' . $id,
            'lead' => $lead,
            'partenaires' => $partenaires,
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        try {
            $data = [];

            $statut = trim((string) ($_POST['statut'] ?? ''));
            if ($statut !== '') {
                $data['statut'] = $statut;
            }

            $score = trim((string) ($_POST['score'] ?? ''));
            if (in_array($score, ['chaud', 'tiede', 'froid'], true)) {
                $data['score'] = $score;
            }

            $partenaireId = isset($_POST['partenaire_id']) && $_POST['partenaire_id'] !== ''
                ? (int) $_POST['partenaire_id'] : null;
            $data['partenaire_id'] = $partenaireId;

            $commissionTaux = isset($_POST['commission_taux']) && $_POST['commission_taux'] !== ''
                ? (float) $_POST['commission_taux'] : null;
            $data['commission_taux'] = $commissionTaux;

            $commissionMontant = isset($_POST['commission_montant']) && $_POST['commission_montant'] !== ''
                ? (float) $_POST['commission_montant'] : null;
            $data['commission_montant'] = $commissionMontant;

            $assigneA = trim((string) ($_POST['assigne_a'] ?? ''));
            $data['assigne_a'] = $assigneA !== '' ? $assigneA : null;

            $dateMandat = trim((string) ($_POST['date_mandat'] ?? ''));
            $data['date_mandat'] = $dateMandat !== '' ? $dateMandat : null;

            $dateCompromis = trim((string) ($_POST['date_compromis'] ?? ''));
            $data['date_compromis'] = $dateCompromis !== '' ? $dateCompromis : null;

            $dateSignature = trim((string) ($_POST['date_signature'] ?? ''));
            $data['date_signature'] = $dateSignature !== '' ? $dateSignature : null;

            $prixVente = isset($_POST['prix_vente']) && $_POST['prix_vente'] !== ''
                ? (float) $_POST['prix_vente'] : null;
            $data['prix_vente'] = $prixVente;

            $leadModel = new Lead();
            $oldLead = $leadModel->findById($id);
            $leadModel->updateLeadDetails($id, $data);

            // Log activity for status change
            if ($oldLead !== null && isset($data['statut']) && $oldLead['statut'] !== $data['statut']) {
                try {
                    $activityModel = new LeadActivity();
                    $activityModel->log($id, 'statut_change', 'Statut modifié de "' . ($oldLead['statut'] ?? '') . '" à "' . $data['statut'] . '"');
                } catch (\Throwable) {
                }
            }

            header('Location: /admin/leads/detail?id=' . $id);
            exit;
        } catch (\Throwable $e) {
            $leadModel = new Lead();
            $lead = $leadModel->findById($id);
            $partenaires = [];
            try {
                $partenaireModel = new Partenaire();
                $partenaires = $partenaireModel->findActifs();
            } catch (\Throwable) {
            }

            View::renderAdmin('admin/lead-edit', [
                'page_title' => 'Modifier Lead #' . $id,
                'admin_page' => 'leads',
                'breadcrumb' => 'Modifier Lead #' . $id,
                'lead' => $lead,
                'partenaires' => $partenaires,
                'errors' => [$e->getMessage()],
            ]);
        }
    }

    public function updateStatut(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $statut = trim((string) ($_POST['statut'] ?? ''));

        if ($id > 0 && $statut !== '') {
            $leadModel = new Lead();
            $oldLead = $leadModel->findById($id);
            $leadModel->updateStatut($id, $statut);

            if ($oldLead !== null && $oldLead['statut'] !== $statut) {
                try {
                    $activityModel = new LeadActivity();
                    $activityModel->log($id, 'statut_change', 'Statut modifié de "' . ($oldLead['statut'] ?? '') . '" à "' . $statut . '"');
                } catch (\Throwable) {
                }
            }
        }

        header('Location: /admin/leads');
        exit;
    }

    public function addNote(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $leadId = isset($_POST['lead_id']) ? (int) $_POST['lead_id'] : 0;
        $content = trim((string) ($_POST['content'] ?? ''));
        $author = trim((string) ($_SESSION['admin_name'] ?? 'Admin'));

        if ($leadId > 0 && $content !== '') {
            try {
                $noteModel = new LeadNote();
                $noteModel->create($leadId, $content, $author);

                $activityModel = new LeadActivity();
                $activityModel->log($leadId, 'note_added', 'Note ajoutée par ' . $author);
            } catch (\Throwable) {
            }
        }

        header('Location: /admin/leads/detail?id=' . $leadId);
        exit;
    }

    public function deleteNote(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $noteId = isset($_POST['note_id']) ? (int) $_POST['note_id'] : 0;
        $leadId = isset($_POST['lead_id']) ? (int) $_POST['lead_id'] : 0;

        if ($noteId > 0) {
            try {
                $noteModel = new LeadNote();
                $noteModel->delete($noteId);
            } catch (\Throwable) {
            }
        }

        header('Location: /admin/leads/detail?id=' . $leadId);
        exit;
    }

    public function profile(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $lead = $leadModel->findById($id);
        if ($lead === null) {
            header('Location: /admin/leads');
            exit;
        }

        $notes = [];
        try {
            $noteModel = new LeadNote();
            $notes = $noteModel->findByLeadId($id);
        } catch (\Throwable) {
        }

        View::renderAdmin('admin/lead-profile', [
            'page_title' => 'Profil Prospect - ' . ($lead['nom'] ?: 'Lead #' . $id),
            'admin_page_title' => 'Profil Prospect',
            'admin_page' => 'leads',
            'breadcrumb' => 'Profil Prospect',
            'lead' => $lead,
            'notes' => $notes,
        ]);
    }

    public function saveProfile(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            header('Location: /admin/leads');
            exit;
        }

        $allowedPersonas = ['analytique', 'expressif', 'directif', 'aimable'];
        $allowedNiveaux = ['inconscient', 'probleme', 'solution', 'produit', 'tres_conscient'];

        $neuropersona = trim((string) ($_POST['neuropersona'] ?? ''));
        $niveauConscience = trim((string) ($_POST['niveau_conscience'] ?? ''));

        $data = [];
        $data['neuropersona'] = in_array($neuropersona, $allowedPersonas, true) ? $neuropersona : null;
        $data['niveau_conscience'] = in_array($niveauConscience, $allowedNiveaux, true) ? $niveauConscience : null;

        $leadModel = new Lead();
        $leadModel->updateLeadDetails($id, $data);

        try {
            $activityModel = new LeadActivity();
            $activityModel->log($id, 'profile_updated', 'Profil prospect mis à jour (persona: ' . ($data['neuropersona'] ?? '-') . ', conscience: ' . ($data['niveau_conscience'] ?? '-') . ')');
        } catch (\Throwable) {
        }

        header('Location: /admin/leads/profile?id=' . $id);
        exit;
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            $leadModel = new Lead();
            $leadModel->deleteById($id);
        }

        header('Location: /admin/leads');
        exit;
    }

    public function createTables(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $tables = isset($_POST['tables']) ? (array) $_POST['tables'] : [];
        $created = [];

        $sqlStatements = [
            'leads' => "CREATE TABLE IF NOT EXISTS leads (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL,
                lead_type ENUM('tendance','qualifie') NOT NULL DEFAULT 'qualifie',
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
                score ENUM('chaud','tiede','froid') NOT NULL DEFAULT 'froid',
                statut ENUM('nouveau','contacte','rdv_pris','visite_realisee','mandat_simple','mandat_exclusif','compromis_vente','signe','co_signature_partenaire','assigne_autre') NOT NULL DEFAULT 'nouveau',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_website_id (website_id),
                INDEX idx_lead_type (lead_type),
                INDEX idx_email (email),
                INDEX idx_statut (statut),
                INDEX idx_created_at (created_at),
                INDEX idx_partenaire_id (partenaire_id),
                INDEX idx_date_signature (date_signature)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'lead_notes' => "CREATE TABLE IF NOT EXISTS lead_notes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                lead_id INT UNSIGNED NOT NULL,
                content TEXT NOT NULL,
                author VARCHAR(120) NOT NULL DEFAULT 'Admin',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_lead_id (lead_id),
                INDEX idx_created_at (created_at),
                CONSTRAINT fk_lead_notes_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'lead_activities' => "CREATE TABLE IF NOT EXISTS lead_activities (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                lead_id INT UNSIGNED NOT NULL,
                activity_type VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_lead_id (lead_id),
                INDEX idx_activity_type (activity_type),
                INDEX idx_created_at (created_at),
                CONSTRAINT fk_lead_activities_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];

        try {
            $pdo = Database::connection();

            // Ensure 'leads' is created first (other tables depend on it)
            if (in_array('lead_notes', $tables, true) || in_array('lead_activities', $tables, true)) {
                if (!in_array('leads', $tables, true) && !Database::tableExists('leads')) {
                    array_unshift($tables, 'leads');
                }
            }

            foreach ($tables as $table) {
                if (!isset($sqlStatements[$table])) {
                    continue;
                }

                if (Database::tableExists($table)) {
                    $this->addMissingColumns($pdo, $table);
                    $created[] = $table . ' (colonnes mises à jour)';
                    continue;
                }

                $pdo->exec($sqlStatements[$table]);
                $created[] = $table;
            }

            if (!empty($created)) {
                $_SESSION['leads_flash'] = ['type' => 'success', 'message' => 'Tables créées avec succès : ' . implode(', ', $created)];
            }
        } catch (\Throwable $e) {
            $_SESSION['leads_flash'] = ['type' => 'error', 'message' => 'Erreur : ' . $e->getMessage()];
        }

        header('Location: /admin/leads');
        exit;
    }

    private function addMissingColumns(\PDO $pdo, string $table): void
    {
        if (!isset(self::REQUIRED_TABLES[$table])) {
            return;
        }

        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        $actualColumns = array_column($stmt->fetchAll(), 'Field');
        $missing = array_diff(self::REQUIRED_TABLES[$table], $actualColumns);

        $columnDefs = [
            'leads' => [
                'website_id' => 'INT UNSIGNED NOT NULL DEFAULT 1',
                'lead_type' => "ENUM('tendance','qualifie') NOT NULL DEFAULT 'qualifie'",
                'nom' => 'VARCHAR(120) NULL DEFAULT NULL',
                'email' => 'VARCHAR(180) NULL DEFAULT NULL',
                'telephone' => 'VARCHAR(40) NULL DEFAULT NULL',
                'adresse' => 'VARCHAR(255) NULL DEFAULT NULL',
                'ville' => 'VARCHAR(120) NOT NULL DEFAULT \'Bordeaux\'',
                'type_bien' => 'VARCHAR(80) NULL',
                'surface_m2' => 'DECIMAL(8,2) NULL',
                'pieces' => 'INT UNSIGNED NULL',
                'estimation' => 'DECIMAL(12,2) NOT NULL DEFAULT 0',
                'urgence' => 'VARCHAR(40) NULL DEFAULT NULL',
                'motivation' => 'VARCHAR(80) NULL DEFAULT NULL',
                'notes' => 'TEXT NULL',
                'partenaire_id' => 'INT UNSIGNED NULL',
                'commission_taux' => 'DECIMAL(5,2) NULL DEFAULT NULL',
                'commission_montant' => 'DECIMAL(12,2) NULL DEFAULT NULL',
                'assigne_a' => 'VARCHAR(180) NULL DEFAULT NULL',
                'date_mandat' => 'DATE NULL DEFAULT NULL',
                'date_compromis' => 'DATE NULL DEFAULT NULL',
                'date_signature' => 'DATE NULL DEFAULT NULL',
                'prix_vente' => 'DECIMAL(12,2) NULL DEFAULT NULL',
                'score' => "ENUM('chaud','tiede','froid') NOT NULL DEFAULT 'froid'",
                'statut' => "ENUM('nouveau','contacte','rdv_pris','visite_realisee','mandat_simple','mandat_exclusif','compromis_vente','signe','co_signature_partenaire','assigne_autre') NOT NULL DEFAULT 'nouveau'",
                'neuropersona' => "VARCHAR(30) NULL DEFAULT NULL",
                'niveau_conscience' => "VARCHAR(30) NULL DEFAULT NULL",
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'lead_notes' => [
                'lead_id' => 'INT UNSIGNED NOT NULL',
                'content' => 'TEXT NOT NULL',
                'author' => "VARCHAR(120) NOT NULL DEFAULT 'Admin'",
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
            'lead_activities' => [
                'lead_id' => 'INT UNSIGNED NOT NULL',
                'activity_type' => 'VARCHAR(50) NOT NULL',
                'description' => 'TEXT NOT NULL',
                'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            ],
        ];

        foreach ($missing as $col) {
            if ($col === 'id') {
                continue;
            }
            $def = $columnDefs[$table][$col] ?? 'TEXT NULL';
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$def}");
        }
    }
}
