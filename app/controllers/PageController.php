<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Validator;
use App\Core\View;
use App\Models\NewsletterSubscriber;

final class PageController
{
    private function cityName(): string
    {
        return (string) (Config::get('city.name', '') ?: 'Votre ville');
    }

    private function cityAreaLabel(): string
    {
        $city = $this->cityName();
        return (string) (Config::get('city.region', '') ?: $city . ' et Métropole');
    }

    public function home(): void
    {
        $city = $this->cityName();
        $area = $this->cityAreaLabel();
        View::render('pages/home', [
            'page_title' => "Estimation immobilière vendeurs {$area} | Vendre au bon prix",
            'meta_description' => "Propriétaire vendeur à {$area} : obtenez une estimation immobilière claire, locale et sans engagement pour lancer votre vente sereinement.",
            'city_name' => $city,
            'area_label' => $area,
        ]);
    }

    public function sitemap(): void
    {
        $branding = getBrandingConfig();
        $baseUrl = rtrim((string) ($branding['base_url'] ?? ''), '/');

        if ($baseUrl === '') {
            $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            $scheme = $isHttps ? 'https' : 'http';
            $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
            $baseUrl = $scheme . '://' . $host;
        }

        $entries = [
            ['path' => '/', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['path' => '/estimation', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['path' => '/services', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/quartiers', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/financement', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/villes', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/ville/toulon', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => '/blog', 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['path' => '/processus-estimation', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/exemples-estimation', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/guides', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/a-propos', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['path' => '/contact', 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['path' => '/newsletter', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['path' => '/mentions-legales', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['path' => '/politique-confidentialite', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['path' => '/conditions-utilisation', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['path' => '/rgpd', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        header('Content-Type: application/xml; charset=UTF-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($entries as $entry) {
            $path = (string) ($entry['path'] ?? '/');
            $loc = $baseUrl . ($path === '/' ? '/' : $path);
            $changefreq = (string) ($entry['changefreq'] ?? 'monthly');
            $priority = (string) ($entry['priority'] ?? '0.5');

            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . "</loc>\n";
            echo '    <changefreq>' . htmlspecialchars($changefreq, ENT_QUOTES, 'UTF-8') . "</changefreq>\n";
            echo '    <priority>' . htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') . "</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }

    public function services(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/services', [
            'page_title' => "Services d'Estimation Immobilière | {$area}",
        ]);
    }

    public function about(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/a_propos', [
            'page_title' => "À Propos | Estimation Immobilier {$area}",
        ]);
    }

    public function aPropos(): void
    {
        $this->about();
    }

    public function processusEstimation(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/processus_estimation', [
            'page_title' => "Notre Processus d'Estimation Immobilière | {$area}",
        ]);
    }

    public function newsletter(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/newsletter', [
            'page_title' => "Newsletter Immobilier | {$area}",
        ]);
    }

    public function guides(): void
    {
        $city = $this->cityName();
        View::render('pages/guides', [
            'page_title' => "Guides Immobiliers {$city} - Conseils & Astuces",
        ]);
    }


    public function exemplesEstimation(): void
    {
        $city = $this->cityName();
        View::render('pages/exemples_estimation', [
            'page_title' => "Exemple Estimation - Cas Réels {$city} | Nos Résultats",
        ]);
    }


    public function quartiers(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/quartiers', [
            'page_title' => "Prix Immobilier par Quartier à {$area} | Guide 2026",
        ]);
    }

    public function contact(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/contact', [
            'page_title' => "Contact vendeurs | Estimation immobilière {$area}",
            'meta_description' => "Contactez notre équipe dédiée aux propriétaires vendeurs à {$area}. Réponse rapide et accompagnement local.",
        ]);
    }


    public function newsletterSubscribe(): void
    {
        $hasConsent = isset($_POST['newsletter_rgpd']) && $_POST['newsletter_rgpd'] === 'on';

        try {
            $email = mb_strtolower(Validator::email($_POST, 'newsletter_email'));
        } catch (\InvalidArgumentException) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
                'error_message' => 'Adresse email invalide. Merci de vérifier votre saisie.',
            ]);
            return;
        }

        if (!$hasConsent) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
                'error_message' => 'Le consentement RGPD est requis pour finaliser votre inscription.',
            ]);
            return;
        }

        $token = $this->generateNewsletterToken($email);
        $confirmLink = $this->buildNewsletterConfirmLink($token);

        if (!$this->sendNewsletterConfirmationEmail($email, $confirmLink)) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
                'error_message' => 'Impossible d\'envoyer l\'email de confirmation pour le moment. Réessayez dans quelques minutes.',
            ]);
            return;
        }

        View::render('pages/newsletter', [
            'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
            'success_message' => 'Un email de confirmation vient d\'être envoyé. Cliquez sur le lien reçu pour activer votre abonnement.',
        ]);
    }

    public function newsletterConfirm(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));

        if ($token === '') {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
                'error_message' => 'Lien de confirmation invalide.',
            ]);
            return;
        }

        $email = $this->validateNewsletterToken($token);
        if ($email === null) {
            View::render('pages/newsletter', [
                'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
                'error_message' => 'Le lien de confirmation est invalide ou expiré.',
            ]);
            return;
        }

        $subscriberModel = new NewsletterSubscriber();
        $subscriberModel->confirmByEmail($email);

        View::render('pages/newsletter', [
            'page_title' => 'Newsletter Immobilier | ' . $this->cityAreaLabel(),
            'success_message' => 'Inscription confirmée ✅ Vous recevrez désormais notre newsletter.',
        ]);
    }

    public function contactSubmit(): void
    {
        View::render('pages/contact', [
            'page_title' => 'Contact | Estimation Immobilier ' . $this->cityAreaLabel(),
            'success_message' => 'Merci ! Votre message a bien été reçu. Nous vous répondrons sous 24h.',
        ]);
    }




    public function financement(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/financement', [
            'page_title' => "Financement immobilier vendeur | {$area}",
            'meta_description' => "Anticipez votre financement à {$area} : capacité, achat-revente, crédit relais et stratégie de transition pour vendre sereinement.",
        ]);
    }

    public function villes(): void
    {
        View::render('pages/villes', [
            'page_title' => 'Villes couvertes | Estimation immobilière locale',
            'meta_description' => 'Découvrez nos pages locales d’estimation immobilière pour propriétaires vendeurs.',
        ]);
    }

    public function ville(string $slug): void
    {
        $slug = trim(mb_strtolower($slug));
        $map = [
            'toulon' => [
                'name' => 'Toulon',
                'market' => 'Le marché toulonnais reste actif avec des écarts marqués selon la proximité mer et les quartiers résidentiels.',
                'areas' => ['Le Mourillon', 'Le Faron', 'Pont-du-Las', 'Cap Brun'],
            ],
            'hyeres' => [
                'name' => 'Hyères',
                'market' => 'À Hyères, les biens bien valorisés et bien présentés se distinguent rapidement, surtout sur les secteurs recherchés.',
                'areas' => ['Centre-ville', 'Costebelle', 'Giens', 'Hyères Ouest'],
            ],
            'la-seyne-sur-mer' => [
                'name' => 'La Seyne-sur-Mer',
                'market' => 'Le marché seynois offre des opportunités mais impose un positionnement prix précis pour éviter l’allongement des délais.',
                'areas' => ['Les Sablettes', 'Tamaris', 'Balaguier', 'Centre'],
            ],
            'sanary-sur-mer' => [
                'name' => 'Sanary-sur-Mer',
                'market' => 'Sanary attire une demande qualitative ; la cohérence prix/prestations reste la clé pour vendre efficacement.',
                'areas' => ['Port', 'Beaucours', 'La Gorguette', 'Pierredon'],
            ],
            'bordeaux' => [
                'name' => 'Bordeaux',
                'market' => 'Le marché bordelais se segmente fortement : l’adresse, l’état du bien et le DPE influencent fortement la vitesse de vente.',
                'areas' => ['Chartrons', 'Caudéran', 'Bastide', 'Saint-Michel'],
            ],
        ];
        $cityData = $map[$slug] ?? null;
        $cityName = $cityData['name'] ?? ucwords(str_replace('-', ' ', $slug));
        $marketInsight = $cityData['market'] ?? 'Le marché local reste sélectif : les biens bien positionnés se vendent plus vite que la moyenne.';
        $localAreas = $cityData['areas'] ?? [];

        View::render('pages/ville', [
            'city_name' => $cityName,
            'market_insight' => $marketInsight,
            'local_areas' => $localAreas,
            'page_title' => "Estimation immobilière à {$cityName} | Propriétaires vendeurs",
            'meta_description' => "Estimation immobilière à {$cityName} : repères marché local, stratégie vendeurs et CTA pour vendre au bon prix.",
        ]);
    }

    public function mentionsLegales(): void
    {
        View::render('legal/mentions', [
            'page_title' => 'Mentions Légales | Estimation Immobilier ' . $this->cityAreaLabel(),
        ]);
    }

    public function politiqueConfidentialite(): void
    {
        View::render('legal/confidentialite', [
            'page_title' => 'Politique de Confidentialité | Estimation Immobilier ' . $this->cityAreaLabel(),
        ]);
    }

    public function conditionsUtilisation(): void
    {
        View::render('legal/cgu', [
            'page_title' => 'Conditions d\'Utilisation | Estimation Immobilier ' . $this->cityAreaLabel(),
        ]);
    }

    public function rgpd(): void
    {
        View::render('legal/rgpd', [
            'page_title' => 'RGPD | Estimation Immobilier ' . $this->cityAreaLabel(),
        ]);
    }

}
