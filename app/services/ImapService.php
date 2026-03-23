<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;

final class ImapService
{
    private static ?Client $client = null;

    /**
     * Get IMAP connection configuration.
     */
    public static function getConfig(): array
    {
        return [
            'host' => (string) Config::get('mail.imap_host', Config::get('mail.smtp_host', '')),
            'port' => (int) Config::get('mail.imap_port', 993),
            'encryption' => (string) Config::get('mail.imap_encryption', 'ssl'),
            'username' => (string) Config::get('mail.imap_user', Config::get('mail.smtp_user', '')),
            'password' => (string) Config::get('mail.imap_pass', Config::get('mail.smtp_pass', '')),
        ];
    }

    /**
     * Check if IMAP is configured.
     */
    public static function isConfigured(): bool
    {
        $cfg = self::getConfig();
        return $cfg['host'] !== '' && $cfg['username'] !== '' && $cfg['password'] !== '';
    }

    /**
     * Get IMAP client (lazy connect).
     */
    public static function connect(): Client
    {
        if (self::$client !== null) {
            return self::$client;
        }

        $cfg = self::getConfig();

        $cm = new ClientManager();
        self::$client = $cm->make([
            'host' => $cfg['host'],
            'port' => $cfg['port'],
            'encryption' => $cfg['encryption'],
            'validate_cert' => false,
            'username' => $cfg['username'],
            'password' => $cfg['password'],
            'protocol' => 'imap',
        ]);

        self::$client->connect();
        return self::$client;
    }

    /**
     * Disconnect IMAP client.
     */
    public static function disconnect(): void
    {
        if (self::$client !== null) {
            try {
                self::$client->disconnect();
            } catch (\Throwable $e) {
                // ignore
            }
            self::$client = null;
        }
    }

    /**
     * Fetch emails from a folder.
     *
     * @return array{emails: array, total: int, page: int, per_page: int}
     */
    public static function fetchEmails(string $folder = 'INBOX', int $page = 1, int $perPage = 20): array
    {
        $client = self::connect();
        $imapFolder = $client->getFolder($folder);

        if ($imapFolder === null) {
            return ['emails' => [], 'total' => 0, 'page' => $page, 'per_page' => $perPage];
        }

        // Get total count
        $status = $imapFolder->examine();
        $total = (int) ($status['exists'] ?? 0);

        // Fetch latest emails with pagination (newest first)
        $start = max(1, $total - ($page * $perPage) + 1);
        $end = max(1, $total - (($page - 1) * $perPage));

        if ($start > $end) {
            return ['emails' => [], 'total' => $total, 'page' => $page, 'per_page' => $perPage];
        }

        $messages = $imapFolder->query()
            ->all()
            ->setFetchOrder('desc')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get();

        $emails = [];
        foreach ($messages as $message) {
            $emails[] = self::messageToArray($message);
        }

        self::disconnect();

        return [
            'emails' => $emails,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Search emails by keyword.
     */
    public static function searchEmails(string $keyword, string $folder = 'INBOX', int $limit = 50): array
    {
        $client = self::connect();
        $imapFolder = $client->getFolder($folder);

        if ($imapFolder === null) {
            return [];
        }

        $messages = $imapFolder->query()
            ->orWhere([
                ['SUBJECT', $keyword],
                ['FROM', $keyword],
                ['BODY', $keyword],
            ])
            ->setFetchOrder('desc')
            ->limit($limit)
            ->get();

        $emails = [];
        foreach ($messages as $message) {
            $emails[] = self::messageToArray($message);
        }

        self::disconnect();
        return $emails;
    }

    /**
     * Fetch a single email by UID.
     */
    public static function fetchEmail(int $uid, string $folder = 'INBOX'): ?array
    {
        $client = self::connect();
        $imapFolder = $client->getFolder($folder);

        if ($imapFolder === null) {
            return null;
        }

        $message = $imapFolder->query()
            ->whereUid($uid)
            ->get()
            ->first();

        if ($message === null) {
            self::disconnect();
            return null;
        }

        // Mark as seen
        $message->setFlag('Seen');

        $data = self::messageToArray($message, true);

        // Get attachments
        $data['attachments'] = [];
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            $data['attachments'][] = [
                'name' => $attachment->getName(),
                'size' => $attachment->getSize(),
                'mime' => $attachment->getMimeType(),
            ];
        }

        self::disconnect();
        return $data;
    }

    /**
     * Get list of folders.
     */
    public static function getFolders(): array
    {
        $client = self::connect();
        $folders = $client->getFolders();

        $result = [];
        foreach ($folders as $folder) {
            $result[] = [
                'name' => $folder->name,
                'path' => $folder->path,
                'full_name' => $folder->full_name,
            ];
        }

        self::disconnect();
        return $result;
    }

    /**
     * Delete an email by UID.
     */
    public static function deleteEmail(int $uid, string $folder = 'INBOX'): bool
    {
        $client = self::connect();
        $imapFolder = $client->getFolder($folder);

        if ($imapFolder === null) {
            return false;
        }

        $message = $imapFolder->query()
            ->whereUid($uid)
            ->get()
            ->first();

        if ($message === null) {
            self::disconnect();
            return false;
        }

        $message->delete();
        self::disconnect();
        return true;
    }

    /**
     * Get unread count for a folder.
     */
    public static function getUnreadCount(string $folder = 'INBOX'): int
    {
        $client = self::connect();
        $imapFolder = $client->getFolder($folder);

        if ($imapFolder === null) {
            return 0;
        }

        $count = $imapFolder->query()
            ->whereUnseen()
            ->count();

        self::disconnect();
        return (int) $count;
    }

    /**
     * Convert a Message to a simple array.
     */
    private static function messageToArray(Message $message, bool $includeBody = false): array
    {
        $from = $message->getFrom();
        $to = $message->getTo();
        $cc = $message->getCc();
        $date = $message->getDate();

        $fromArr = [];
        if ($from) {
            foreach ($from->toArray() as $addr) {
                $fromArr[] = [
                    'email' => $addr->mail ?? '',
                    'name' => $addr->personal ?? '',
                ];
            }
        }

        $toArr = [];
        if ($to) {
            foreach ($to->toArray() as $addr) {
                $toArr[] = [
                    'email' => $addr->mail ?? '',
                    'name' => $addr->personal ?? '',
                ];
            }
        }

        $ccArr = [];
        if ($cc) {
            foreach ($cc->toArray() as $addr) {
                $ccArr[] = [
                    'email' => $addr->mail ?? '',
                    'name' => $addr->personal ?? '',
                ];
            }
        }

        $flags = $message->getFlags();
        $isSeen = $flags->contains('Seen') || $flags->contains('\Seen');

        $data = [
            'uid' => $message->getUid(),
            'subject' => (string) $message->getSubject(),
            'from' => $fromArr,
            'to' => $toArr,
            'cc' => $ccArr,
            'date' => $date ? $date->first()->format('Y-m-d H:i:s') : '',
            'is_seen' => $isSeen,
            'has_attachments' => $message->hasAttachments(),
        ];

        if ($includeBody) {
            $htmlBody = $message->getHTMLBody();
            $textBody = $message->getTextBody();
            $data['body_html'] = $htmlBody ?: '';
            $data['body_text'] = $textBody ?: '';
        }

        return $data;
    }
}
