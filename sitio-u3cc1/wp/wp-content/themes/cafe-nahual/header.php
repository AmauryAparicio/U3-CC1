<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="sitio-cabecera">
	<div class="cabecera-interior">
		<a class="marca" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<strong>Café Nahual</strong>
			<span>Tostadores de café de altura · México</span>
		</a>
		<nav class="menu-principal" aria-label="Menú principal">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'principal',
				'container'      => false,
				'fallback_cb'    => false,
			) );
			?>
		</nav>
	</div>
</header>
