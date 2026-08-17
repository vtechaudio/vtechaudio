<?php
/**
 * VTECH child theme. Enqueue parent styles + your overrides here.
 *
 * @package VTECH_AV_Child
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'wp_enqueue_scripts', function () {
	// Parent critical/deferred CSS is handled in the parent theme's inc/performance.php.
	// Add child overrides:
	wp_enqueue_style( 'vtech-child', get_stylesheet_directory_uri() . '/style.css', array(), '1.0.0' );
}, 20 );

// Example override hook:
// add_filter( 'vtech_something', function ( $v ) { return $v; } );
