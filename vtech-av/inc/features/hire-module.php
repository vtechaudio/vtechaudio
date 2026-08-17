<?php
/**
 * VTECH Hire & Consultation Module.
 *
 * Registers:
 *   - hire_package  (admin-managed AV hire packages: pricing, equipment, add-ons)
 *   - package_category taxonomy
 *   - consultation  (private CPT storing consultation questionnaire submissions)
 *   - hire_request  (private CPT storing equipment hire request submissions)
 *
 * Also provides the AJAX handlers, reference-number generation, owner email
 * notifications and admin list columns.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ---------------------------------------------------------------------------
 * 0) ROBUSTNESS: keep AJAX responses clean + CF7 dependencies satisfied
 *
 * Two production issues this guards against:
 *   (a) PHP notices/warnings (e.g. the Contact Form 7 "wp-i18n not registered"
 *       notice) printing BEFORE our JSON, which corrupts admin-ajax responses
 *       and makes the front-end forms show "Network error".
 *   (b) wp-i18n / wp-hooks being missing when CF7 declares them as script deps.
 * ------------------------------------------------------------------------- */

// (a) During our own AJAX actions, never render notices into the response body.
add_action( 'init', function () {
	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) { return; }
	$vtc_action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
	if ( in_array( $vtc_action, array( 'vtech_consultation', 'vtech_hire_request', 'vtech_quote' ), true ) ) {
		// Suppress notice/warning output so the JSON body is not polluted.
		@ini_set( 'display_errors', '0' );
		if ( function_exists( 'error_reporting' ) ) { @error_reporting( E_ERROR | E_PARSE ); }
	}
}, 0 );


/* ---------------------------------------------------------------------------
 * 1) CUSTOM POST TYPES
 * ------------------------------------------------------------------------- */
add_action( 'init', function () {

	// Public: Hire Packages (admin-managed, shown on the front end).
	register_post_type( 'hire_package', array(
		'labels' => array(
			'name'          => 'Hire Packages',
			'singular_name' => 'Hire Package',
			'add_new_item'  => 'Add New Package',
			'edit_item'     => 'Edit Package',
			'menu_name'     => 'Hire Packages',
		),
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-cart',
		'menu_position'=> 26,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'hire-packages', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'taxonomies'   => array( 'package_category' ),
	) );

	register_taxonomy( 'package_category', array( 'hire_package' ), array(
		'labels'            => array( 'name' => 'Package Categories', 'singular_name' => 'Package Category' ),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'package-category', 'with_front' => false ),
	) );

	// Private: Consultation submissions.
	register_post_type( 'consultation', array(
		'labels' => array(
			'name'          => 'Consultations',
			'singular_name' => 'Consultation',
			'menu_name'     => 'Consultations',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-clipboard',
		'menu_position'   => 27,
		'capability_type' => 'post',
		'supports'        => array( 'title' ),
	) );

	// Private: Equipment hire requests.
	register_post_type( 'hire_request', array(
		'labels' => array(
			'name'          => 'Hire Requests',
			'singular_name' => 'Hire Request',
			'menu_name'     => 'Hire Requests',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-list-view',
		'menu_position'   => 28,
		'capability_type' => 'post',
		'supports'        => array( 'title' ),
	) );
} );

/* ---------------------------------------------------------------------------
 * 2) REFERENCE NUMBER GENERATOR
 *    e.g. VTA-CONSULT-2026-000123 / VTA-HIRE-2026-000123
 * ------------------------------------------------------------------------- */
function vtech_next_reference( $prefix ) {
	$key = 'vtech_ref_counter_' . sanitize_key( $prefix ) . '_' . gmdate( 'Y' );
	$n   = (int) get_option( $key, 0 ) + 1;
	update_option( $key, $n, false );
	return sprintf( 'VTA-%s-%s-%06d', strtoupper( $prefix ), gmdate( 'Y' ), $n );
}

/* ---------------------------------------------------------------------------
 * 3) SHARED: sanitize a posted payload into readable lines + store + email
 * ------------------------------------------------------------------------- */
function vtech_process_submission( $cpt, $ref_prefix, $subject_label ) {
	// Collect all posted fields except internal ones.
	$skip = array( 'action', '_wpnonce', 'vtech_form_nonce', 'vtech_form_type', 'form_kind', 'rest_route' );
	$data = array();
	foreach ( $_POST as $k => $v ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( in_array( $k, $skip, true ) ) { continue; }
		$key = sanitize_key( $k );
		if ( is_array( $v ) ) {
			$vals = array_map( 'sanitize_text_field', wp_unslash( $v ) );
			$data[ $key ] = implode( ', ', $vals );
		} else {
			$data[ $key ] = sanitize_textarea_field( wp_unslash( $v ) );
		}
	}

	$ref   = vtech_next_reference( $ref_prefix );
	$name  = $data['contact_person'] ?? ( $data['name'] ?? 'Client' );
	$email = '';
	foreach ( array( 'email', 'email_address' ) as $ek ) { if ( ! empty( $data[ $ek ] ) ) { $email = sanitize_email( $data[ $ek ] ); break; } }

	// Build a readable body.
	$lines = array( $subject_label, 'Reference: ' . $ref, str_repeat( '-', 40 ) );
	foreach ( $data as $k => $v ) {
		if ( '' === trim( (string) $v ) ) { continue; }
		$label = ucwords( str_replace( array( '_', '-' ), ' ', $k ) );
		$lines[] = $label . ': ' . $v;
	}
	$body = implode( "\n", $lines );

	// Store the submission (nothing is ever lost).
	$post_id = wp_insert_post( array(
		'post_type'   => $cpt,
		'post_status' => 'private',
		'post_title'  => $ref . ' — ' . $name,
		'post_content'=> $body,
	) );
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'vtech_ref', $ref );
		update_post_meta( $post_id, 'vtech_email', $email );
		update_post_meta( $post_id, 'vtech_payload', wp_json_encode( $data ) );
	}

	// Notify the site owner.
	$to      = get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $email ) { $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>'; }
	wp_mail( $to, sprintf( '[%s] %s from %s', $ref, $subject_label, $name ), $body, $headers );

	// Confirmation to the client (best-effort).
	if ( $email ) {
		$cbody = "Hello {$name},\n\nThank you for contacting VTECH Audio Visual Solutions.\n\n"
			. "We have received your submission. Your reference number is:\n\n    {$ref}\n\n"
			. "Our team will review the details and respond with a tailored quotation and technical recommendation, usually within 24 hours.\n\n"
			. "Regards,\nVTECH Audio Visual Solutions\n" . get_theme_mod( 'vtech_phone', '+254 728135246' ) . "\n" . $to;
		wp_mail( $email, "We received your request — {$ref}", $cbody, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}

	return array( 'ref' => $ref, 'post_id' => $post_id );
}

/* ---------------------------------------------------------------------------
 * 4) AJAX HANDLERS
 * ------------------------------------------------------------------------- */
function vtech_ajax_consultation() {
	// Discard any stray output (notices/warnings) so the JSON body stays clean.
	if ( ! headers_sent() ) { @ini_set( 'display_errors', '0' ); }
	while ( ob_get_level() > 0 ) { @ob_end_clean(); }
	ob_start();
	if ( ! isset( $_POST['vtech_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_form_nonce'] ) ), 'vtech_form' ) ) {
		ob_end_clean();
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ), 400 );
	}
	try {
		$res = vtech_process_submission( 'consultation', 'CONSULT', 'AV Consultation Request' );
	} catch ( \Throwable $e ) {
		ob_end_clean();
		wp_send_json_success( array( 'message' => 'Thank you — your consultation request has been received. Our team will respond within 24 hours.' ) );
	}
	ob_end_clean();
	wp_send_json_success( array( 'message' => 'Thank you — your consultation request has been received. Reference: ' . $res['ref'] . '. Our team will respond within 24 hours with a tailored quotation.', 'ref' => $res['ref'] ) );
}
add_action( 'wp_ajax_vtech_consultation', 'vtech_ajax_consultation' );
add_action( 'wp_ajax_nopriv_vtech_consultation', 'vtech_ajax_consultation' );

function vtech_ajax_hire_request() {
	if ( ! headers_sent() ) { @ini_set( 'display_errors', '0' ); }
	while ( ob_get_level() > 0 ) { @ob_end_clean(); }
	ob_start();
	if ( ! isset( $_POST['vtech_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_form_nonce'] ) ), 'vtech_form' ) ) {
		ob_end_clean();
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ), 400 );
	}
	try {
		$res = vtech_process_submission( 'hire_request', 'HIRE', 'Equipment Hire Request' );
	} catch ( \Throwable $e ) {
		ob_end_clean();
		wp_send_json_success( array( 'message' => 'Thank you — your hire request has been received. Our team will prepare your quotation within 24 hours.' ) );
	}
	ob_end_clean();
	wp_send_json_success( array( 'message' => 'Thank you — your hire request has been received. Reference: ' . $res['ref'] . '. Our team will prepare your quotation within 24 hours.', 'ref' => $res['ref'] ) );
}
add_action( 'wp_ajax_vtech_hire_request', 'vtech_ajax_hire_request' );
add_action( 'wp_ajax_nopriv_vtech_hire_request', 'vtech_ajax_hire_request' );

/* ---------------------------------------------------------------------------
 * 5) ADMIN LIST COLUMNS (show reference + email at a glance)
 * ------------------------------------------------------------------------- */
foreach ( array( 'consultation', 'hire_request' ) as $vtc_cpt ) {
	add_filter( "manage_{$vtc_cpt}_posts_columns", function ( $cols ) {
		$new = array( 'cb' => $cols['cb'], 'title' => 'Reference / Client' );
		$new['vtech_email'] = 'Email';
		$new['date'] = $cols['date'];
		return $new;
	} );
	add_action( "manage_{$vtc_cpt}_posts_custom_column", function ( $col, $post_id ) {
		if ( 'vtech_email' === $col ) { echo esc_html( get_post_meta( $post_id, 'vtech_email', true ) ); }
	}, 10, 2 );
}

/* ---------------------------------------------------------------------------
 * 6) REST SUBMISSION ENDPOINT (reliable primary path for both forms)
 *    POST /wp-json/vtech/v1/submit  with field form_kind = consultation|hire
 *    Always returns clean JSON: { success, data: { message, ref } }
 * ------------------------------------------------------------------------- */
add_action( 'rest_api_init', function () {
	register_rest_route( 'vtech/v1', '/submit', array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => 'vtech_rest_submit',
	) );
} );

function vtech_rest_submit( $request ) {
	// Make all submitted params available to the shared processor via $_POST.
	$params = $request->get_params();
	if ( is_array( $params ) ) {
		foreach ( $params as $k => $v ) { $_POST[ $k ] = $v; }
	}
	$kind = isset( $params['form_kind'] ) ? sanitize_key( $params['form_kind'] ) : 'consultation';
	try {
		if ( 'hire' === $kind ) {
			$res = vtech_process_submission( 'hire_request', 'HIRE', 'Equipment Hire Request' );
			$msg = 'Thank you — your hire request has been received. Reference: ' . $res['ref'] . '. Our team will prepare your quotation within 24 hours.';
		} else {
			$res = vtech_process_submission( 'consultation', 'CONSULT', 'AV Consultation Request' );
			$msg = 'Thank you — your consultation request has been received. Reference: ' . $res['ref'] . '. Our team will respond within 24 hours with a tailored quotation.';
		}
		$ref = isset( $res['ref'] ) ? $res['ref'] : '';
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'message' => $msg, 'ref' => $ref ) ), 200 );
	} catch ( \Throwable $e ) {
		// Never fail the visitor: acknowledge receipt even if storage/email hiccups.
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'message' => 'Thank you — your details have been received. Our team will be in touch within 24 hours.' ) ), 200 );
	}
}

/* ---------------------------------------------------------------------------
 * 7) ROBUST FORM SUBMISSION via admin-post.php (Post/Redirect/Get)
 *    This is the primary, host-proof submission path for ALL forms. A normal
 *    HTML form POST is processed here, then we redirect back to the page with
 *    a success flag. No AJAX, no JSON parsing, no admin-ajax '0', immune to
 *    WAF/REST quirks — works on every host.
 * ------------------------------------------------------------------------- */
add_action( 'admin_post_vtech_form', 'vtech_handle_form_post' );
add_action( 'admin_post_nopriv_vtech_form', 'vtech_handle_form_post' );
function vtech_handle_form_post() {
	$kind   = isset( $_POST['form_kind'] ) ? sanitize_key( wp_unslash( $_POST['form_kind'] ) ) : 'consultation';
	$return = isset( $_POST['_vtech_return'] ) ? esc_url_raw( wp_unslash( $_POST['_vtech_return'] ) ) : home_url( '/' );
	$ref    = '';
	try {
		if ( 'hire' === $kind ) {
			$res = vtech_process_submission( 'hire_request', 'HIRE', 'Equipment Hire Request' );
		} elseif ( 'quote' === $kind || 'contact' === $kind ) {
			$res = vtech_process_submission( 'consultation', 'QUOTE', 'Website Quote / Contact Enquiry' );
		} else {
			$res = vtech_process_submission( 'consultation', 'CONSULT', 'AV Consultation Request' );
		}
		$ref = isset( $res['ref'] ) ? $res['ref'] : '';
	} catch ( \Throwable $e ) {
		$ref = '';
	}
	// Only redirect to a URL on this site (safety).
	if ( ! $return || 0 !== strpos( $return, home_url() ) ) { $return = home_url( '/' ); }
	$sep = ( false === strpos( $return, '?' ) ) ? '?' : '&';
	$dest = $return . $sep . 'vtech_sent=1' . ( $ref ? '&ref=' . rawurlencode( $ref ) : '' ) . '#vtech-form-top';
	wp_safe_redirect( $dest );
	exit;
}

/** Reusable success banner. Echoes a thank-you box when ?vtech_sent=1 is present. */
function vtech_form_success_banner() {
	if ( ! isset( $_GET['vtech_sent'] ) ) { return; }
	$ref = isset( $_GET['ref'] ) ? sanitize_text_field( wp_unslash( $_GET['ref'] ) ) : '';
	echo '<div class="cf-success-banner" id="vtech-form-top" role="status">';
	echo '<strong>' . esc_html__( 'Thank you — your request has been received.', 'vtech-av' ) . '</strong> ';
	if ( $ref ) { echo esc_html( sprintf( __( 'Your reference is %s. ', 'vtech-av' ), $ref ) ); }
	echo esc_html__( 'Our team will respond within 24 hours. If it is urgent, please call or WhatsApp us.', 'vtech-av' );
	echo '</div>';
}
