<?php
/**
 * Brand logos: add an editable logo image to each Brand taxonomy term, and a
 * helper to fetch brands (with logos) for display in a marquee/grid.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* --- Add a "Brand Logo" image field to the Add-Brand form --- */
add_action( 'brand_add_form_fields', function () {
	wp_nonce_field( 'vtech_brand_logo_save', 'vtech_brand_logo_nonce' );
	?>
	<div class="form-field">
		<label for="vtech_brand_logo"><?php esc_html_e( 'Brand Logo', 'vtech-av' ); ?></label>
		<input type="hidden" id="vtech_brand_logo" name="vtech_brand_logo" value="">
		<button type="button" class="button vtech-brand-logo-pick"><?php esc_html_e( 'Select logo', 'vtech-av' ); ?></button>
		<button type="button" class="button vtech-brand-logo-clear"><?php esc_html_e( 'Clear', 'vtech-av' ); ?></button>
		<p class="vtech-brand-logo-preview"></p>
		<p class="description"><?php esc_html_e( 'Upload the brand logo (PNG with transparent background works best).', 'vtech-av' ); ?></p>
	</div>
	<?php
} );

/* --- Add the field to the Edit-Brand form --- */
add_action( 'brand_edit_form_fields', function ( $term ) {
	$logo_id  = (int) get_term_meta( $term->term_id, 'vtech_brand_logo', true );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	wp_nonce_field( 'vtech_brand_logo_save', 'vtech_brand_logo_nonce' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="vtech_brand_logo"><?php esc_html_e( 'Brand Logo', 'vtech-av' ); ?></label></th>
		<td>
			<input type="hidden" id="vtech_brand_logo" name="vtech_brand_logo" value="<?php echo esc_attr( $logo_id ); ?>">
			<button type="button" class="button vtech-brand-logo-pick"><?php esc_html_e( 'Select logo', 'vtech-av' ); ?></button>
			<button type="button" class="button vtech-brand-logo-clear"><?php esc_html_e( 'Clear', 'vtech-av' ); ?></button>
			<p class="vtech-brand-logo-preview"><?php if ( $logo_url ) { echo '<img src="' . esc_url( $logo_url ) . '" style="max-height:60px;height:auto;width:auto">'; } ?></p>
			<p class="description"><?php esc_html_e( 'Upload the brand logo (PNG with transparent background works best).', 'vtech-av' ); ?></p>
		</td>
	</tr>
	<?php
} );

/* --- Save the logo term meta --- */
function vtech_save_brand_logo( $term_id ) {
	if ( ! isset( $_POST['vtech_brand_logo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vtech_brand_logo_nonce'] ) ), 'vtech_brand_logo_save' ) ) { return; }
	if ( ! current_user_can( 'manage_categories' ) ) { return; }
	$logo_id = isset( $_POST['vtech_brand_logo'] ) ? (int) $_POST['vtech_brand_logo'] : 0;
	if ( $logo_id ) { update_term_meta( $term_id, 'vtech_brand_logo', $logo_id ); }
	else { delete_term_meta( $term_id, 'vtech_brand_logo' ); }
}
add_action( 'created_brand', 'vtech_save_brand_logo' );
add_action( 'edited_brand', 'vtech_save_brand_logo' );

/* --- Enqueue the WP media picker on the Brands admin screen --- */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) { return; }
	if ( ! isset( $_GET['taxonomy'] ) || 'brand' !== $_GET['taxonomy'] ) { return; }
	wp_enqueue_media();
	$js = "jQuery(function($){function prev(u){return u?'<img src=\"'+u+'\" style=\"max-height:60px;height:auto;width:auto\">':'';}"
		. "$(document).on('click','.vtech-brand-logo-pick',function(e){e.preventDefault();var b=$(this);"
		. "var f=wp.media({title:'Select Brand Logo',button:{text:'Use logo'},multiple:false}).on('select',function(){"
		. "var a=f.state().get('selection').first().toJSON();b.closest('td,.form-field').find('#vtech_brand_logo').val(a.id);"
		. "var u=(a.sizes&&a.sizes.medium)?a.sizes.medium.url:a.url;b.closest('td,.form-field').find('.vtech-brand-logo-preview').html(prev(u));}).open();});"
		. "$(document).on('click','.vtech-brand-logo-clear',function(e){e.preventDefault();var w=$(this).closest('td,.form-field');w.find('#vtech_brand_logo').val('');w.find('.vtech-brand-logo-preview').html('');});});";
	wp_add_inline_script( 'jquery-core', $js );
} );

/**
 * Return brands with logos for display.
 * Each item: array( 'name' => string, 'logo' => url|'', 'url' => term_link ).
 */
function vtech_get_brands( $limit = 0 ) {
	$with_logo = array();
	$no_logo   = array();
	$terms = get_terms( array( 'taxonomy' => 'brand', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) { return array(); }
	foreach ( $terms as $t ) {
		$logo_id  = (int) get_term_meta( $t->term_id, 'vtech_brand_logo', true );
		$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		$row = array(
			'name' => $t->name,
			'logo' => $logo_url,
			'url'  => get_term_link( $t ),
		);
		if ( $logo_url ) { $with_logo[] = $row; } else { $no_logo[] = $row; }
	}
	// Prefer brands that actually have an uploaded logo so real logos are not
	// mixed with text-only placeholders. Only fall back to text names if there
	// are no logo'd brands at all.
	$out = ! empty( $with_logo ) ? $with_logo : $no_logo;
	if ( $limit > 0 ) { $out = array_slice( $out, 0, $limit ); }
	return $out;
}
