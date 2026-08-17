<?php
/**
 * Security hardening (safe subset).
 *
 * All header() calls and DISALLOW_FILE_EDIT have been intentionally REMOVED.
 * Manual header() calls are fragile across hosts (they fail with "headers
 * already sent" if any byte is output early) and are better handled at the
 * server / a security plugin level. Everything here is output-free and cannot
 * break page rendering or hide the Theme File Editor.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Remove WP version fingerprint.
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Disable XML-RPC (a common brute-force / DDoS vector).
add_filter( 'xmlrpc_enabled', '__return_false' );

// Remove pingback header.
add_filter( 'wp_headers', function ( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
} );

// Disable emoji script/style (perf + surface reduction).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

// Remove RSD / WLW manifest links.
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

// Hide login errors detail.
add_filter( 'login_errors', function () { return __( 'Invalid credentials.', 'vtech-av' ); } );

// NOTE: header() security headers and DISALLOW_FILE_EDIT removed on purpose.
// Add security headers via .htaccess or a security plugin if desired.


/* -------------------------------------------------------------------------
 * Extended hardening (v5.28.0): safe response headers, file-editor lockdown,
 * and user/author enumeration blocks. All applied via WordPress filters/hooks
 * (never raw header()), so they cannot cause "headers already sent" errors.
 * ------------------------------------------------------------------------- */

// Safe security response headers (applied by WP through the wp_headers filter).
add_filter( 'wp_headers', function ( $headers ) {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']     = 'geolocation=(), microphone=(), camera=(), interest-cohort=()';
	if ( function_exists( 'is_ssl' ) && is_ssl() ) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
	}
	return $headers;
} );

// Disable the built-in Theme/Plugin File Editor (a common privilege-escalation vector).
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) { define( 'DISALLOW_FILE_EDIT', true ); }

// Block REST API user enumeration for logged-out visitors.
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) { unset( $endpoints['/wp/v2/users'] ); }
		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) { unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ); }
	}
	return $endpoints;
} );

// Block ?author=N and /author/ archive enumeration on the public front end.
add_action( 'template_redirect', function () {
	if ( is_admin() ) { return; }
	if ( ( isset( $_GET['author'] ) || is_author() ) && ! is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
