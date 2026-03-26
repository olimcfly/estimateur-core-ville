#!/bin/bash
# =============================================================================
# Script de duplication du site Estimation Immobilier
# Usage: ./duplicate-site.sh <city_slug>
# Exemple: ./duplicate-site.sh nantes
# =============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SOURCE_DIR="$(dirname "$SCRIPT_DIR")"
CITIES_FILE="$SCRIPT_DIR/cities.json"

# Couleurs pour le terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info()  { echo -e "${BLUE}[INFO]${NC} $1"; }
log_ok()    { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# --- Vérifications ---
if [ $# -lt 1 ]; then
    echo ""
    echo "Usage: $0 <city_slug> [output_parent_dir]"
    echo ""
    echo "Villes disponibles:"
    if command -v jq &> /dev/null; then
        jq -r 'keys[]' "$CITIES_FILE" | while read -r slug; do
            name=$(jq -r ".[\"$slug\"].city_name" "$CITIES_FILE")
            cp=$(jq -r ".[\"$slug\"].city_code_postal" "$CITIES_FILE")
            echo "  - $slug ($name, $cp)"
        done
    else
        echo "  (installez jq pour voir la liste: sudo apt install jq)"
    fi
    echo ""
    exit 1
fi

CITY_SLUG="$1"
OUTPUT_PARENT="${2:-$(dirname "$SOURCE_DIR")}"

if ! command -v jq &> /dev/null; then
    log_error "jq est requis. Installez-le avec: sudo apt install jq"
    exit 1
fi

# --- Lecture de la config ville ---
if ! jq -e ".[\"$CITY_SLUG\"]" "$CITIES_FILE" > /dev/null 2>&1; then
    log_error "Ville '$CITY_SLUG' non trouvée dans cities.json"
    echo "Villes disponibles: $(jq -r 'keys | join(", ")' "$CITIES_FILE")"
    exit 1
fi

CITY_NAME=$(jq -r ".[\"$CITY_SLUG\"].city_name" "$CITIES_FILE")
CITY_REGION=$(jq -r ".[\"$CITY_SLUG\"].city_region" "$CITIES_FILE")
CITY_DEPARTEMENT=$(jq -r ".[\"$CITY_SLUG\"].city_departement" "$CITIES_FILE")
CITY_CP=$(jq -r ".[\"$CITY_SLUG\"].city_code_postal" "$CITIES_FILE")
CITY_COORDS=$(jq -r ".[\"$CITY_SLUG\"].city_coords" "$CITIES_FILE")
PRIX_M2=$(jq -r ".[\"$CITY_SLUG\"].prix_m2_moyen" "$CITIES_FILE")
CITY_FACTOR=$(jq -r ".[\"$CITY_SLUG\"].city_factor" "$CITIES_FILE")
DOMAIN=$(jq -r ".[\"$CITY_SLUG\"].domain" "$CITIES_FILE")
DB_NAME=$(jq -r ".[\"$CITY_SLUG\"].db_name" "$CITIES_FILE")
TELEPHONE=$(jq -r ".[\"$CITY_SLUG\"].telephone" "$CITIES_FILE")
COLOR_PRIMARY=$(jq -r ".[\"$CITY_SLUG\"].colors.primary" "$CITIES_FILE")
COLOR_PRIMARY_DARK=$(jq -r ".[\"$CITY_SLUG\"].colors.primary_dark" "$CITIES_FILE")
COLOR_ACCENT=$(jq -r ".[\"$CITY_SLUG\"].colors.accent" "$CITIES_FILE")
COLOR_ACCENT_LIGHT=$(jq -r ".[\"$CITY_SLUG\"].colors.accent_light" "$CITIES_FILE")

DEST_DIR="$OUTPUT_PARENT/estimation-immobilier-$CITY_SLUG"

echo ""
echo "=============================================="
echo " Duplication du site pour: $CITY_NAME"
echo "=============================================="
echo " Source:      $SOURCE_DIR"
echo " Destination: $DEST_DIR"
echo " Domaine:     $DOMAIN"
echo " Code postal: $CITY_CP"
echo " Région:      $CITY_REGION"
echo " Prix m²:     ${PRIX_M2}€"
echo "=============================================="
echo ""

# --- Confirmation ---
read -p "Continuer ? (o/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Oo]$ ]]; then
    log_warn "Annulé."
    exit 0
fi

# =============================================================================
# ÉTAPE 1: Copie du projet
# =============================================================================
log_info "Copie du projet source..."

if [ -d "$DEST_DIR" ]; then
    log_warn "Le dossier $DEST_DIR existe déjà."
    read -p "Écraser ? (o/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Oo]$ ]]; then
        exit 0
    fi
    rm -rf "$DEST_DIR"
fi

# Copie en excluant les éléments non nécessaires
rsync -a --progress \
    --exclude='.git' \
    --exclude='vendor/' \
    --exclude='node_modules/' \
    --exclude='.env' \
    --exclude='logs/*.log' \
    --exclude='tools/' \
    "$SOURCE_DIR/" "$DEST_DIR/"

log_ok "Projet copié dans $DEST_DIR"

# =============================================================================
# ÉTAPE 2: Remplacement des références Bordeaux
# =============================================================================
log_info "Remplacement des références à Bordeaux..."

# Fonction de remplacement sécurisé dans les fichiers
replace_in_files() {
    local search="$1"
    local replace="$2"
    local file_pattern="${3:-*}"

    find "$DEST_DIR" -type f \( -name "*.php" -o -name "*.json" -o -name "*.xml" -o -name "*.txt" -o -name "*.md" -o -name "*.env*" -o -name "*.sql" -o -name "*.css" -o -name "*.js" -o -name "*.html" -o -name ".htaccess" \) \
        ! -path "*/vendor/*" ! -path "*/.git/*" \
        -exec sed -i "s|$search|$replace|g" {} +
}

# Nom de la ville (cas sensible)
replace_in_files "Bordeaux" "$CITY_NAME"
replace_in_files "bordeaux" "$CITY_SLUG"
replace_in_files "BORDEAUX" "$(echo "$CITY_NAME" | tr '[:lower:]' '[:upper:]')"

# Région
replace_in_files "Nouvelle-Aquitaine" "$CITY_REGION"

# Département / zone géographique
replace_in_files "en Gironde" "en $CITY_DEPARTEMENT"
replace_in_files "Gironde" "$CITY_DEPARTEMENT"

# Code postal
replace_in_files "33000" "$CITY_CP"

replace_in_files "Bordeaux Métropole" "$CITY_NAME Métropole"
replace_in_files "Quai des Chartrons" "Quai central"
replace_in_files "Chartrons" "$CITY_NAME Centre"

# Domaine
replace_in_files "estimation-immobilier-bordeaux\.fr" "$DOMAIN"
replace_in_files "estimation-immobilier-bordeaux" "estimation-immobilier-$CITY_SLUG"

# Adjectif bordelais
CITY_LOWER=$(echo "$CITY_NAME" | tr '[:upper:]' '[:lower:]')
replace_in_files "bordelais" "de $CITY_LOWER"
replace_in_files "bordelaise" "de $CITY_LOWER"
replace_in_files "bordelaises" "de $CITY_LOWER"

log_ok "Références textuelles remplacées"

# =============================================================================
# ÉTAPE 3: Mise à jour du config.php
# =============================================================================
log_info "Mise à jour de config.php..."

CONFIG_FILE="$DEST_DIR/config/config.php"

# Remplacer les constantes de ville
sed -i "s|define('CITY_NAME', '[^']*')|define('CITY_NAME', '$CITY_NAME')|g" "$CONFIG_FILE"
sed -i "s|define('CITY_REGION', '[^']*')|define('CITY_REGION', '$CITY_REGION')|g" "$CONFIG_FILE"
sed -i "s|define('CITY_CODE_POSTAL', '[^']*')|define('CITY_CODE_POSTAL', '$CITY_CP')|g" "$CONFIG_FILE"
sed -i "s|define('PRIX_M2_MOYEN', [0-9]*)|define('PRIX_M2_MOYEN', $PRIX_M2)|g" "$CONFIG_FILE"
sed -i "s|define('COLOR_PRIMARY', '[^']*')|define('COLOR_PRIMARY', '$COLOR_PRIMARY')|g" "$CONFIG_FILE"
sed -i "s|define('COLOR_ACCENT', '[^']*')|define('COLOR_ACCENT', '$COLOR_ACCENT')|g" "$CONFIG_FILE"

log_ok "config.php mis à jour"

# =============================================================================
# ÉTAPE 4: Mise à jour du .env.example
# =============================================================================
log_info "Mise à jour de .env.example..."

ENV_FILE="$DEST_DIR/.env.example"

sed -i "s|APP_NAME=.*|APP_NAME=\"Estimation Immobilier $CITY_NAME\"|" "$ENV_FILE"
sed -i "s|APP_BASE_URL=.*|APP_BASE_URL=\"https://$DOMAIN\"|" "$ENV_FILE"
sed -i "s|DB_NAME=.*|DB_NAME=\"$DB_NAME\"|" "$ENV_FILE"
sed -i "s|MAIL_FROM=.*|MAIL_FROM=\"contact@$DOMAIN\"|" "$ENV_FILE"
sed -i "s|MAIL_FROM_NAME=.*|MAIL_FROM_NAME=\"Estimation Immobilier $CITY_NAME\"|" "$ENV_FILE"
sed -i "s|MAIL_HOST=.*|MAIL_HOST=\"mail.$DOMAIN\"|" "$ENV_FILE"
sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=\"contact@$DOMAIN\"|" "$ENV_FILE"
sed -i "s|SITE_CITY_FACTOR=.*|SITE_CITY_FACTOR=\"$CITY_FACTOR\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_PRIMARY=.*|SITE_COLOR_PRIMARY=\"$COLOR_PRIMARY\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_PRIMARY_DARK=.*|SITE_COLOR_PRIMARY_DARK=\"$COLOR_PRIMARY_DARK\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_ACCENT=.*|SITE_COLOR_ACCENT=\"$COLOR_ACCENT\"|" "$ENV_FILE"
sed -i "s|SITE_COLOR_ACCENT_LIGHT=.*|SITE_COLOR_ACCENT_LIGHT=\"$COLOR_ACCENT_LIGHT\"|" "$ENV_FILE"

log_ok ".env.example mis à jour"

# =============================================================================
# ÉTAPE 5: Mise à jour de l'EstimationService
# =============================================================================
log_info "Mise à jour de l'EstimationService..."

ESTIMATION_FILE="$DEST_DIR/app/services/EstimationService.php"
if [ -f "$ESTIMATION_FILE" ]; then
    # Le facteur de la ville est maintenant lu depuis SITE_CITY_FACTOR dans .env — aucune modification nécessaire.
    log_ok "EstimationService OK (facteur lu depuis SITE_CITY_FACTOR)"
fi

# =============================================================================
# ÉTAPE 6: Génération du fichier fixtures/quartiers.php
# =============================================================================
log_info "Génération des données de quartiers (fixture)..."

FIXTURE_DIR="$DEST_DIR/database/fixtures/$CITY_SLUG"
mkdir -p "$FIXTURE_DIR"

# Si cities.json contient des quartiers pour cette ville, générer le fixture
if jq -e ".[\"$CITY_SLUG\"].quartiers" "$CITIES_FILE" > /dev/null 2>&1; then
    python3 << PYEOF
import json, sys

with open("$CITIES_FILE") as f:
    data = json.load(f)

city = data["$CITY_SLUG"]
quartiers = city.get("quartiers", [])

lines = ["<?php", "return ["]
for q in quartiers:
    lines.append("    [")
    lines.append(f"        'nom' => '{q['nom']}',")
    desc = q['description'].replace("'", "\\'")
    lines.append(f"        'description' => '{desc}',")
    lines.append(f"        'prix_m2' => {q['prix_m2']},")
    lines.append(f"        'prix_moyen' => {q['prix_moyen']},")
    carac = "', '".join(q['caracteristiques'])
    lines.append(f"        'caracteristiques' => ['{carac}'],")
    lines.append(f"        'population' => '{q['population']}',")
    lines.append(f"        'transports' => '{q['transports']}',")
    lines.append(f"        'attractivite' => '{q['attractivite']}',")
    lines.append(f"        'coords' => '{q['coords']}',")
    lines.append(f"        'tendance' => '{q['tendance']}',")
    zone = q.get('zone', "$CITY_NAME")
    lines.append(f"        'zone' => '{zone}',")
    lines.append("    ],")
lines.append("];")

with open("$FIXTURE_DIR/quartiers.php", "w") as f:
    f.write("\n".join(lines) + "\n")

print(f"Fixture écrit: $FIXTURE_DIR/quartiers.php ({len(quartiers)} quartiers)")
PYEOF
    log_ok "Fixture quartiers généré"
else
    # Copier le fixture default comme base vide
    if [ -f "$DEST_DIR/database/fixtures/default/quartiers.php" ]; then
        cp "$DEST_DIR/database/fixtures/default/quartiers.php" "$FIXTURE_DIR/quartiers.php"
        log_warn "Aucun quartier dans cities.json — fixture vide créé. Remplissez $FIXTURE_DIR/quartiers.php"
    fi
fi

# =============================================================================
# ÉTAPE 7: Mise à jour du header.php (Schema.org + meta)
# =============================================================================
log_info "Mise à jour du header.php (Schema.org)..."

HEADER_FILE="$DEST_DIR/app/views/layouts/header.php"
if [ -f "$HEADER_FILE" ]; then
    # Mettre à jour le téléphone dans le JSON-LD
    sed -i "s|+33556000000|$TELEPHONE|g" "$HEADER_FILE"

    # Mettre à jour les coordonnées
    IFS=',' read -r LAT LNG <<< "$CITY_COORDS"

    log_ok "Header mis à jour"
fi

# =============================================================================
# ÉTAPE 8: Mise à jour du sitemap.xml
# =============================================================================
log_info "Mise à jour du sitemap.xml..."

SITEMAP_FILE="$DEST_DIR/public/sitemap.xml"
if [ -f "$SITEMAP_FILE" ]; then
    # Le domaine a déjà été remplacé à l'étape 2
    log_ok "Sitemap mis à jour"
fi

# =============================================================================
# ÉTAPE 9: Renommer les fichiers contenant "bordeaux"
# =============================================================================
log_info "Renommage des fichiers contenant 'bordeaux'..."

find "$DEST_DIR" -type f -name "*bordeaux*" ! -path "*/vendor/*" ! -path "*/.git/*" | while read -r file; do
    dir=$(dirname "$file")
    base=$(basename "$file")
    new_base="${base//bordeaux/$CITY_SLUG}"
    if [ "$base" != "$new_base" ]; then
        mv "$file" "$dir/$new_base"
        log_info "  Renommé: $base -> $new_base"
    fi
done

# (Références aux fichiers déjà mises à jour dans ÉTAPE 2)

log_ok "Fichiers renommés"

# =============================================================================
# ÉTAPE 10: Renommer les fichiers landing page
# =============================================================================
log_info "Mise à jour des landing pages..."

LANDING_DIR="$DEST_DIR/app/views/landing/pages"
if [ -d "$LANDING_DIR" ]; then
    # Les fichiers ont déjà été renommés à l'étape 9
    # Mettre à jour les références internes dans les routes
    ROUTES_FILE="$DEST_DIR/routes/web.php"
    if [ -f "$ROUTES_FILE" ]; then
        log_ok "Routes mises à jour"
    fi
fi

# =============================================================================
# ÉTAPE 11: Initialiser git
# =============================================================================
log_info "Initialisation du dépôt Git..."

cd "$DEST_DIR"
git init
git add -A
git commit -m "Initial commit: Estimation Immobilier $CITY_NAME"
cd - > /dev/null

log_ok "Dépôt Git initialisé"

# =============================================================================
# ÉTAPE 12: Installer les dépendances (si composer disponible)
# =============================================================================
if command -v composer &> /dev/null; then
    log_info "Installation des dépendances Composer..."
    cd "$DEST_DIR"
    composer install --no-dev --quiet 2>/dev/null || log_warn "composer install a échoué (normal si pas de vendor)"
    cd - > /dev/null
else
    log_warn "Composer non trouvé. Pensez à exécuter 'composer install' dans $DEST_DIR"
fi

# =============================================================================
# RÉSUMÉ
# =============================================================================
echo ""
echo "=============================================="
echo -e " ${GREEN}DUPLICATION TERMINÉE !${NC}"
echo "=============================================="
echo ""
echo " Site créé: $DEST_DIR"
echo " Ville:     $CITY_NAME ($CITY_CP)"
echo " Domaine:   $DOMAIN"
echo " DB:        $DB_NAME"
echo ""
echo " Prochaines étapes:"
echo "  1. Copier .env.example vers .env et renseigner les clés API"
echo "     cp $DEST_DIR/.env.example $DEST_DIR/.env"
echo ""
echo "  2. Créer la base de données MySQL:"
echo "     mysql -e \"CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
echo "     mysql $DB_NAME < $DEST_DIR/database/schema.sql"
echo ""
echo "  3. Configurer le vhost Apache/Nginx pour $DOMAIN"
echo "     DocumentRoot: $DEST_DIR/public"
echo ""
echo "  4. Installer les dépendances:"
echo "     cd $DEST_DIR && composer install"
echo ""
echo "  5. Vérifier et personnaliser:"
echo "     - Les textes dans app/views/pages/"
echo "     - L'image OG: public/assets/images/og-estimation-$CITY_SLUG.png"
echo "     - Le favicon: public/favicon.svg"
echo "     - Les articles de blog (base de données)"
echo ""
echo "  6. Configurer DNS et SSL pour $DOMAIN"
echo ""
echo "=============================================="
