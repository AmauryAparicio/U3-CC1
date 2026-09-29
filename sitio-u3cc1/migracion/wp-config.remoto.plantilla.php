<?php
/**
 * Plantilla de wp-config.php para el hosting (InfinityFree).
 * Sustituye los cuatro datos entre <> con los que muestra tu panel (MySQL Databases).
 * NO subas este archivo a ningún repositorio con los datos ya llenos.
 */
define( 'DB_NAME',     '<if0_XXXXXXXX_wp>' );
define( 'DB_USER',     '<if0_XXXXXXXX>' );
define( 'DB_PASSWORD', '<contraseña-del-panel>' );
define( 'DB_HOST',     '<sqlXXX.infinityfree.com>' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// Genera llaves nuevas en https://api.wordpress.org/secret-key/1.1/salt/ y pégalas aquí:
define( 'AUTH_KEY',         'pon-una-frase-unica-aqui' );
define( 'SECURE_AUTH_KEY',  'pon-una-frase-unica-aqui' );
define( 'LOGGED_IN_KEY',    'pon-una-frase-unica-aqui' );
define( 'NONCE_KEY',        'pon-una-frase-unica-aqui' );
define( 'AUTH_SALT',        'pon-una-frase-unica-aqui' );
define( 'SECURE_AUTH_SALT', 'pon-una-frase-unica-aqui' );
define( 'LOGGED_IN_SALT',   'pon-una-frase-unica-aqui' );
define( 'NONCE_SALT',       'pon-una-frase-unica-aqui' );

$table_prefix = 'wp_';
define( 'WP_DEBUG', false );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
