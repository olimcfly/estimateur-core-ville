#!/bin/bash
# =============================================================================
# Dupliquer le site pour TOUTES les villes configurées dans cities.json
# Usage: ./duplicate-all.sh [output_parent_dir]
# =============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CITIES_FILE="$SCRIPT_DIR/cities.json"
OUTPUT_PARENT="${1:-$(dirname "$(dirname "$SCRIPT_DIR")")}"

if ! command -v jq &> /dev/null; then
    echo "[ERROR] jq est requis. Installez-le avec: sudo apt install jq"
    exit 1
fi

echo ""
echo "=============================================="
echo " Duplication multi-villes"
echo "=============================================="
echo ""

CITIES=$(jq -r 'keys[]' "$CITIES_FILE")
TOTAL=$(echo "$CITIES" | wc -l)
CURRENT=0

for slug in $CITIES; do
    CURRENT=$((CURRENT + 1))
    name=$(jq -r ".[\"$slug\"].city_name" "$CITIES_FILE")
    echo ""
    echo "[$CURRENT/$TOTAL] Duplication pour $name ($slug)..."
    echo "----------------------------------------------"

    # Exécution non-interactive
    DUPLICATION_FORCE_YES=1 "$SCRIPT_DIR/duplicate-site.sh" "$slug" "$OUTPUT_PARENT"
done

echo ""
echo "=============================================="
echo " Toutes les duplications sont terminées !"
echo "=============================================="
echo ""
echo " Sites créés:"
for slug in $CITIES; do
    name=$(jq -r ".[\"$slug\"].city_name" "$CITIES_FILE")
    echo "  - $OUTPUT_PARENT/estimation-immobilier-$slug/ ($name)"
done
echo ""
