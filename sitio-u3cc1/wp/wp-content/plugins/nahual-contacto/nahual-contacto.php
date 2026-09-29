<?php
/**
 * Plugin Name: Nahual Contacto
 * Description: Formulario de contacto propio que guarda Nombre, Correo, Teléfono y Mensaje en la tabla {prefijo}contactos. Shortcode: [formulario_contacto]
 * Version: 1.0
 * Author: Jorge Amaury Aparicio Cuevas
 * Text Domain: nahual-contacto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NAHUAL_CONTACTO_VERSION', '1.0' );

/**
 * Nombre de la tabla (wp_contactos con el prefijo por defecto).
 */
function nahual_contacto_tabla() {
	global $wpdb;
	return $wpdb->prefix . 'contactos';
}

/**
 * Crea la tabla al activar el plugin.
 */
function nahual_contacto_activar() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$tabla   = nahual_contacto_tabla();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$tabla} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		nombre varchar(120) NOT NULL,
		correo varchar(190) NOT NULL,
		telefono varchar(20) NOT NULL,
		mensaje text NOT NULL,
		fecha datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) {$charset};";

	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'nahual_contacto_activar' );

/**
 * URL de la página del formulario con el aviso de éxito.
 * (wp_get_referer() no sirve aquí: devuelve false si el POST va a la misma URL.)
 */
function nahual_contacto_url_exito() {
	$pagina = get_permalink( get_queried_object_id() );
	return add_query_arg( 'contacto', 'ok', $pagina ? $pagina : home_url( '/' ) );
}

/**
 * Procesa el envío antes de imprimir la página, para poder redirigir (patrón
 * Post/Redirect/Get) y evitar que un refresh reenvíe el formulario.
 */
function nahual_contacto_procesar() {
	global $nahual_contacto_estado;
	$nahual_contacto_estado = array( 'errores' => array(), 'valores' => array() );

	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || empty( $_POST['nahual_contacto_enviar'] ) ) {
		return;
	}

	// 1. Nonce: comprueba que el envío viene de nuestro formulario.
	if ( ! isset( $_POST['nahual_contacto_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nahual_contacto_nonce'] ) ), 'nahual_contacto_enviar' ) ) {
		$nahual_contacto_estado['errores'][] = 'La sesión del formulario caducó. Recarga la página e inténtalo de nuevo.';
		return;
	}

	// 2. Honeypot: los bots suelen llenar el campo oculto.
	if ( ! empty( $_POST['sitio_web'] ) ) {
	wp_safe_redirect( nahual_contacto_url_exito() );
		exit;
	}

	// 3. Sanitización.
	$nombre   = sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) );
	$correo   = sanitize_email( wp_unslash( $_POST['correo'] ?? '' ) );
	$telefono = preg_replace( '/\D+/', '', sanitize_text_field( wp_unslash( $_POST['telefono'] ?? '' ) ) );
	$mensaje  = sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ?? '' ) );

	$nahual_contacto_estado['valores'] = compact( 'nombre', 'correo', 'telefono', 'mensaje' );

	// 4. Validación.
	$errores = array();
	if ( mb_strlen( $nombre ) < 2 || mb_strlen( $nombre ) > 120 ) {
		$errores[] = 'Escribe tu nombre (entre 2 y 120 caracteres).';
	}
	if ( ! is_email( $correo ) ) {
		$errores[] = 'El correo electrónico no es válido.';
	}
	if ( strlen( $telefono ) !== 10 ) {
		$errores[] = 'El teléfono debe tener 10 dígitos.';
	}
	if ( mb_strlen( $mensaje ) < 5 || mb_strlen( $mensaje ) > 2000 ) {
		$errores[] = 'El mensaje debe tener entre 5 y 2000 caracteres.';
	}

	if ( $errores ) {
		$nahual_contacto_estado['errores'] = $errores;
		return;
	}

	// 5. Inserción con consulta preparada ($wpdb->insert escapa cada valor).
	global $wpdb;
	$ok = $wpdb->insert(
		nahual_contacto_tabla(),
		array(
			'nombre'   => $nombre,
			'correo'   => $correo,
			'telefono' => $telefono,
			'mensaje'  => $mensaje,
			'fecha'    => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $ok ) {
		$nahual_contacto_estado['errores'][] = 'No pudimos guardar tu mensaje. Inténtalo más tarde.';
		return;
	}

	wp_safe_redirect( nahual_contacto_url_exito() );
	exit;
}
add_action( 'template_redirect', 'nahual_contacto_procesar' );

/**
 * Shortcode [formulario_contacto].
 */
function nahual_contacto_shortcode() {
	global $nahual_contacto_estado;
	$errores = $nahual_contacto_estado['errores'] ?? array();
	$v       = $nahual_contacto_estado['valores'] ?? array();

	ob_start();

	if ( isset( $_GET['contacto'] ) && 'ok' === $_GET['contacto'] ) {
		echo '<div class="aviso aviso-ok" role="status"><strong>¡Gracias!</strong> Recibimos tu mensaje y te responderemos pronto.</div>';
	}

	if ( $errores ) {
		echo '<div class="aviso aviso-error" role="alert"><strong>Revisa lo siguiente:</strong><ul>';
		foreach ( $errores as $e ) {
			echo '<li>' . esc_html( $e ) . '</li>';
		}
		echo '</ul></div>';
	}
	?>
	<form class="nahual-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>" novalidate>
		<?php wp_nonce_field( 'nahual_contacto_enviar', 'nahual_contacto_nonce' ); ?>

		<label for="nc-nombre">Nombre</label>
		<input type="text" id="nc-nombre" name="nombre" maxlength="120" required value="<?php echo esc_attr( $v['nombre'] ?? '' ); ?>">

		<label for="nc-correo">Correo electrónico</label>
		<input type="email" id="nc-correo" name="correo" maxlength="190" required value="<?php echo esc_attr( $v['correo'] ?? '' ); ?>">

		<label for="nc-telefono">Teléfono (10 dígitos)</label>
		<input type="tel" id="nc-telefono" name="telefono" maxlength="20" required value="<?php echo esc_attr( $v['telefono'] ?? '' ); ?>">

		<label for="nc-mensaje">Mensaje</label>
		<textarea id="nc-mensaje" name="mensaje" rows="6" maxlength="2000" required><?php echo esc_textarea( $v['mensaje'] ?? '' ); ?></textarea>

		<div class="trampa" aria-hidden="true">
			<label for="nc-web">No llenes este campo</label>
			<input type="text" id="nc-web" name="sitio_web" tabindex="-1" autocomplete="off">
		</div>

		<button type="submit" name="nahual_contacto_enviar" value="1">Enviar mensaje</button>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'formulario_contacto', 'nahual_contacto_shortcode' );
