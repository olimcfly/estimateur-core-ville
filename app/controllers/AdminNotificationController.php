<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\AdminNotification;
use App\Models\AdminUser;

final class AdminNotificationController
{
    public function index(): void
    {
        AuthController::requireAuth();

        $role = AdminUser::currentRole();
        $userId = (int) ($_SESSION['admin_user_id'] ?? 0);
        $notifications = AdminNotification::getForUser($role, $userId, 100);
        $unreadCount = AdminNotification::countUnread($role, $userId);

        View::renderAdmin('admin/notifications', [
            'page_title' => 'Notifications',
            'admin_page' => 'notifications',
            'admin_page_title' => 'Notifications',
            'breadcrumb' => 'Notifications',
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * AJAX: Get unread count + recent notifications for the bell dropdown.
     */
    public function fetch(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $role = AdminUser::currentRole();
        $userId = (int) ($_SESSION['admin_user_id'] ?? 0);

        $unread = AdminNotification::countUnread($role, $userId);
        $recent = AdminNotification::getForUser($role, $userId, 10);

        $items = array_map(function (array $n) {
            return [
                'id' => (int) $n['id'],
                'title' => $n['title'],
                'message' => mb_substr($n['message'], 0, 100),
                'type' => $n['type'],
                'link' => $n['link'],
                'is_read' => (bool) $n['is_read'],
                'created_at' => $n['created_at'],
                'time_ago' => self::timeAgo($n['created_at']),
            ];
        }, $recent);

        echo json_encode(['success' => true, 'unread' => $unread, 'notifications' => $items]);
    }

    public function markRead(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            AdminNotification::markAsRead($id);
        }

        echo json_encode(['success' => true]);
    }

    public function markAllRead(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $role = AdminUser::currentRole();
        $userId = (int) ($_SESSION['admin_user_id'] ?? 0);
        AdminNotification::markAllAsRead($role, $userId);

        echo json_encode(['success' => true]);
    }

    public function delete(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            AdminNotification::delete($id);
        }

        echo json_encode(['success' => true]);
    }

    /**
     * Cleanup old notifications (can be called via cron or manually).
     */
    public function cleanup(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json; charset=utf-8');

        if (!AdminUser::isSuperUser()) {
            echo json_encode(['success' => false, 'error' => 'Acces refuse.']);
            return;
        }

        $deleted = AdminNotification::deleteOlderThan(30);

        echo json_encode(['success' => true, 'deleted' => $deleted, 'message' => "{$deleted} notification(s) supprimee(s)."]);
    }

    private static function timeAgo(string $datetime): string
    {
        $now = time();
        $ts = strtotime($datetime);
        if ($ts === false) {
            return '';
        }
        $diff = $now - $ts;

        if ($diff < 60) {
            return 'A l\'instant';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return "Il y a {$m} min";
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return "Il y a {$h}h";
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return "Il y a {$d}j";
        }

        return date('d/m/Y', $ts);
    }
}
