<?php
/**
 * VTECH Audio Visual — theme bootstrap.
 *
 * @package VTECH_AV
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'VTECH_VERSION', '5.35.0' );
define( 'VTECH_BUILD', 'v5.35-2026-08-20' );
define( 'VTECH_DIR', get_template_directory() );
define( 'VTECH_URI', get_template_directory_uri() );

/**
 * Theme supports & core setup.
 */
function vtech_setup() {
	load_theme_textdomain( 'vtech-av', VTECH_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array( 'height' => 48, 'width' => 180, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );

	// Image sizes tuned for AV portfolio + WebP.
	add_image_size( 'vtech-hero', 1920, 1080, true );
	add_image_size( 'vtech-card', 720, 480, true );
	add_image_size( 'vtech-square', 600, 600, true );
	add_image_size( 'vtech-og', 1200, 630, true );

	register_nav_menus( array(
		'footer_company' => __( 'Footer Company Links', 'vtech-av' ),
		'primary'   => __( 'Primary Menu', 'vtech-av' ),
		'footer'    => __( 'Footer Menu', 'vtech-av' ),
		'utility'   => __( 'Utility (top bar)', 'vtech-av' ),
	) );
}
add_action( 'after_setup_theme', 'vtech_setup' );

/**
 * Load theme includes defensively. Using require_once + file_exists guards
 * means a problem in any single file can never abort the whole theme (which
 * previously stopped the VTECH Setup menu from registering). The setup wizard
 * and required-plugins are loaded FIRST so the setup menu always appears even
 * if a later file has an issue.
 */
function vtech_require( $rel ) {
	$path = VTECH_DIR . $rel;
	if ( file_exists( $path ) ) { require_once $path; }
}

// Setup + plugins first — guarantees the "VTECH Setup" menu always loads.
vtech_require( '/inc/form-helpers.php' );
vtech_require( '/inc/setup-wizard.php' );
vtech_require( '/inc/required-plugins.php' );

// Content structure.
vtech_require( '/inc/post-types.php' );
vtech_require( '/inc/taxonomies.php' );
vtech_require( '/inc/acf-fields.php' );

// SEO & structured data.
vtech_require( '/inc/seo-meta.php' );
vtech_require( '/inc/schema.php' );
vtech_require( '/inc/breadcrumbs.php' );

// Performance layer.
vtech_require( '/inc/performance.php' );

// Blocks, patterns, template options.
vtech_require( '/inc/block-patterns.php' );
vtech_require( '/inc/theme-options.php' );

// Advanced feature stubs.
vtech_require( '/inc/features/cost-estimator.php' );
vtech_require( '/inc/features/equipment-catalog.php' );
vtech_require( '/inc/features/client-portal.php' );
vtech_require( '/inc/features/contact-form.php' );
vtech_require( '/inc/features/hire-module.php' );
vtech_require( '/inc/features/package-meta.php' );
vtech_require( '/inc/features/inventory.php' );
vtech_require( '/inc/lib/class-vtech-pdf.php' );
vtech_require( '/inc/features/quote-invoice.php' );
vtech_require( '/inc/features/payments.php' );
vtech_require( '/inc/features/accept-booking.php' );
vtech_require( '/inc/features/clients.php' );
vtech_require( '/inc/features/brand-logos.php' );

// Demo importer.
vtech_require( '/inc/demo-import.php' );

/**
 * Enqueue front-end assets. Body CSS is deferred; critical CSS is inlined
 * in the head (see inc/performance.php). No jQuery on the front end.
 */
function vtech_assets() {
	// main.css is now inlined in <head> by inc/performance.php (foolproof).
	// A no-op handle is still registered so child themes / plugins that
	// declare 'vtech-main' as a dependency don't error.
	wp_enqueue_style( 'vtech-main', VTECH_URI . '/assets/css/main.css', array(), VTECH_VERSION );

	// Vanilla JS modules, deferred.
	wp_enqueue_script( 'vtech-app', VTECH_URI . '/assets/js/app.js', array(), VTECH_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );

	wp_localize_script( 'vtech-app', 'VTECH', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'vtech_nonce' ),
		'whatsapp'  => get_theme_mod( 'vtech_whatsapp', '254728135246' ),
		'phone'     => get_theme_mod( 'vtech_phone', '+254 728 135 246' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'vtech_assets' );

/**
 * Editor styles so Gutenberg matches the front end.
 */
function vtech_editor_assets() {
	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'vtech_editor_assets' );

/**
 * Register widget areas (footer only; site is mostly block-based).
 */
function vtech_widgets() {
	register_sidebar( array(
		'name'          => __( 'Footer — Column', 'vtech-av' ),
		'id'            => 'footer-1',
		'before_widget' => '<div class="footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer-widget__title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'vtech_widgets' );

/**
 * Body classes to help scope CSS per template.
 */
function vtech_body_classes( $classes ) {
	if ( is_front_page() ) { $classes[] = 'is-home'; }
	if ( is_singular( 'service' ) ) { $classes[] = 'is-service'; }
	if ( is_post_type_archive( 'project' ) || is_singular( 'project' ) ) { $classes[] = 'is-project'; }
	return $classes;
}
add_filter( 'body_class', 'vtech_body_classes' );

/**
 * Security hardening — remove version fingerprints, disable XML-RPC pingback,
 * strip emoji script, and set safe headers.
 */
require VTECH_DIR . '/inc/security.php';

/**
 * Clean archive titles — remove the "Archives:", "Category:" etc. prefixes so
 * industry/project/blog archives show a professional bare title.
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
add_filter( 'get_the_archive_title', function ( $title ) {
	if ( is_post_type_archive( 'industry' ) ) { return 'Industries We Serve in Kenya'; }
	if ( is_post_type_archive( 'project' ) )  { return 'Featured AV Projects in Kenya'; }
	if ( is_tax() ) { return single_term_title( '', false ); }
	return $title;
}, 20 );

/**
 * Flush rewrite rules on theme activation so all custom-post-type URLs
 * (services, hire packages, industries, projects) resolve immediately —
 * no need to visit Settings > Permalinks or re-run setup.
 */
add_action( 'after_switch_theme', function () {
	// On activation register our types directly, then flush once.
	if ( function_exists( 'vtech_register_cpts' ) ) { vtech_register_cpts(); }
	if ( function_exists( 'vtech_register_taxonomies' ) ) { vtech_register_taxonomies(); }
	do_action( 'vtech_register_module_types' );
	flush_rewrite_rules( false );
	delete_option( 'vtech_rewrite_ver' );
} );

/**
 * VERSION-GATED FLUSH (bulletproof).
 * Runs late on every 'init' AFTER all CPTs/taxonomies/rewrite rules are
 * registered. If the stored rewrite version does not match the code version,
 * flush and store the new version. This guarantees custom + regular page URLs
 * resolve even when activation-time flushes don't take on a given host — no
 * need to visit Settings > Permalinks. It flushes at most once per version.
 */
define( 'VTECH_REWRITE_VER', '27' );
add_action( 'init', 'vtech_versioned_flush', 99999 );
function vtech_versioned_flush() {
	if ( get_option( 'vtech_rewrite_ver' ) !== VTECH_REWRITE_VER ) {
		flush_rewrite_rules( false );
		update_option( 'vtech_rewrite_ver', VTECH_REWRITE_VER );
	}
}

/**
 * HOST-PROOF TEMPLATE FORCING.
 * Some hosts drop the _wp_page_template meta or fail to match the custom
 * template, leaving pages like Equipment Hire blank (its template renders
 * directly, not via the_content). Force the correct template by page slug so
 * it always applies, independent of the stored meta.
 */
add_filter( 'template_include', 'vtech_force_page_templates', 9999 );
function vtech_force_page_templates( $template ) {
	$map = array(
		'equipment-hire'         => 'page-equipment-hire.php',
		'equipment-hire-request' => 'page-hire-request.php',
		'consultation'           => 'page-consultation.php',
		'about'                  => 'page-about.php',
		'contact'                => 'page-contact.php',
	);
	// Resolve the requested page by slug directly. Do NOT rely on is_page(),
	// which can be false on some hosts/permalink setups, letting index.php win.
	$slug = '';
	$obj  = get_queried_object();
	if ( $obj instanceof WP_Post ) {
		$slug = $obj->post_name;
	} else {
		$req = trim( (string) get_query_var( 'pagename' ), '/' );
		if ( '' === $req ) { $req = trim( (string) get_query_var( 'name' ), '/' ); }
		if ( $req ) { $parts = explode( '/', $req ); $slug = end( $parts ); }
	}
	if ( $slug && isset( $map[ $slug ] ) ) {
		$located = locate_template( $map[ $slug ] );
		if ( $located ) { return $located; }
	}
	return $template;
}

/**
 * Build a WhatsApp click-to-chat URL pre-filled with a hire package's details.
 * Lets customers book without filling a form.
 */
function vtech_whatsapp_book_url( $post_id ) {
	$wa = preg_replace( '/\\D+/', '', (string) get_theme_mod( 'vtech_whatsapp', '254728135246' ) );
	$title = get_the_title( $post_id );
	$af    = function_exists( 'get_field' );
	$price = $af ? get_field( 'price', $post_id ) : get_post_meta( $post_id, 'price', true );
	$cap   = $af ? get_field( 'capacity', $post_id ) : get_post_meta( $post_id, 'capacity', true );
	$url   = get_permalink( $post_id );
	$lines = array();
	$lines[] = 'Hello VTECH, I would like to book the ' . $title . '.';
	if ( $price ) { $lines[] = 'Price: KES ' . number_format( (float) $price ); }
	if ( $cap )   { $lines[] = 'Capacity: ' . $cap . ' guests'; }
	if ( $url )   { $lines[] = 'Package: ' . $url; }
	$lines[] = 'Please share availability and next steps.';
	$msg = implode( "\n", $lines );
	return 'https://wa.me/' . $wa . '?text=' . rawurlencode( $msg );
}
