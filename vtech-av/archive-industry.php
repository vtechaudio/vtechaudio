<?php
/**
 * Industries archive — all sectors VTECH serves in Kenya & East Africa.
 * Clean H1 (no "Archives:" prefix), professional intro + card grid.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>

<div class="container section">
	<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
	<header class="archive-header">
		<h1 class="archive-title"><?php esc_html_e( 'Industries We Serve in Kenya', 'vtech-av' ); ?></h1>
		<p class="archive-desc"><?php esc_html_e( 'Tailored audio-visual solutions for every sector across Kenya and East Africa — from churches and schools to hotels, government, corporates, healthcare and media houses.', 'vtech-av' ); ?></p>
	</header>

	<div class="card-grid card-grid--3">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<a class="card" href="<?php the_permalink(); ?>">
			<?php if ( has_post_thumbnail() ) : ?><figure class="card__media"><?php the_post_thumbnail( 'vtech-card', array( 'loading' => 'lazy' ) ); ?></figure><?php endif; ?>
			<div class="card__body">
				<h2 class="card__title"><?php the_title(); ?></h2>
				<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
				<span class="card__link"><?php esc_html_e( 'Explore', 'vtech-av' ); ?></span>
			</div>
		</a>
	<?php endwhile; else : ?>
		<p><?php esc_html_e( 'Industry pages are being added. Please check back soon.', 'vtech-av' ); ?></p>
	<?php endif; ?>
	</div>
	<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
</div>

<?php get_footer();
