<?php
/**
 * Fallback index. Most output is block/template-driven; this covers the blog.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
// HOST-PROOF: some hosts misroute custom pages to index.php. If this request
// is really one of our template-driven pages, load its template and stop.
$vtech_force_map = array(
	'equipment-hire'         => 'page-equipment-hire.php',
	'equipment-hire-request' => 'page-hire-request.php',
	'consultation'           => 'page-consultation.php',
	'about'                  => 'page-about.php',
	'contact'                => 'page-contact.php',
);
$vtech_req_slug = '';
$vtech_obj = get_queried_object();
if ( $vtech_obj instanceof WP_Post ) {
	$vtech_req_slug = $vtech_obj->post_name;
} else {
	$vtech_pn = trim( (string) get_query_var( 'pagename' ), '/' );
	if ( '' === $vtech_pn ) { $vtech_pn = trim( (string) get_query_var( 'name' ), '/' ); }
	if ( '' === $vtech_pn ) { $vtech_pn = trim( parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH ), '/' ); }
	if ( $vtech_pn ) { $vtech_parts = explode( '/', $vtech_pn ); $vtech_req_slug = end( $vtech_parts ); }
}
if ( $vtech_req_slug && isset( $vtech_force_map[ $vtech_req_slug ] ) && locate_template( $vtech_force_map[ $vtech_req_slug ] ) ) {
	include locate_template( $vtech_force_map[ $vtech_req_slug ] );
	return;
}
get_header(); ?>

<div class="container section">
	<?php vtech_breadcrumbs(); ?>
	<header class="archive-header">
		<h1 class="archive-title"><?php echo is_home() ? esc_html__( 'AV Insights & Guides', 'vtech-av' ) : get_the_archive_title(); ?></h1>
		<p class="archive-desc"><?php esc_html_e( 'Practical audio-visual advice for Kenyan organisations — buying guides, install checklists and case studies.', 'vtech-av' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="card-grid card-grid--3">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'vtech-card' ); ?></a>
					<?php endif; ?>
					<div class="card__body">
						<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article', 'vtech-av' ); ?></a>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No articles yet — check back soon.', 'vtech-av' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer();
