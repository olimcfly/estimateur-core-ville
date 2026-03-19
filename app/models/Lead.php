<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Lead
{
    public function all(): array
    {
        $sql = 'SELECT id, nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at
                FROM leads
                ORDER BY created_at DESC, id DESC';

        $stmt = Database::connection()->query($sql);
        $rows = $stmt->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO leads (nom, email, telephone, ville, estimation, urgence, motivation, score, statut, created_at)
                VALUES (:nom, :email, :telephone, :ville, :estimation, :urgence, :motivation, :score, :statut, NOW())';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':ville' => $data['ville'],
            ':estimation' => $data['estimation'],
            ':urgence' => $data['urgence'],
            ':motivation' => $data['motivation'],
            ':score' => $data['score'],
            ':statut' => $data['statut'] ?? 'nouveau',
        ]);

        return (int) Database::connection()->lastInsertId();
    }
}
