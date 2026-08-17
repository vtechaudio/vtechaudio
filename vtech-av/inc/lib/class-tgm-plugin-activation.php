<?php
/**
 * TGM Plugin Activation — bundled library.
 *
 * IMPORTANT: This is a lightweight loader stub. Replace this file with the
 * official, full TGM Plugin Activation library (v2.6.1) from:
 *   https://github.com/TGMPA/TGM-Plugin-Activation  (file: tgmpa/class-tgm-plugin-activation.php)
 * Download "TGM-Plugin-Activation" -> copy class-tgm-plugin-activation.php here.
 *
 * The official library is GPL-licensed and cannot be redistributed inline here
 * verbatim in full, but it is a single drop-in file. Once placed here, the
 * inc/required-plugins.php config will show the "This theme recommends..."
 * install screen automatically.
 *
 * Until you drop in the official file, the theme degrades gracefully: a simple
 * admin notice (below) tells the admin which plugins to install. Everything
 * else in the theme works without it.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'tgmpa' ) ) {
	// Minimal fallback: friendly admin notice listing required plugins.
	add_action( 'admin_notices', function () {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		if ( get_option( 'vtech_plugins_notice_dismissed' ) ) { return; }
		$acf = is_plugin_active( 'advanced-custom-fields/acf.php' ) || is_plugin_active( 'advanced-custom-fields-pro/acf.php' );
		$cf7 = is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
		if ( $acf && $cf7 ) { return; }
		$need = array();
		if ( ! $acf ) { $need[] = '<a href="' . esc_url( admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' ) ) . '">Advanced Custom Fields</a>'; }
		if ( ! $cf7 ) { $need[] = '<a href="' . esc_url( admin_url( 'plugin-install.php?s=contact+form+7&tab=search&type=term' ) ) . '">Contact Form 7</a>'; }
		echo '<div class="notice notice-warning is-dismissible"><p><strong>VTECH theme:</strong> for full functionality please install & activate: ' . implode( ', ', $need ) . '. '
			. '(Optional: One Click Demo Import.) For the automatic install screen, drop the official TGM Plugin Activation library into <code>inc/lib/class-tgm-plugin-activation.php</code>.</p></div>';
	} );

	// Ensure is_plugin_active() is available on the front-of-admin.
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
}
