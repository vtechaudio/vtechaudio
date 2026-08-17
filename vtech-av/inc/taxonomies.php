<?php
/**
 * Custom taxonomies.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_register_taxonomies() {
	$taxes = array(
		'service_category'   => array( 'Service Category', 'Service Categories', array( 'service' ), 'service-category' ),
		'industry'           => array( 'Industry', 'Industries', array( 'project', 'case_study' ), 'industry' ),
		'technology'         => array( 'Technology Used', 'Technologies', array( 'project' ), 'technology' ),
		'project_location'   => array( 'Location', 'Locations', array( 'project' ), 'location' ),
		'equipment_category' => array( 'Equipment Category', 'Equipment Categories', array( 'equipment' ), 'equipment-category' ),
		'brand'              => array( 'Brand', 'Brands', array( 'equipment' ), 'equipment-brand' ),
		'faq_topic'          => array( 'FAQ Topic', 'FAQ Topics', array( 'faq' ), 'faq-topic' ),
	);

	foreach ( $taxes as $slug => $t ) {
		register_taxonomy( $slug, $t[2], array(
			'labels'            => array( 'name' => $t[1], 'singular_name' => $t[0] ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => $t[3], 'with_front' => false ),
		) );
	}
}
add_action( 'init', 'vtech_register_taxonomies' );
