<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class Estimation
{
    public function create(array $data): int
    {
        $sql = 'INSERT INTO estimations (website_id, ville, type_bien, surface_m2, pieces, per_sqm_low, per_sqm_mid, per_sqm_high, estimated_low, estimated_mid, estimated_high, created_at)
                VALUES (:website_id, :ville, :type_bien, :surface_m2, :pieces, :per_sqm_low, :per_sqm_mid, :per_sqm_high, :estimated_low, :estimated_mid, :estimated_high, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':website_id' => $this->websiteId(),
            ':ville' => $data['ville'],
            ':type_bien' => $data['type_bien'],
            ':surface_m2' => $data['surface_m2'],
            ':pieces' => $data['pieces'],
            ':per_sqm_low' => $data['per_sqm_low'],
            ':per_sqm_mid' => $data['per_sqm_mid'],
            ':per_sqm_high' => $data['per_sqm_high'],
            ':estimated_low' => $data['estimated_low'],
            ':estimated_mid' => $data['estimated_mid'],
            ':estimated_high' => $data['estimated_high'],
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, website_id, ville, estimated_mid
             FROM estimations
             WHERE id = :id AND website_id = :website_id
             LIMIT 1'
        );
        $stmt->execute([
            ':id' => $id,
            ':website_id' => $this->websiteId(),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    private function websiteId(): int
    {
        return (int) Config::get('website.id', 1);
    }
}
