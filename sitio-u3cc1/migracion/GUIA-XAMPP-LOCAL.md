# Guía: replicar el sitio en TU XAMPP (para las capturas de evidencia)

El sitio se construyó y probó en un contenedor Linux (MariaDB + PHP). Para la actividad necesitas capturas **de tu XAMPP**; estos pasos las generan en ~20 minutos.

1. **Instalar XAMPP** (<https://www.apachefriends.org>). Capturas: instalador terminando, y **Panel de control** con Apache y MySQL en verde.
2. **Crear la BD**: abre <http://localhost/phpmyadmin> → *Nueva* → nombre `wp_cafenahual`, cotejamiento `utf8mb4_unicode_ci` → *Crear*. Captura.
3. **Instalar WordPress desde cero**: descarga <https://es-mx.wordpress.org>, descomprime en `C:\xampp\htdocs\cafenahual\`, abre <http://localhost/cafenahual>. Datos: BD `wp_cafenahual`, usuario `root`, contraseña vacía, host `localhost`. Título "Café Nahual". Capturas: pantalla de conexión a la BD y "¡Éxito!".
4. **Copiar el tema y el plugin** desde este repo a `C:\xampp\htdocs\cafenahual\wp-content\`:
   `themes\cafe-nahual\` y `plugins\nahual-contacto\`. En el panel: *Apariencia → Temas → Activar Café Nahual*; *Plugins → Activar Nahual Contacto* (esto crea `wp_contactos`).
5. **Cargar las páginas**: *Herramientas → Importar → WordPress* (instala el importador) y sube `migracion/contenido-paginas.xml`. Luego *Ajustes → Lectura → Una página estática → Inicio*, *Ajustes → Enlaces permanentes → Nombre de la entrada*, y *Apariencia → Menús → asignar "Principal" a Menú principal*.
6. **Dos mensajes reales**: en <http://localhost/cafenahual/contacto/> envía el formulario dos veces. Captura de cada confirmación.
7. **Registros en phpMyAdmin**: `wp_cafenahual` → tabla `wp_contactos` → *Examinar*. Captura con las 2 filas.
