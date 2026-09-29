<?php get_header(); ?>
<main class="contenedor">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article <?php post_class(); ?>>
		<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
		<?php the_content(); ?>
	</article>
<?php endwhile; else : ?>
	<h1>No encontramos lo que buscabas</h1>
	<p>Regresa al <a href="<?php echo esc_url( home_url( '/' ) ); ?>">inicio</a>.</p>
<?php endif; ?>
</main>
<?php get_footer(); ?>
