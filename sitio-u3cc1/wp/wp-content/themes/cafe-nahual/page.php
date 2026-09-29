<?php get_header(); ?>
<?php if ( is_front_page() ) : ?>
<section class="portada-inicio">
	<h1>Café con nombre y con origen</h1>
	<p>Tostamos en lotes pequeños café de altura de Chiapas, Veracruz y Oaxaca, y lo mandamos directo a tu taza.</p>
	<a class="boton" href="<?php echo esc_url( home_url( '/productos-y-servicios/' ) ); ?>">Ver nuestro café</a>
</section>
<?php endif; ?>
<main class="contenedor">
<?php while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<?php if ( ! is_front_page() ) : ?>
			<h1><?php the_title(); ?></h1>
		<?php endif; ?>
		<?php the_content(); ?>
	</article>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
