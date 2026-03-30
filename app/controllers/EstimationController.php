<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Core\Config;
use App\Core\Validator;
use App\Core\View;
use App\Models\DesignTemplate;
use App\Models\Estimation;
use App\Models\Lead;
use App\Services\EstimationService;
use App\Services\LeadNotificationService;
use App\Services\LeadScoringService;
use App\Services\PerplexityService;

final class EstimationController
{
    private const LEAD_FORM_TTL_SECONDS = 1800;
    private const LEAD_SUBMIT_COOLDOWN_SECONDS = 60;
    private const ALLOWED_URGENCIES = ['rapide', 'moyen', 'long'];
    private const ALLOWED_MOTIVATIONS = ['vente', 'succession', 'divorce', 'investissement', 'autre'];

    private EstimationService $estimationService;

    public function __construct(?EstimationService $estimationService = null)
    {
        $this->estimationService = $estimationService ?? new EstimationService(new PerplexityService());
    }

    public function index(): void
    {
        $city = (string) (Config::get('city.name', '') ?: site('city', 'votre ville'));
        View::render('estimation/index', [
            'errors' => [],
            'estimationContext' => getEstimationContext(),
            'page_title' => "Estimation immobilière à {$city} | Propriétaires vendeurs",
            'meta_description' => "Obtenez votre estimation immobilière à {$city} en 60 secondes : formulaire simple, sans engagement, orienté vendeurs.",
        ]);
    }

    public function leads(): void
    {
        AuthController::requireAuth();

        $leads = [];
        $dbError = null;
        $statutCounts = [];

        $filterScore = isset($_GET['score']) ? trim((string) $_GET['score']) : null;
        $filterStatut = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $filterType = isset($_GET['type']) ? trim((string) $_GET['type']) : null;

        try {
            $leadModel = new Lead();
            $hasFilters = ($filterScore !== null && $filterScore !== '')
                       || ($filterStatut !== null && $filterStatut !== '')
                       || ($filterType !== null && $filterType !== '');

            if ($hasFilters) {
                $leads = $leadModel->findAllLeadsFiltered(
                    $filterScore ?: null,
                    $filterStatut ?: null,
                    $filterType ?: null
                );
            } else {
                $leads = $leadModel->findAllLeads();
            }
            $statutCounts = $leadModel->countByStatut();
        } catch (\Throwable $e) {
            $dbError = 'Base de données indisponible : les leads ne peuvent pas être chargés.';
        }

        View::renderAdmin('admin/leads', [
            'page_title' => 'Gestion des Leads - Admin',
            'admin_page_title' => 'Leads',
            'admin_current_page' => 'leads',
            'leads' => $leads,
            'leadCount' => count($leads),
            'dbError' => $dbError,
            'statutCounts' => $statutCounts,
            'filterScore' => $filterScore,
            'filterStatut' => $filterStatut,
            'filterType' => $filterType,
        ]);
    }

    public function estimate(): void
    {
        try {
            $city = Validator::string($_POST, 'ville', 2, 120);
            $typeKey = array_key_exists('type', $_POST) ? 'type' : 'type_bien';
            $propertyType = Validator::string($_POST, $typeKey, 2, 80);
            $surface = Validator::float($_POST, 'surface', 5, 10000);

            $roomsRaw = trim((string) ($_POST['pieces'] ?? ''));
            $rooms = $roomsRaw !== '' ? Validator::int($_POST, 'pieces', 1, 50) : 3;

            $estimate = $this->estimationService->estimate($city, $propertyType, $surface, $rooms);
            $now = time();
            $estimationId = 0;
            $_SESSION['last_estimation_result'] = $estimate;
            $_SESSION['lead_form_context'] = [
                'ip' => $this->getClientIp(),
                'issued_at' => $now,
                'expires_at' => $now + self::LEAD_FORM_TTL_SECONDS,
            ];

            try {
                $estimationId = (new Estimation())->create([
                    'ville' => $city,
                    'type_bien' => $propertyType,
                    'surface_m2' => $surface,
                    'pieces' => $rooms,
                    'per_sqm_low' => $estimate['per_sqm_low'],
                    'per_sqm_mid' => $estimate['per_sqm_mid'],
                    'per_sqm_high' => $estimate['per_sqm_high'],
                    'estimated_low' => $estimate['estimated_low'],
                    'estimated_mid' => $estimate['estimated_mid'],
                    'estimated_high' => $estimate['estimated_high'],
                ]);
            } catch (\Throwable $e) {
                error_log('Estimation save failed: ' . $e->getMessage());
            }

            // Capture lead "tendance" (sans coordonnées)
            try {
                $leadModel = new Lead();
                $leadModel->create([
                    'lead_type' => 'tendance',
                    'ville' => $city,
                    'type_bien' => $propertyType,
                    'surface_m2' => $surface,
                    'pieces' => $rooms,
                    'estimation' => $estimate['estimated_mid'],
                    'score' => 'froid',
                    'statut' => 'nouveau',
                ]);
            } catch (\Throwable $e) {
                // Silently fail — don't block the estimation result
                error_log('Tendance lead capture failed: ' . $e->getMessage());
            }

            View::render('estimation/result', [
                'estimate' => $estimate,
                'estimationId' => $estimationId,
                'errors' => [],
            ]);
        } catch (\Throwable $throwable) {
            View::render('estimation/index', [
                'errors' => [$throwable->getMessage()],
            ]);
        }
    }

    public function apiEstimate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = $this->readApiInput();
            $sanitized = $this->sanitizeEstimationPayload($input);

            $cityKey = $sanitized['ville'] !== '' ? 'ville' : 'localisation';
            $typeKey = $sanitized['type'] !== '' ? 'type' : 'type_bien';

            $city = Validator::string($sanitized, $cityKey, 2, 120);
            $propertyType = Validator::string($sanitized, $typeKey, 2, 80);
            $surface = Validator::float($sanitized, 'surface', 5, 10000);
            $rooms = Validator::int($sanitized, 'pieces', 1, 50);

            $estimate = $this->estimationService->estimate($city, $propertyType, $surface, $rooms);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $estimate,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $throwable->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }
    }

    public function storeLead(): void
    {
        try {
            $this->assertLeadRequestAllowed();

            $nom = Validator::string($_POST, 'nom', 2, 120);
            $email = Validator::email($_POST, 'email');
            $telephone = Validator::string($_POST, 'telephone', 6, 30);
            $adresseInput = trim((string) ($_POST['adresse'] ?? ''));
            $adresse = $adresseInput !== '' ? Validator::string($_POST, 'adresse', 5, 255) : 'Non renseignée';
            $ville = Validator::string($_POST, 'ville', 2, 120);
            $estimation = Validator::float($_POST, 'estimation', 10000, 100000000);
            $estimationId = isset($_POST['estimation_id']) ? (int) $_POST['estimation_id'] : 0;
            $urgence = Validator::string($_POST, 'urgence', 3, 40);
            $motivation = Validator::string($_POST, 'motivation', 3, 80);
            $this->assertAllowedValue($urgence, self::ALLOWED_URGENCIES, 'urgence');
            $this->assertAllowedValue($motivation, self::ALLOWED_MOTIVATIONS, 'motivation');
            $notesRaw = trim((string) ($_POST['notes'] ?? ($_POST['message'] ?? '')));
            $contactPrefere = trim((string) ($_POST['contact_prefere'] ?? ''));
            $layout = trim((string) ($_POST['layout'] ?? ''));
            $notes = $notesRaw;
            if ($contactPrefere !== '') {
                $notes = $notes !== '' ? "Contact préféré: {$contactPrefere}\n{$notes}" : "Contact préféré: {$contactPrefere}";
            }
            if ($layout !== '') {
                $template = (new DesignTemplate())->findBySlug($layout);
                if ($template === null) {
                    throw new \InvalidArgumentException("Template layout inconnu: {$layout}");
                }

                $layoutNote = 'Template layout: ' . (string) $template['slug'];
                $notes = $notes !== '' ? "{$layoutNote}\n{$notes}" : $layoutNote;
            }
            if (mb_strlen($notes) > 1500) {
                throw new \InvalidArgumentException('Les notes ne doivent pas dépasser 1500 caractères.');
            }

            $scoring = new LeadScoringService();
            $temperature = $scoring->score($estimation, $urgence, $motivation);

            $leadModel = new Lead();
            $leadId = $leadModel->create([
                'lead_type' => 'qualifie',
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'estimation' => $estimation,
                'urgence' => $urgence,
                'motivation' => $motivation,
                'notes' => $notes,
                'score' => $temperature,
                'statut' => 'nouveau',
            ]);

            $_SESSION['lead_last_submit'] = [
                'ip' => $this->getClientIp(),
                'submitted_at' => time(),
            ];

            LeadNotificationService::notify($leadId, $temperature, [
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'ville' => $ville,
                'estimation' => $estimation,
                'urgence' => $urgence,
                'motivation' => $motivation,
                'notes' => $notes,
                'statut' => 'nouveau',
            ]);

            $_SESSION['lead_confirmation'] = [
                'leadId' => $leadId,
                'temperature' => $temperature,
                'estimationId' => $estimationId,
                'lead' => [
                    'nom' => $nom,
                    'email' => $email,
                    'telephone' => $telephone,
                    'adresse' => $adresse,
                    'ville' => $ville,
                    'estimation' => $estimation,
                    'urgence' => $urgence,
                    'motivation' => $motivation,
                    'notes' => $notes,
                    'statut' => 'nouveau',
                ],
            ];

            header('Location: /estimation/confirmation');
            exit;
        } catch (\Throwable $throwable) {
            $this->renderLeadStepWithErrors([$throwable->getMessage()]);
        }
    }

    public function apiStorePopupLead(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $rawBody = (string) file_get_contents('php://input');
            $input = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($input)) {
                throw new \InvalidArgumentException('Payload invalide.');
            }

            $prenom = trim((string) ($input['prenom'] ?? ''));
            $email = trim((string) ($input['email'] ?? ''));
            $telephone = preg_replace('/\s+/', '', trim((string) ($input['telephone'] ?? ''))) ?? '';
            $ville = trim((string) ($input['ville'] ?? ''));
            $typeBien = trim((string) ($input['type_bien'] ?? ''));
            $surface = (float) ($input['surface'] ?? 0);
            $pieces = (int) ($input['pieces'] ?? 0);
            $estimationMoyenne = (float) ($input['estimation_moyenne'] ?? 0);
            $source = trim((string) ($input['source'] ?? 'estimation_popup'));

            if ($prenom === '' || mb_strlen($prenom) < 2) {
                throw new \InvalidArgumentException('Le prénom est requis.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Email invalide.');
            }
            if (!preg_match('/^(0|\+33)[1-9][0-9]{8}$/', $telephone)) {
                throw new \InvalidArgumentException('Téléphone invalide.');
            }
            if ($ville === '' || mb_strlen($ville) < 2) {
                throw new \InvalidArgumentException('Ville invalide.');
            }
            if ($estimationMoyenne <= 0) {
                throw new \InvalidArgumentException('Estimation invalide.');
            }

            $leadId = (new Lead())->create([
                'lead_type' => 'qualifie',
                'nom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'ville' => $ville,
                'type_bien' => $typeBien !== '' ? mb_substr($typeBien, 0, 80) : null,
                'surface_m2' => $surface > 0 ? $surface : null,
                'pieces' => $pieces > 0 ? $pieces : null,
                'estimation' => $estimationMoyenne,
                'notes' => 'Source: ' . mb_substr($source, 0, 80),
                'score' => 'tiede',
                'statut' => 'nouveau',
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'lead_id' => $leadId,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $throwable->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public function confirmation(): void
    {
        $confirmation = $_SESSION['lead_confirmation'] ?? null;

        if (!is_array($confirmation)) {
            header('Location: /estimation');
            exit;
        }

        unset($_SESSION['lead_confirmation']);

        View::render('estimation/lead_saved', [
            'leadId' => $confirmation['leadId'] ?? 0,
            'temperature' => $confirmation['temperature'] ?? 'froid',
            'lead' => $confirmation['lead'] ?? [],
            'estimationId' => $confirmation['estimationId'] ?? 0,
        ]);
    }


    public function updateLeadStatut(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $statut = trim((string) ($_POST['statut'] ?? ''));

        if ($id > 0 && $statut !== '') {
            $leadModel = new Lead();
            $leadModel->updateStatut($id, $statut);
        }

        header('Location: /admin/leads');
        exit;
    }

    public function updateLeadInline(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $field = trim((string) ($_POST['field'] ?? ''));
        $value = trim((string) ($_POST['value'] ?? ''));

        if ($id <= 0 || $field === '' || $value === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Paramètres manquants'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $leadModel = new Lead();
        $ok = false;

        if ($field === 'statut') {
            $ok = $leadModel->updateStatut($id, $value);
        } elseif ($field === 'score') {
            $ok = $leadModel->updateScore($id, $value);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Champ non supporté'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
    }

    public function pipeline(): void
    {
        AuthController::requireAuth();

        $leadModel = new Lead();

        try {
            $statutCounts = $leadModel->countByStatut();
            $totalLeads = array_sum($statutCounts);

            $allStatuts = [
                'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
                'mandat_simple', 'mandat_exclusif', 'compromis_vente',
                'signe', 'co_signature_partenaire', 'assigne_autre',
            ];

            $leadsByStatut = [];
            foreach ($allStatuts as $s) {
                $leadsByStatut[$s] = $leadModel->findByStatut($s);
            }

            $pdo = \App\Core\Database::connection();
            $websiteId = (int) \App\Core\Config::get('website.id', 1);
            $stmt = $pdo->prepare('SELECT score, COUNT(*) as cnt FROM leads WHERE website_id = :wid AND lead_type = :lt GROUP BY score');
            $stmt->execute([':wid' => $websiteId, ':lt' => 'qualifie']);
            $scoreData = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
        } catch (\Throwable $e) {
            $statutCounts = [];
            $leadsByStatut = [];
            $scoreData = [];
            $totalLeads = 0;
        }

        View::renderAdmin('admin/pipeline', [
            'page_title' => 'Pipeline - Admin CRM',
            'admin_page' => 'pipeline',
            'breadcrumb' => 'Pipeline',
            'statutCounts' => $statutCounts,
            'leadsByStatut' => $leadsByStatut,
            'scoreData' => $scoreData,
            'totalLeads' => $totalLeads,
        ]);
    }

    private function assertLeadRequestAllowed(): void
    {
        $context = $_SESSION['lead_form_context'] ?? null;

        if (!is_array($context)) {
            throw new \RuntimeException('Session expirée. Merci de relancer une estimation avant de soumettre vos coordonnées.');
        }

        $ip = $this->getClientIp();
        $issuedAt = (int) ($context['issued_at'] ?? 0);
        $expiresAt = (int) ($context['expires_at'] ?? 0);

        $strictIp = (bool) Config::get('lead.strict_ip', true);
        if ($strictIp && ($context['ip'] ?? '') !== $ip) {
            unset($_SESSION['lead_form_context']);
            throw new \RuntimeException('Vérification de sécurité invalide. Merci de refaire une estimation.');
        }

        $now = time();
        if ($issuedAt <= 0 || $now > $expiresAt) {
            unset($_SESSION['lead_form_context']);
            throw new \RuntimeException('Le formulaire a expiré. Merci de relancer une estimation.');
        }

        $lastSubmit = $_SESSION['lead_last_submit'] ?? null;
        if (is_array($lastSubmit) && ($lastSubmit['ip'] ?? '') === $ip) {
            $lastSubmittedAt = (int) ($lastSubmit['submitted_at'] ?? 0);
            $secondsSinceLastSubmit = $now - $lastSubmittedAt;

            if ($lastSubmittedAt > 0 && $secondsSinceLastSubmit < self::LEAD_SUBMIT_COOLDOWN_SECONDS) {
                throw new \RuntimeException('Merci de patienter une minute avant d\'envoyer une nouvelle demande.');
            }
        }
    }

    private function getClientIp(): string
    {
        $forwardedFor = trim((string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($forwardedFor !== '') {
            $forwardedIp = trim(explode(',', $forwardedFor)[0]);
            if ($forwardedIp !== '') {
                return $forwardedIp;
            }
        }

        $realIp = trim((string) ($_SERVER['HTTP_X_REAL_IP'] ?? ''));
        if ($realIp !== '') {
            return $realIp;
        }

        return trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    private function assertAllowedValue(string $value, array $allowed, string $field): void
    {
        if (!in_array(mb_strtolower(trim($value)), $allowed, true)) {
            throw new \InvalidArgumentException("Champ invalide: {$field}");
        }
    }

    private function renderLeadStepWithErrors(array $errors): void
    {
        $estimate = $_SESSION['last_estimation_result'] ?? null;
        if (!is_array($estimate)) {
            View::render('estimation/index', ['errors' => $errors]);
            return;
        }

        $estimationId = isset($_POST['estimation_id']) ? (int) $_POST['estimation_id'] : 0;
        $leadOld = [
            'nom' => trim((string) ($_POST['nom'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'telephone' => trim((string) ($_POST['telephone'] ?? '')),
            'urgence' => trim((string) ($_POST['urgence'] ?? '')),
            'motivation' => trim((string) ($_POST['motivation'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? ($_POST['message'] ?? ''))),
        ];

        View::render('estimation/result', [
            'estimate' => $estimate,
            'estimationId' => $estimationId,
            'leadErrors' => $errors,
            'leadOld' => $leadOld,
            'errors' => [],
        ]);
    }
}
