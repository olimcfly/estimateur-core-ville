#!/usr/bin/env php
<?php

/**
 * Daily GMB publication notification sender.
 *
 * This script:
 * 1. Finds all publications scheduled for today that haven't been notified yet
 * 2. Sends an HTML notification email for each one
 * 3. Marks publications as notified
 * 4. Expires old unpublished publications (>7 days)
 *
 * Usage:
 *   php cron/notify-gmb.php [--dry-run] [--website-id=X] [--force]
 *
 * Crontab (daily at 8am):
 *   0 8 * * * /usr/bin/php /path/to/cron/notify-gmb.php >> /var/log/gmb-notify.log 2>&1
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Config;
use App\Core\Database;
use App\Models\AdminNotification;
use App\Models\GmbPublication;
use App\Services\Mailer;

echo "[" . date('Y-m-d H:i:s') . "] === Notifications GMB quotidiennes ===\n";

// Parse CLI arguments
$dryRun = in_array('--dry-run', $argv ?? [], true);
$force = in_array('--force', $argv ?? [], true);
$websiteId = null;

foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--website-id=')) {
        $websiteId = (int) substr($arg, 13);
    }
}

if ($dryRun) {
    echo "  [DRY RUN] Aucun email ne sera envoyé.\n";
}

try {
    $model = new GmbPublication();
    $baseUrl = rtrim((string) Config::get('base_url', ''), '/');

    // ──────────────────────────────────────────────
    // 1. Expire old scheduled publications (>7 days)
    // ──────────────────────────────────────────────
    $expiredCount = $model->expireOldScheduled();
    if ($expiredCount > 0) {
        echo "  {$expiredCount} publication(s) expirée(s) (non publiée(s) depuis +7 jours).\n";

        if (!$dryRun) {
            AdminNotification::create(
                title: 'Publications GMB expirées',
                message: "{$expiredCount} publication(s) GMB programmée(s) n'ont pas été publiée(s) dans les 7 jours et ont été marquées comme expirées.",
                type: AdminNotification::TYPE_WARNING,
                link: '/admin/gmb-publications',
                targetRole: 'all',
                createdBy: 'cron:notify-gmb'
            );

            // Send warning email for expired publications
            $notifEmail = $model->getSetting('notification_email');
            if ($notifEmail) {
                $subject = "⚠️ {$expiredCount} publication(s) GMB expirée(s)";
                $body = buildExpiredWarningEmail($expiredCount, $baseUrl);
                Mailer::send($notifEmail, $subject, $body);
                echo "  Email d'avertissement envoyé pour les publications expirées.\n";
            }
        }
    }

    // ──────────────────────────────────────────────
    // 2. Get today's pending notifications
    // ──────────────────────────────────────────────
    $today = date('Y-m-d');

    if ($force) {
        // --force: get all scheduled for today, even already notified
        $publications = getScheduledForTodayFull($today, $websiteId);
        echo "  [FORCE] Récupération de toutes les publications du jour.\n";
    } else {
        $publications = getPendingForToday($today, $websiteId);
    }

    if (empty($publications)) {
        echo "  Aucune publication à notifier aujourd'hui.\n";
        echo "[" . date('Y-m-d H:i:s') . "] === Terminé ===\n\n";
        exit(0);
    }

    echo "  " . count($publications) . " publication(s) à notifier.\n";

    // ──────────────────────────────────────────────
    // 3. Send notification for each publication
    // ──────────────────────────────────────────────
    $sent = 0;
    $errors = 0;

    foreach ($publications as $pub) {
        $pubId = (int) $pub['id'];
        $pubTitle = $pub['title'] ?: mb_substr($pub['content'] ?? '', 0, 50);
        echo "\n  Publication #{$pubId} : {$pubTitle}\n";

        // Get notification settings
        $notifEmail = $model->getSetting('notification_email');
        $gmbProfileUrl = $model->getSetting('gmb_profile_url');

        if (!$notifEmail) {
            echo "    ERREUR: Pas d'email de notification configuré.\n";
            $errors++;
            continue;
        }

        // Build email
        $subject = "📌 Publication GMB à poster - " . mb_substr($pubTitle, 0, 50);
        $htmlBody = buildNotificationEmail($pub, $gmbProfileUrl, $baseUrl);

        if ($dryRun) {
            echo "    [DRY RUN] Email qui serait envoyé à : {$notifEmail}\n";
            echo "    Sujet : {$subject}\n";
            echo "    Type : {$pub['post_type']}\n";
            $sent++;
            continue;
        }

        // Send email
        $mailSent = Mailer::send($notifEmail, $subject, $htmlBody);

        if ($mailSent) {
            echo "    Email envoyé à {$notifEmail}.\n";

            // Mark as notified
            if (!$force || empty($pub['notified_at'])) {
                $model->markAsNotified($pubId);
                echo "    Marqué comme notifié.\n";
            }

            $sent++;
        } else {
            echo "    ERREUR: Échec de l'envoi de l'email.\n";
            $errors++;
        }
    }

    echo "\n  Résultat : {$sent} envoyé(s), {$errors} erreur(s).\n";

    if ($sent > 0 && !$dryRun) {
        AdminNotification::create(
            title: 'Notifications GMB envoyées',
            message: "{$sent} notification(s) GMB envoyée(s) pour les publications du jour.",
            type: AdminNotification::TYPE_SUCCESS,
            link: '/admin/gmb-publications',
            targetRole: 'all',
            createdBy: 'cron:notify-gmb'
        );
    }

    echo "[" . date('Y-m-d H:i:s') . "] === Terminé avec succès ===\n\n";

} catch (\Throwable $e) {
    echo "  EXCEPTION: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";

    try {
        AdminNotification::create(
            title: 'Erreur cron GMB',
            message: $e->getMessage(),
            type: AdminNotification::TYPE_ERROR,
            createdBy: 'cron:notify-gmb'
        );
    } catch (\Throwable) {
        // Ignore logging errors
    }

    exit(1);
}

// ──────────────────────────────────────────────────
// Helper functions
// ──────────────────────────────────────────────────

/**
 * Get publications scheduled for today that haven't been notified yet.
 */
function getPendingForToday(string $date, ?int $websiteId): array
{
    $sql = 'SELECT gp.*, a.title AS article_title, a.slug AS article_slug,
                   act.title AS actualite_title, act.slug AS actualite_slug
            FROM gmb_publications gp
            LEFT JOIN articles a ON gp.article_id = a.id
            LEFT JOIN actualites act ON gp.actualite_id = act.id
            WHERE gp.status = :status
              AND DATE(gp.scheduled_at) = :date
              AND gp.notified_at IS NULL';

    $params = [':status' => 'scheduled', ':date' => $date];

    if ($websiteId !== null) {
        $sql .= ' AND gp.website_id = :website_id';
        $params[':website_id'] = $websiteId;
    }

    $sql .= ' ORDER BY gp.scheduled_at ASC';

    $stmt = Database::connection()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Get all publications scheduled for today (including already notified, for --force).
 */
function getScheduledForTodayFull(string $date, ?int $websiteId): array
{
    $sql = 'SELECT gp.*, a.title AS article_title, a.slug AS article_slug,
                   act.title AS actualite_title, act.slug AS actualite_slug
            FROM gmb_publications gp
            LEFT JOIN articles a ON gp.article_id = a.id
            LEFT JOIN actualites act ON gp.actualite_id = act.id
            WHERE gp.status IN (:status1, :status2)
              AND DATE(gp.scheduled_at) = :date';

    $params = [':status1' => 'scheduled', ':status2' => 'notified', ':date' => $date];

    if ($websiteId !== null) {
        $sql .= ' AND gp.website_id = :website_id';
        $params[':website_id'] = $websiteId;
    }

    $sql .= ' ORDER BY gp.scheduled_at ASC';

    $stmt = Database::connection()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Build the HTML notification email for a GMB publication.
 */
function buildNotificationEmail(array $pub, ?string $gmbProfileUrl, string $baseUrl): string
{
    $postType = $pub['post_type'] ?? 'update';
    $title = htmlspecialchars($pub['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($pub['content'] ?? '', ENT_QUOTES, 'UTF-8');
    $ctaType = $pub['cta_type'] ?? '';
    $ctaUrl = htmlspecialchars($pub['cta_url'] ?? '', ENT_QUOTES, 'UTF-8');
    $imagePath = $pub['image_path'] ?? '';
    $pubId = (int) $pub['id'];
    $scheduledAt = $pub['scheduled_at'] ?? '';

    // Post type badge colors
    $badgeColors = [
        'update'  => '#4CAF50',
        'event'   => '#2196F3',
        'offer'   => '#FF9800',
        'product' => '#9C27B0',
    ];
    $badgeColor = $badgeColors[$postType] ?? '#757575';
    $postTypeLabel = ucfirst($postType);

    // CTA type labels
    $ctaLabels = [
        'learn_more' => 'En savoir plus',
        'book'       => 'Réserver',
        'order'      => 'Commander',
        'shop'       => 'Acheter',
        'sign_up'    => "S'inscrire",
        'call'       => 'Appeler',
    ];
    $ctaLabel = $ctaLabels[$ctaType] ?? $ctaType;

    // Source article info
    $articleTitle = $pub['article_title'] ?? $pub['actualite_title'] ?? '';
    $articleSlug = $pub['article_slug'] ?? $pub['actualite_slug'] ?? '';
    $articleLink = '';
    if ($articleTitle && $articleSlug) {
        $articleType = !empty($pub['article_id']) ? 'articles' : 'actualites';
        $articleLink = $baseUrl . '/' . $articleType . '/' . $articleSlug;
    }

    // Image
    $imageHtml = '';
    if ($imagePath) {
        $imageUrl = $baseUrl . '/' . ltrim($imagePath, '/');
        $imageHtml = '
            <div style="margin: 15px 0; text-align: center;">
                <img src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '"
                     alt="Image de la publication"
                     style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #e0e0e0;" />
            </div>';
    }

    // Event dates
    $eventHtml = '';
    if ($postType === 'event') {
        $eventStart = $pub['event_start'] ?? '';
        $eventEnd = $pub['event_end'] ?? '';
        if ($eventStart) {
            $eventHtml = '
            <div style="margin: 10px 0; padding: 10px 15px; background: #E3F2FD; border-left: 4px solid #2196F3; border-radius: 4px;">
                <strong style="color: #1565C0;">📅 Événement</strong><br />
                <span style="color: #333;">Début : ' . htmlspecialchars($eventStart, ENT_QUOTES, 'UTF-8') . '</span>';
            if ($eventEnd) {
                $eventHtml .= '<br /><span style="color: #333;">Fin : ' . htmlspecialchars($eventEnd, ENT_QUOTES, 'UTF-8') . '</span>';
            }
            $eventHtml .= '
            </div>';
        }
    }

    // Offer details
    $offerHtml = '';
    if ($postType === 'offer') {
        $offerCode = $pub['offer_code'] ?? '';
        $offerTerms = $pub['offer_terms'] ?? '';
        if ($offerCode || $offerTerms) {
            $offerHtml = '
            <div style="margin: 10px 0; padding: 10px 15px; background: #FFF3E0; border-left: 4px solid #FF9800; border-radius: 4px;">
                <strong style="color: #E65100;">🏷️ Offre</strong><br />';
            if ($offerCode) {
                $offerHtml .= '<span style="color: #333;">Code promo : <strong>' . htmlspecialchars($offerCode, ENT_QUOTES, 'UTF-8') . '</strong></span><br />';
            }
            if ($offerTerms) {
                $offerHtml .= '<span style="color: #666; font-size: 13px;">' . htmlspecialchars($offerTerms, ENT_QUOTES, 'UTF-8') . '</span>';
            }
            $offerHtml .= '
            </div>';
        }
    }

    // CTA section
    $ctaHtml = '';
    if ($ctaType && $ctaUrl) {
        $ctaHtml = '
            <div style="margin: 10px 0; padding: 8px 15px; background: #F5F5F5; border-radius: 4px;">
                <strong>CTA prévu :</strong> ' . htmlspecialchars($ctaLabel, ENT_QUOTES, 'UTF-8') . '
                → <a href="' . $ctaUrl . '" style="color: #1976D2; text-decoration: underline;">' . $ctaUrl . '</a>
            </div>';
    }

    // Source article section
    $sourceHtml = '';
    if ($articleTitle) {
        $sourceHtml = '
            <div style="margin: 10px 0; padding: 8px 15px; background: #F5F5F5; border-radius: 4px;">
                <strong>Article source :</strong> ';
        if ($articleLink) {
            $sourceHtml .= '<a href="' . htmlspecialchars($articleLink, ENT_QUOTES, 'UTF-8') . '" style="color: #1976D2; text-decoration: underline;">'
                . htmlspecialchars($articleTitle, ENT_QUOTES, 'UTF-8') . '</a>';
        } else {
            $sourceHtml .= htmlspecialchars($articleTitle, ENT_QUOTES, 'UTF-8');
        }
        $sourceHtml .= '
            </div>';
    }

    // GMB button
    $gmbButtonHtml = '';
    if ($gmbProfileUrl) {
        $gmbButtonHtml = '
            <div style="text-align: center; margin: 25px 0 10px;">
                <a href="' . htmlspecialchars($gmbProfileUrl, ENT_QUOTES, 'UTF-8') . '"
                   style="display: inline-block; padding: 14px 30px; background-color: #4285F4; color: #ffffff;
                          text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: bold;">
                    📍 Ouvrir ma fiche Google
                </a>
            </div>';
    }

    // Mark as published button
    $markPublishedUrl = $baseUrl . '/admin/gmb/mark-published/' . $pubId;
    $markPublishedHtml = '
        <div style="text-align: center; margin: 10px 0 25px;">
            <a href="' . htmlspecialchars($markPublishedUrl, ENT_QUOTES, 'UTF-8') . '"
               style="display: inline-block; padding: 12px 25px; background-color: #43A047; color: #ffffff;
                      text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold;">
                ✅ Marquer comme publié
            </a>
        </div>';

    // Title section (only for event/offer/product)
    $titleHtml = '';
    if ($title && in_array($postType, ['event', 'offer', 'product'], true)) {
        $titleHtml = '
            <h3 style="margin: 10px 0 5px; color: #333; font-size: 18px;">' . $title . '</h3>';
    }

    return '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Publication GMB à poster</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                       style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 10px;
                              box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1a237e; padding: 25px 30px; border-radius: 10px 10px 0 0; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: bold;">
                                Estimation <?= site("city", "") ?>
                            </h1>
                            <p style="margin: 5px 0 0; color: #B3C6FF; font-size: 13px;">
                                Notification de publication GMB
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px; color: #333; font-size: 15px; line-height: 1.5;">
                                Bonjour, une publication Google My Business est prévue aujourd\'hui.
                            </p>

                            <!-- Publication card -->
                            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">

                                <!-- Post type badge -->
                                <span style="display: inline-block; padding: 4px 12px; background-color: ' . $badgeColor . ';
                                             color: #fff; border-radius: 20px; font-size: 12px; font-weight: bold;
                                             text-transform: uppercase; letter-spacing: 0.5px;">
                                    ' . $postTypeLabel . '
                                </span>

                                ' . $titleHtml . '

                                <!-- Publication text -->
                                <div style="margin: 15px 0; padding: 15px; background-color: #f8f9fa; border-radius: 6px;
                                            border: 1px solid #e9ecef; font-size: 14px; line-height: 1.6; color: #333;
                                            white-space: pre-wrap; word-wrap: break-word;">
' . $content . '
                                </div>

                                ' . $imageHtml . '
                                ' . $eventHtml . '
                                ' . $offerHtml . '
                                ' . $ctaHtml . '
                                ' . $sourceHtml . '

                            </div>

                            <!-- Action buttons -->
                            ' . $gmbButtonHtml . '
                            ' . $markPublishedHtml . '

                            <p style="margin: 20px 0 0; color: #999; font-size: 12px; text-align: center; line-height: 1.4;">
                                Programmé pour le ' . htmlspecialchars($scheduledAt, ENT_QUOTES, 'UTF-8') . '
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; border-radius: 0 0 10px 10px;
                                   text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999; font-size: 12px; line-height: 1.4;">
                                Ce rappel a été généré automatiquement par votre plateforme Estimation <?= site("city", "") ?>.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

/**
 * Build a warning email for expired publications.
 */
function buildExpiredWarningEmail(int $count, string $baseUrl): string
{
    return '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                       style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 10px;
                              box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #E65100; padding: 25px 30px; border-radius: 10px 10px 0 0; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: bold;">
                                ⚠️ Estimation <?= site("city", "") ?>
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 15px; color: #333; font-size: 15px; line-height: 1.5;">
                                <strong>' . $count . ' publication(s) GMB</strong> programmée(s) n\'ont pas été publiée(s)
                                dans les 7 jours suivant leur programmation et ont été automatiquement marquées comme <strong>expirées</strong>.
                            </p>
                            <p style="margin: 0 0 20px; color: #666; font-size: 14px; line-height: 1.5;">
                                Pensez à vérifier régulièrement vos publications programmées et à les poster sur votre fiche Google.
                            </p>
                            <div style="text-align: center; margin: 20px 0;">
                                <a href="' . htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') . '/admin/gmb-publications"
                                   style="display: inline-block; padding: 12px 25px; background-color: #1a237e; color: #ffffff;
                                          text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold;">
                                    Voir les publications
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; border-radius: 0 0 10px 10px;
                                   text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="margin: 0; color: #999; font-size: 12px;">
                                Ce rappel a été généré automatiquement par votre plateforme Estimation <?= site("city", "") ?>.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}
