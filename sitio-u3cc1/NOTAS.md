# Notas de entorno LOCAL (solo pruebas; no son credenciales de hosting)

| Elemento | Valor |
|---|---|
| Sitio local | http://localhost:8080 |
| phpMyAdmin local | http://localhost:8081 |
| Base de datos | `wp_cafenahual` (utf8mb4) |
| Usuario BD | `wpuser` / `wp1234` |
| Usuario root MariaDB | sin contraseña (solo socket local) |
| Admin WordPress | `admin` / `admin1234` (http://localhost:8080/wp-admin) |
| Tabla del formulario | `wp_contactos` (id, nombre, correo, telefono, mensaje, fecha) |

## Levantar todo de nuevo (contenedor Linux usado para el desarrollo)
```bash
mysqld_safe --user=mysql &
cd sitio-u3cc1/wp  && php -S 127.0.0.1:8080 -t . ../scripts/router.php &
cd sitio-u3cc1/pma && php -S 127.0.0.1:8081 &
```

## En XAMPP (Windows)
Con XAMPP se usa `C:\xampp\htdocs\cafenahual\` para los archivos y `http://localhost/cafenahual` como URL;
el usuario de MySQL es `root` sin contraseña. Ver `migracion/GUIA-XAMPP-LOCAL.md`.

Los datos del hosting (FTP, MySQL remoto) **no** se guardan aquí.
