<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class DemandeFinancement
{
    public function findAll(): array
    {
        $sql = 'SELECT * FROM demandes_financement
                WHERE website_id = :website_id
                ORDER BY created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findAllFiltered(?string $statut = null): array
    {
        $conditions = ['website_id = :website_id'];
        $params = [':website_id' => $this->websiteId()];

        if ($statut !== null && $statut !== '') {
            $conditions[] = 'statut = :statut';
            $params[':statut'] = $statut;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM demandes_financement
                WHERE {$where}
                ORDER BY created_at DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM demandes_financement
                WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO demandes_financement (
                    website_id, lead_id, nom, prenom, email, telephone,
                    situation_pro, revenus_mensuels, co_emprunteur, revenus_co_emprunteur,
                    type_projet, montant_projet, apport_personnel, montant_pret_souhaite, duree_souhaitee_mois,
                    type_bien, ville, quartier,
                    statut, courtier_reference, notes_internes,
                    source, utm_source, utm_medium, utm_campaign, ip_address,
                    created_at
                ) VALUES (
                    :website_id, :lead_id, :nom, :prenom, :email, :telephone,
                    :situation_pro, :revenus_mensuels, :co_emprunteur, :revenus_co_emprunteur,
                    :type_projet, :montant_projet, :apport_personnel, :montant_pret_souhaite, :duree_souhaitee_mois,
                    :type_bien, :ville, :quartier,
                    :statut, :courtier_reference, :notes_internes,
                    :source, :utm_source, :utm_medium, :utm_campaign, :ip_address,
                    NOW()
                )';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':lead_id' => $data['lead_id'] ?? null,
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? null,
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? null,
            ':situation_pro' => $data['situation_pro'] ?? null,
            ':revenus_mensuels' => $data['revenus_mensuels'] ?? null,
            ':co_emprunteur' => (int) ($data['co_emprunteur'] ?? 0),
            ':revenus_co_emprunteur' => $data['revenus_co_emprunteur'] ?? null,
            ':type_projet' => $data['type_projet'] ?? 'achat_residence_principale',
            ':montant_projet' => $data['montant_projet'] ?? null,
            ':apport_personnel' => $data['apport_personnel'] ?? null,
            ':montant_pret_souhaite' => $data['montant_pret_souhaite'] ?? null,
            ':duree_souhaitee_mois' => $data['duree_souhaitee_mois'] ?? null,
            ':type_bien' => $data['type_bien'] ?? null,
            ':ville' => $data['ville'] ?? 'Bordeaux',
            ':quartier' => $data['quartier'] ?? null,
            ':statut' => $data['statut'] ?? 'nouvelle',
            ':courtier_reference' => $data['courtier_reference'] ?? '2L Courtage',
            ':notes_internes' => $data['notes_internes'] ?? null,
            ':source' => $data['source'] ?? 'site_web',
            ':utm_source' => $data['utm_source'] ?? null,
            ':utm_medium' => $data['utm_medium'] ?? null,
            ':utm_campaign' => $data['utm_campaign'] ?? null,
            ':ip_address' => $data['ip_address'] ?? null,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE demandes_financement SET
                    nom = :nom, prenom = :prenom, email = :email, telephone = :telephone,
                    situation_pro = :situation_pro, revenus_mensuels = :revenus_mensuels,
                    co_emprunteur = :co_emprunteur, revenus_co_emprunteur = :revenus_co_emprunteur,
                    type_projet = :type_projet, montant_projet = :montant_projet,
                    apport_personnel = :apport_personnel, montant_pret_souhaite = :montant_pret_souhaite,
                    duree_souhaitee_mois = :duree_souhaitee_mois,
                    type_bien = :type_bien, ville = :ville, quartier = :quartier,
                    statut = :statut, date_transmission = :date_transmission,
                    courtier_reference = :courtier_reference,
                    notes_internes = :notes_internes, notes_courtier = :notes_courtier
                WHERE id = :id AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'] ?? null,
            ':email' => $data['email'],
            ':telephone' => $data['telephone'] ?? null,
            ':situation_pro' => $data['situation_pro'] ?? null,
            ':revenus_mensuels' => $data['revenus_mensuels'] ?? null,
            ':co_emprunteur' => (int) ($data['co_emprunteur'] ?? 0),
            ':revenus_co_emprunteur' => $data['revenus_co_emprunteur'] ?? null,
            ':type_projet' => $data['type_projet'] ?? 'achat_residence_principale',
            ':montant_projet' => $data['montant_projet'] ?? null,
            ':apport_personnel' => $data['apport_personnel'] ?? null,
            ':montant_pret_souhaite' => $data['montant_pret_souhaite'] ?? null,
            ':duree_souhaitee_mois' => $data['duree_souhaitee_mois'] ?? null,
            ':type_bien' => $data['type_bien'] ?? null,
            ':ville' => $data['ville'] ?? 'Bordeaux',
            ':quartier' => $data['quartier'] ?? null,
            ':statut' => $data['statut'] ?? 'nouvelle',
            ':date_transmission' => $data['date_transmission'] ?? null,
            ':courtier_reference' => $data['courtier_reference'] ?? '2L Courtage',
            ':notes_internes' => $data['notes_internes'] ?? null,
            ':notes_courtier' => $data['notes_courtier'] ?? null,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM demandes_financement WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        return $stmt->rowCount() > 0;
    }

    public function markTransmise(int $id): bool
    {
        $sql = 'UPDATE demandes_financement
                SET statut = :statut, date_transmission = :date_transmission
                WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
            ':statut' => 'transmise_courtier',
            ':date_transmission' => date('Y-m-d'),
        ]);
        return $stmt->rowCount() > 0;
    }

    public function getStats(): array
    {
        $sql = 'SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = "nouvelle" THEN 1 ELSE 0 END) as nouvelles,
                    SUM(CASE WHEN statut = "transmise_courtier" THEN 1 ELSE 0 END) as transmises,
                    SUM(CASE WHEN statut = "acceptee" THEN 1 ELSE 0 END) as acceptees,
                    SUM(CASE WHEN statut = "en_cours" THEN 1 ELSE 0 END) as en_cours,
                    COALESCE(SUM(montant_pret_souhaite), 0) as volume_total
                FROM demandes_financement WHERE website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'nouvelles' => 0, 'transmises' => 0,
            'acceptees' => 0, 'en_cours' => 0, 'volume_total' => 0,
        ];
    }

    public function countByStatut(): array
    {
        $sql = 'SELECT statut, COUNT(*) as cnt FROM demandes_financement WHERE website_id = :website_id GROUP BY statut';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * Recuperer les demandes non transmises pour export courtier.
     */
    public function findNonTransmises(): array
    {
        $sql = 'SELECT * FROM demandes_financement
                WHERE website_id = :website_id AND statut IN ("nouvelle", "contactee", "en_cours")
                ORDER BY created_at ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
