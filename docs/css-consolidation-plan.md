# Unification CSS — conseiller-core

## A. Cartographie CSS

### CSS effectivement chargées (référencées dans les vues)

- `/assets/css/app.css` (layout public + admin).  
  Références: `app/views/layouts/header.php`, `app/views/layouts/admin.php`.
- `/assets/css/shell.css` (nouvelle couche canonique header/nav mobile-first).  
  Référence: `app/views/layouts/header.php`.
- `/assets/css/landing.css` (pages landing LP dédiées).  
  Référence: `app/views/landing/layout.php`.
- `/assets/css/guide.css` (guide Google Ads admin).  
  Référence: `app/views/admin/landing-guide.php`.
- `/assets/css/google-ads.css` (module admin Google Ads).  
  Références: `app/views/admin/gads-campaigns/*`.

### CSS présentes mais non référencées actuellement

- `public/assets/css/gmb.css` (aucune référence trouvée).
- `front/css/estimation.css` (système front legacy non branché sur le routeur MVC actuel).

## B. CSS à conserver / supprimer

### À conserver (actif)

- `public/assets/css/app.css`
- `public/assets/css/shell.css`
- `public/assets/css/landing.css`
- `public/assets/css/guide.css`
- `public/assets/css/google-ads.css`

### À supprimer / déprécier (mort ou doublon)

- `front/css/estimation.css` (legacy non utilisé côté rendu actuel).
- `public/assets/css/gmb.css` (à confirmer fonctionnellement, puis supprimer si non utilisé).

### Doublons majeurs traités

- Styles header (`.site-header*`) dupliqués entre inline PHP et `app.css`.
- Consolidation réalisée vers `shell.css` + tokens dynamiques centralisés.

## C. Design tokens recommandés

Base minimale (white-label, mobile-first) :

- **Couleurs**
  - `--bg`, `--surface`, `--text`, `--muted`
  - `--primary`, `--primary-dark`
  - `--accent`, `--accent-light`
  - `--border`
  - `--success`, `--warning`, `--danger`, `--info`
- **Typo**
  - `--font-display`
  - (body via stack système + DM Sans déjà existante)
- **Spacing**
  - échelle recommandée: `4, 8, 12, 16, 24, 32` (px) via variables futures (`--space-*`)
- **Radius**
  - base recommandée: `10px`, `12px`, `16px`, `999px`
- **Shadow**
  - `--shadow-sm`, `--shadow-md`, `--shadow-lg` (tokens à normaliser progressivement)
- **CTA**
  - `--cta-bg: var(--accent)`
  - `--cta-color: var(--text-inverse, #fff)`
  - `--cta-radius: 10px`
  - `--cta-weight: 700`

## D. Plan de migration

1. **Stabiliser le shell canonique**
   - Garder uniquement tokens dynamiques en inline (`:root`).
   - Déporter tout style structurel header/nav/mobile vers `shell.css`.
2. **Éviter les conflits**
   - Supprimer surcharges `.site-header*` dans `app.css` (fait).
3. **Segmenter par domaine UI**
   - `shell.css` = chrome global (header/nav/footer nav mobile)
   - `app.css` = composants/pages public
   - CSS modules admin/LP conservées séparées
4. **Nettoyage**
   - Retirer fichiers legacy non branchés après vérif env de prod/staging.
5. **Industrialisation multi-sites**
   - S’appuyer exclusivement sur tokens + settings DB pour marque/couleurs/CTA.

## E. Tests de non-régression visuelle

### Checklist desktop

- Header sticky: logo, nav, téléphone, CTA visibles.
- Etats hover/focus nav + CTA.
- Footer: colonnes, CTA band, newsletter, trust badges.

### Checklist mobile

- Burger ouvre/ferme proprement.
- Menu mobile: liens, téléphone, CTA.
- Footer accordéon: expand/collapse.
- Navigation mobile bottom (sticky CTA) inchangée.

### Vérifications techniques minimales

- Lint PHP des layouts.
- Smoke test rendu pages: `/`, `/estimation`, `/services`, `/contact`, `/blog`.
