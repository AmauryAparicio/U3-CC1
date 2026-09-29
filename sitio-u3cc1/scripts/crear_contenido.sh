#!/usr/bin/env bash
# Crea páginas, menú y ajustes del sitio (idempotente sobre una instalación limpia).
set -e
cd "$(dirname "$0")/../wp"
W="wp --allow-root"
C=../scripts/contenido
$W theme activate cafe-nahual
$W plugin activate nahual-contacto
$W post delete 1 2 --force 2>/dev/null || true
mk() { $W post create "$C/$1.html" --post_type=page --post_status=publish --post_title="$2" --post_name="$3" --porcelain; }
ID_INI=$(mk inicio "Inicio" inicio)
ID_HIS=$(mk historia "Historia" historia)
ID_PRO=$(mk productos "Productos y servicios" productos-y-servicios)
ID_ENL=$(mk enlaces "Enlaces de interés" enlaces-de-interes)
ID_CON=$(mk contacto "Contacto" contacto)
$W option update show_on_front page
$W option update page_on_front "$ID_INI"
$W option update blogdescription "Tostadores de café de altura · México"
$W rewrite structure '/%postname%/' --hard
$W menu create "Principal"
for id in $ID_INI $ID_HIS $ID_PRO $ID_ENL $ID_CON; do $W menu item add-post principal $id; done
$W menu location assign principal principal
$W rewrite flush --hard
