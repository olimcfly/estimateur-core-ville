<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class Lead
{
    public function listByScore(?string $score = null): array
    {
        $allowedScores = ['chaud', 'tiede', 'froid'];
        $isFiltered = $score !== null && in_array($score, $allowedScores, true);

        if ($isFiltered) {
            $stmt = Database::connection()->prepare(
                'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
                 FROM leads
                 WHERE website_id = :website_id
                   AND score = :score
                 ORDER BY created_at DESC'
            );
            $stmt->execute([
                ':website_id' => $this->websiteId(),
                ':score' => $score,
            ]);

            return $stmt->fetchAll() ?: [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
             FROM leads
             WHERE website_id = :website_id
             ORDER BY created_at DESC'
        );
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll() ?: [];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO leads (website_id, lead_type, nom, email, telephone, adresse, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, notes, score, statut, created_at)
                VALUES (:website_id, :lead_type, :nom, :email, :telephone, :adresse, :ville, :type_bien, :surface_m2, :pieces, :estimation, :urgence, :motivation, :notes, :score, :statut, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':lead_type' => $data['lead_type'] ?? 'qualifie',
            ':nom' => $data['nom'] ?? null,
            ':email' => $data['email'] ?? null,
            ':telephone' => $data['telephone'] ?? null,
            ':adresse' => $data['adresse'] ?? null,
            ':ville' => $data['ville'],
            ':type_bien' => $data['type_bien'] ?? null,
            ':surface_m2' => $data['surface_m2'] ?? null,
            ':pieces' => $data['pieces'] ?? null,
            ':estimation' => $data['estimation'],
            ':urgence' => $data['urgence'] ?? null,
            ':motivation' => $data['motivation'] ?? null,
            ':notes' => !empty($data['notes']) ? $data['notes'] : null,
            ':score' => $data['score'],
            ':statut' => $data['statut'] ?? 'nouveau',
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllLeads(): array
    {
        $sql = 'SELECT id, lead_type, nom, email, telephone, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                WHERE website_id = :website_id
                ORDER BY created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateStatut(int $id, string $statut): bool
    {
        $allowed = [
            'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
            'mandat_simple', 'mandat_exclusif', 'compromis_vente',
            'signe', 'co_signature_partenaire', 'assigne_autre',
        ];
        if (!in_array($statut, $allowed, true)) {
            return false;
        }

        $sql = 'UPDATE leads
                SET statut = :statut
                WHERE id = :id
                  AND website_id = :website_id';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':website_id', $this->websiteId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function updateLeadDetails(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id, ':website_id' => $this->websiteId()];

        $updatable = ['statut', 'partenaire_id', 'commission_taux', 'commission_montant',
                      'assigne_a', 'date_mandat', 'date_compromis', 'date_signature', 'prix_vente', 'score',
                      'neuropersona', 'niveau_conscience'];

        foreach ($updatable as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE leads SET ' . implode(', ', $fields) . ' WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM leads WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':id' => $id, ':website_id' => $this->websiteId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array<string, int>
     */
    public function countByStatut(): array
    {
        $sql = 'SELECT statut, COUNT(*) as cnt
                FROM leads
                WHERE website_id = :website_id AND lead_type = :lt
                GROUP BY statut';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId(), ':lt' => 'qualifie']);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByStatut(string $statut): array
    {
        $sql = 'SELECT id, lead_type, nom, email, telephone, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                WHERE website_id = :website_id AND statut = :statut
                ORDER BY
                  CASE score WHEN "chaud" THEN 1 WHEN "tiede" THEN 2 ELSE 3 END,
                  created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId(), ':statut' => $statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllLeadsFiltered(?string $score = null, ?string $statut = null, ?string $type = null): array
    {
        $conditions = ['website_id = :website_id'];
        $params = [':website_id' => $this->websiteId()];

        if ($score !== null && in_array($score, ['chaud', 'tiede', 'froid'], true)) {
            $conditions[] = 'score = :score';
            $params[':score'] = $score;
        }
        if ($statut !== null && $statut !== '') {
            $conditions[] = 'statut = :statut';
            $params[':statut'] = $statut;
        }
        if ($type !== null && in_array($type, ['tendance', 'qualifie'], true)) {
            $conditions[] = 'lead_type = :lead_type';
            $params[':lead_type'] = $type;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT id, lead_type, nom, email, telephone, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                WHERE {$where}
                ORDER BY created_at DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateScore(int $id, string $score): bool
    {
        $allowed = ['chaud', 'tiede', 'froid'];
        if (!in_array($score, $allowed, true)) {
            return false;
        }

        $sql = 'UPDATE leads SET score = :score WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':score', $score, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':website_id', $this->websiteId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function deleteById(int $id): bool
    {
        $sql = 'DELETE FROM leads WHERE id = :id AND website_id = :website_id';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':website_id', $this->websiteId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Advanced search/filter with pagination support.
     *
     * @return array{leads: array, total: int}
     */
    public function searchLeads(
        ?string $search = null,
        ?string $score = null,
        ?string $statut = null,
        ?string $type = null,
        ?string $ville = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        string $sortBy = 'created_at',
        string $sortDir = 'DESC',
        int $page = 1,
        int $perPage = 25
    ): array {
        $conditions = ['website_id = :website_id'];
        $params = [':website_id' => $this->websiteId()];

        if ($search !== null && $search !== '') {
            $conditions[] = '(nom LIKE :search OR email LIKE :search OR telephone LIKE :search OR ville LIKE :search OR adresse LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        if ($score !== null && in_array($score, ['chaud', 'tiede', 'froid'], true)) {
            $conditions[] = 'score = :score';
            $params[':score'] = $score;
        }
        if ($statut !== null && $statut !== '') {
            $conditions[] = 'statut = :statut';
            $params[':statut'] = $statut;
        }
        if ($type !== null && in_array($type, ['tendance', 'qualifie'], true)) {
            $conditions[] = 'lead_type = :lead_type';
            $params[':lead_type'] = $type;
        }
        if ($ville !== null && $ville !== '') {
            $conditions[] = 'ville = :ville';
            $params[':ville'] = $ville;
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $conditions[] = 'DATE(created_at) >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== null && $dateTo !== '') {
            $conditions[] = 'DATE(created_at) <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        $where = implode(' AND ', $conditions);

        $allowedSort = ['id', 'nom', 'email', 'ville', 'estimation', 'score', 'statut', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        $sortDir = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

        // Count total
        $countSql = "SELECT COUNT(*) FROM leads WHERE {$where}";
        $countStmt = Database::connection()->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch page
        $offset = max(0, ($page - 1) * $perPage);
        $sql = "SELECT id, lead_type, nom, email, telephone, adresse, ville, type_bien, surface_m2, pieces, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                WHERE {$where}
                ORDER BY {$sortBy} {$sortDir}
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return ['leads' => $leads, 'total' => $total];
    }

    /**
     * Get distinct cities for filter dropdown.
     * @return string[]
     */
    public function getDistinctVilles(): array
    {
        $sql = 'SELECT DISTINCT ville FROM leads WHERE website_id = :website_id AND ville IS NOT NULL AND ville != \'\' ORDER BY ville';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([':website_id' => $this->websiteId()]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * Bulk delete multiple leads.
     */
    public function deleteBulk(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM leads WHERE id IN ({$placeholders}) AND website_id = ?";
        $stmt = Database::connection()->prepare($sql);
        $params = array_map('intval', $ids);
        $params[] = $this->websiteId();
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Bulk update score for multiple leads.
     */
    public function bulkUpdateScore(array $ids, string $score): int
    {
        if (empty($ids) || !in_array($score, ['chaud', 'tiede', 'froid'], true)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE leads SET score = ? WHERE id IN ({$placeholders}) AND website_id = ?";
        $stmt = Database::connection()->prepare($sql);
        $params = [$score];
        foreach ($ids as $id) {
            $params[] = (int) $id;
        }
        $params[] = $this->websiteId();
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Bulk update statut for multiple leads.
     */
    public function bulkUpdateStatut(array $ids, string $statut): int
    {
        $allowed = [
            'nouveau', 'contacte', 'rdv_pris', 'visite_realisee',
            'mandat_simple', 'mandat_exclusif', 'compromis_vente',
            'signe', 'co_signature_partenaire', 'assigne_autre',
        ];
        if (empty($ids) || !in_array($statut, $allowed, true)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE leads SET statut = ? WHERE id IN ({$placeholders}) AND website_id = ?";
        $stmt = Database::connection()->prepare($sql);
        $params = [$statut];
        foreach ($ids as $id) {
            $params[] = (int) $id;
        }
        $params[] = $this->websiteId();
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get all leads matching filters (no pagination) for CSV export.
     * @return array<int, array<string, mixed>>
     */
    public function exportLeads(
        ?string $search = null,
        ?string $score = null,
        ?string $statut = null,
        ?string $type = null,
        ?string $ville = null,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): array {
        $conditions = ['website_id = :website_id'];
        $params = [':website_id' => $this->websiteId()];

        if ($search !== null && $search !== '') {
            $conditions[] = '(nom LIKE :search OR email LIKE :search OR telephone LIKE :search OR ville LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        if ($score !== null && in_array($score, ['chaud', 'tiede', 'froid'], true)) {
            $conditions[] = 'score = :score';
            $params[':score'] = $score;
        }
        if ($statut !== null && $statut !== '') {
            $conditions[] = 'statut = :statut';
            $params[':statut'] = $statut;
        }
        if ($type !== null && in_array($type, ['tendance', 'qualifie'], true)) {
            $conditions[] = 'lead_type = :lead_type';
            $params[':lead_type'] = $type;
        }
        if ($ville !== null && $ville !== '') {
            $conditions[] = 'ville = :ville';
            $params[':ville'] = $ville;
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $conditions[] = 'DATE(created_at) >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== null && $dateTo !== '') {
            $conditions[] = 'DATE(created_at) <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM leads WHERE {$where} ORDER BY created_at DESC";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
