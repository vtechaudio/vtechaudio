<?php
/**
 * Hire Package editor box.
 *
 * Adds a simple, ACF-free "What's Included & Pricing" panel to the Hire Package
 * edit screen so the owner can edit the plain meta the single-package template
 * reads first (vtech_pkg_equipment / vtech_pkg_services / vtech_pkg_price /
 * vtech_pkg_capacity). One item per line — no serialized data to hand-edit.
 *
 * @package VTECH_AV
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Register the meta box on the Hire Package edit screen. */
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'vtech_pkg_details',
		__( "What's Included & Pricing", 'vtech-av' ),
		'vtech_pkg_details_box',
		'hire_package',
		'normal',
		'high'
	);
} );

/**
 * Render the editor fields.
 *
 * @param WP_Post $post Current package post.
 */
function vtech_pkg_details_box( $post ) {
	wp_nonce_field( 'vtech_pkg_details_save', 'vtech_pkg_details_nonce' );

	$equipment = get_post_meta( $post->ID, 'vtech_pkg_equipment', true );
	$services  = get_post_meta( $post->ID, 'vtech_pkg_services', true );
	$price     = get_post_meta( $post->ID, 'vtech_pkg_price', true );
	$capacity  = get_post_meta( $post->ID, 'vtech_pkg_capacity', true );

	$equip_txt = is_array( $equipment ) ? implode( "\n", $equipment ) : (string) $equipment;
	$svc_txt   = is_array( $services ) ? implode( "\n", $services ) : (string) $services;
	?>
	<style>
		.vtech-pkg-field{margin:0 0 1.25rem}
		.vtech-pkg-field label{display:block;font-weight:600;margin:0 0 .35rem}
		.vtech-pkg-field .description{color:#666;font-style:italic;margin:.25rem 0 .5rem}
		.vtech-pkg-field textarea{width:100%;font-family:inherit;line-height:1.5}
		.vtech-pkg-row{display:flex;gap:1.5rem;flex-wrap:wrap}
		.vtech-pkg-row .vtech-pkg-field{flex:1;min-width:200px}
		.vtech-pkg-field input[type=text]{width:100%}
	</style>

	<div class="vtech-pkg-field">
		<label for="vtech_pkg_equipment"><?php esc_html_e( "What's Included", 'vtech-av' ); ?></label>
		<p class="description"><?php esc_html_e( 'One item per line. Each line becomes a bullet in the "What\'s included" list on the package page.', 'vtech-av' ); ?></p>
		<textarea id="vtech_pkg_equipment" name="vtech_pkg_equipment" rows="10" placeholder="2 x Powered PA speakers on stands&#10;2 x Wired microphones&#10;1 x On-site technician"><?php echo esc_textarea( $equip_txt ); ?></textarea>
	</div>

	<div class="vtech-pkg-field">
		<label for="vtech_pkg_services"><?php esc_html_e( 'Services Included', 'vtech-av' ); ?></label>
		<p class="description"><?php esc_html_e( 'One per line (e.g. Delivery, Setup, Professional engineer). Optional.', 'vtech-av' ); ?></p>
		<textarea id="vtech_pkg_services" name="vtech_pkg_services" rows="4" placeholder="Delivery&#10;Setup&#10;Professional technician / engineer"><?php echo esc_textarea( $svc_txt ); ?></textarea>
	</div>

	<div class="vtech-pkg-row">
		<div class="vtech-pkg-field">
			<label for="vtech_pkg_price"><?php esc_html_e( 'Price (KES)', 'vtech-av' ); ?></label>
			<p class="description"><?php esc_html_e( 'Numbers only, e.g. 35000.', 'vtech-av' ); ?></p>
			<input type="text" id="vtech_pkg_price" name="vtech_pkg_price" value="<?php echo esc_attr( $price ); ?>">
		</div>
		<div class="vtech-pkg-field">
			<label for="vtech_pkg_capacity"><?php esc_html_e( 'Capacity (guests)', 'vtech-av' ); ?></label>
			<p class="description"><?php esc_html_e( 'e.g. 150. Optional.', 'vtech-av' ); ?></p>
			<input type="text" id="vtech_pkg_capacity" name="vtech_pkg_capacity" value="<?php echo esc_attr( $capacity ); ?>">
		</div>
	</div>
	<?php
}

/** Save the editor fields. */
add_action( 'save_post_hire_package', function ( $post_id ) {
	if ( ! isset( $_POST['vtech_pkg_details_nonce'] ) ) { return; }
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_pkg_details_nonce'] ) ), 'vtech_pkg_details_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	$to_lines = function ( $raw ) {
		$raw   = (string) wp_unslash( $raw );
		$lines = preg_split( '/\r\n|\r|\n/', $raw );
		$out   = array();
		foreach ( $lines as $line ) {
			$line = sanitize_text_field( $line );
			if ( '' !== trim( $line ) ) { $out[] = $line; }
		}
		return $out;
	};

	if ( isset( $_POST['vtech_pkg_equipment'] ) ) {
		update_post_meta( $post_id, 'vtech_pkg_equipment', $to_lines( $_POST['vtech_pkg_equipment'] ) );
	}
	if ( isset( $_POST['vtech_pkg_services'] ) ) {
		update_post_meta( $post_id, 'vtech_pkg_services', $to_lines( $_POST['vtech_pkg_services'] ) );
	}
	if ( isset( $_POST['vtech_pkg_price'] ) ) {
		$price = preg_replace( '/[^0-9.]/', '', (string) wp_unslash( $_POST['vtech_pkg_price'] ) );
		update_post_meta( $post_id, 'vtech_pkg_price', $price );
		update_post_meta( $post_id, 'price', $price );
	}
	if ( isset( $_POST['vtech_pkg_capacity'] ) ) {
		$cap = sanitize_text_field( wp_unslash( $_POST['vtech_pkg_capacity'] ) );
		update_post_meta( $post_id, 'vtech_pkg_capacity', $cap );
		update_post_meta( $post_id, 'capacity', $cap );
	}
} );
