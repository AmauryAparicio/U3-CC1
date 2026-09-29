# Guía corta: subir Café Nahual a InfinityFree

Antes de empezar necesitas saber la **URL final** de tu sitio (se define en el paso 2). El paquete se genera con esa URL:

```bash
cd sitio-u3cc1/wp && ../scripts/empaquetar.sh https://TU-SUBDOMINIO.infinityfreeapp.com
```

Eso deja en `migracion/`: `cafe-nahual-db.sql` (BD con URLs ya reemplazadas) y `wp-content.zip`.

## 1. Cuenta
1. Entra a <https://www.infinityfree.com> → **Sign Up** y confirma tu correo.
2. En el *Client Area* → **Create Account** (hosting gratuito).

## 2. Dominio gratuito
1. Elige un subdominio (p. ej. `cafenahual`) y una extensión gratuita (`infinityfreeapp.com`, `rf.gd`, etc.).
2. Termina el asistente. Tu URL será `https://cafenahual.infinityfreeapp.com` (usa `http://` si el SSL aún no se emite; el certificado gratuito puede tardar unos minutos).

## 3. Base de datos
1. En el *Client Area* abre tu cuenta → **Control Panel** (vPanel) → **MySQL Databases**.
2. Crea una base (el panel le pone el prefijo `if0_XXXXXXXX_`). Anota: **nombre de la BD**, **usuario**, **contraseña** y **MySQL hostname** (algo como `sql301.infinityfree.com`).
3. Pulsa **Admin** junto a la base para abrir phpMyAdmin → pestaña **Importar** → elige `cafe-nahual-db.sql` → **Continuar**.
   Deben aparecer las 14 tablas (`wp_*` y `wp_contactos`).

## 4. Archivos
1. **FTP Details** de tu cuenta: host `ftpupload.net`, usuario `if0_XXXXXXXX`, contraseña de tu cuenta.
2. Sube **WordPress completo** (`sitio-u3cc1/wp/`, sin `wp-content/themes/twenty*` ni `wp-content/plugins/akismet`) a la carpeta **`htdocs`** del servidor. Dos formas:
   - **Automático**: `scripts/subir_ftp.py` sube todo por FTP y es resistente a cortes (si se interrumpe, vuelve a correrlo y solo sube lo que falte):
     ```bash
     FTP_HOST=ftpupload.net FTP_USER=if0_XXXXXXXX FTP_PASS='tu-contraseña' \
       python3 scripts/subir_ftp.py
     ```
     Son ~3,400 archivos (~95 MB); puede tardar bastante en un hosting gratuito.
   - **Manual**: FileZilla o el *Online File Manager* del panel. Alternativa sin FTP: descarga WordPress es_MX desde <https://es-mx.wordpress.org>, súbelo a `htdocs` y luego descomprime `wp-content.zip` encima de `wp-content`.
3. Copia `wp-config.remoto.plantilla.php` como **`wp-config.php`** en `htdocs` (el script anterior no lo sube a propósito), y rellena los datos de la BD del paso 3 y unas llaves nuevas (<https://api.wordpress.org/secret-key/1.1/salt/>).
4. Borra `htdocs/index2.html` (página de bienvenida del hosting) si existe.

## 5. Verificar
1. Abre tu URL: debe verse igual que en local (colores del tema = `style.css` cargando).
2. Ve a **Contacto**, envía un mensaje de prueba y comprueba que aparece en `wp_contactos` desde phpMyAdmin (paso 3.3).
3. Entra a `TU-URL/wp-admin` con el usuario `admin` del sitio local (`NOTAS.md`) y **cambia esa contraseña**.

## Problemas típicos
- *"Error establishing a database connection"*: revisa host/usuario/contraseña en `wp-config.php` (el host NO es `localhost`).
- *Enlaces a `localhost`*: el `.sql` se generó con otra URL; vuelve a ejecutar `empaquetar.sh` con la URL correcta e impórtalo de nuevo (borra antes las tablas).
- *Página con "This site requires JavaScript"* al probar con `curl`: es la protección anti-bots de InfinityFree; en el navegador funciona normal.
- *Páginas internas dan 404*: en **Ajustes → Enlaces permanentes** pulsa **Guardar cambios** una vez.
