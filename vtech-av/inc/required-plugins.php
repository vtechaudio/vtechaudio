<?php
/**
 * Recommend/require companion plugins. Uses TGM Plugin Activation when the
 * official library is present in /inc/lib/; otherwise shows a safe admin notice.
 *
 * Hardened: never calls is_plugin_active() before wp-admin/includes/plugin.php
 * is loaded, and never fatals — so it cannot block the setup menu from loading.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load the official TGMPA library only if it defines the real class.
$vtech_tgmpa_lib = VTECH_DIR . '/inc/lib/class-tgm-plugin-activation.php';
if ( file_exists( $vtech_tgmpa_lib ) ) {
	require_once $vtech_tgmpa_lib;
}

// Only register TGMPA config if the REAL library loaded (function exists).
if ( function_exists( 'tgmpa' ) ) {
	add_action( 'tgmpa_register', 'vtech_register_required_plugins' );
	function vtech_register_required_plugins() {
		$plugins = array(
			array( 'name' => 'Advanced Custom Fields', 'slug' => 'advanced-custom-fields', 'required' => true ),
			array( 'name' => 'Contact Form 7', 'slug' => 'contact-form-7', 'required' => true ),
			array( 'name' => 'One Click Demo Import', 'slug' => 'one-click-demo-import', 'required' => false ),
		);
		$config = array(
			'id' => 'vtech-av', 'menu' => 'vtech-install-plugins',
			'has_notices' => true, 'dismissable' => true, 'is_automatic' => true,
		);
		tgmpa( $plugins, $config );
	}
} else {
	// Fallback notice — safe, loads plugin.php first, wrapped so it never fatals.
	add_action( 'admin_notices', function () {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$acf = is_plugin_active( 'advanced-custom-fields/acf.php' )
			|| is_plugin_active( 'advanced-custom-fields-pro/acf.php' )
			|| class_exists( 'ACF' );
		$cf7 = is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
		if ( $acf && $cf7 ) { return; }
		$need = array();
		if ( ! $acf ) { $need[] = '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=advanced+custom+fields' ) ) . '">Advanced Custom Fields</a>'; }
		if ( ! $cf7 ) { $need[] = '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=contact+form+7' ) ) . '">Contact Form 7</a>'; }
		echo '<div class="notice notice-warning is-dismissible"><p><strong>VTECH theme:</strong> for full functionality, install &amp; activate: ' . implode( ', ', $need ) . '. (Optional: One Click Demo Import.)</p></div>';
	} );
}
