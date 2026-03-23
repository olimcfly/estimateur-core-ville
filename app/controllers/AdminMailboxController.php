<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Database;
use App\Core\View;
use App\Controllers\AdminSmtpApiController;
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

    /**
     * AI assistant for email composition (POST, AJAX).
     * Supports Claude (Anthropic) and OpenAI as fallback.
     */
    public function aiAssist(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $action = trim((string) ($_POST['action'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $instructions = trim((string) ($_POST['instructions'] ?? ''));
        $recipient = trim((string) ($_POST['recipient'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));

        $validActions = ['write', 'rewrite', 'shorter', 'longer', 'formal', 'friendly', 'translate_en', 'translate_fr', 'fix_grammar', 'subject_ideas'];
        if (!in_array($action, $validActions, true)) {
            echo json_encode(['success' => false, 'message' => 'Action non valide.']);
            return;
        }

        $systemPrompt = "Tu es un assistant spécialisé en rédaction d'emails professionnels pour une agence d'estimation immobilière à Bordeaux (estimation-immobilier-bordeaux.fr). Tu rédiges en français sauf si on te demande de traduire. Tu utilises un ton professionnel mais chaleureux. Tu retournes uniquement le contenu HTML de l'email (avec balises <p>, <strong>, <ul>, etc.), sans balises <html>, <body> ou <head>.";

        $prompts = [
            'write' => "Rédige un email professionnel avec les instructions suivantes :\n{$instructions}\nDestinataire : {$recipient}\nSujet : {$subject}\nRetourne uniquement le contenu HTML du corps de l'email.",
            'rewrite' => "Réécris cet email en l'améliorant (clarté, ton professionnel, structure) :\n\n{$content}\n\nInstructions supplémentaires : {$instructions}",
            'shorter' => "Raccourcis cet email en gardant les points essentiels :\n\n{$content}",
            'longer' => "Développe cet email avec plus de détails et d'arguments :\n\n{$content}",
            'formal' => "Reformule cet email dans un ton plus formel et professionnel :\n\n{$content}",
            'friendly' => "Reformule cet email dans un ton plus amical et chaleureux tout en restant professionnel :\n\n{$content}",
            'translate_en' => "Traduis cet email en anglais en gardant le même ton et la mise en forme HTML :\n\n{$content}",
            'translate_fr' => "Traduis cet email en français en gardant le même ton et la mise en forme HTML :\n\n{$content}",
            'fix_grammar' => "Corrige l'orthographe et la grammaire de cet email sans changer le sens ni le style :\n\n{$content}",
            'subject_ideas' => "Propose 5 objets d'email accrocheurs et professionnels pour cet email. Retourne-les sous forme de liste HTML (<ul><li>).\n\nContenu de l'email :\n{$content}\n\nContexte : {$instructions}",
        ];

        $userPrompt = $prompts[$action];

        // Try Claude first, then OpenAI
        $anthropicKey = trim((string) Config::get('anthropic.api_key', ''));
        $openaiKey = trim((string) Config::get('openai.api_key', ''));

        if ($anthropicKey !== '') {
            $result = $this->callClaude($anthropicKey, $systemPrompt, $userPrompt);
        } elseif ($openaiKey !== '') {
            $result = $this->callOpenAI($openaiKey, $systemPrompt, $userPrompt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune clé API configurée (ANTHROPIC_API_KEY ou OPENAI_API_KEY).']);
            return;
        }

        echo json_encode($result);
    }

    private function callClaude(string $apiKey, string $system, string $userPrompt): array
    {
        $model = (string) Config::get('anthropic.model', 'claude-sonnet-4-20250514');

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'max_tokens' => 1500,
                'system' => $system,
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]),
            CURLOPT_TIMEOUT => 45,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return ['success' => false, 'message' => 'Erreur API Claude (HTTP ' . $httpCode . ')'];
        }

        $data = json_decode($response, true);
        $content = $data['content'][0]['text'] ?? '';

        $inputTokens = (int) ($data['usage']['input_tokens'] ?? 0);
        $outputTokens = (int) ($data['usage']['output_tokens'] ?? 0);
        $cost = round(($inputTokens / 1000) * 0.003 + ($outputTokens / 1000) * 0.015, 6);
        AdminSmtpApiController::logAiUsage('claude', $model, $inputTokens, $outputTokens, $cost, 'email_compose_assist');

        return ['success' => true, 'content' => $content, 'provider' => 'claude'];
    }

    private function callOpenAI(string $apiKey, string $system, string $userPrompt): array
    {
        $endpoint = (string) Config::get('openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $model = (string) Config::get('openai.model', 'gpt-4o-mini');

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]),
            CURLOPT_TIMEOUT => 45,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return ['success' => false, 'message' => 'Erreur API OpenAI (HTTP ' . $httpCode . ')'];
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        $inputTokens = (int) ($data['usage']['prompt_tokens'] ?? 0);
        $outputTokens = (int) ($data['usage']['completion_tokens'] ?? 0);
        $inRate = str_contains($model, '4o-mini') ? 0.00015 : 0.0025;
        $outRate = str_contains($model, '4o-mini') ? 0.0006 : 0.0100;
        $cost = round(($inputTokens / 1000) * $inRate + ($outputTokens / 1000) * $outRate, 6);
        AdminSmtpApiController::logAiUsage('openai', $model, $inputTokens, $outputTokens, $cost, 'email_compose_assist');

        return ['success' => true, 'content' => $content, 'provider' => 'openai'];
    }

    /**
     * Email library: list templates (GET, AJAX).
     */
    public function emailLibrary(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $category = trim((string) ($_GET['category'] ?? ''));
        $search = trim((string) ($_GET['q'] ?? ''));

        $pdo = Database::connection();

        try {
            if (!Database::tableExists('email_library')) {
                echo json_encode(['success' => true, 'templates' => []]);
                return;
            }

            $where = [];
            $params = [];

            if ($category !== '') {
                $where[] = 'category = :category';
                $params['category'] = $category;
            }
            if ($search !== '') {
                $where[] = '(name LIKE :search OR subject LIKE :search2 OR tags LIKE :search3)';
                $params['search'] = '%' . $search . '%';
                $params['search2'] = '%' . $search . '%';
                $params['search3'] = '%' . $search . '%';
            }

            $sql = 'SELECT * FROM email_library';
            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }
            $sql .= ' ORDER BY usage_count DESC, updated_at DESC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $templates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'templates' => $templates]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Email library: save a custom template (POST, AJAX).
     */
    public function emailLibrarySave(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $category = trim((string) ($_POST['category'] ?? 'autre'));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $bodyHtml = trim((string) ($_POST['body_html'] ?? ''));
        $tags = trim((string) ($_POST['tags'] ?? ''));

        if ($name === '' || $subject === '') {
            echo json_encode(['success' => false, 'message' => 'Nom et sujet requis.']);
            return;
        }

        $pdo = Database::connection();

        try {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE email_library SET name = :name, category = :category, subject = :subject, body_html = :body_html, tags = :tags, updated_at = NOW() WHERE id = :id');
                $stmt->execute(['name' => $name, 'category' => $category, 'subject' => $subject, 'body_html' => $bodyHtml, 'tags' => $tags, 'id' => $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO email_library (name, category, subject, body_html, tags) VALUES (:name, :category, :subject, :body_html, :tags)');
                $stmt->execute(['name' => $name, 'category' => $category, 'subject' => $subject, 'body_html' => $bodyHtml, 'tags' => $tags]);
            }
            echo json_encode(['success' => true, 'message' => 'Modèle sauvegardé.']);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Email library: delete a template (POST, AJAX).
     */
    public function emailLibraryDelete(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalide.']);
            return;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM email_library WHERE id = :id');
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Modèle supprimé.']);
    }

    /**
     * Increment usage count for a library template (POST, AJAX).
     */
    public function emailLibraryUse(): void
    {
        AuthController::requireAuth();
        header('Content-Type: application/json');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo = Database::connection();
            $pdo->prepare('UPDATE email_library SET usage_count = usage_count + 1 WHERE id = :id')->execute(['id' => $id]);
        }
        echo json_encode(['success' => true]);
    }
}
