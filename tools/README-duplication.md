# Duplication Multi-Villes - Estimation Immobilier

## Principe

Ce système permet de dupliquer le site d'estimation immobilière de Bordeaux pour d'autres villes en une seule commande. Le script adapte automatiquement :

- Le nom de la ville, la région, le code postal
- Les quartiers avec prix/m², tendances et caractéristiques
- Les couleurs de marque (identité visuelle unique par ville)
- Le domaine, les emails, la base de données
- Le SEO (meta tags, Schema.org, sitemap, Open Graph)
- Le service d'estimation (facteur de prix par ville)
- Les landing pages Google Ads

## Prérequis

```bash
sudo apt install jq rsync python3
```

## Usage

### Dupliquer pour une seule ville

```bash
cd tools/
./duplicate-site.sh nantes
```

Le site sera créé dans `../estimation-immobilier-nantes/`.

Vous pouvez aussi spécifier un dossier de destination :

```bash
./duplicate-site.sh nantes /var/www/
```

### Dupliquer pour toutes les villes

```bash
./duplicate-all.sh
```

Crée les 4 sites d'un coup : Nantes, Nandy, Angers, Lannion.

## Villes configurées

| Ville   | Slug    | CP    | Région            | Prix m² moyen | Couleur primaire |
|---------|---------|-------|-------------------|---------------|------------------|
| Nantes  | nantes  | 44000 | Pays de la Loire  | 3 800 €       | Bleu marine      |
| Nandy   | nandy   | 77176 | Île-de-France     | 3 200 €       | Vert forêt       |
| Angers  | angers  | 49000 | Pays de la Loire  | 3 200 €       | Violet           |
| Lannion | lannion | 22300 | Bretagne          | 2 100 €       | Bleu roi         |

## Ajouter une nouvelle ville

1. Éditer `cities.json` et ajouter une entrée avec la structure suivante :

```json
{
  "ma-ville": {
    "city_name": "Ma Ville",
    "city_slug": "ma-ville",
    "city_region": "Ma Région",
    "city_departement": "Mon Département",
    "city_code_postal": "00000",
    "city_coords": "48.0000,2.0000",
    "prix_m2_moyen": 3000,
    "city_factor": 1.0,
    "domain": "estimation-immobilier-ma-ville.fr",
    "db_name": "estimation_immobilier_ma_ville",
    "telephone": "+33100000000",
    "colors": {
      "primary": "#000000",
      "primary_dark": "#000000",
      "accent": "#000000",
      "accent_light": "#000000"
    },
    "quartiers": [
      {
        "nom": "Centre-Ville",
        "description": "Description du quartier...",
        "prix_m2": 3500,
        "prix_moyen": 300000,
        "caracteristiques": ["Tag1", "Tag2", "Tag3", "Tag4"],
        "population": "~10000 habitants",
        "transports": "Bus, Tram",
        "attractivite": "Haute",
        "coords": "48.0000,2.0000",
        "tendance": "+3.5%"
      }
    ]
  }
}
```

2. Lancer la duplication :

```bash
./duplicate-site.sh ma-ville
```

## Après la duplication

Pour chaque site dupliqué, effectuez ces étapes :

### 1. Configuration

```bash
cd estimation-immobilier-nantes/
cp .env.example .env
nano .env  # Renseigner les clés API et credentials
```

### 2. Base de données

```bash
mysql -u root -p -e "CREATE DATABASE estimation_immobilier_nantes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p estimation_immobilier_nantes < database/schema.sql
```

### 3. Dépendances PHP

```bash
composer install
```

### 4. Hébergement (o2switch ou autre)

- Créer un nouveau compte/sous-domaine pour le domaine
- Pointer le DocumentRoot vers le dossier `public/`
- Configurer le certificat SSL (Let's Encrypt)
- Configurer SPF/DKIM pour les emails

### 5. Personnalisations manuelles

- **Image OG** : Créer `public/assets/images/og-estimation-<ville>.png` (1200x630px)
- **Favicon** : Adapter `public/favicon.svg` aux couleurs de la ville
- **Contenu blog** : Les articles de Bordeaux seront copiés, pensez à les adapter
- **Landing pages** : Vérifier et ajuster le contenu des pages Google Ads

## Structure des fichiers

```
tools/
├── cities.json           # Configuration de toutes les villes
├── duplicate-site.sh     # Script de duplication (une ville)
├── duplicate-all.sh      # Script de duplication (toutes les villes)
└── README-duplication.md # Ce fichier
```
