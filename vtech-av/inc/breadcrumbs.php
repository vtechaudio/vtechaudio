<?php
/**
 * Breadcrumbs with BreadcrumbList schema. Use vtech_breadcrumbs() in templates.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_breadcrumbs() {
	if ( is_front_page() ) { return; }

	$items = array();
	$items[] = array( 'name' => 'Home', 'url' => home_url( '/' ) );

	if ( is_singular( 'service' ) || is_post_type_archive( 'service' ) ) {
		$items[] = array( 'name' => 'Services', 'url' => get_post_type_archive_link( 'service' ) );
	} elseif ( is_singular( 'project' ) || is_post_type_archive( 'project' ) ) {
		$items[] = array( 'name' => 'Projects', 'url' => get_post_type_archive_link( 'project' ) );
	} elseif ( is_singular( 'industry' ) || is_post_type_archive( 'industry' ) ) {
		$items[] = array( 'name' => 'Industries', 'url' => get_post_type_archive_link( 'industry' ) );
	} elseif ( is_singular( 'case_study' ) || is_post_type_archive( 'case_study' ) ) {
		$items[] = array( 'name' => 'Case Studies', 'url' => get_post_type_archive_link( 'case_study' ) );
	} elseif ( is_singular( 'equipment' ) || is_post_type_archive( 'equipment' ) ) {
		$items[] = array( 'name' => 'Equipment Hire', 'url' => get_post_type_archive_link( 'equipment' ) );
	} elseif ( is_singular( 'hire_package' ) || is_post_type_archive( 'hire_package' ) ) {
		$items[] = array( 'name' => 'Hire Packages', 'url' => get_post_type_archive_link( 'hire_package' ) );
	} elseif ( is_singular( 'post' ) || is_home() ) {
		$items[] = array( 'name' => 'Blog', 'url' => get_permalink( get_option( 'page_for_posts' ) ) );
	}

	if ( is_singular() ) {
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_page() ) {
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	}

	// Visible breadcrumb.
	echo '<nav class="breadcrumbs" aria-label="Breadcrumb"><ol>';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i === $last ) {
			echo '<li aria-current="page">' . esc_html( $item['name'] ) . '</li>';
		} else {
			echo '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';

	// Schema — Rank Math (or another SEO plugin) owns BreadcrumbList when active.
	if ( function_exists( 'vtech_seo_plugin_active' ) && vtech_seo_plugin_active() ) { return; }
	$ld = array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array() );
	foreach ( $items as $i => $item ) {
		$ld['itemListElement'][] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $item['name'], 'item' => $item['url'] );
	}
	echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES ) . "</script>\n";
}
