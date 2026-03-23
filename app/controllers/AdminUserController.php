<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\AdminModule;
use App\Models\AdminUser;

final class AdminUserController
{
    public function index(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        AdminUser::createTable(); // Ensure role column exists

        $users = AdminUser::findAll();

        View::renderAdmin('admin/users', [
            'page_title' => 'Gestion des Utilisateurs',
            'admin_page' => 'users',
            'admin_page_title' => 'Utilisateurs',
            'breadcrumb' => 'Utilisateurs',
            'users' => $users,
        ]);
    }

    public function create(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $name = trim((string) ($_POST['name'] ?? ''));
        $role = trim((string) ($_POST['role'] ?? 'admin'));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Email invalide.']);
            return;
        }

        if ($name === '') {
            $name = explode('@', $email)[0];
        }

        if (!in_array($role, [AdminUser::ROLE_SUPERUSER, AdminUser::ROLE_ADMIN], true)) {
            $role = AdminUser::ROLE_ADMIN;
        }

        $result = AdminUser::createUser($email, $name, $role);

        if (!$result) {
            echo json_encode(['success' => false, 'error' => 'Cet email existe deja.']);
            return;
        }

        echo json_encode(['success' => true, 'message' => "Utilisateur {$email} cree avec le role {$role}."]);
    }

    public function update(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non specifie.']);
            return;
        }

        $user = AdminUser::findById($id);
        if ($user === null) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur introuvable.']);
            return;
        }

        $data = [];
        if (isset($_POST['name'])) {
            $data['name'] = trim((string) $_POST['name']);
        }
        if (isset($_POST['role'])) {
            $role = trim((string) $_POST['role']);
            if (in_array($role, [AdminUser::ROLE_SUPERUSER, AdminUser::ROLE_ADMIN], true)) {
                $data['role'] = $role;
            }
        }
        if (isset($_POST['is_active'])) {
            $data['is_active'] = filter_var($_POST['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        AdminUser::updateUser($id, $data);

        echo json_encode(['success' => true, 'message' => 'Utilisateur mis a jour.']);
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non specifie.']);
            return;
        }

        $user = AdminUser::findById($id);
        if ($user === null) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur introuvable.']);
            return;
        }

        // Cannot delete yourself
        $currentEmail = strtolower(trim((string) ($_SESSION['admin_user_email'] ?? '')));
        if (strtolower($user['email']) === $currentEmail) {
            echo json_encode(['success' => false, 'error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
            return;
        }

        AdminUser::deleteUser($id);

        echo json_encode(['success' => true, 'message' => 'Utilisateur supprime.']);
    }

    public function userModules(int $id): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $user = AdminUser::findById($id);
        if ($user === null) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur introuvable.']);
            return;
        }

        $allModules = AdminModule::findAll();
        $userModules = AdminUser::getUserModules($id);

        $modules = [];
        foreach ($allModules as $mod) {
            if ((bool) $mod['superuser_only']) {
                continue;
            }
            $enabled = empty($userModules) ? true : ($userModules[$mod['slug']] ?? true);
            $modules[] = [
                'slug' => $mod['slug'],
                'name' => $mod['name'],
                'icon' => $mod['icon'],
                'category' => $mod['category'],
                'is_active' => (bool) $mod['is_active'],
                'enabled' => $enabled,
            ];
        }

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ],
            'modules' => $modules,
        ]);
    }

    public function saveUserModules(): void
    {
        AuthController::requireAuth();
        self::requireSuperUser();

        header('Content-Type: application/json; charset=utf-8');

        $userId = (int) ($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non specifie.']);
            return;
        }

        $user = AdminUser::findById($userId);
        if ($user === null) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur introuvable.']);
            return;
        }

        $enabledSlugs = isset($_POST['modules']) ? (array) $_POST['modules'] : [];
        AdminUser::setUserModulesBulk($userId, $enabledSlugs);

        echo json_encode(['success' => true, 'message' => "Modules mis a jour pour {$user['email']}."]);
    }

    private static function requireSuperUser(): void
    {
        if (!AdminUser::isSuperUser()) {
            http_response_code(403);
            echo '<div style="text-align:center;padding:4rem;font-family:sans-serif;">';
            echo '<h1 style="color:#8B1538;">Acces refuse</h1>';
            echo '<p>Seul le super-utilisateur peut acceder a cette page.</p>';
            echo '<a href="/admin">Retour au tableau de bord</a>';
            echo '</div>';
            exit;
        }
    }
}
