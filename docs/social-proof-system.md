# Système de preuves sociales dynamiques (conseiller-core)

## A. Liste des preuves sociales recommandées

Minimum utile conversion :

1. Nombre d’avis Google + note moyenne
2. Délai moyen de réponse
3. Nombre de vendeurs/clients accompagnés
4. Nombre de ventes accompagnées
5. Nombre de secteurs couverts
6. Label d’accompagnement local
7. Lien Google Maps / fiche avis

## B. Priorité d’implémentation

P1 :
- Avis Google (count + rating)
- Délai moyen
- Accompagnement local

P2 :
- Clients accompagnés
- Ventes accompagnées
- Secteurs couverts

P3 :
- Lien Maps enrichi + variantes par page

## C. Mapping DB / fallback

Clés SiteSettings proposées :

- `social_proof_google_reviews_count` (int)
- `social_proof_google_rating` (string)
- `social_proof_avg_delay_hours` (int)
- `social_proof_clients_supported` (int)
- `social_proof_sales_count` (int)
- `social_proof_sectors_count` (int)
- `social_proof_local_support_label` (string)
- `social_proof_google_maps_url` (url)

Fallbacks :

- `avg_delay_hours` => 24
- `sectors_count` => nombre de quartiers de `getSiteConfig()`
- `local_support_label` => “Accompagnement local à {ville}”
- métriques absentes => bloc masqué proprement

## D. Blocs UI recommandés

1. **Hero estimation** : mini grille de preuves (badges discrets)
2. **Trust block** : chips “ventes / secteurs / avis Google”

Design :
- compact
- lisible mobile
- sans surcharge visuelle

## E. Tests manuels

- Sans settings :
  - fallback local + délai 24h visibles
  - pas de trous visuels
- Avec settings complets :
  - badges/chips affichent les bonnes valeurs
  - lien Google Maps cliquable
- Mobile :
  - badges non tronqués
  - pas de débordement horizontal
