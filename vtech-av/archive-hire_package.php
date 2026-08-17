<?php
/**
 * Hire Packages archive — filterable grid of admin-managed AV hire packages.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$img = VTECH_URI . '/assets/img/';
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'AV Hire Packages', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Ready-made audio, video, lighting and stage packages for events across Kenya — book as-is, customise, or request a tailored quote.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section"><div class="container">
	<?php $cats = get_terms( array( 'taxonomy' => 'package_category', 'hide_empty' => true ) ); if ( $cats && ! is_wp_error( $cats ) ) : ?>
	<div class="filters" data-filter aria-label="<?php esc_attr_e( 'Filter packages', 'vtech-av' ); ?>">
		<button class="chip is-active" data-filter-value="*"><?php esc_html_e( 'All', 'vtech-av' ); ?></button>
		<?php foreach ( $cats as $t ) : ?><button class="chip" data-filter-value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button><?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="pkg-grid" data-filter-grid>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
		$id = get_the_ID();
		$af = function_exists( 'get_field' );
		$price = $af ? get_field( 'price', $id ) : '';
		$cap   = $af ? get_field( 'capacity', $id ) : '';
		$hl    = $af ? get_field( 'highlights', $id ) : array();
		$terms = wp_get_post_terms( $id, 'package_category', array( 'fields' => 'slugs' ) );
		?>
		<article class="pkg-card" data-tags="<?php echo esc_attr( implode( ' ', $terms ) ); ?>">
			<?php if ( has_post_thumbnail() ) : ?><a class="pkg-card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'vtech-card', array( 'loading' => 'lazy' ) ); ?></a><?php endif; ?>
			<div class="pkg-card__body">
				<?php if ( $af && get_field( 'featured', $id ) ) : ?><span class="pkg-card__badge"><?php esc_html_e( 'Popular', 'vtech-av' ); ?></span><?php endif; ?>
				<h2 class="pkg-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php if ( $cap ) : ?><p class="pkg-card__cap"><?php printf( esc_html__( 'Suitable for %s guests', 'vtech-av' ), esc_html( $cap ) ); ?></p><?php endif; ?>
				<?php if ( is_array( $hl ) && $hl ) : ?>
				<ul class="pkg-card__list">
					<?php foreach ( array_slice( $hl, 0, 6 ) as $h ) : $t = is_array( $h ) ? ( $h['text'] ?? '' ) : ''; if ( ! $t ) { continue; } ?><li><?php echo esc_html( $t ); ?></li><?php endforeach; ?>
				</ul>
				<?php else : ?><p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p><?php endif; ?>
				<?php if ( $price ) : ?><div class="pkg-card__price"><?php printf( esc_html__( 'KES %s', 'vtech-av' ), esc_html( number_format( (float) $price ) ) ); ?></div><?php endif; ?>
				<div class="pkg-card__actions">
					<a class="btn btn--accent" href="<?php echo esc_url( add_query_arg( 'package', get_post_field( 'post_name', $id ), home_url( '/equipment-hire-request/' ) ) ); ?>"><?php esc_html_e( 'Book Now', 'vtech-av' ); ?></a>
					<a class="btn btn--ghost" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View / Customize', 'vtech-av' ); ?></a>
					<a class="btn btn--wa" href="<?php echo esc_url( vtech_whatsapp_book_url( $id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'vtech-av' ); ?></a>
				</div>
			</div>
		</article>
	<?php endwhile; else : ?>
		<p><?php esc_html_e( 'Packages are being added. Please check back soon, or', 'vtech-av' ); ?> <a href="<?php echo esc_url( home_url( '/equipment-hire-request/' ) ); ?>"><?php esc_html_e( 'request a custom quote', 'vtech-av' ); ?></a>.</p>
	<?php endif; ?>
	</div>
	<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
</div></section>

<section class="section"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
	<h2><?php esc_html_e( 'Need something custom?', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Tell us about your event and we\'ll build a tailored package with a same-day quote.', 'vtech-av' ); ?></p>
	<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/equipment-hire-request/' ) ); ?>"><?php esc_html_e( 'Request a Hire Quote', 'vtech-av' ); ?></a>
</div></div></div></section>

<?php get_footer();
