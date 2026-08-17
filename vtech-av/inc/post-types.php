<?php
/**
 * Custom Post Types for VTECH Audio Visual.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_register_cpts() {

	$cpts = array(
		'service' => array(
			'singular' => 'Service', 'plural' => 'Services', 'slug' => 'services',
			'icon' => 'dashicons-megaphone', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'taxes' => array( 'service_category' ),
		),
		'project' => array(
			'singular' => 'Project', 'plural' => 'Projects', 'slug' => 'projects',
			'icon' => 'dashicons-portfolio', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxes' => array( 'industry', 'technology', 'project_location' ),
		),
		'industry' => array(
			'singular' => 'Industry Page', 'plural' => 'Industries', 'slug' => 'industries',
			'icon' => 'dashicons-building', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxes' => array(),
		),
		'equipment' => array(
			'singular' => 'Equipment', 'plural' => 'Equipment Hire', 'slug' => 'equipment-hire',
			'icon' => 'dashicons-media-audio', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxes' => array( 'equipment_category', 'brand' ),
		),
		'brand' => array(
			'singular' => 'Brand', 'plural' => 'Brands', 'slug' => 'brands',
			'icon' => 'dashicons-awards', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'taxes' => array(),
		),
		'testimonial' => array(
			'singular' => 'Testimonial', 'plural' => 'Testimonials', 'slug' => 'testimonials',
			'icon' => 'dashicons-format-quote', 'has_archive' => false,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'taxes' => array(),
		),
		'case_study' => array(
			'singular' => 'Case Study', 'plural' => 'Case Studies', 'slug' => 'case-studies',
			'icon' => 'dashicons-analytics', 'has_archive' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'taxes' => array( 'industry' ),
		),
		'faq' => array(
			'singular' => 'FAQ', 'plural' => 'FAQs', 'slug' => 'faqs',
			'icon' => 'dashicons-editor-help', 'has_archive' => false,
			'supports' => array( 'title', 'editor' ),
			'taxes' => array( 'faq_topic' ),
		),
	);

	foreach ( $cpts as $key => $c ) {
		$labels = array(
			'name'          => $c['plural'],
			'singular_name' => $c['singular'],
			'add_new_item'  => sprintf( __( 'Add New %s', 'vtech-av' ), $c['singular'] ),
			'edit_item'     => sprintf( __( 'Edit %s', 'vtech-av' ), $c['singular'] ),
			'menu_name'     => $c['plural'],
		);
		register_post_type( $key, array(
			'labels'       => $labels,
			'public'       => true,
			'show_in_rest' => true, // Gutenberg + headless-ready.
			'menu_icon'    => $c['icon'],
			'has_archive'  => $c['has_archive'],
			'rewrite'      => array( 'slug' => $c['slug'], 'with_front' => false ),
			'supports'     => $c['supports'],
			'taxonomies'   => $c['taxes'],
		) );
	}
}
add_action( 'init', 'vtech_register_cpts' );
