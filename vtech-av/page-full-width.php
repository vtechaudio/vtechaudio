<?php
/**
 * Template Name: Full Width (no sidebar)
 *
 * IMPORTANT: If this template is assigned to the site's front page, a page
 * template overrides front-page.php in the WordPress hierarchy. To guarantee
 * the homepage ALWAYS renders our full design, when this template is used on
 * the front page we load front-page.php directly. Otherwise it renders the
 * page's own block content full-width.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// If this page IS the front page, render the full coded homepage instead.
if ( is_front_page() ) {
	$fp = locate_template( 'front-page.php' );
	if ( $fp ) { include $fp; return; }
}

get_header();
while ( have_posts() ) : the_post();
	the_content();
endwhile;
get_footer();
