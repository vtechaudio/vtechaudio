<?php
/**
 * VTECH Inventory & Availability Locking.
 *
 * Adds per-equipment stock, records bookings against date ranges, and provides
 * a real-time availability checker so the same unit cannot be double-booked.
 *
 * Data model (no extra tables — uses post meta + a bookings CPT):
 *   equipment post meta: stock_qty (int)
 *   booking (CPT): dates + line items (equipment_id => qty) + status.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Booking CPT to hold reservations that lock inventory. */
add_action( 'init', function () {
	register_post_type( 'vtech_booking', array(
		'labels'          => array( 'name' => 'Bookings', 'singular_name' => 'Booking', 'menu_name' => 'Bookings' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-calendar-alt',
		'menu_position'   => 29,
		'capability_type' => 'post',
		'supports'        => array( 'title' ),
	) );
} );

/* Booking statuses that count as "locking" inventory. */
function vtech_locking_statuses() {
	return array( 'confirmed', 'tentative' );
}

/**
 * Count how many units of an equipment item are already booked over a date range.
 * Overlap = booking.start <= range.end AND booking.end >= range.start.
 */
function vtech_units_booked( $equipment_id, $start, $end, $exclude_booking = 0 ) {
	$start_ts = strtotime( $start );
	$end_ts   = strtotime( $end );
	if ( ! $start_ts || ! $end_ts ) { return 0; }

	$q = new WP_Query( array(
		'post_type'      => 'vtech_booking',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array( 'key' => 'status', 'value' => vtech_locking_statuses(), 'compare' => 'IN' ),
		),
	) );
	$booked = 0;
	foreach ( $q->posts as $bid ) {
		if ( $bid === (int) $exclude_booking ) { continue; }
		$b_start = strtotime( get_post_meta( $bid, 'start_date', true ) );
		$b_end   = strtotime( get_post_meta( $bid, 'end_date', true ) );
		if ( ! $b_start || ! $b_end ) { continue; }
		// Overlap test.
		if ( $b_start <= $end_ts && $b_end >= $start_ts ) {
			$items = json_decode( (string) get_post_meta( $bid, 'items', true ), true );
			if ( is_array( $items ) && isset( $items[ $equipment_id ] ) ) {
				$booked += (int) $items[ $equipment_id ];
			}
		}
	}
	wp_reset_postdata();
	return $booked;
}

/** Units available for an item over a range. */
function vtech_units_available( $equipment_id, $start, $end, $exclude_booking = 0 ) {
	$stock = (int) get_post_meta( $equipment_id, 'stock_qty', true );
	if ( $stock <= 0 ) { $stock = (int) get_field( 'stock_qty', $equipment_id ); }
	$booked = vtech_units_booked( $equipment_id, $start, $end, $exclude_booking );
	return max( 0, $stock - $booked );
}

/* ---------------------------------------------------------------------------
 * AJAX: live availability check for the front end.
 * POST: equipment_id, start, end, qty
 * ------------------------------------------------------------------------- */
function vtech_ajax_check_availability() {
	$equipment_id = (int) ( $_POST['equipment_id'] ?? 0 );
	$start = sanitize_text_field( wp_unslash( $_POST['start'] ?? '' ) );
	$end   = sanitize_text_field( wp_unslash( $_POST['end'] ?? $start ) );
	$qty   = max( 1, (int) ( $_POST['qty'] ?? 1 ) );
	if ( ! $equipment_id || ! $start ) {
		wp_send_json_error( array( 'message' => 'Please select an item and date.' ), 400 );
	}
	$avail = vtech_units_available( $equipment_id, $start, $end );
	wp_send_json_success( array(
		'available'   => $avail,
		'requested'   => $qty,
		'can_book'    => $avail >= $qty,
		'message'     => $avail >= $qty
			? sprintf( '%d unit(s) available for your dates.', $avail )
			: ( $avail > 0 ? sprintf( 'Only %d unit(s) available for your dates.', $avail ) : 'Fully booked for your dates — try alternative dates or contact us.' ),
	) );
}
add_action( 'wp_ajax_vtech_check_availability', 'vtech_ajax_check_availability' );
add_action( 'wp_ajax_nopriv_vtech_check_availability', 'vtech_ajax_check_availability' );

/**
 * Create a booking that locks inventory. Called when a quote is accepted /
 * a request is converted. $items = array( equipment_id => qty ).
 */
function vtech_create_booking( $title, $start, $end, $items, $status = 'tentative', $meta = array() ) {
	$bid = wp_insert_post( array(
		'post_type'   => 'vtech_booking',
		'post_status' => 'publish',
		'post_title'  => $title,
	) );
	if ( is_wp_error( $bid ) || ! $bid ) { return 0; }
	update_post_meta( $bid, 'start_date', sanitize_text_field( $start ) );
	update_post_meta( $bid, 'end_date', sanitize_text_field( $end ) );
	update_post_meta( $bid, 'items', wp_json_encode( array_map( 'intval', (array) $items ) ) );
	update_post_meta( $bid, 'status', in_array( $status, vtech_locking_statuses(), true ) ? $status : 'tentative' );
	foreach ( $meta as $k => $v ) { update_post_meta( $bid, sanitize_key( $k ), $v ); }
	return $bid;
}

/* Admin: stock column on equipment list + booking status column. */
add_filter( 'manage_equipment_posts_columns', function ( $cols ) {
	$cols['vtech_stock'] = 'Stock';
	return $cols;
} );
add_action( 'manage_equipment_posts_custom_column', function ( $col, $post_id ) {
	if ( 'vtech_stock' === $col ) {
		$s = (int) get_post_meta( $post_id, 'stock_qty', true );
		if ( ! $s && function_exists( 'get_field' ) ) { $s = (int) get_field( 'stock_qty', $post_id ); }
		echo esc_html( $s ?: '—' );
	}
}, 10, 2 );

add_filter( 'manage_vtech_booking_posts_columns', function ( $cols ) {
	$new = array( 'cb' => $cols['cb'], 'title' => 'Booking', 'vb_dates' => 'Dates', 'vb_status' => 'Status', 'date' => $cols['date'] );
	return $new;
} );
add_action( 'manage_vtech_booking_posts_custom_column', function ( $col, $post_id ) {
	if ( 'vb_dates' === $col ) {
		echo esc_html( get_post_meta( $post_id, 'start_date', true ) . ' → ' . get_post_meta( $post_id, 'end_date', true ) );
	} elseif ( 'vb_status' === $col ) {
		echo '<strong>' . esc_html( ucfirst( get_post_meta( $post_id, 'status', true ) ?: 'tentative' ) ) . '</strong>';
	}
}, 10, 2 );
