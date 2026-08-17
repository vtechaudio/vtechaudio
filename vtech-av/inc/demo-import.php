<?php
/**
 * One-click demo/starter content registration.
 *
 * Two supported paths:
 *  A) A companion importer plugin (e.g. "One Click Demo Import") reads the
 *     filter below to locate the bundled WXR + widgets + customizer files.
 *  B) Manual: Tools → Import → WordPress → upload docs/demo-content.xml.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_filter( 'ocdi/import_files', function () {
	return array(
		array(
			'import_file_name'           => 'VTECH Starter Content',
			'local_import_file'          => VTECH_DIR . '/docs/demo-content.xml',
			'local_import_customizer_file' => VTECH_DIR . '/docs/demo-customizer.dat',
			'import_preview_image_url'   => VTECH_URI . '/assets/img/og-default.jpg',
			'import_notice'              => 'Imports sample Services, Projects, Industries, Testimonials and FAQs. Add your own images afterwards.',
		),
	);
} );

// After import: set front page + menus automatically.
add_action( 'ocdi/after_import', function () {
	$home = get_page_by_path( 'home' );
	$blog = get_page_by_path( 'blog' );
	if ( $home ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $home->ID ); }
	if ( $blog ) { update_option( 'page_for_posts', $blog->ID ); }

	$menu = wp_get_nav_menu_object( 'Primary' );
	if ( $menu ) {
		set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu->term_id ) );
	}
	flush_rewrite_rules();
} );
