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

/** Rate card intentionally empty — VTECH never publishes fabricated prices. */
function vtech_estimator_rate_card() {
	// No fabricated pricing. Empty until a real rate card is provided; the
	// endpoint returns a "book a survey" message instead of invented numbers.
	return array();
}

add_action( 'wp_ajax_vtech_estimate', 'vtech_handle_estimate' );
add_action( 'wp_ajax_nopriv_vtech_estimate', 'vtech_handle_estimate' );
function vtech_handle_estimate() {
	check_ajax_referer( 'vtech_nonce', 'nonce' );
	// No fabricated pricing is ever returned — always route to a real quote.
	wp_send_json_success( array(
		'currency' => 'KES',
		'note'     => 'For accurate pricing, book a free site survey — we quote within 24 hours.',
	) );
}
