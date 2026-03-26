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
        return (string) (Config::get('city.name', '') ?: 'Bordeaux');
    }

    private function cityAreaLabel(): string
    {
        $city = $this->cityName();
        return (string) (Config::get('city.region', '') ?: $city . ' et Métropole');
    }

    public function home(): void
    {
        $area = $this->cityAreaLabel();
        View::render('pages/home', [
            'page_title' => "Estimation Immobilier {$area} | Avis de Valeur Gratuit",
        ]);
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
            'page_title' => "Contact | Estimation Immobilier {$area}",
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
