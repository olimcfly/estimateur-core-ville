<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Validator;
use App\Core\View;
use App\Models\DemandeFinancement;

final class AdminFinancementController
{
    private const STATUTS = [
        'nouvelle', 'contactee', 'en_cours', 'transmise_courtier', 'acceptee', 'refusee', 'annulee',
    ];

    private const STATUT_LABELS = [
        'nouvelle' => 'Nouvelle',
        'contactee' => 'Contactee',
        'en_cours' => 'En cours',
        'transmise_courtier' => 'Transmise courtier',
        'acceptee' => 'Acceptee',
        'refusee' => 'Refusee',
        'annulee' => 'Annulee',
    ];

    private const TYPE_PROJET_LABELS = [
        'achat_residence_principale' => 'Achat residence principale',
        'achat_secondaire' => 'Achat secondaire',
        'investissement_locatif' => 'Investissement locatif',
        'rachat_credit' => 'Rachat de credit',
        'renogociation' => 'Renegociation',
        'autre' => 'Autre',
    ];

    private const SITUATION_PRO_LABELS = [
        'salarie_cdi' => 'Salarie CDI',
        'salarie_cdd' => 'Salarie CDD',
        'fonctionnaire' => 'Fonctionnaire',
        'independant' => 'Independant',
        'profession_liberale' => 'Profession liberale',
        'retraite' => 'Retraite',
        'autre' => 'Autre',
    ];

    public function index(): void
    {
        AuthController::requireAuth();

        $model = new DemandeFinancement();

        $statut = isset($_GET['statut']) && in_array($_GET['statut'], self::STATUTS, true) ? $_GET['statut'] : null;

        $demandes = $model->findAllFiltered($statut);
        $stats = $model->getStats();
        $statutCounts = $model->countByStatut();

        $tableExists = Database::tableExists('demandes_financement');

        View::renderAdmin('admin/financement', [
            'page_title' => 'Demandes de Financement - Admin',
            'admin_page_title' => 'Demandes de Financement',
            'admin_page' => 'financement',
            'breadcrumb' => 'Demandes de Financement',
            'demandes' => $demandes,
            'stats' => $stats,
            'statutCounts' => $statutCounts,
            'statutLabels' => self::STATUT_LABELS,
            'typeProjetLabels' => self::TYPE_PROJET_LABELS,
            'situationProLabels' => self::SITUATION_PRO_LABELS,
            'filterStatut' => $statut,
            'tableExists' => $tableExists,
        ]);
    }

    public function edit(): void
    {
        AuthController::requireAuth();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $demande = null;

        if ($id > 0) {
            $model = new DemandeFinancement();
            $demande = $model->findById($id);
            if ($demande === null) {
                header('Location: /admin/financement');
                exit;
            }
        }

        View::renderAdmin('admin/financement-edit', [
            'page_title' => $demande ? 'Modifier Demande' : 'Nouvelle Demande',
            'admin_page' => 'financement',
            'breadcrumb' => $demande ? 'Modifier Demande' : 'Nouvelle Demande',
            'demande' => $demande,
            'statutLabels' => self::STATUT_LABELS,
            'typeProjetLabels' => self::TYPE_PROJET_LABELS,
            'situationProLabels' => self::SITUATION_PRO_LABELS,
            'errors' => [],
        ]);
    }

    public function save(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        try {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $nom = Validator::string($_POST, 'nom', 2, 180);
            $email = Validator::email($_POST, 'email');

            $data = [
                'nom' => $nom,
                'prenom' => trim((string) ($_POST['prenom'] ?? '')),
                'email' => $email,
                'telephone' => trim((string) ($_POST['telephone'] ?? '')),
                'situation_pro' => in_array($_POST['situation_pro'] ?? '', array_keys(self::SITUATION_PRO_LABELS), true)
                    ? $_POST['situation_pro'] : null,
                'revenus_mensuels' => !empty($_POST['revenus_mensuels']) ? (float) $_POST['revenus_mensuels'] : null,
                'co_emprunteur' => isset($_POST['co_emprunteur']) ? 1 : 0,
                'revenus_co_emprunteur' => !empty($_POST['revenus_co_emprunteur']) ? (float) $_POST['revenus_co_emprunteur'] : null,
                'type_projet' => in_array($_POST['type_projet'] ?? '', array_keys(self::TYPE_PROJET_LABELS), true)
                    ? $_POST['type_projet'] : 'achat_residence_principale',
                'montant_projet' => !empty($_POST['montant_projet']) ? (float) $_POST['montant_projet'] : null,
                'apport_personnel' => !empty($_POST['apport_personnel']) ? (float) $_POST['apport_personnel'] : null,
                'montant_pret_souhaite' => !empty($_POST['montant_pret_souhaite']) ? (float) $_POST['montant_pret_souhaite'] : null,
                'duree_souhaitee_mois' => !empty($_POST['duree_souhaitee_mois']) ? (int) $_POST['duree_souhaitee_mois'] : null,
                'type_bien' => trim((string) ($_POST['type_bien'] ?? '')),
                'ville' => trim((string) ($_POST['ville'] ?? '')) ?: 'Bordeaux',
                'quartier' => trim((string) ($_POST['quartier'] ?? '')),
                'statut' => in_array($_POST['statut'] ?? '', self::STATUTS, true)
                    ? $_POST['statut'] : 'nouvelle',
                'date_transmission' => !empty($_POST['date_transmission']) ? $_POST['date_transmission'] : null,
                'courtier_reference' => trim((string) ($_POST['courtier_reference'] ?? '')) ?: '2L Courtage',
                'notes_internes' => trim((string) ($_POST['notes_internes'] ?? '')),
                'notes_courtier' => trim((string) ($_POST['notes_courtier'] ?? '')),
            ];

            $model = new DemandeFinancement();

            if ($id > 0) {
                $model->update($id, $data);
            } else {
                $data['source'] = 'admin';
                $id = $model->create($data);
            }

            header('Location: /admin/financement');
            exit;
        } catch (\Throwable $e) {
            View::renderAdmin('admin/financement-edit', [
                'page_title' => 'Demande de Financement',
                'admin_page' => 'financement',
                'breadcrumb' => 'Demande de Financement',
                'demande' => $_POST,
                'statutLabels' => self::STATUT_LABELS,
                'typeProjetLabels' => self::TYPE_PROJET_LABELS,
                'situationProLabels' => self::SITUATION_PRO_LABELS,
                'errors' => [$e->getMessage()],
            ]);
        }
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $model = new DemandeFinancement();
            $model->delete($id);
        }

        header('Location: /admin/financement');
        exit;
    }

    public function transmettre(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $model = new DemandeFinancement();
            $model->markTransmise($id);
            $_SESSION['financement_flash'] = [
                'type' => 'success',
                'message' => 'Demande marquee comme transmise a 2L Courtage.',
            ];
        }

        header('Location: /admin/financement');
        exit;
    }

    public function exportCourtier(): void
    {
        AuthController::requireAuth();

        $model = new DemandeFinancement();
        $demandes = $model->findNonTransmises();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="demandes-financement-2l-courtage-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // BOM UTF-8

        fputcsv($output, [
            'ID', 'Date', 'Nom', 'Prenom', 'Email', 'Telephone',
            'Situation Pro', 'Revenus Mensuels', 'Co-emprunteur', 'Revenus Co-emprunteur',
            'Type Projet', 'Montant Projet', 'Apport', 'Pret Souhaite', 'Duree (mois)',
            'Type Bien', 'Ville', 'Quartier', 'Statut', 'Notes',
        ], ';');

        foreach ($demandes as $d) {
            fputcsv($output, [
                $d['id'],
                date('d/m/Y', strtotime((string) $d['created_at'])),
                $d['nom'],
                $d['prenom'] ?? '',
                $d['email'],
                $d['telephone'] ?? '',
                self::SITUATION_PRO_LABELS[$d['situation_pro'] ?? ''] ?? '',
                $d['revenus_mensuels'] ?? '',
                $d['co_emprunteur'] ? 'Oui' : 'Non',
                $d['revenus_co_emprunteur'] ?? '',
                self::TYPE_PROJET_LABELS[$d['type_projet'] ?? ''] ?? '',
                $d['montant_projet'] ?? '',
                $d['apport_personnel'] ?? '',
                $d['montant_pret_souhaite'] ?? '',
                $d['duree_souhaitee_mois'] ?? '',
                $d['type_bien'] ?? '',
                $d['ville'] ?? '',
                $d['quartier'] ?? '',
                self::STATUT_LABELS[$d['statut'] ?? ''] ?? '',
                $d['notes_internes'] ?? '',
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function createTable(): void
    {
        AuthController::requireAuth();
        AuthController::verifyCsrfToken();

        try {
            $pdo = Database::connection();
            $sql = file_get_contents(dirname(__DIR__, 2) . '/database/migration_financement.sql');
            if ($sql === false) {
                throw new \RuntimeException('Fichier de migration introuvable.');
            }

            $sql = preg_replace('/--.*$/m', '', $sql);
            $sql = trim($sql);

            if ($sql !== '') {
                $pdo->exec($sql);
            }

            $_SESSION['financement_flash'] = ['type' => 'success', 'message' => 'Table "demandes_financement" creee avec succes !'];
        } catch (\Throwable $e) {
            $_SESSION['financement_flash'] = ['type' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
        }

        header('Location: /admin/financement');
        exit;
    }
}
