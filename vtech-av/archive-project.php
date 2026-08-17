<?php
/**
 * Projects archive with filter chips (industry / technology / location).
 * Filtering is progressive-enhancement via the vtech-filter JS module;
 * without JS it degrades to standard taxonomy archive links.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header(); ?>

<div class="container section">
	<?php vtech_breadcrumbs(); ?>
	<header class="archive-header">
		<h1 class="archive-title"><?php esc_html_e( 'Featured AV Projects in Kenya', 'vtech-av' ); ?></h1>
		<p class="archive-desc"><?php esc_html_e( 'Sound, LED, lighting and conference installations delivered for churches, hotels, schools, corporates and government across Kenya.', 'vtech-av' ); ?></p>
	</header>

	<div class="filters" data-filter aria-label="<?php esc_attr_e( 'Filter projects', 'vtech-av' ); ?>">
		<button class="chip is-active" data-filter-value="*"><?php esc_html_e( 'All', 'vtech-av' ); ?></button>
		<?php foreach ( get_terms( array( 'taxonomy' => 'industry', 'hide_empty' => true ) ) as $t ) : ?>
			<button class="chip" data-filter-value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
		<?php endforeach; ?>
	</div>

	<div class="card-grid card-grid--3" data-filter-grid>
		<?php while ( have_posts() ) : the_post();
			$terms = wp_get_post_terms( get_the_ID(), 'industry', array( 'fields' => 'slugs' ) );
			$client = function_exists( 'get_field' ) ? get_field( 'client' ) : '';
			?>
			<article class="card card--project" data-tags="<?php echo esc_attr( implode( ' ', $terms ) ); ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'vtech-card' ); ?></a>
				<?php endif; ?>
				<div class="card__body">
					<?php if ( $client ) : ?><span class="card__eyebrow"><?php echo esc_html( $client ); ?></span><?php endif; ?>
					<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
</div>

<?php get_footer();
