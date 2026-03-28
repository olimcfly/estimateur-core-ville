# CTA sticky mobile intelligent (conseiller-core)

## A. Stratégie UX

- CTA flottant discret, unique, au-dessus de la bottom-nav.
- Toujours orienté action business immédiate (estimation ou appel).
- Apparition uniquement mobile, avec animation légère.
- Comportement non intrusif sur `/estimation` : masqué quand le formulaire est visible.

## B. Variante CTA recommandée

- **Sur `/estimation`** : `Estimation gratuite`
- **Sur autres pages** :
  - `Appeler le conseiller` si téléphone disponible,
  - sinon fallback `Estimation gratuite`

## C. Fichiers à modifier

- `app/views/layouts/sticky-cta.php` (logique de contexte + markup CTA)
- `public/assets/css/app.css` (style CTA flottant + spacing mobile body)
- `app/views/layouts/footer.php` (script existant de visibilité déjà branché)

## D. Code exact

- CTA flottant `.sticky-cta` injecté avant la bottom-nav.
- Choix du texte/href via contexte (`/estimation` vs autres pages + téléphone).
- Réutilisation du script déjà présent (`.sticky-cta--visible`) pour afficher/masquer.

## E. Tests mobile

- `/estimation` :
  - CTA visible hors formulaire.
  - CTA masqué quand le formulaire est à l’écran.
- `/`, `/services`, `/contact` :
  - CTA visible.
  - si téléphone configuré => “Appeler le conseiller”.
- Vérifier qu’aucun champ n’est masqué en bas d’écran.
