<?php
/**
 * Funciones del tema Café Nahual.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cafe_nahual_configurar() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'principal' => __( 'Menú principal', 'cafe-nahual' ) ) );
}
add_action( 'after_setup_theme', 'cafe_nahual_configurar' );

// La hoja de estilos externa (style.css) se carga con wp_enqueue_style.
function cafe_nahual_estilos() {
	wp_enqueue_style(
		'cafe-nahual',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'cafe_nahual_estilos' );
