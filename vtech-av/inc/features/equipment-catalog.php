<?php
/**
 * Equipment Hire Catalog + quote request — STUB (partially functional).
 *
 * The `equipment` CPT (see post-types.php) + ACF fields (daily_rate,
 * availability, specs) power a real, browsable catalog with filters today.
 * What is stubbed: real-time availability (calendar-based booking) and the
 * cart/quote-basket persistence.
 *
 * TODO (backend work required):
 *  - Wire availability to a booking calendar (e.g. a `booking` CPT with dates).
 *  - Store multi-item quote requests + email/notify sales.
 *  - Optional WooCommerce Bookings integration for paid reservations.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Add-to-quote AJAX (session-based basket). */
add_action( 'wp_ajax_vtech_add_quote', 'vtech_add_quote' );
add_action( 'wp_ajax_nopriv_vtech_add_quote', 'vtech_add_quote' );
function vtech_add_quote() {
	check_ajax_referer( 'vtech_nonce', 'nonce' );
	if ( ! session_id() ) { @session_start(); }
	$id = absint( $_POST['id'] ?? 0 );
	if ( ! $id ) { wp_send_json_error(); }
	$_SESSION['vtech_quote'] = array_unique( array_merge( $_SESSION['vtech_quote'] ?? array(), array( $id ) ) );
	wp_send_json_success( array( 'count' => count( $_SESSION['vtech_quote'] ) ) );
}

/** Availability badge helper for templates. */
function vtech_availability_badge( $post_id ) {
	$status = function_exists( 'get_field' ) ? get_field( 'availability', $post_id ) : 'available';
	$labels = array( 'available' => 'Available', 'limited' => 'Limited stock', 'booked' => 'Fully booked' );
	$label  = $labels[ $status ] ?? 'Available';
	return '<span class="badge badge--' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}
