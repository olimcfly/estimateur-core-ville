<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Models\AdminModule;
use App\Models\AdminNotification;
use App\Services\Mailer;

final class LeadNotificationService
{
    /**
     * Send both prospect confirmation and admin notification emails.
     *
     * @param array{
     *     nom: string,
     *     email: string,
     *     telephone: string,
     *     adresse: string,
     *     ville: string,
     *     estimation: float|string,
     *     urgence: string,
     *     motivation: string,
     *     notes: string,
     *     statut: string,
     * } $lead
     */
    public static function notify(int $leadId, string $temperature, array $lead): void
    {
        // Email notifications (if module active)
        if (AdminModule::isActive('notifications_email')) {
            self::sendProspectEmail($lead);
            self::sendAdminEmail($leadId, $temperature, $lead);
            self::sendAllAdminUsersEmail($leadId, $temperature, $lead);
        }

        // Internal notification (if module active)
        if (AdminModule::isActive('notifications_internes')) {
            AdminNotification::notifyNewLead(
                $leadId,
                (string) ($lead['nom'] ?? 'Anonyme'),
                (string) ($lead['ville'] ?? ''),
                $temperature
            );
        }
    }

    /**
     * Send notification email to all active admin users (except main admin who already gets it).
     */
    private static function sendAllAdminUsersEmail(int $leadId, string $temperature, array $lead): void
    {
        $mainAdminEmail = strtolower(trim((string) Config::get('mail.admin_email', '')));

        try {
            $users = \App\Models\AdminUser::findAll();
        } catch (\Throwable $e) {
            return;
        }

        foreach ($users as $user) {
            if (!(bool) ($user['is_active'] ?? true)) {
                continue;
            }
            $userEmail = strtolower(trim($user['email']));
            // Skip main admin (already notified) and prospect email
            if ($userEmail === $mainAdminEmail) {
                continue;
            }
            if ($userEmail === strtolower(trim((string) ($lead['email'] ?? '')))) {
                continue;
            }

            // Check if user has access to notifications_email module
            $userId = (int) $user['id'];
            if (!\App\Models\AdminUser::hasModuleAccess($userId, 'notifications_email')) {
                continue;
            }

            $nom = htmlspecialchars((string) $lead['nom'], ENT_QUOTES, 'UTF-8');
            $ville = htmlspecialchars((string) $lead['ville'], ENT_QUOTES, 'UTF-8');
            $subject = "Nouveau lead #{$leadId} - {$nom} ({$ville})";

            $html = '<div style="font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;">'
                . '<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">'
                . '<div style="background:#1a1410;padding:20px 30px;"><h2 style="margin:0;color:#fff;font-size:16px;">Nouveau lead recu</h2></div>'
                . '<div style="padding:20px 30px;">'
                . '<p><strong>Lead #' . $leadId . '</strong> - ' . htmlspecialchars($temperature, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<p>Nom: <strong>' . $nom . '</strong></p>'
                . '<p>Ville: <strong>' . $ville . '</strong></p>'
                . '<p style="margin-top:15px;"><a href="' . htmlspecialchars((string) Config::get('base_url', ''), ENT_QUOTES, 'UTF-8') . '/admin/leads/' . $leadId . '" style="background:#8B1538;color:#fff;padding:8px 20px;border-radius:6px;text-decoration:none;display:inline-block;">Voir le lead</a></p>'
                . '</div></div></div>';

            try {
                Mailer::send($userEmail, $subject, $html);
            } catch (\Throwable $e) {
                error_log("LeadNotification: failed to send to {$userEmail}: " . $e->getMessage());
            }
        }
    }

    private static function sendProspectEmail(array $lead): void
    {
        $nom = htmlspecialchars((string) $lead['nom'], ENT_QUOTES, 'UTF-8');
        $ville = htmlspecialchars((string) $lead['ville'], ENT_QUOTES, 'UTF-8');
        $estimation = number_format((float) $lead['estimation'], 0, ',', ' ');
        $urgenceLabel = self::urgenceLabel((string) $lead['urgence']);
        $motivationLabel = self::motivationLabel((string) $lead['motivation']);
        $fromName = (string) (Config::get('mail.from_name')
            ?: Config::get('app_name')
            ?: ('Estimation Immobilier ' . (Config::get('city.name') ?: 'Local')));
        $brandName = htmlspecialchars($fromName, ENT_QUOTES, 'UTF-8');

        $subject = "Votre demande d'avis de valeur a bien été enregistrée";

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

  <!-- Header -->
  <tr>
    <td style="background:#8B1538;padding:30px 40px;text-align:center;">
      <h1 style="margin:0;color:#ffffff;font-size:22px;">{$brandName}</h1>
    </td>
  </tr>

  <!-- Body -->
  <tr>
    <td style="padding:35px 40px;">
      <h2 style="margin:0 0 15px;color:#1a1410;font-size:20px;">Bonjour {$nom},</h2>
      <p style="color:#333;line-height:1.7;margin:0 0 20px;">
        Nous avons bien reçu votre demande d'avis de valeur. Un conseiller immobilier spécialisé sur <strong>{$ville}</strong> vous recontactera très prochainement pour organiser un rendez-vous.
      </p>

      <!-- Recap -->
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf9f7;border-radius:8px;border:1px solid #e8dfd7;margin:20px 0;">
        <tr>
          <td style="padding:20px 25px;">
            <h3 style="margin:0 0 15px;color:#8B1538;font-size:16px;">Récapitulatif de votre demande</h3>
            <table width="100%" cellpadding="4" cellspacing="0">
              <tr>
                <td style="color:#6b6459;width:40%;padding:6px 0;border-bottom:1px solid #e8dfd7;">Ville</td>
                <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><strong>{$ville}</strong></td>
              </tr>
              <tr>
                <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Estimation en ligne</td>
                <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><strong>{$estimation} &euro;</strong></td>
              </tr>
              <tr>
                <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Délai souhaité</td>
                <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;">{$urgenceLabel}</td>
              </tr>
              <tr>
                <td style="color:#6b6459;padding:6px 0;">Raison</td>
                <td style="color:#1a1410;padding:6px 0;">{$motivationLabel}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

      <!-- Next steps -->
      <h3 style="margin:25px 0 10px;color:#1a1410;font-size:16px;">Prochaines étapes</h3>
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:6px 10px 6px 0;vertical-align:top;color:#8B1538;font-weight:bold;">1.</td>
          <td style="padding:6px 0;color:#333;line-height:1.6;">Un conseiller vous contacte pour convenir d'un rendez-vous</td>
        </tr>
        <tr>
          <td style="padding:6px 10px 6px 0;vertical-align:top;color:#8B1538;font-weight:bold;">2.</td>
          <td style="padding:6px 0;color:#333;line-height:1.6;">Visite de votre bien pour une évaluation précise</td>
        </tr>
        <tr>
          <td style="padding:6px 10px 6px 0;vertical-align:top;color:#8B1538;font-weight:bold;">3.</td>
          <td style="padding:6px 0;color:#333;line-height:1.6;">Remise de votre avis de valeur détaillé</td>
        </tr>
      </table>

      <p style="color:#333;line-height:1.7;margin:25px 0 0;">
        À très bientôt,<br>
        <strong>L'équipe {$fromName}</strong>
      </p>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#faf9f7;padding:20px 40px;text-align:center;border-top:1px solid #e8dfd7;">
      <p style="margin:0;font-size:12px;color:#6b6459;">
        Cet email a été envoyé suite à votre demande d'avis de valeur sur notre site.<br>
        Vos données sont traitées conformément à notre politique de confidentialité.
      </p>
    </td>
  </tr>

</table>
</td></tr></table>
</body>
</html>
HTML;

        Mailer::send((string) $lead['email'], $subject, $html);
    }

    private static function sendAdminEmail(int $leadId, string $temperature, array $lead): void
    {
        $configuredAdminEmail = trim((string) Config::get('mail.admin_email'));
        $configuredFromEmail = trim((string) Config::get('mail.from'));
        $adminEmail = $configuredAdminEmail !== ''
            ? $configuredAdminEmail
            : ($configuredFromEmail !== '' ? $configuredFromEmail : 'contact@localhost');

        $nom = htmlspecialchars((string) $lead['nom'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars((string) $lead['email'], ENT_QUOTES, 'UTF-8');
        $telephone = htmlspecialchars((string) $lead['telephone'], ENT_QUOTES, 'UTF-8');
        $adresse = htmlspecialchars((string) $lead['adresse'], ENT_QUOTES, 'UTF-8');
        $ville = htmlspecialchars((string) $lead['ville'], ENT_QUOTES, 'UTF-8');
        $estimation = number_format((float) $lead['estimation'], 0, ',', ' ');
        $urgenceLabel = self::urgenceLabel((string) $lead['urgence']);
        $motivationLabel = self::motivationLabel((string) $lead['motivation']);
        $notes = htmlspecialchars((string) ($lead['notes'] ?: 'Aucune'), ENT_QUOTES, 'UTF-8');
        $temperatureHtml = htmlspecialchars($temperature, ENT_QUOTES, 'UTF-8');
        $date = date('d/m/Y à H:i');
        $brandName = (string) (Config::get('mail.from_name')
            ?: Config::get('app_name')
            ?: 'Estimation Immobilière');
        $brandName = htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8');

        $tempColor = match (true) {
            str_contains(strtolower($temperature), 'chaud') => '#e24b4a',
            str_contains(strtolower($temperature), 'tiède'), str_contains(strtolower($temperature), 'tiede') => '#f97316',
            default => '#3b82f6',
        };

        $subject = "Nouveau lead #{$leadId} - {$nom} ({$ville})";

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

  <!-- Header -->
  <tr>
    <td style="background:#1a1410;padding:25px 40px;">
      <h1 style="margin:0;color:#ffffff;font-size:18px;">Nouveau lead reçu</h1>
      <p style="margin:5px 0 0;color:#D4AF37;font-size:14px;">#{$leadId} &mdash; {$date}</p>
    </td>
  </tr>

  <!-- Temperature badge -->
  <tr>
    <td style="padding:25px 40px 0;">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:{$tempColor};color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:bold;">
            Score : {$temperatureHtml}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Contact info -->
  <tr>
    <td style="padding:20px 40px;">
      <h3 style="margin:0 0 12px;color:#8B1538;font-size:16px;">Coordonnées du prospect</h3>
      <table width="100%" cellpadding="4" cellspacing="0">
        <tr>
          <td style="color:#6b6459;width:35%;padding:6px 0;border-bottom:1px solid #e8dfd7;">Nom</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><strong>{$nom}</strong></td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Email</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><a href="mailto:{$email}" style="color:#8B1538;">{$email}</a></td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Téléphone</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><a href="tel:{$telephone}" style="color:#8B1538;">{$telephone}</a></td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;">Adresse du bien</td>
          <td style="color:#1a1410;padding:6px 0;">{$adresse}</td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Estimation details -->
  <tr>
    <td style="padding:0 40px 20px;">
      <h3 style="margin:0 0 12px;color:#8B1538;font-size:16px;">Détails de la demande</h3>
      <table width="100%" cellpadding="4" cellspacing="0">
        <tr>
          <td style="color:#6b6459;width:35%;padding:6px 0;border-bottom:1px solid #e8dfd7;">Ville</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><strong>{$ville}</strong></td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Estimation en ligne</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;"><strong>{$estimation} &euro;</strong></td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Délai souhaité</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;">{$urgenceLabel}</td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;border-bottom:1px solid #e8dfd7;">Motivation</td>
          <td style="color:#1a1410;padding:6px 0;border-bottom:1px solid #e8dfd7;">{$motivationLabel}</td>
        </tr>
        <tr>
          <td style="color:#6b6459;padding:6px 0;">Notes</td>
          <td style="color:#1a1410;padding:6px 0;">{$notes}</td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td style="background:#faf9f7;padding:20px 40px;text-align:center;border-top:1px solid #e8dfd7;">
      <p style="margin:0;font-size:12px;color:#6b6459;">
        Notification automatique &mdash; {$brandName}
      </p>
    </td>
  </tr>

</table>
</td></tr></table>
</body>
</html>
HTML;

        Mailer::send($adminEmail, $subject, $html);
    }

    private static function urgenceLabel(string $urgence): string
    {
        return match ($urgence) {
            'rapide' => 'Rapide (moins de 3 mois)',
            'moyen' => 'Moyen (3 à 6 mois)',
            'long' => 'Pas pressé (6+ mois)',
            default => $urgence,
        };
    }

    private static function motivationLabel(string $motivation): string
    {
        return match ($motivation) {
            'vente' => 'Vente',
            'succession' => 'Succession',
            'divorce' => 'Séparation',
            'investissement' => 'Investissement',
            'autre' => 'Autre',
            default => $motivation,
        };
    }
}
