<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Services\ImapService;
use App\Services\Mailer;

final class AdminMailboxController
{
    /**
     * Inbox - list emails.
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $folder = trim((string) ($_GET['folder'] ?? 'INBOX'));
        $search = trim((string) ($_GET['q'] ?? ''));
        $error = null;
        $emails = [];
        $total = 0;
        $perPage = 20;
        $folders = [];
        $unreadCount = 0;

        if (!ImapService::isConfigured()) {
            $error = 'IMAP non configuré. Ajoutez les paramètres IMAP dans votre fichier .env (MAIL_IMAP_HOST, MAIL_IMAP_PORT, etc.) ou utilisez le même serveur que SMTP.';
        } else {
            try {
                if ($search !== '') {
                    $emails = ImapService::searchEmails($search, $folder);
                    $total = count($emails);
                } else {
                    $result = ImapService::fetchEmails($folder, $page, $perPage);
                    $emails = $result['emails'];
                    $total = $result['total'];
                }

                try {
                    $folders = ImapService::getFolders();
                } catch (\Throwable $e) {
                    $folders = [['name' => 'INBOX', 'path' => 'INBOX', 'full_name' => 'INBOX']];
                }

                try {
                    $unreadCount = ImapService::getUnreadCount($folder);
                } catch (\Throwable $e) {
                    $unreadCount = 0;
                }
            } catch (\Throwable $e) {
                $error = 'Erreur de connexion IMAP : ' . $e->getMessage();
                error_log('Mailbox IMAP error: ' . $e->getMessage());
            }
        }

        $totalPages = $perPage > 0 ? max(1, (int) ceil($total / $perPage)) : 1;

        View::renderAdmin('admin/mailbox', [
            'page_title' => 'Boîte Email',
            'admin_page_title' => 'Boîte Email — contact@estimation-immobilier-bordeaux.fr',
            'admin_page' => 'mailbox',
            'breadcrumb' => 'Boîte Email',
            'emails' => $emails,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'folder' => $folder,
            'folders' => $folders,
            'search' => $search,
            'unreadCount' => $unreadCount,
            'error' => $error,
            'mailAddress' => (string) Config::get('mail.from', 'contact@estimation-immobilier-bordeaux.fr'),
        ]);
    }

    /**
     * Read a single email.
     */
    public function read(): void
    {
        AuthController::requireAuth();

        $uid = (int) ($_GET['uid'] ?? 0);
        $folder = trim((string) ($_GET['folder'] ?? 'INBOX'));

        if ($uid <= 0) {
            header('Location: /admin/mailbox');
            exit;
        }

        $error = null;
        $email = null;

        try {
            $email = ImapService::fetchEmail($uid, $folder);
        } catch (\Throwable $e) {
            $error = 'Erreur lors de la lecture de l\'email : ' . $e->getMessage();
            error_log('Mailbox read error: ' . $e->getMessage());
        }

        if ($email === null && $error === null) {
            $error = 'Email introuvable.';
        }

        View::renderAdmin('admin/mailbox-read', [
            'page_title' => $email ? htmlspecialchars($email['subject']) : 'Lire email',
            'admin_page_title' => 'Lire email',
            'admin_page' => 'mailbox',
            'breadcrumb' => 'Lire email',
            'email' => $email,
            'folder' => $folder,
            'error' => $error,
        ]);
    }

    /**
     * Compose a new email.
     */
    public function compose(): void
    {
        AuthController::requireAuth();

        $replyTo = trim((string) ($_GET['reply_to'] ?? ''));
        $replySubject = trim((string) ($_GET['subject'] ?? ''));
        $replyBody = '';

        // If replying to an email, fetch original
        $replyUid = (int) ($_GET['reply_uid'] ?? 0);
        $replyFolder = trim((string) ($_GET['folder'] ?? 'INBOX'));
        if ($replyUid > 0) {
            try {
                $original = ImapService::fetchEmail($replyUid, $replyFolder);
                if ($original) {
                    if ($replyTo === '' && !empty($original['from'])) {
                        $replyTo = $original['from'][0]['email'] ?? '';
                    }
                    if ($replySubject === '') {
                        $sub = $original['subject'];
                        $replySubject = str_starts_with(strtolower($sub), 're:') ? $sub : 'Re: ' . $sub;
                    }
                    $fromName = $original['from'][0]['name'] ?? $original['from'][0]['email'] ?? '';
                    $replyBody = '<br><br><hr><p><strong>' . htmlspecialchars($fromName) . '</strong> a écrit le ' . htmlspecialchars($original['date']) . ' :</p>';
                    $replyBody .= $original['body_html'] ?: nl2br(htmlspecialchars($original['body_text'] ?? ''));
                }
            } catch (\Throwable $e) {
                error_log('Mailbox compose reply error: ' . $e->getMessage());
            }
        }

        $fromAddress = (string) Config::get('mail.from', 'contact@estimation-immobilier-bordeaux.fr');
        $fromName = (string) Config::get('mail.from_name', 'Estimation Immobilier Bordeaux');

        View::renderAdmin('admin/mailbox-compose', [
            'page_title' => 'Nouveau message',
            'admin_page_title' => 'Nouveau message',
            'admin_page' => 'mailbox',
            'breadcrumb' => 'Nouveau message',
            'replyTo' => $replyTo,
            'replySubject' => $replySubject,
            'replyBody' => $replyBody,
            'fromAddress' => $fromAddress,
            'fromName' => $fromName,
        ]);
    }

    /**
     * Send an email (POST).
     */
    public function send(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $to = trim((string) ($_POST['to'] ?? ''));
        $cc = trim((string) ($_POST['cc'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));

        if ($to === '' || $subject === '' || $body === '') {
            echo json_encode(['success' => false, 'message' => 'Destinataire, sujet et message sont requis.']);
            return;
        }

        // Validate emails
        $recipients = array_map('trim', explode(',', $to));
        foreach ($recipients as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Adresse email invalide : ' . $email]);
                return;
            }
        }

        $sent = false;
        $lastError = '';

        foreach ($recipients as $recipient) {
            $result = Mailer::send($recipient, $subject, $body);
            if ($result) {
                $sent = true;
            } else {
                $lastError = 'Échec de l\'envoi vers ' . $recipient;
            }
        }

        // Send to CC if specified
        if ($cc !== '') {
            $ccRecipients = array_map('trim', explode(',', $cc));
            foreach ($ccRecipients as $ccEmail) {
                if (filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    Mailer::send($ccEmail, $subject, $body);
                }
            }
        }

        echo json_encode([
            'success' => $sent,
            'message' => $sent ? 'Email envoyé avec succès.' : ($lastError ?: 'Échec de l\'envoi.'),
        ]);
    }

    /**
     * Delete an email (POST, AJAX).
     */
    public function delete(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $uid = (int) ($_POST['uid'] ?? 0);
        $folder = trim((string) ($_POST['folder'] ?? 'INBOX'));

        if ($uid <= 0) {
            echo json_encode(['success' => false, 'message' => 'UID invalide.']);
            return;
        }

        try {
            $deleted = ImapService::deleteEmail($uid, $folder);
            echo json_encode([
                'success' => $deleted,
                'message' => $deleted ? 'Email supprimé.' : 'Impossible de supprimer cet email.',
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: get unread count.
     */
    public function unreadCount(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        try {
            $count = ImapService::getUnreadCount();
            echo json_encode(['success' => true, 'count' => $count]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => true, 'count' => 0]);
        }
    }
}
