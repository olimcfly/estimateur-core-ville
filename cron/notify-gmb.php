#!/usr/bin/env php
<?php

/**
 * GMB notification cron: sends email reminders for scheduled publications.
 *
 * This script:
 * 1. Finds GMB publications with status "scheduled" whose scheduled_at has passed
 * 2. Sends an email notification for each publication
 * 3. Marks them as "notified"
 * 4. Expires old scheduled publications (> 7 days past due)
 *
 * Usage:
 *   php cron/notify-gmb.php [--dry-run]
 *
 * Crontab (every day at configured hour, default 8am):
 *   0 8 * * * /usr/bin/php /path/to/cron/notify-gmb.php >> /var/log/gmb-notify.log 2>&1
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Models\GmbPublication;
use App\Services\Mailer;

echo "[" . date('Y-m-d H:i:s') . "] === Notification GMB ===\n";

$dryRun = in_array('--dry-run', $argv ?? [], true);
if ($dryRun) {
    echo "MODE DRY-RUN: aucun email ne sera envoye.\n";
}

$model = new GmbPublication();

// 1. Expire old scheduled publications
try {
    $expired = $model->expireOldScheduled();
    if ($expired > 0) {
        echo "1. {$expired} publication(s) expiree(s).\n";
    } else {
        echo "1. Aucune publication expiree.\n";
    }
} catch (\Throwable $e) {
    echo "ERREUR expiration: " . $e->getMessage() . "\n";
}

// 2. Find pending notifications
try {
    $pending = $model->getPendingNotifications();
    echo "2. " . count($pending) . " publication(s) en attente de notification.\n";
} catch (\Throwable $e) {
    echo "ERREUR recherche pending: " . $e->getMessage() . "\n";
    exit(1);
}

if (empty($pending)) {
    echo "Rien a notifier. Fin.\n";
    exit(0);
}

// 3. Get notification email
$notificationEmail = $model->getSetting('notification_email', '');
if ($notificationEmail === '') {
    echo "ATTENTION: Aucun email de notification configure. Configurez-le dans les parametres GMB.\n";
    // Still mark as notified to avoid repeat processing
    if (!$dryRun) {
        foreach ($pending as $pub) {
            $model->markAsNotified((int) $pub['id']);
        }
    }
    exit(0);
}

// 4. Send notifications
$sent = 0;
$errors = 0;

foreach ($pending as $pub) {
    $title = $pub['title'] ?? ($pub['article_title'] ?? ($pub['actualite_title'] ?? 'Publication GMB'));
    $contentPreview = mb_substr(strip_tags($pub['content'] ?? ''), 0, 200);
    $scheduledAt = $pub['scheduled_at'] ?? 'Non planifie';
    $postType = $pub['post_type'] ?? 'update';
    $profileUrl = $model->getSetting('gmb_profile_url', '');

    $subject = "GMB : Publication a poster - {$title}";

    $htmlBody = <<<HTML
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background: #4285f4; color: #fff; padding: 20px; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 20px;">Publication Google My Business</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">A publier maintenant sur votre fiche</p>
        </div>
        <div style="background: #fff; padding: 20px; border: 1px solid #e0e0e0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555; width: 120px;">Type</td>
                    <td style="padding: 8px 0;">{$postType}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Titre</td>
                    <td style="padding: 8px 0;">{$title}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #555;">Planifie</td>
                    <td style="padding: 8px 0;">{$scheduledAt}</td>
                </tr>
            </table>

            <div style="background: #f5f5f5; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <p style="margin: 0 0 5px; font-weight: bold; color: #333;">Contenu a copier-coller :</p>
                <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.5; color: #333;">{$contentPreview}</div>
            </div>

            <div style="text-align: center; margin: 20px 0;">
    HTML;

    if ($profileUrl !== '') {
        $htmlBody .= <<<HTML
                <a href="{$profileUrl}" style="display: inline-block; background: #4285f4; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                    Ouvrir Google My Business
                </a>
        HTML;
    }

    $htmlBody .= <<<HTML
            </div>
        </div>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 0 0 8px 8px; border: 1px solid #e0e0e0; border-top: 0; text-align: center; color: #999; font-size: 12px;">
            Notification automatique - Estimation Immobilier Bordeaux
        </div>
    </div>
    HTML;

    if ($dryRun) {
        echo "  [DRY-RUN] Email pour publication #{$pub['id']}: {$title}\n";
        $sent++;
        continue;
    }

    try {
        $ok = Mailer::send($notificationEmail, $subject, $htmlBody);
        if ($ok) {
            $model->markAsNotified((int) $pub['id']);
            echo "  OK: Publication #{$pub['id']} - {$title}\n";
            $sent++;
        } else {
            echo "  ERREUR EMAIL: Publication #{$pub['id']} - {$title}\n";
            $errors++;
        }
    } catch (\Throwable $e) {
        echo "  EXCEPTION: Publication #{$pub['id']} - " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\nResultat: {$sent} notification(s) envoyee(s), {$errors} erreur(s).\n";
echo "[" . date('Y-m-d H:i:s') . "] === Fin notification GMB ===\n";
