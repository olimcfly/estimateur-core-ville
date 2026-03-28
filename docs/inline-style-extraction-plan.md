# Extraction des styles inline critiques (front public)

## A. Styles inline repérés

Audit rapide des vues front :

- `app/views/estimation/index.php` : nombreux styles inline dans les blocs critiques de conversion (testimonial, alertes, CTA, cards comparatives, CTA final).
- `app/views/pages/home.php` : styles inline principalement décoratifs (icônes/couleurs/espacements).
- `app/views/pages/processus_estimation.php` : très forte densité inline (layout + cards + typographie).
- `app/views/pages/contact.php`, `newsletter.php`, `services.php`, `guides.php`, `quartiers.php` : inline ponctuels.
- `app/views/layouts/header.php` : avant refactor, styles structurels inline (désormais sortis vers `shell.css`, inline limité aux tokens runtime).

## B. Priorité d’extraction

Priorité haute (impact conversion + maintenance) :

1. `app/views/estimation/index.php`
2. `app/views/layouts/header.php` (déjà traité via `shell.css`)

Priorité moyenne :

3. `app/views/pages/home.php`
4. `app/views/pages/contact.php`

Priorité basse :

5. `processus_estimation.php`, `newsletter.php`, `guides.php`, `quartiers.php`

## C. Nouvelle organisation fichiers

- `public/assets/css/shell.css` : shell global (header/navigation responsive).
- `public/assets/css/app.css` : styles front partagés + classes extraites des vues (dont estimation).
- (phase 2 recommandée) `public/assets/css/pages/*.css` pour découpler les pages très volumineuses.

## D. Refactor minimal appliqué

- Extraction des styles inline critiques de `app/views/estimation/index.php` vers des classes CSS préfixées `est-inline-*`.
- Ajout des classes correspondantes dans `public/assets/css/app.css`.
- Objectif : zéro changement de structure HTML majeur, rendu visuel conservé, maintenance améliorée.

## E. Vérifications visuelles

Desktop :

- Hero estimation + formulaire : alignements, CTA pleine largeur, alertes erreurs.
- Section comparaison : bordures accent/primary, icônes colorées, espacements.
- CTA final : fond dégradé, centrage, taille bouton.

Mobile :

- Largeur boutons, lisibilité des blocs, espacement vertical des listes.
- Aucun débordement horizontal sur cards/CTA.
