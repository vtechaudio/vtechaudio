<?php
/**
 * Project Cost Estimator — STUB.
 *
 * This ships a working front-end UI (see /patterns/estimator.php and the
 * `vtech-estimator` JS module) plus a documented AJAX endpoint contract.
 * The pricing LOGIC is intentionally a stub: it returns an indicative range
 * from a config table. A real estimator needs your live rate card and rules.
 *
 * TODO (backend work required):
 *  - Replace $rate_card below with real VTECH pricing.
 *  - Add room-size / channel-count / venue-type multipliers.
 *  - Persist submissions to a CPT `estimate` + email the sales team.
 *  - Optionally integrate WooCommerce for deposit payments.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Indicative rate card (KES). REPLACE with real figures. */
function vtech_estimator_rate_card() {
	return array(
		// service_slug => array( base, per_seat )
		'conference-systems' => array( 'base' => 180000, 'per_seat' => 9500 ),
		'pa-systems'         => array( 'base' => 120000, 'per_seat' => 3200 ),
		'led-screens'        => array( 'base' => 350000, 'per_sqm' => 42000 ),
		'stage-lighting'     => array( 'base' => 220000, 'per_fixture' => 18000 ),
		'acoustic-treatment' => array( 'base' => 90000,  'per_sqm' => 6500 ),
	);
}

add_action( 'wp_ajax_vtech_estimate', 'vtech_handle_estimate' );
add_action( 'wp_ajax_nopriv_vtech_estimate', 'vtech_handle_estimate' );
function vtech_handle_estimate() {
	check_ajax_referer( 'vtech_nonce', 'nonce' );

	$service = sanitize_key( $_POST['service'] ?? '' );
	$qty     = max( 1, absint( $_POST['qty'] ?? 1 ) );
	$card    = vtech_estimator_rate_card();

	if ( ! isset( $card[ $service ] ) ) {
		wp_send_json_error( array( 'message' => 'Unknown service.' ) );
	}

	$c   = $card[ $service ];
	$var = 0;
	foreach ( array( 'per_seat', 'per_sqm', 'per_fixture' ) as $k ) {
		if ( isset( $c[ $k ] ) ) { $var = $c[ $k ] * $qty; break; }
	}
	$low  = ( $c['base'] + $var );
	$high = round( $low * 1.35 );

	wp_send_json_success( array(
		'currency' => 'KES',
		'low'      => $low,
		'high'     => $high,
		'note'     => 'Indicative range only. Book a free site survey for an exact quote in 24 hours.',
	) );
}
