<?php
/**
 * Structured data (JSON-LD). Output in wp_head.
 * LocalBusiness + Organization site-wide; Service / Project / FAQ / Breadcrumb
 * / Review contextually. Uses ACF + theme options where available.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function vtech_nap() {
	// Pull live social profiles from theme options so schema matches the site.
	$same = array();
	foreach ( array( 'vtech_facebook', 'vtech_instagram', 'vtech_linkedin', 'vtech_x', 'vtech_youtube' ) as $vtc_sk ) {
		$vtc_su = get_theme_mod( $vtc_sk, '' );
		if ( $vtc_su ) { $same[] = $vtc_su; }
	}
	return array(
		'name'    => 'VTECH Audio Visual Solutions',
		'email'   => get_theme_mod( 'vtech_email', 'info@vtechaudio.co.ke' ),
		'phone'   => get_theme_mod( 'vtech_phone', '+254 728 135 246' ),
		'street'  => get_theme_mod( 'vtech_address', 'Ground Floor, Mpaka Plaza, Mpaka Road' ),
		'locality'=> 'Nairobi',
		'region'  => 'Nairobi County',
		'country' => 'KE',
		'geo'     => array( 'lat' => (float) get_theme_mod( 'vtech_geo_lat', -1.2669 ), 'lng' => (float) get_theme_mod( 'vtech_geo_lng', 36.8047 ) ),
		'url'     => home_url( '/' ),
		'hours'   => 'Mo-Fr 09:00-18:00',
		'sameAs'  => $same,
	);
}

function vtech_json_ld( $data ) {
	echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}

add_action( 'wp_head', function () {
	// Rank Math (or another SEO plugin) owns the structured-data graph when active.
	if ( function_exists( 'vtech_seo_plugin_active' ) && vtech_seo_plugin_active() ) { return; }
	$nap = vtech_nap();
	$logo = get_theme_mod( 'vtech_logo_url', VTECH_URI . '/assets/img/logo.png' );

	// Organization + LocalBusiness (site-wide).
	vtech_json_ld( array(
		'@context' => 'https://schema.org',
		'@type'    => array( 'Organization', 'LocalBusiness', 'ElectronicsStore' ),
		'@id'      => $nap['url'] . '#business',
		'name'     => $nap['name'],
		'url'      => $nap['url'],
		'email'    => $nap['email'],
		'telephone'=> $nap['phone'],
		'logo'     => $logo,
		'image'    => $logo,
		'priceRange' => 'KES',
		'address'  => array(
			'@type' => 'PostalAddress',
			'streetAddress' => $nap['street'],
			'addressLocality' => $nap['locality'],
			'addressRegion' => $nap['region'],
			'addressCountry' => $nap['country'],
		),
		'geo' => array( '@type' => 'GeoCoordinates', 'latitude' => $nap['geo']['lat'], 'longitude' => $nap['geo']['lng'] ),
		'areaServed' => array(
			array( '@type' => 'Country', 'name' => 'Kenya' ),
			array( '@type' => 'Place', 'name' => 'East Africa' ),
		),
		'openingHours' => $nap['hours'],
		'sameAs' => $nap['sameAs'],
		'slogan' => 'Kenya\'s premium audio-visual integrator — designed, installed and supported.',
	) );

	// WebSite + Sitelinks search box.
	vtech_json_ld( array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'url'      => $nap['url'],
		'name'     => $nap['name'],
		'potentialAction' => array(
			'@type' => 'SearchAction',
			'target' => array( '@type' => 'EntryPoint', 'urlTemplate' => $nap['url'] . '?s={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		),
	) );

	// Service schema on single service.
	if ( is_singular( 'service' ) ) {
		$id = get_the_ID();
		$price = function_exists( 'get_field' ) ? get_field( 'price_from', $id ) : '';
		$svc = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Service',
			'name'     => get_the_title(),
			'description' => wp_strip_all_tags( get_the_excerpt() ),
			'provider' => array( '@type' => 'LocalBusiness', 'name' => $nap['name'], '@id' => $nap['url'] . '#business' ),
			'areaServed' => array( '@type' => 'Country', 'name' => 'Kenya' ),
			'url' => get_permalink(),
		);
		if ( $price ) {
			$svc['offers'] = array( '@type' => 'Offer', 'priceCurrency' => 'KES', 'price' => (string) $price, 'availability' => 'https://schema.org/InStock' );
		}
		vtech_json_ld( $svc );

		// FAQ schema from ACF repeater.
		if ( function_exists( 'get_field' ) ) {
			$faqs = get_field( 'faqs', $id );
			if ( $faqs ) {
				$items = array();
				foreach ( $faqs as $f ) {
					$items[] = array( '@type' => 'Question', 'name' => $f['question'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $f['answer'] ) ) );
				}
				vtech_json_ld( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ) );
			}
		}
	}

}, 20 );
