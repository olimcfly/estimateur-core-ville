<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Validator;
use App\Core\View;
use App\Models\Lead;
use App\Services\EstimationService;
use App\Services\LeadNotificationService;
use App\Services\LeadScoringService;
use App\Services\PerplexityService;
use App\Services\UtmTrackingService;

/**
 * Handles Google Ads landing pages (capture pages without site navigation).
 *
 * Each landing page is a standalone page optimised for a specific
 * Google Ads keyword group. Pages use the "bare" layout (no header/footer)
 * to eliminate distractions and maximise conversion rate.
 */
final class LandingPageController
{
    private const LEAD_SUBMIT_COOLDOWN_SECONDS = 60;

    // ─── Landing Pages ───────────────────────────────────────

    public function estimation(): void
    {
        UtmTrackingService::capture();
        $location = $this->locationContext();

        View::renderBare('landing/layout', [
            'page_title'       => "Estimation Immobilière {$location['area']} | Gratuite en 60 secondes",
            'meta_description' => "Obtenez une estimation immobilière gratuite à {$location['area']} en 60 secondes. Résultat instantané basé sur les données réelles du marché local.",
            'landing_view'     => 'landing/pages/estimation-bordeaux',
            'landing_slug'     => 'estimation',
        ]);
    }

    public function vendreMaison(): void
    {
        UtmTrackingService::capture();
        $location = $this->locationContext();

        View::renderBare('landing/layout', [
            'page_title'       => "Vendre sa Maison à {$location['area']} | Estimation Gratuite",
            'meta_description' => "Vous vendez votre maison à {$location['area']} ? Obtenez une estimation gratuite et découvrez le prix de vente optimal. Sans engagement.",
            'landing_view'     => 'landing/pages/vendre-maison-bordeaux',
            'landing_slug'     => 'vendre-maison',
        ]);
    }

    public function avisValeurGratuit(): void
    {
        UtmTrackingService::capture();
        $location = $this->locationContext();

        View::renderBare('landing/layout', [
            'page_title'       => "Avis de Valeur Gratuit {$location['city']} | Sans Engagement",
            'meta_description' => "Recevez un avis de valeur gratuit pour votre bien immobilier à {$location['area']}. Analyse basée sur le marché actuel. Résultat immédiat.",
            'landing_view'     => 'landing/pages/avis-valeur-gratuit',
            'landing_slug'     => 'avis-valeur-gratuit',
        ]);
    }

    public function legacyEstimationBordeaux(): void
    {
        $this->redirectLegacy('/lp/estimation');
    }

    public function legacyVendreMaisonBordeaux(): void
    {
        $this->redirectLegacy('/lp/vendre-maison');
    }

    // ─── Form Submission (lead capture) ──────────────────────

    public function submitLead(): void
    {
        UtmTrackingService::capture();

        try {
            $this->assertCooldownRespected();

            $nom       = Validator::string($_POST, 'nom', 2, 120);
            $email     = Validator::email($_POST, 'email');
            $telephone = Validator::string($_POST, 'telephone', 6, 30);
            $location = $this->locationContext();
            $ville     = trim((string) ($_POST['ville'] ?? $location['city']));
            if ($ville === '') {
                $ville = $location['city'];
            }
            $typeBien  = trim((string) ($_POST['type_bien'] ?? ''));
            $surface   = trim((string) ($_POST['surface'] ?? ''));
            $landingSlug = trim((string) ($_POST['landing_slug'] ?? 'estimation'));

            // Build notes with UTM tracking data
            $notes = "Source: Landing page Google Ads ({$landingSlug})";
            $utmNote = UtmTrackingService::toLeadNote();
            if ($utmNote !== '') {
                $notes .= "\n" . $utmNote;
            }

            // Forward UTM hidden fields to notes
            foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid'] as $utmKey) {
                $postVal = trim((string) ($_POST[$utmKey] ?? ''));
                if ($postVal !== '' && !str_contains($notes, $postVal)) {
                    $notes .= "\n{$utmKey}: {$postVal}";
                }
            }

            if (mb_strlen($notes) > 1500) {
                $notes = mb_substr($notes, 0, 1500);
            }

            // Estimate if we have enough data
            $estimation = 0.0;
            if ($typeBien !== '' && $surface !== '' && (float) $surface > 0) {
                try {
                    $service = new EstimationService(new PerplexityService());
                    $result = $service->estimate($ville, $typeBien, (float) $surface, 3);
                    $estimation = (float) $result['estimated_mid'];
                } catch (\Throwable) {
                    // estimation is optional on landing pages
                }
            }

            $scoring = new LeadScoringService();
            $temperature = $scoring->score(
                $estimation > 0 ? $estimation : 300000.0,
                'moins-3-mois',
                'vendre'
            );

            $leadModel = new Lead();
            $leadId = $leadModel->create([
                'lead_type'  => 'qualifie',
                'nom'        => $nom,
                'email'      => $email,
                'telephone'  => $telephone,
                'adresse'    => 'Non renseignée',
                'ville'      => $ville,
                'type_bien'  => $typeBien !== '' ? $typeBien : null,
                'surface_m2' => $surface !== '' ? (float) $surface : null,
                'estimation' => $estimation > 0 ? $estimation : null,
                'urgence'    => 'moins-3-mois',
                'motivation' => 'vendre',
                'notes'      => $notes,
                'score'      => $temperature,
                'statut'     => 'nouveau',
            ]);

            $_SESSION['lead_last_submit'] = [
                'ip'           => $this->getClientIp(),
                'submitted_at' => time(),
            ];

            LeadNotificationService::notify($leadId, $temperature, [
                'nom'        => $nom,
                'email'      => $email,
                'telephone'  => $telephone,
                'adresse'    => 'Non renseignée',
                'ville'      => $ville,
                'estimation' => $estimation,
                'urgence'    => 'moins-3-mois',
                'motivation' => 'vendre',
                'notes'      => $notes,
                'statut'     => 'nouveau',
            ]);

            // Render thank-you page
            View::renderBare('landing/layout', [
                'page_title'       => 'Merci ! Votre demande a été enregistrée',
                'meta_description' => 'Votre demande d\'estimation a bien été enregistrée. Un expert vous contactera très bientôt.',
                'landing_view'     => 'landing/pages/merci',
                'landing_slug'     => $landingSlug,
                'lead_nom'         => $nom,
                'lead_email'       => $email,
                'lead_id'          => $leadId,
                'estimation'       => $estimation,
            ]);
        } catch (\Throwable $e) {
            $location = $this->locationContext();
            $landingSlug = trim((string) ($_POST['landing_slug'] ?? 'estimation'));
            $landingView = 'landing/pages/' . preg_replace('/[^a-z0-9\-]/', '', $landingSlug);

            // Check view exists, fallback to estimation
            $viewPath = __DIR__ . '/../views/' . $landingView . '.php';
            if (!is_file($viewPath)) {
                $landingView = 'landing/pages/estimation-bordeaux';
            }

            View::renderBare('landing/layout', [
                'page_title'       => "Estimation Immobilière {$location['area']}",
                'meta_description' => "Estimation immobilière gratuite à {$location['area']}.",
                'landing_view'     => $landingView,
                'landing_slug'     => $landingSlug,
                'form_error'       => $e->getMessage(),
            ]);
        }
    }

    // ─── Admin: Google Ads Guide ─────────────────────────────

    public function guide(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/landing-guide', [
            'page_title'  => 'Guide Google Ads & Landing Pages',
            'admin_page_title' => 'Google Ads',
            'admin_page'  => 'google-ads-guide',
            'breadcrumb'  => 'Guide Google Ads',
        ]);
    }

    // ─── Admin: Google Ads Campaign Dashboard ─────────────────

    public function campaigns(): void
    {
        AuthController::requireAuth();

        View::renderAdmin('admin/google-ads-campaigns', [
            'page_title'       => 'Generateur de Campagnes Google Ads',
            'admin_page_title' => 'Campagnes Google Ads',
            'admin_page'       => 'google-ads-campaigns',
            'breadcrumb'       => 'Generateur Campagnes',
        ]);
    }

    // ─── Private helpers ─────────────────────────────────────

    private function assertCooldownRespected(): void
    {
        $lastSubmit = $_SESSION['lead_last_submit'] ?? null;
        $ip = $this->getClientIp();

        if (is_array($lastSubmit) && ($lastSubmit['ip'] ?? '') === $ip) {
            $elapsed = time() - (int) ($lastSubmit['submitted_at'] ?? 0);
            if ($elapsed < self::LEAD_SUBMIT_COOLDOWN_SECONDS) {
                throw new \RuntimeException('Merci de patienter une minute avant d\'envoyer une nouvelle demande.');
            }
        }
    }

    private function getClientIp(): string
    {
        $forwardedFor = trim((string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($forwardedFor !== '') {
            return trim(explode(',', $forwardedFor)[0]);
        }

        $realIp = trim((string) ($_SERVER['HTTP_X_REAL_IP'] ?? ''));
        if ($realIp !== '') {
            return $realIp;
        }

        return trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    /**
     * @return array{city:string,area:string}
     */
    private function locationContext(): array
    {
        $branding = function_exists('getBrandingConfig') ? \getBrandingConfig() : [];
        $city = trim((string) ($branding['city_name'] ?? ''));
        if ($city === '') {
            $city = 'Votre ville';
        }

        $area = trim((string) ($branding['area_label'] ?? ''));
        if ($area === '') {
            $area = $city !== '' ? $city : 'votre secteur';
        }

        return ['city' => $city, 'area' => $area];
    }

    private function redirectLegacy(string $targetPath): void
    {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $location = $targetPath . ($query !== '' ? '?' . $query : '');
        header('Location: ' . $location, true, 301);
        exit;
    }
}
