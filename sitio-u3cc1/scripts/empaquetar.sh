#!/usr/bin/env bash
# Genera el paquete de migración. Uso: ./empaquetar.sh https://mi-sitio.infinityfreeapp.com
# - Exporta la BD con las URLs reemplazadas (search-replace sobre serializados incluido).
# - Comprime wp-content.
set -euo pipefail
NUEVA_URL="${1:?Indica la URL final, p. ej. https://cafenahual.infinityfreeapp.com}"
NUEVA_URL="${NUEVA_URL%/}"
VIEJA_URL="http://localhost:8080"
RAIZ="$(cd "$(dirname "$0")/.." && pwd)"
SALIDA="$RAIZ/migracion"
cd "$RAIZ/wp"
mkdir -p "$SALIDA"
wp --allow-root search-replace "$VIEJA_URL" "$NUEVA_URL" --all-tables --export="$SALIDA/cafe-nahual-db.sql" >/dev/null
# Comprobación: no debe quedar ninguna referencia a localhost (los guid también se reemplazan; en un proyecto escolar es inocuo)
if grep -q "localhost:8080" "$SALIDA/cafe-nahual-db.sql"; then
  echo "ERROR: el .sql aún contiene localhost:8080" >&2; exit 1
fi
rm -f "$SALIDA/wp-content.zip"
zip -qr "$SALIDA/wp-content.zip" wp-content -x "wp-content/cache/*" "wp-content/upgrade/*" "wp-content/themes/twenty*" "wp-content/plugins/akismet/*"
echo "Listo: $SALIDA/cafe-nahual-db.sql y $SALIDA/wp-content.zip"
ls -lh "$SALIDA"
