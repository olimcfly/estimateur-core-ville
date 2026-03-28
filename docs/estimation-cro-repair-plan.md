# Réparation CRO de `/estimation` (white-label local immobilier)

## A. Diagnostic CRO

- Promesse initiale trop générique, pas assez orientée vendeur.
- Réassurance légale insuffisamment explicite sur la différence estimation vs avis de valeur.
- Manque de preuve locale immédiatement visible (quartiers / couverture locale).
- Bloc conseiller fragile quand les données `advisor_*` sont incomplètes.
- Risque mobile : trop de friction si les preuves de confiance sont reléguées bas.

## B. Structure cible de la page

1. Hero orienté vendeur (promesse claire + bénéfice immédiat).
2. Formulaire court (3 champs) + CTA principal.
3. Réassurance légale explicite (indicatif, sans mandat obligatoire).
4. Bloc confiance local (conseiller + témoignages + raisons d’avancer).
5. Comparatif pédagogique estimation vs avis de valeur.
6. CTA final de relance.

## C. Blocs à ajouter / corriger / supprimer

### Ajouter
- Preuve locale visible en hero (`quartiers couverts` / `zone locale`).
- Bloc légal synthétique dédié.
- JSON-LD `Service` orienté estimation vendeur locale.

### Corriger
- Titre H1 et méta orientés “vendeur”.
- Bloc conseiller : fallback nom/zone/tagline/expérience + CTA contact (tel/email/contact).
- Compatibilité CSS trust-block avec le markup actuel.

### Supprimer
- Rien de structurel (refactor minimal, sans casse).

## D. Fichiers à modifier

- `app/views/estimation/index.php`
- `app/views/estimation/partials/trust_block.php`
- `public/assets/css/app.css`

## E. Tests manuels desktop/mobile

Desktop:
- Vérifier hero vendeur + preuve locale + CTA.
- Vérifier affichage bloc légal.
- Vérifier bloc conseiller même sans photo/nom en settings.

Mobile:
- Vérifier lisibilité des nouveaux blocs.
- Vérifier CTA principaux pleine largeur.
- Vérifier absence de débordement horizontal.
