<?php
/**
 * Block pattern registration. Patterns live as PHP files in /patterns and are
 * auto-registered by WP 6.x from the /patterns dir; here we register the
 * pattern category and the reusable homepage section grouping.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
	register_block_pattern_category( 'vtech', array( 'label' => __( 'VTECH', 'vtech-av' ) ) );
	register_block_pattern_category( 'vtech-home', array( 'label' => __( 'VTECH — Homepage Sections', 'vtech-av' ) ) );
	register_block_pattern_category( 'vtech-service', array( 'label' => __( 'VTECH — Service Page', 'vtech-av' ) ) );
} );

/**
 * Register the "one-click" homepage composition as a synced pattern so that a
 * fresh install can assemble the front page from all the section patterns.
 * The individual /patterns/*.php files carry the actual markup.
 */
add_filter( 'block_categories_all', function ( $cats ) {
	array_unshift( $cats, array( 'slug' => 'vtech-blocks', 'title' => __( 'VTECH Blocks', 'vtech-av' ) ) );
	return $cats;
} );
