#!/bin/bash
#
# Script d'installation de l'admin sur un autre site
# Copie uniquement les fichiers admin SANS écraser la config personnalisée
#
# Usage: ./tools/install-admin.sh /chemin/vers/site-destination
#

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Vérifier l'argument
if [ -z "$1" ]; then
    echo -e "${RED}Erreur: Chemin de destination manquant${NC}"
    echo ""
    echo "Usage: $0 /chemin/vers/site-destination"
    echo ""
    echo "Exemple: $0 /var/www/mon-autre-site"
    exit 1
fi

DEST="$1"
SOURCE="$(cd "$(dirname "$0")/.." && pwd)"

# Vérifier que la source existe
if [ ! -d "$SOURCE/app" ]; then
    echo -e "${RED}Erreur: Dossier source invalide: $SOURCE${NC}"
    exit 1
fi

# Vérifier que la destination existe
if [ ! -d "$DEST" ]; then
    echo -e "${RED}Erreur: Le dossier de destination n'existe pas: $DEST${NC}"
    echo "Créer le dossier d'abord avec: mkdir -p $DEST"
    exit 1
fi

echo ""
echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Installation Admin - Immobilier       ${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""
echo -e "Source:      ${GREEN}$SOURCE${NC}"
echo -e "Destination: ${GREEN}$DEST${NC}"
echo ""

# ============================================================
# FICHIERS QUI NE SERONT JAMAIS ÉCRASES (config personnalisée)
# ============================================================
PROTECTED_FILES=(
    "config/config.php"
    ".env"
    ".env.local"
    ".htaccess"
    "database/"
    "logs/"
    "vendor/"
    "public/assets/images/ai-generated/"
)

echo -e "${YELLOW}Fichiers protégés (ne seront PAS écrasés):${NC}"
for f in "${PROTECTED_FILES[@]}"; do
    echo -e "  - $f"
done
echo ""

# Compteur
COPIED=0
SKIPPED=0
CREATED_DIRS=0

# Fonction pour copier un fichier avec sécurité
copy_file() {
    local rel_path="$1"
    local src="$SOURCE/$rel_path"
    local dst="$DEST/$rel_path"

    # Vérifier si le fichier est protégé
    for protected in "${PROTECTED_FILES[@]}"; do
        if [[ "$rel_path" == "$protected"* ]]; then
            echo -e "  ${YELLOW}[SKIP]${NC} $rel_path (protégé)"
            SKIPPED=$((SKIPPED + 1))
            return
        fi
    done

    # Créer le dossier parent si nécessaire
    local dir=$(dirname "$dst")
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        CREATED_DIRS=$((CREATED_DIRS + 1))
    fi

    # Copier le fichier
    cp "$src" "$dst"
    echo -e "  ${GREEN}[OK]${NC}   $rel_path"
    COPIED=$((COPIED + 1))
}

# ============================================================
# 1. DOSSIER admin/ (Google Ads module)
# ============================================================
echo -e "${BLUE}[1/6] Dossier admin/ (module Google Ads)${NC}"
while IFS= read -r file; do
    rel_path="${file#$SOURCE/}"
    copy_file "$rel_path"
done < <(find "$SOURCE/admin" -type f 2>/dev/null)
echo ""

# ============================================================
# 2. CONTRÔLEURS ADMIN
# ============================================================
echo -e "${BLUE}[2/6] Contrôleurs Admin${NC}"
for file in "$SOURCE"/app/controllers/Admin*.php; do
    if [ -f "$file" ]; then
        rel_path="${file#$SOURCE/}"
        copy_file "$rel_path"
    fi
done
# Aussi copier AuthController (login admin)
if [ -f "$SOURCE/app/controllers/AuthController.php" ]; then
    copy_file "app/controllers/AuthController.php"
fi
echo ""

# ============================================================
# 3. VUES ADMIN
# ============================================================
echo -e "${BLUE}[3/6] Vues Admin${NC}"
while IFS= read -r file; do
    rel_path="${file#$SOURCE/}"
    copy_file "$rel_path"
done < <(find "$SOURCE/app/views/admin" -type f 2>/dev/null)
# Layout admin
if [ -f "$SOURCE/app/views/layouts/admin.php" ]; then
    copy_file "app/views/layouts/admin.php"
fi
echo ""

# ============================================================
# 4. MODÈLES (nécessaires pour l'admin)
# ============================================================
echo -e "${BLUE}[4/6] Modèles${NC}"
ADMIN_MODELS=(
    "app/models/AdminModule.php"
    "app/models/AdminNotification.php"
    "app/models/AdminUser.php"
    "app/models/Lead.php"
    "app/models/LeadActivity.php"
    "app/models/LeadNote.php"
    "app/models/Article.php"
    "app/models/Achat.php"
    "app/models/Actualite.php"
    "app/models/Partenaire.php"
    "app/models/DesignTemplate.php"
    "app/models/NewsletterSubscriber.php"
    "app/models/RssSource.php"
    "app/models/RssArticle.php"
)
for model in "${ADMIN_MODELS[@]}"; do
    if [ -f "$SOURCE/$model" ]; then
        copy_file "$model"
    fi
done
echo ""

# ============================================================
# 5. SERVICES (nécessaires pour l'admin)
# ============================================================
echo -e "${BLUE}[5/6] Services${NC}"
ADMIN_SERVICES=(
    "app/services/AIService.php"
    "app/services/ActualiteService.php"
    "app/services/ImageGeneratorService.php"
    "app/services/LeadNotificationService.php"
    "app/services/LeadScoringService.php"
    "app/services/Mailer.php"
    "app/services/PerplexityService.php"
    "app/services/SeoAnalyzerService.php"
    "app/services/SmtpAuthClient.php"
    "app/services/SmtpLogger.php"
    "app/services/UtmTrackingService.php"
    "app/services/RssFeedService.php"
)
for service in "${ADMIN_SERVICES[@]}"; do
    if [ -f "$SOURCE/$service" ]; then
        copy_file "$service"
    fi
done
echo ""

# ============================================================
# 6. FICHIERS CORE (framework nécessaire)
# ============================================================
echo -e "${BLUE}[6/6] Fichiers Core${NC}"
CORE_FILES=(
    "app/core/bootstrap.php"
    "app/core/Config.php"
    "app/core/Database.php"
    "app/core/Router.php"
    "app/core/Validator.php"
    "app/core/View.php"
    "app/core/helpers.php"
    "routes/web.php"
)
for core in "${CORE_FILES[@]}"; do
    if [ -f "$SOURCE/$core" ]; then
        copy_file "$core"
    fi
done
echo ""

# ============================================================
# RÉSUMÉ
# ============================================================
echo -e "${BLUE}=========================================${NC}"
echo -e "${BLUE}  Résumé de l'installation               ${NC}"
echo -e "${BLUE}=========================================${NC}"
echo ""
echo -e "  Fichiers copiés:     ${GREEN}$COPIED${NC}"
echo -e "  Fichiers protégés:   ${YELLOW}$SKIPPED${NC}"
echo -e "  Dossiers créés:      ${BLUE}$CREATED_DIRS${NC}"
echo ""
echo -e "${YELLOW}RAPPELS:${NC}"
echo -e "  1. Vérifier que ${GREEN}config/config.php${NC} existe sur le site cible"
echo -e "  2. Vérifier que ${GREEN}.env${NC} est configuré sur le site cible"
echo -e "  3. Lancer ${GREEN}composer install${NC} si vendor/ n'existe pas"
echo -e "  4. Exécuter les migrations SQL si nécessaire:"
echo -e "     ${GREEN}mysql -u USER -p DATABASE < database/migration_rss.sql${NC}"
echo -e "     ${GREEN}mysql -u USER -p DATABASE < database/migration_leads.sql${NC}"
echo ""
echo -e "${GREEN}Installation terminée !${NC}"
