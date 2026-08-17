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
		'phone'   => get_theme_mod( 'vtech_phone', '+254 728135246' ),
		'street'  => get_theme_mod( 'vtech_address', 'Mpaka Plaza, Mpaka Road' ),
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

	// Aggregate Review schema from testimonials (site-wide, homepage).
	if ( is_front_page() ) {
		$q = new WP_Query( array( 'post_type' => 'testimonial', 'posts_per_page' => 20, 'no_found_rows' => true ) );
		if ( $q->have_posts() && function_exists( 'get_field' ) ) {
			$reviews = array(); $sum = 0; $count = 0;
			foreach ( $q->posts as $p ) {
				$rating = (int) get_field( 'rating', $p->ID ) ?: 5;
				$sum += $rating; $count++;
				$reviews[] = array(
					'@type' => 'Review',
					'author' => array( '@type' => 'Person', 'name' => get_field( 'author', $p->ID ) ),
					'reviewRating' => array( '@type' => 'Rating', 'ratingValue' => (string) $rating, 'bestRating' => '5' ),
					'reviewBody' => wp_strip_all_tags( $p->post_content ),
				);
			}
			if ( $count ) {
				vtech_json_ld( array(
					'@context' => 'https://schema.org',
					'@type' => 'Organization',
					'@id' => $nap['url'] . '#business',
					'aggregateRating' => array( '@type' => 'AggregateRating', 'ratingValue' => number_format( $sum / $count, 1 ), 'reviewCount' => (string) $count, 'bestRating' => '5' ),
					'review' => $reviews,
				) );
			}
		}
		wp_reset_postdata();
	}
}, 20 );
