<?php
/**
 * Theme Settings Panel via the Customizer (native, no plugin).
 * Company NAP, social, hero, CTA, tracking, and toggle for float buttons.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'customize_register', function ( $wp_customize ) {

	$wp_customize->add_panel( 'vtech_panel', array( 'title' => 'VTECH Theme Options', 'priority' => 10 ) );

	/* --- Company / NAP --- */
	$wp_customize->add_section( 'vtech_company', array( 'title' => 'Company Details', 'panel' => 'vtech_panel' ) );
	$fields = array(
		'vtech_phone'    => array( 'Phone', '+254 728135246' ),
		'vtech_whatsapp' => array( 'WhatsApp number (digits only)', '254703201241' ),
		'vtech_email'    => array( 'Email', 'info@vtechaudio.co.ke' ),
		'vtech_address'  => array( 'Address', 'Mpaka Plaza, Mpaka Road, Nairobi' ),
		'vtech_hours'    => array( 'Business Hours', 'Mon–Fri, 9:00 AM – 6:00 PM' ),
		'vtech_map_embed'=> array( 'Google Map embed URL', '' ),
		'vtech_geo_lat'  => array( 'Map latitude (for SEO, e.g. -1.2669)', '-1.2669' ),
		'vtech_geo_lng'  => array( 'Map longitude (for SEO, e.g. 36.8047)', '36.8047' ),
	);
	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => $f[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'vtech_company', 'type' => 'text' ) );
	}

	/* --- Social Media --- */
	$wp_customize->add_section( 'vtech_social', array( 'title' => 'Social Media Links', 'panel' => 'vtech_panel' ) );
	$social = array(
		'vtech_facebook'  => 'Facebook URL',
		'vtech_instagram' => 'Instagram URL',
		'vtech_linkedin'  => 'LinkedIn URL',
		'vtech_x'         => 'X (Twitter) URL',
		'vtech_youtube'   => 'YouTube URL',
	);
	foreach ( $social as $sid => $slabel ) {
		$wp_customize->add_setting( $sid, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( $sid, array( 'label' => $slabel, 'description' => 'Leave blank to hide this icon.', 'section' => 'vtech_social', 'type' => 'url' ) );
	}

	/* --- Hero --- */
	$wp_customize->add_section( 'vtech_hero', array( 'title' => 'Homepage Hero', 'panel' => 'vtech_panel' ) );
	$wp_customize->add_setting( 'vtech_hero_title', array( 'default' => 'Kenya\'s Premium Audio Visual Company', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'vtech_hero_title', array( 'label' => 'Hero Title', 'section' => 'vtech_hero', 'type' => 'text' ) );
	$wp_customize->add_setting( 'vtech_hero_sub', array( 'default' => 'Sound, LED screens, stage lighting, conference & PA systems, acoustics and CCTV — designed, installed and supported across Kenya and East Africa.', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'vtech_hero_sub', array( 'label' => 'Hero Subtitle', 'section' => 'vtech_hero', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'vtech_hero_img', array( 'default' => VTECH_URI . '/assets/img/hero.webp', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'vtech_hero_img', array( 'label' => 'Hero Image (LCP)', 'section' => 'vtech_hero' ) ) );

	/* --- Conversion toggles --- */
	$wp_customize->add_section( 'vtech_conv', array( 'title' => 'Conversion Elements', 'panel' => 'vtech_panel' ) );
	foreach ( array(
		'vtech_show_whatsapp' => 'Show floating WhatsApp button',
		'vtech_show_call'     => 'Show floating call button',
		'vtech_show_sticky_cta' => 'Show sticky "Get a Quote" bar',
		'vtech_show_exit_intent' => 'Enable exit-intent lead popup',
	) as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'vtech_conv', 'type' => 'checkbox' ) );
	}

	/* --- Homepage Trust Stats --- */
	$wp_customize->add_section( 'vtech_stats', array( 'title' => 'Homepage Trust Stats', 'panel' => 'vtech_panel' ) );
	$vtc_stat_defaults = array(
		'vtech_stat1_num' => array( 'Stat 1 number', '200+' ),
		'vtech_stat1_lbl' => array( 'Stat 1 label', 'Installations delivered' ),
		'vtech_stat2_num' => array( 'Stat 2 number', '47' ),
		'vtech_stat2_lbl' => array( 'Stat 2 label', 'Counties served' ),
		'vtech_stat3_num' => array( 'Stat 3 number', '24h' ),
		'vtech_stat3_lbl' => array( 'Stat 3 label', 'Quote turnaround' ),
		'vtech_stat4_num' => array( 'Stat 4 number', '12mo' ),
		'vtech_stat4_lbl' => array( 'Stat 4 label', 'Support & warranty' ),
	);
	foreach ( $vtc_stat_defaults as $vtc_sid => $vtc_sf ) {
		$wp_customize->add_setting( $vtc_sid, array( 'default' => $vtc_sf[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $vtc_sid, array( 'label' => $vtc_sf[0], 'description' => 'Leave blank to hide this stat.', 'section' => 'vtech_stats', 'type' => 'text' ) );
	}

	/* --- Footer --- */
	$wp_customize->add_section( 'vtech_footer', array( 'title' => 'Footer', 'panel' => 'vtech_panel' ) );
	$wp_customize->add_setting( 'vtech_footer_blurb', array( 'default' => "Kenya's premium audio-visual integrator. Sound, LED, lighting, conference & PA systems, acoustics and CCTV designed, installed and supported across Kenya and East Africa.", 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'vtech_footer_blurb', array( 'label' => 'Footer description', 'section' => 'vtech_footer', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'vtech_footer_copyright', array( 'default' => 'VTECH Audio Visual Solutions. All rights reserved.', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'vtech_footer_copyright', array( 'label' => 'Copyright text (year is added automatically)', 'section' => 'vtech_footer', 'type' => 'text' ) );

	/* --- Homepage content counts --- */
	$wp_customize->add_section( 'vtech_home_counts', array( 'title' => 'Homepage Content Counts', 'panel' => 'vtech_panel' ) );
	$wp_customize->add_setting( 'vtech_home_projects', array( 'default' => 6, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'vtech_home_projects', array( 'label' => 'Recent Projects to show on homepage', 'description' => 'How many of the most recent projects to display in the "Recent Projects in Kenya" section.', 'section' => 'vtech_home_counts', 'type' => 'number', 'input_attrs' => array( 'min' => 1, 'max' => 24, 'step' => 1 ) ) );

	/* --- Tracking --- */
	$wp_customize->add_section( 'vtech_track', array( 'title' => 'Analytics & Tracking', 'panel' => 'vtech_panel' ) );
	$wp_customize->add_setting( 'vtech_gtag', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'vtech_gtag', array( 'label' => 'Google Analytics / GTM ID', 'section' => 'vtech_track', 'type' => 'text' ) );
} );

/** Helper accessors used across templates. */
if ( ! function_exists( 'vtech_opt' ) ) {
	function vtech_opt( $key, $default = '' ) { return get_theme_mod( $key, $default ); }
}
