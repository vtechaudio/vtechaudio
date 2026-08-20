<?php
/**
 * Native quote/contact form handler. Powers the fallback form on the Contact
 * page (page-contact.php) when Contact Form 7 is not configured. Emails the
 * site owner (Theme Options -> vtech_email) and returns JSON for the JS.
 *
 * If Contact Form 7 IS installed and a form ID is set in Theme Options
 * (vtech_cf7_id), the page renders the CF7 form instead and this handler is
 * simply unused — no conflict.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_handle_quote() {
	// Nonce check (both AJAX and non-JS POST paths).
	if ( ! isset( $_POST['vtech_quote_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_quote_nonce'] ) ), 'vtech_quote' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ), 400 );
	}

	// Spam honeypot: the hidden "website" field must stay empty for humans.
	if ( \! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => 'Thank you — your request has been received.' ) );
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$org     = sanitize_text_field( wp_unslash( $_POST['organisation'] ?? '' ) );
	$service = sanitize_text_field( wp_unslash( $_POST['service'] ?? '' ) );
	$loc     = sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( ! $name || ! $email || ! $message ) {
		wp_send_json_error( array( 'message' => 'Please complete the required fields.' ), 422 );
	}

	$to      = get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' );
	$subject = sprintf( 'New quote request from %s — %s', $name, $service ?: 'AV enquiry' );
	$body    = "New quote request via vtechaudio.co.ke\n\n"
		. "Name: {$name}\n"
		. "Email: {$email}\n"
		. "Phone: {$phone}\n"
		. "Organisation: {$org}\n"
		. "Service: {$service}\n"
		. "Location: {$loc}\n\n"
		. "Details:\n{$message}\n";
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	// Store as a lead (custom post) so nothing is ever lost, even if mail fails.
	wp_insert_post( array(
		'post_type'   => 'vtech_lead',
		'post_status' => 'private',
		'post_title'  => $subject,
		'post_content'=> $body,
	) );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Thank you — your request has been sent. We\'ll respond within 24 hours.' ) );
	}
	wp_send_json_error( array( 'message' => 'We saved your request but email delivery failed. Please also call or WhatsApp us.' ), 200 );
}
add_action( 'wp_ajax_vtech_quote', 'vtech_handle_quote' );
add_action( 'wp_ajax_nopriv_vtech_quote', 'vtech_handle_quote' );

// Lightweight private CPT to capture leads.
add_action( 'init', function () {
	register_post_type( 'vtech_lead', array(
		'labels'   => array( 'name' => 'Quote Leads', 'singular_name' => 'Quote Lead' ),
		'public'   => false,
		'show_ui'  => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-email-alt',
		'supports'     => array( 'title', 'editor' ),
		'capability_type' => 'post',
	) );
} );
