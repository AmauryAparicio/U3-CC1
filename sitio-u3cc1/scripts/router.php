<?php
// Router para `php -S` (equivale a las reglas de .htaccess de WordPress).
$ruta = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$archivo = $_SERVER['DOCUMENT_ROOT'] . $ruta;
if ( $ruta !== '/' && is_file( $archivo ) ) {
	return false;
}
if ( is_dir( $archivo ) && is_file( rtrim( $archivo, '/' ) . '/index.php' ) ) {
	$_SERVER['SCRIPT_NAME'] = rtrim( $ruta, '/' ) . '/index.php';
	require rtrim( $archivo, '/' ) . '/index.php';
	return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
