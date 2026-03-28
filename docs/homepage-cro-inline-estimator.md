# Refonte homepage orientée réduction de friction vers estimation

## A. Diagnostic CRO homepage

- Les CTA poussaient majoritairement vers `/estimation` => saut de page inutile.
- Pas de retour immédiat de valeur sur la homepage.
- Friction mobile élevée (scroll + changement de contexte).
- Opportunité ratée de pré-qualifier l’intention vendeur dès le hero.

## B. Structure cible

1. Hero vendeur + preuve locale
2. Mini-estimateur inline (3 champs)
3. Résultat instantané (fourchette indicative)
4. CTA vers avis détaillé (`/estimation`) pour la phase lead qualifié
5. Sections pédagogiques/SEO inchangées

## C. Formulaire inline recommandé

- Champs : type bien, surface, ville (+ pièces caché=3)
- Submit JS vers `/api/estimation`
- Affichage inline :
  - fourchette basse/haute
  - base €/m² indicative
  - CTA “Recevoir un avis de valeur détaillé”
- Fallback sans JS : action POST existante `/estimation`

## D. Fichiers à modifier

- `app/views/pages/home.php`
- `public/assets/css/app.css`

## E. Tests desktop/mobile

- Desktop :
  - submit inline affiche une fourchette sans changer de page
  - CTA final renvoie bien vers formulaire détaillé
- Mobile :
  - formulaire visible sans friction
  - bloc résultat lisible et non intrusif
  - aucun chevauchement avec éléments sticky
