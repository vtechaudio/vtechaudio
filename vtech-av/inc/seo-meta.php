<?php
/**
 * SEO meta output. Defers to Yoast/RankMath/SEOPress when active
 * (checks known constants) to avoid duplicate tags; otherwise outputs
 * a full title/description/canonical/OpenGraph/Twitter set.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

function vtech_meta_description() {
	$d = '';
	if ( is_singular() ) {
		$d = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content' ) ), 30 );
	} elseif ( is_post_type_archive( 'service' ) ) {
		$d = 'Professional audio visual services in Kenya — sound systems, LED screens, stage lighting, conference systems, acoustic treatment and installation by VTECH.';
	} elseif ( is_post_type_archive( 'project' ) ) {
		$d = 'Featured audio visual installation projects across Kenya by VTECH — sound systems, LED screens, conference AV, stage lighting and acoustics.';
	} elseif ( is_post_type_archive( 'industry' ) ) {
		$d = 'AV solutions by sector in Kenya — churches, hotels, schools, corporates, government and healthcare — designed, installed and supported by VTECH.';
	} elseif ( is_post_type_archive( 'equipment' ) || is_post_type_archive( 'hire_package' ) ) {
		$d = 'Audio visual equipment hire in Nairobi and across Kenya — sound systems, PA, LED screens, lighting and conference gear with delivery, setup and technical support.';
	} elseif ( is_front_page() ) {
		$d = 'VTECH Audio Visual Solutions — Kenya\'s premium AV company. Professional sound systems, LED screens, conference & PA systems, stage lighting, acoustics, CCTV and equipment hire. Get a quote in 24 hours.';
	} else {
		$d = get_bloginfo( 'description' );
	}
	return trim( wp_strip_all_tags( $d ) );
}

add_action( 'wp_head', function () {
	if ( vtech_seo_plugin_active() ) { return; } // Let the SEO plugin own meta.

	$desc = esc_attr( vtech_meta_description() );
	$url  = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	$title = wp_get_document_title();
	$img  = get_theme_mod( 'vtech_og_default', VTECH_URI . '/assets/img/og-default.jpg' );
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'vtech-og' );
	}

	echo '<meta name="description" content="' . $desc . '">' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";

	// OpenGraph.
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . $desc . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="VTECH Audio Visual Solutions">' . "\n";
	echo '<meta property="og:locale" content="en_KE">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";

	// Twitter.
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . $desc . '">' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n";
}, 1 );

// Title template: "%page% — VTECH Audio Visual Solutions | Kenya".
add_filter( 'document_title_parts', function ( $parts ) {
	if ( vtech_seo_plugin_active() ) { return $parts; }
	$parts['site'] = 'VTECH Audio Visual Solutions';
	if ( is_front_page() ) {
		$parts['title'] = 'Audio Visual Company in Kenya';
		$parts['tagline'] = 'Sound, LED, Lighting & Conference Systems';
	}
	return $parts;
} );
add_filter( 'document_title_separator', function () { return '|'; } );
