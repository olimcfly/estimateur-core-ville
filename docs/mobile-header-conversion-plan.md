# Optimisation header mobile (conversion)

## A. Diagnostic mobile

- Le téléphone n’était pas priorisé visuellement sur petit écran.
- Le CTA estimation pouvait être relégué dans le menu, donc moins immédiat.
- Structure d’actions peu robuste selon largeur (risque d’encombrement).
- Besoin d’un rendu sobre/pro sans éléments décoratifs.

## B. Structure header mobile idéale

Barre unique, lisible en 1 regard :

1. Brand (logo + nom court)
2. Bouton téléphone (si disponible)
3. CTA estimation court et explicite (“Estimer”)
4. Bouton menu

## C. Modifications à faire

- Introduire deux variantes téléphone :
  - desktop : numéro texte complet,
  - mobile : bouton icône circulaire.
- Ajouter un CTA mobile toujours visible dans le header (`Estimer`).
- Cacher les variantes desktop sur <=1023px.
- Garder le menu mobile pour navigation secondaire.

## D. Code exact

- `app/views/layouts/header.php`
  - Ajout `site-header__phone--desktop`, `site-header__phone--mobile`, `site-header__cta--mobile`.
- `public/assets/css/shell.css`
  - Styles des variantes mobile téléphone/CTA.
  - Règles de visibilité responsive pour éviter les doublons d’actions.

## E. Tests smartphone

- iPhone/Android portrait:
  - téléphone visible et cliquable,
  - CTA “Estimer” visible sans ouvrir le menu,
  - menu burger fonctionnel.
- Vérifier qu’il n’y a qu’un seul set d’actions (pas de doublons).
- Vérifier absence de débordement horizontal.
