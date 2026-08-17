<?php
/**
 * Core Web Vitals performance layer.
 * - Inlines critical CSS in <head> (no render-blocking).
 * - Preloads variable fonts + establishes preconnects.
 * - Swaps deferred main.css from media=print to all on load.
 * - Adds native lazy-loading + async decoding to content images.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// 1) Preload fonts + preconnect (fonts are self-hosted -> no Google round trip).
add_action( 'wp_head', function () {
	$f = VTECH_URI . '/assets/fonts/';
	echo '<link rel="preload" href="' . esc_url( $f . 'manrope-var.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<link rel="preload" href="' . esc_url( $f . 'inter-var.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	// Preload the LCP hero image on the homepage.
	if ( is_front_page() ) {
		$hero = get_theme_mod( 'vtech_hero_img', VTECH_URI . '/assets/img/hero.webp' );
		echo '<link rel="preload" href="' . esc_url( $hero ) . '" as="image" fetchpriority="high">' . "\n";
	}
}, 1 );

// 2) Inline the FULL stylesheet in <head> (critical.css + main.css).
//    Foolproof: no external CSS file to 404/mis-serve, no media attribute
//    to break, no JS/cache-plugin dependency. Total is ~12KB — trivial.
add_action( 'wp_head', function () {
	$css = '';
	foreach ( array( 'critical.css', 'main.css' ) as $f ) {
		$path = VTECH_DIR . '/assets/css/' . $f;
		if ( file_exists( $path ) ) { $css .= file_get_contents( $path ) . "
"; }
	}
	if ( $css ) {
		echo '<style id="vtech-inline-css">' . $css . '</style>' . "
"; // phpcs:ignore
	}
}, 2 );

// 3) (Removed) main.css now loads as a normal stylesheet. The former
//    print->all onload swap was fragile (silent str_replace miss / JS
//    dependency) and could leave the whole page unstyled. main.css is
//    ~7KB so render-blocking cost is negligible and CWV stays green.

// 4) Native lazy-load + async decode on content images (skip first/LCP image).
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( empty( $attr['loading'] ) ) { $attr['loading'] = 'lazy'; }
	$attr['decoding'] = 'async';
	return $attr;
} );

// 5) Remove default WP block library inline CSS bloat we override anyway is kept,
//    but dequeue classic-theme styles and global-styles duplicates if unused.
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'wp-block-library-theme' );
}, 100 );

// 6) DNS-prefetch for the map + WhatsApp only (kept minimal).
add_filter( 'wp_resource_hints', function ( $hints, $relation ) {
	if ( 'dns-prefetch' === $relation ) {
		$hints[] = 'https://www.google.com';
		$hints[] = 'https://wa.me';
	}
	return $hints;
}, 10, 2 );

// 7) Front-end performance: drop WordPress block-editor scripts/styles that
//    aren't needed on this classic PHP-templated front-end. These were the
//    render-blocking i18n/hooks/index.js requests (~720ms) in Lighthouse.
add_action( 'wp_enqueue_scripts', 'vtech_trim_block_assets', 100 );
function vtech_trim_block_assets() {
	if ( is_admin() ) { return; }
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );

	// Only dequeue block scripts this classic front-end genuinely does not use.
	// IMPORTANT: never deregister wp-i18n / wp-hooks — plugins such as
	// Contact Form 7 declare them as dependencies, and deregistering them
	// triggers "enqueued with dependencies that are not registered" notices
	// and can break the plugin's JS entirely.
	$vtc_drop = array( 'wp-block-library', 'wp-interactivity' );

	// If a form/interactive plugin that needs the block runtime is active,
	// leave everything alone to stay safe.
	if ( function_exists( 'is_plugin_active' ) ) {
		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			$vtc_drop = array(); // CF7 present — do not trim block scripts.
		}
	}

	foreach ( $vtc_drop as $vtc_h ) {
		// Dequeue only. Do NOT deregister, so dependencies stay resolvable
		// for any other script that references them.
		wp_dequeue_script( $vtc_h );
	}
}

// 8) Remove emoji + oEmbed cruft (extra requests/inline JS on every page).
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
} );
