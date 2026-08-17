<?php
/**
 * Clients feature: an editable "Clients" manager in the WordPress admin.
 * Each client is a post with a featured image (the logo) and an optional
 * website URL. The homepage logo marquee renders these when present.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Register the Clients post type (admin-managed, not public single pages). */
add_action( 'init', function () {
	register_post_type( 'vtech_client', array(
		'labels' => array(
			'name'          => 'Clients',
			'singular_name' => 'Client',
			'add_new_item'  => 'Add New Client',
			'edit_item'     => 'Edit Client',
			'menu_name'     => 'Clients',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-groups',
		'menu_position'=> 26,
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
	) );
} );

/* Add a simple "Website URL" field to the Client editor. */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'vtech_client_url', 'Client Website (optional)', function ( $post ) {
		$url = get_post_meta( $post->ID, 'client_url', true );
		wp_nonce_field( 'vtech_client_url_save', 'vtech_client_url_nonce' );
		echo '<p>Enter the client\'s website (optional). The logo image is set via the Featured Image box.</p>';
		echo '<input type="url" name="vtech_client_url" value="' . esc_attr( $url ) . '" placeholder="https://example.com" style="width:100%" />';
	}, 'vtech_client', 'normal', 'default' );
} );

add_action( 'save_post_vtech_client', function ( $post_id ) {
	if ( ! isset( $_POST['vtech_client_url_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_client_url_nonce'] ) ), 'vtech_client_url_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	$url = isset( $_POST['vtech_client_url'] ) ? esc_url_raw( wp_unslash( $_POST['vtech_client_url'] ) ) : '';
	update_post_meta( $post_id, 'client_url', $url );
} );

/**
 * Return client logos to render in the marquee.
 * Each item: array( 'name' => string, 'logo' => url|'', 'url' => url|'' ).
 * If no clients exist, returns an empty array (caller falls back to names).
 */
function vtech_get_clients() {
	$out = array();
	$q = new WP_Query( array(
		'post_type'      => 'vtech_client',
		'posts_per_page' => 30,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );
	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$out[] = array(
				'name' => get_the_title(),
				'logo' => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'medium' ) : '',
				'url'  => (string) get_post_meta( get_the_ID(), 'client_url', true ),
			);
		}
	}
	wp_reset_postdata();
	return $out;
}
