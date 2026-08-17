<?php
/**
 * Single Hire Package — full detail with equipment list, services, add-ons,
 * pricing, terms, and actions (Book Now / Customize / Request Quote).
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$id = get_the_ID();
	$af = function_exists( 'get_field' );
	// Read plain, ACF-independent meta first (set by the setup wizard) so the
	// package always renders its details even when ACF is inactive or its
	// repeater data is not returned. Fall back to ACF, then to blanks.
	$price = get_post_meta( $id, 'vtech_pkg_price', true );
	if ( '' === $price || null === $price ) { $price = get_post_meta( $id, 'price', true ); }
	if ( ( '' === $price || null === $price ) && $af ) { $price = get_field( 'price', $id ); }
	$cap = get_post_meta( $id, 'vtech_pkg_capacity', true );
	if ( '' === $cap || null === $cap ) { $cap = get_post_meta( $id, 'capacity', true ); }
	if ( ( '' === $cap || null === $cap ) && $af ) { $cap = get_field( 'capacity', $id ); }
	$equip = get_post_meta( $id, 'vtech_pkg_equipment', true );
	if ( ! is_array( $equip ) || empty( $equip ) ) { $equip = $af ? get_field( 'equipment', $id ) : array(); }
	$services = get_post_meta( $id, 'vtech_pkg_services', true );
	if ( ! is_array( $services ) || empty( $services ) ) { $services = $af ? get_field( 'services', $id ) : array(); }
	$hl      = $af ? get_field( 'highlights', $id ) : array();
	$addons  = $af ? get_field( 'addons', $id ) : array();
	$venues  = $af ? get_field( 'venue_types', $id ) : array();
	$deposit = $af ? get_field( 'deposit_pct', $id ) : '';
	$cancel  = $af ? get_field( 'cancellation', $id ) : '';
	if ( ! is_array( $equip ) ) { $equip = array(); }
	if ( ! is_array( $services ) ) { $services = array(); }
	if ( ! is_array( $hl ) ) { $hl = array(); }
	if ( ! is_array( $addons ) ) { $addons = array(); }
	if ( ! is_array( $venues ) ) { $venues = array(); }
	$book_url = add_query_arg( 'package', get_post_field( 'post_name', $id ), home_url( '/equipment-hire-request/' ) );
	?>
	<section class="about-hero">
		<div class="container">
			<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( $cap ) : ?><p><?php printf( esc_html__( 'Suitable for %s guests', 'vtech-av' ), esc_html( $cap ) ); ?><?php if ( $venues ) { echo ' · ' . esc_html( implode( ', ', $venues ) ); } ?></p><?php endif; ?>
		</div>
	</section>

	<section class="section"><div class="container quote-grid">
		<div>
			<?php if ( has_post_thumbnail() ) : ?><figure class="pkg-single__media"><?php the_post_thumbnail( 'vtech-hero', array( 'loading' => 'eager' ) ); ?></figure><?php endif; ?>
			<?php if ( trim( get_the_content() ) ) : ?><div class="svc-intro"><?php the_content(); ?></div><?php endif; ?>

			<?php if ( $equip ) : ?>
			<h2 class="section__title" style="margin-top:2rem"><?php esc_html_e( 'What\'s included', 'vtech-av' ); ?></h2>
			<ul class="pkg-included">
				<?php foreach ( $equip as $e ) :
					if ( is_array( $e ) ) { $line = trim( ( $e['qty'] ?? '' ) . ' ' . ( $e['item'] ?? '' ) ); }
					else { $line = (string) $e; }
					if ( '' === $line ) { continue; } ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if ( $services ) : ?>
			<h3><?php esc_html_e( 'Included services', 'vtech-av' ); ?></h3>
			<ul class="pkg-services"><?php foreach ( $services as $s ) : ?><li><?php echo esc_html( $s ); ?></li><?php endforeach; ?></ul>
			<?php endif; ?>

			<?php if ( $addons ) : ?>
			<h3><?php esc_html_e( 'Optional add-ons', 'vtech-av' ); ?></h3>
			<ul class="pkg-addons">
				<?php foreach ( $addons as $a ) : $n = is_array( $a ) ? ( $a['name'] ?? '' ) : ''; $p = is_array( $a ) ? ( $a['price'] ?? '' ) : ''; if ( ! $n ) { continue; } ?>
					<li><span><?php echo esc_html( $n ); ?></span><?php if ( $p ) : ?><em><?php printf( esc_html__( '+ KES %s', 'vtech-av' ), esc_html( number_format( (float) $p ) ) ); ?></em><?php endif; ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<?php if ( $cancel ) : ?><h3><?php esc_html_e( 'Terms', 'vtech-av' ); ?></h3><p class="card__excerpt"><?php echo esc_html( $cancel ); ?></p><?php endif; ?>
		</div>

		<aside class="quote-info pkg-sidebar">
			<?php if ( $price ) : ?><div class="pkg-sidebar__price"><?php printf( esc_html__( 'KES %s', 'vtech-av' ), esc_html( number_format( (float) $price ) ) ); ?></div><?php endif; ?>
			<?php if ( $deposit ) : ?><p class="pkg-sidebar__note"><?php printf( esc_html__( '%s%% deposit to confirm booking', 'vtech-av' ), esc_html( $deposit ) ); ?></p><?php endif; ?>
			<?php if ( $hl ) : ?><ul><?php foreach ( $hl as $h ) : $t = is_array( $h ) ? ( $h['text'] ?? '' ) : ''; if ( ! $t ) { continue; } ?><li><?php echo esc_html( $t ); ?></li><?php endforeach; ?></ul><?php endif; ?>
			<a class="btn btn--accent btn--block" href="<?php echo esc_url( add_query_arg( array( 'package' => get_post_field( 'post_name', $id ), 'mode' => 'book' ), home_url( '/equipment-hire-request/' ) ) ); ?>"><?php esc_html_e( 'Book This Package', 'vtech-av' ); ?></a>
			<p class="pkg-sidebar__hint"><?php esc_html_e( 'Reserve this exact package as listed and confirm your date.', 'vtech-av' ); ?></p>
				<a class="btn btn--wa btn--block" style="margin-top:.6rem" href="<?php echo esc_url( vtech_whatsapp_book_url( $id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Book on WhatsApp', 'vtech-av' ); ?></a>
			<a class="btn btn--ghost btn--block" style="margin-top:1rem" href="<?php echo esc_url( add_query_arg( array( 'package' => get_post_field( 'post_name', $id ), 'mode' => 'customize' ), home_url( '/equipment-hire-request/' ) ) ); ?>"><?php esc_html_e( 'Customize This Package', 'vtech-av' ); ?></a>
			<p class="pkg-sidebar__hint"><?php esc_html_e( 'Start from this package and add, remove or change items to fit your event.', 'vtech-av' ); ?></p>
		</aside>
	</div></section>

<?php endwhile; get_footer();
