# Notas de entorno LOCAL (solo pruebas; no son credenciales de hosting)

Este sitio se construyó y corre en **WSL2 (Ubuntu) sobre Windows**, no con XAMPP: PHP, MariaDB, wp-cli y
phpMyAdmin están instalados con Homebrew (Linuxbrew) en `/home/linuxbrew/.linuxbrew/`, porque el equipo
no tenía esas herramientas y el instalador gráfico de XAMPP es específico de Windows. Cumplen el mismo
papel (servidor PHP, base de datos y administrador de BD). Ver `documento/evidencias.template.html`,
sección 2, para la explicación que va en el PDF.

| Elemento | Valor |
|---|---|
| Sitio local | http://localhost:8080 |
| phpMyAdmin local | http://localhost:8081 |
| Base de datos | `wp_cafenahual` (utf8mb4) |
| Usuario BD | `wpuser` / `wp1234` |
| Usuario root MariaDB | sin contraseña (socket `/tmp/mysql.sock`) |
| Admin WordPress | `admin` / `admin1234` (http://localhost:8080/wp-admin) |
| Tabla del formulario | `wp_contactos` (id, nombre, correo, telefono, mensaje, fecha) |

## Levantar todo de nuevo (en este WSL2, tras reiniciar Windows)
```bash
BREW=/home/linuxbrew/.linuxbrew
$BREW/opt/mariadb/bin/mariadbd --datadir=$BREW/var/mysql --socket=/tmp/mysql.sock --port=3306 --bind-address=127.0.0.1 &

cd sitio-u3cc1/wp
$BREW/bin/php -S 127.0.0.1:8080 -t . ../scripts/router.php &

$BREW/bin/php -S 127.0.0.1:8081 -t $BREW/share/phpmyadmin &
# requiere que scripts/pma_config.inc.php esté copiado a $BREW/share/phpmyadmin/config.inc.php
# (el symlink original de phpMyAdmin apunta a /etc, que no es escribible sin sudo; se reemplazó por un archivo real)
```

## Regenerar capturas y PDF
```bash
cd sitio-u3cc1/scripts
node capturas.mjs           # sitio + 2 envíos reales del formulario
node capturas_pma.mjs       # phpMyAdmin: BD, estructura y registros de wp_contactos
node generar_pdf.mjs        # documento/Evidencias_U3CC1.pdf
```
`scripts/capturas_instalador.mjs` genera aparte, en una copia y BD temporales (`wp_demo_installer`,
borrada al terminar), las dos capturas del asistente web de WordPress — no toca el sitio real.

## Pendiente: XAMPP real
Las dos capturas de "instalación de XAMPP" y "panel de control de XAMPP" (sección 2 del PDF) requieren
XAMPP para Windows de verdad; no se pudieron generar desde WSL2. Si el profesor las exige tal cual, hay
que instalar XAMPP en Windows (no en WSL) y tomarlas ahí; de lo contrario, el PDF ya explica que se usó
un stack equivalente.

Los datos del hosting (FTP, MySQL remoto) **no** se guardan aquí.
