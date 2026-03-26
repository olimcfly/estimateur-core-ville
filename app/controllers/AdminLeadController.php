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

        $this->ensureLeadTables();

        $leads = [];
        $dbError = null;
        $tableExists = false;
        $total = 0;
        $villes = [];

        try {
            $tableExists = Database::tableExists('leads');
        } catch (\Throwable $e) {
            $dbError = 'Base de données indisponible : les leads ne peuvent pas être chargés.';
        }

        // Read filters from query string
        $search = isset($_GET['q']) ? trim((string) $_GET['q']) : null;
        $scoreFilter = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
        $typeFilter = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
        $statutFilter = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $villeFilter = isset($_GET['ville']) ? trim((string) $_GET['ville']) : null;
        $dateFrom = isset($_GET['date_from']) ? trim((string) $_GET['date_from']) : null;
        $dateTo = isset($_GET['date_to']) ? trim((string) $_GET['date_to']) : null;
        $sortBy = isset($_GET['sort']) ? trim((string) $_GET['sort']) : 'created_at';
        $sortDir = isset($_GET['dir']) ? trim((string) $_GET['dir']) : 'DESC';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 25;

        if ($tableExists) {
            try {
                $leadModel = new Lead();
                $result = $leadModel->searchLeads(
                    $search, $scoreFilter, $statutFilter, $typeFilter,
                    $villeFilter, $dateFrom, $dateTo,
                    $sortBy, $sortDir, $page, $perPage
                );
                $leads = $result['leads'];
                $total = $result['total'];
                $villes = $leadModel->getDistinctVilles();
            } catch (\Throwable $e) {
                $dbError = 'Erreur lors du chargement des leads : ' . $e->getMessage();
            }
        }

        $totalPages = max(1, (int) ceil($total / $perPage));

        // Count stats (unfiltered)
        $allStats = ['total' => 0, 'chaud' => 0, 'today' => 0];
        if ($tableExists) {
            try {
                $leadModel = $leadModel ?? new Lead();
                $statsResult = $leadModel->searchLeads(null, null, null, null, null, null, null, 'id', 'DESC', 1, 1);
                $allStats['total'] = $statsResult['total'];
                $hotResult = $leadModel->searchLeads(null, 'chaud', null, null, null, null, null, 'id', 'DESC', 1, 1);
                $allStats['chaud'] = $hotResult['total'];
                $todayResult = $leadModel->searchLeads(null, null, null, null, null, date('Y-m-d'), date('Y-m-d'), 'id', 'DESC', 1, 1);
                $allStats['today'] = $todayResult['total'];
            } catch (\Throwable) {
            }
        }

        View::renderAdmin('admin/leads', [
            'page_title' => 'Leads - Admin CRM',
            'admin_page_title' => 'Leads',
            'admin_page' => 'leads',
            'breadcrumb' => 'Leads',
            'leads' => $leads,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'villes' => $villes,
            'allStats' => $allStats,
            'filters' => [
                'q' => $search,
                'score' => $scoreFilter,
                'type' => $typeFilter,
                'statut' => $statutFilter,
                'ville' => $villeFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sortBy,
                'dir' => $sortDir,
            ],
            'dbError' => $dbError,
            'tableExists' => $tableExists,
        ]);
    }

    public function show(): void
    {
        AuthController::requireAuth();
        $this->ensureLeadTables();

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

    public function quickUpdate(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        // Validate CSRF manually to return JSON (not plain text) on failure
        $token = (string) ($_POST['csrf_token'] ?? '');
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $csrfValid = ($sessionToken !== '' && $token !== '' && hash_equals($sessionToken, $token));
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if (!$csrfValid) {
            echo json_encode(['success' => false, 'error' => 'Session expirée. Veuillez réessayer.', 'csrf_token' => $_SESSION['csrf_token']]);
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $field = trim((string) ($_POST['field'] ?? ''));
        $value = trim((string) ($_POST['value'] ?? ''));

        if ($id <= 0 || $field === '' || $value === '') {
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
            return;
        }

        if (!in_array($field, ['statut', 'score'], true)) {
            echo json_encode(['success' => false, 'error' => 'Champ non autorisé.']);
            return;
        }

        try {
            $leadModel = new Lead();
            $oldLead = $leadModel->findById($id);

            if ($oldLead === null) {
                echo json_encode(['success' => false, 'error' => 'Lead introuvable.']);
                return;
            }

            $updated = false;

            if ($field === 'statut') {
                $updated = $leadModel->updateStatut($id, $value);
                if ($updated && $oldLead['statut'] !== $value) {
                    try {
                        $activityModel = new LeadActivity();
                        $activityModel->log($id, 'statut_change', 'Statut modifié de "' . ($oldLead['statut'] ?? '') . '" à "' . $value . '"');
                    } catch (\Throwable) {
                    }
                }
            } elseif ($field === 'score') {
                $updated = $leadModel->updateScore($id, $value);
                if ($updated && $oldLead['score'] !== $value) {
                    try {
                        $activityModel = new LeadActivity();
                        $activityModel->log($id, 'score_change', 'Score modifié de "' . ($oldLead['score'] ?? '') . '" à "' . $value . '"');
                    } catch (\Throwable) {
                    }
                }
            }

            if ($updated) {
                echo json_encode(['success' => true, 'lead_id' => $id, 'field' => $field, 'value' => $value, 'csrf_token' => $_SESSION['csrf_token'] ?? '']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Valeur invalide ou aucune modification.']);
            }
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    public function ajaxDetail(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID invalide.']);
            return;
        }

        try {
            $leadModel = new Lead();
            $lead = $leadModel->findById($id);
            if ($lead === null) {
                echo json_encode(['success' => false, 'error' => 'Lead introuvable.']);
                return;
            }

            $notes = [];
            $activities = [];
            try {
                $noteModel = new LeadNote();
                $notes = $noteModel->findByLeadId($id);
            } catch (\Throwable) {
            }

            try {
                $activityModel = new LeadActivity();
                $activities = $activityModel->findByLeadId($id, 20);
            } catch (\Throwable) {
            }

            $partenaire = null;
            if (!empty($lead['partenaire_id'])) {
                try {
                    $partenaireModel = new Partenaire();
                    $partenaire = $partenaireModel->findById((int) $lead['partenaire_id']);
                } catch (\Throwable) {
                }
            }

            echo json_encode([
                'success' => true,
                'lead' => $lead,
                'notes' => $notes,
                'activities' => $activities,
                'partenaire' => $partenaire,
                'csrf_token' => $_SESSION['csrf_token'] ?? '',
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    public function exportCsv(): void
    {
        AuthController::requireAuth();

        $this->ensureLeadTables();

        $search = isset($_GET['q']) ? trim((string) $_GET['q']) : null;
        $scoreFilter = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
        $typeFilter = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
        $statutFilter = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $villeFilter = isset($_GET['ville']) ? trim((string) $_GET['ville']) : null;
        $dateFrom = isset($_GET['date_from']) ? trim((string) $_GET['date_from']) : null;
        $dateTo = isset($_GET['date_to']) ? trim((string) $_GET['date_to']) : null;

        $leadModel = new Lead();
        $leads = $leadModel->exportLeads($search, $scoreFilter, $statutFilter, $typeFilter, $villeFilter, $dateFrom, $dateTo);

        $filename = 'leads_export_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['ID', 'Type', 'Nom', 'Email', 'Telephone', 'Adresse', 'Ville', 'Type Bien', 'Surface m2', 'Pieces', 'Estimation', 'Urgence', 'Motivation', 'Score', 'Statut', 'Date Creation'], ';');

        foreach ($leads as $lead) {
            fputcsv($output, [
                $lead['id'],
                $lead['lead_type'] ?? '',
                $lead['nom'] ?? '',
                $lead['email'] ?? '',
                $lead['telephone'] ?? '',
                $lead['adresse'] ?? '',
                $lead['ville'] ?? '',
                $lead['type_bien'] ?? '',
                $lead['surface_m2'] ?? '',
                $lead['pieces'] ?? '',
                $lead['estimation'] ?? '',
                $lead['urgence'] ?? '',
                $lead['motivation'] ?? '',
                $lead['score'] ?? '',
                $lead['statut'] ?? '',
                $lead['created_at'] ?? '',
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function bulkAction(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $action = trim((string) ($_POST['bulk_action'] ?? ''));
        $ids = isset($_POST['lead_ids']) ? array_map('intval', (array) $_POST['lead_ids']) : [];
        $ids = array_filter($ids, fn($id) => $id > 0);

        if (empty($ids) || $action === '') {
            header('Location: /admin/leads');
            exit;
        }

        $leadModel = new Lead();
        $count = 0;

        if ($action === 'delete') {
            $count = $leadModel->deleteBulk($ids);
            $_SESSION['leads_flash'] = ['type' => 'success', 'message' => $count . ' lead(s) supprimé(s).'];
        } elseif (str_starts_with($action, 'score_')) {
            $score = substr($action, 6);
            $count = $leadModel->bulkUpdateScore($ids, $score);
            $_SESSION['leads_flash'] = ['type' => 'success', 'message' => $count . ' lead(s) mis à jour (score: ' . $score . ').'];
        } elseif (str_starts_with($action, 'statut_')) {
            $statut = substr($action, 7);
            $count = $leadModel->bulkUpdateStatut($ids, $statut);
            $_SESSION['leads_flash'] = ['type' => 'success', 'message' => $count . ' lead(s) mis à jour (statut: ' . $statut . ').'];
        }

        header('Location: /admin/leads');
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
                'ville' => 'VARCHAR(120) NULL DEFAULT NULL',
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

    private function ensureLeadTables(): void
    {
        try {
            $pdo = Database::connection();

            if (!Database::tableExists('leads')) {
                return;
            }

            if (!Database::tableExists('lead_notes')) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS lead_notes (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    lead_id INT UNSIGNED NOT NULL,
                    content TEXT NOT NULL,
                    author VARCHAR(120) NOT NULL DEFAULT 'Admin',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_lead_id (lead_id),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            if (!Database::tableExists('lead_activities')) {
                $pdo->exec("CREATE TABLE IF NOT EXISTS lead_activities (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    lead_id INT UNSIGNED NOT NULL,
                    activity_type VARCHAR(50) NOT NULL,
                    description TEXT NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_lead_id (lead_id),
                    INDEX idx_activity_type (activity_type),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }
        } catch (\Throwable) {
        }
    }
}
