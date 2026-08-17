<?php
/**
 * ACF field group definitions registered in PHP (works with ACF free/pro).
 * Wrapped in acf_add_local_field_group so the theme is install-and-go and
 * fields are version-controlled. If ACF is not active, this silently no-ops.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) { return; }

	/* ---- SERVICE FIELDS ---- */
	acf_add_local_field_group( array(
		'key'      => 'group_service',
		'title'    => 'Service Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'service' ) ) ),
		'fields'   => array(
			array( 'key' => 'svc_icon', 'label' => 'Icon (SVG slug)', 'name' => 'icon', 'type' => 'text', 'instructions' => 'Matches an SVG in /assets/icons (e.g. "sound", "led", "lighting").' ),
			array( 'key' => 'svc_tagline', 'label' => 'Hero Tagline', 'name' => 'tagline', 'type' => 'text' ),
			array( 'key' => 'svc_benefits', 'label' => 'Benefits', 'name' => 'benefits', 'type' => 'repeater', 'sub_fields' => array(
				array( 'key' => 'svc_b_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
				array( 'key' => 'svc_b_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea' ),
			) ),
			array( 'key' => 'svc_process', 'label' => 'Process Steps', 'name' => 'process', 'type' => 'repeater', 'sub_fields' => array(
				array( 'key' => 'svc_p_title', 'label' => 'Step Title', 'name' => 'title', 'type' => 'text' ),
				array( 'key' => 'svc_p_text', 'label' => 'Step Text', 'name' => 'text', 'type' => 'textarea' ),
			) ),
			array( 'key' => 'svc_faqs', 'label' => 'FAQs (for FAQ schema)', 'name' => 'faqs', 'type' => 'repeater', 'sub_fields' => array(
				array( 'key' => 'svc_f_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
				array( 'key' => 'svc_f_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea' ),
			) ),
			array( 'key' => 'svc_related', 'label' => 'Related Services', 'name' => 'related', 'type' => 'relationship', 'post_type' => array( 'service' ), 'max' => 3 ),
			array( 'key' => 'svc_gallery', 'label' => 'Gallery', 'name' => 'gallery', 'type' => 'gallery' ),
			array( 'key' => 'svc_case', 'label' => 'Featured Case Study', 'name' => 'case_study', 'type' => 'post_object', 'post_type' => array( 'case_study' ) ),
			array( 'key' => 'svc_price_from', 'label' => 'Price From (KES)', 'name' => 'price_from', 'type' => 'number', 'instructions' => 'Optional. Powers "from KES X" transparency + Service schema offer.' ),
		),
	) );

	/* ---- PROJECT FIELDS ---- */
	acf_add_local_field_group( array(
		'key'      => 'group_project',
		'title'    => 'Project Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ) ) ),
		'fields'   => array(
			array( 'key' => 'prj_client', 'label' => 'Client', 'name' => 'client', 'type' => 'text' ),
			array( 'key' => 'prj_outcome', 'label' => 'Outcome / Result', 'name' => 'outcome', 'type' => 'textarea' ),
			array( 'key' => 'prj_video', 'label' => 'Video URL (YouTube/Vimeo)', 'name' => 'video_url', 'type' => 'url' ),
			array( 'key' => 'prj_gallery', 'label' => 'Gallery', 'name' => 'gallery', 'type' => 'gallery' ),
			array( 'key' => 'prj_stats', 'label' => 'Result Stats', 'name' => 'stats', 'type' => 'repeater', 'sub_fields' => array(
				array( 'key' => 'prj_s_val', 'label' => 'Value', 'name' => 'value', 'type' => 'text' ),
				array( 'key' => 'prj_s_lbl', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
			) ),
		),
	) );

	/* ---- TESTIMONIAL FIELDS ---- */
	acf_add_local_field_group( array(
		'key'      => 'group_testimonial',
		'title'    => 'Testimonial Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'testimonial' ) ) ),
		'fields'   => array(
			array( 'key' => 'tst_author', 'label' => 'Author Name', 'name' => 'author', 'type' => 'text' ),
			array( 'key' => 'tst_role', 'label' => 'Role & Company', 'name' => 'role', 'type' => 'text' ),
			array( 'key' => 'tst_rating', 'label' => 'Rating (1-5)', 'name' => 'rating', 'type' => 'number', 'default_value' => 5, 'min' => 1, 'max' => 5 ),
		),
	) );

	/* ---- EQUIPMENT FIELDS ---- */
	acf_add_local_field_group( array(
		'key'      => 'group_equipment',
		'title'    => 'Equipment Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'equipment' ) ) ),
		'fields'   => array(
			array( 'key' => 'eq_daily', 'label' => 'Daily Hire Rate (KES)', 'name' => 'daily_rate', 'type' => 'number' ),
			array( 'key' => 'eq_avail', 'label' => 'Availability', 'name' => 'availability', 'type' => 'select', 'choices' => array( 'available' => 'Available', 'limited' => 'Limited', 'booked' => 'Fully Booked' ), 'default_value' => 'available' ),
			array( 'key' => 'eq_stock', 'label' => 'Stock Quantity (units owned)', 'name' => 'stock_qty', 'type' => 'number', 'instructions' => 'Total units of this item you own. Used for availability locking.' ),
			array( 'key' => 'eq_specs', 'label' => 'Key Specs', 'name' => 'specs', 'type' => 'textarea' ),
		),
	) );

	/* ---- GLOBAL COMPANY / NAP (options page) ---- */
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( array( 'page_title' => 'VTECH Theme Options', 'menu_slug' => 'vtech-options', 'icon_url' => 'dashicons-admin-generic' ) );
	}

	/* ---- HIRE PACKAGE FIELDS ---- */
	acf_add_local_field_group( array(
		'key'      => 'group_hire_package',
		'title'    => 'Package Details',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'hire_package' ) ) ),
		'fields'   => array(
			array( 'key' => 'pkg_code', 'label' => 'Package Code', 'name' => 'code', 'type' => 'text', 'instructions' => 'e.g. PKG-CONF-A. Leave blank to auto-generate.' ),
			array( 'key' => 'pkg_featured', 'label' => 'Featured Package', 'name' => 'featured', 'type' => 'true_false', 'ui' => 1 ),
			array( 'key' => 'pkg_capacity', 'label' => 'Suitable For (capacity)', 'name' => 'capacity', 'type' => 'select', 'choices' => array( '20'=>'20 People','50'=>'50 People','100'=>'100 People','150'=>'150 People','200'=>'200 People','300'=>'300 People','500'=>'500 People','800'=>'800 People','1000'=>'1000 People','1500'=>'1500 People','2000+'=>'2000+','Custom'=>'Custom' ) ),
			array( 'key' => 'pkg_venue', 'label' => 'Venue Types', 'name' => 'venue_types', 'type' => 'checkbox', 'choices' => array( 'Indoor'=>'Indoor','Outdoor'=>'Outdoor','Church'=>'Church','Hotel'=>'Hotel','Hall'=>'Hall','School'=>'School','Tent'=>'Tent','Stadium'=>'Stadium','Office'=>'Office','Open Ground'=>'Open Ground' ) ),

			array( 'key' => 'pkg_tab_pricing', 'label' => 'Pricing', 'type' => 'tab' ),
			array( 'key' => 'pkg_price', 'label' => 'Rental Price (KES)', 'name' => 'price', 'type' => 'number', 'instructions' => 'Main headline price shown on the card.' ),
			array( 'key' => 'pkg_daily', 'label' => 'Daily Rate', 'name' => 'daily_rate', 'type' => 'number' ),
			array( 'key' => 'pkg_weekly', 'label' => 'Weekly Rate', 'name' => 'weekly_rate', 'type' => 'number' ),
			array( 'key' => 'pkg_weekend', 'label' => 'Weekend Rate', 'name' => 'weekend_rate', 'type' => 'number' ),
			array( 'key' => 'pkg_discount', 'label' => 'Discount %', 'name' => 'discount', 'type' => 'number' ),
			array( 'key' => 'pkg_vat', 'label' => 'VAT Included', 'name' => 'vat_included', 'type' => 'true_false', 'ui' => 1 ),
			array( 'key' => 'pkg_deposit', 'label' => 'Deposit Required (%)', 'name' => 'deposit_pct', 'type' => 'number' ),
			array( 'key' => 'pkg_transport', 'label' => 'Transport Charges', 'name' => 'transport', 'type' => 'number' ),
			array( 'key' => 'pkg_setup', 'label' => 'Setup Charges', 'name' => 'setup_charge', 'type' => 'number' ),
			array( 'key' => 'pkg_operator', 'label' => 'Operator Charges', 'name' => 'operator_charge', 'type' => 'number' ),

			array( 'key' => 'pkg_tab_equip', 'label' => 'Equipment & Services', 'type' => 'tab' ),
			array( 'key' => 'pkg_equipment', 'label' => 'Included Equipment', 'name' => 'equipment', 'type' => 'repeater', 'button_label' => 'Add Item', 'sub_fields' => array(
				array( 'key' => 'pkg_eq_qty', 'label' => 'Qty', 'name' => 'qty', 'type' => 'text' ),
				array( 'key' => 'pkg_eq_item', 'label' => 'Item', 'name' => 'item', 'type' => 'text' ),
			) ),
			array( 'key' => 'pkg_services', 'label' => 'Included Services', 'name' => 'services', 'type' => 'checkbox', 'choices' => array( 'Delivery'=>'Delivery','Collection'=>'Collection','Setup'=>'Setup','Dismantling'=>'Dismantling','Sound Engineer'=>'Sound Engineer','Lighting Technician'=>'Lighting Technician','Video Technician'=>'Video Technician','Event Support'=>'Event Support','Equipment Testing'=>'Equipment Testing','Backup Equipment'=>'Backup Equipment' ) ),
			array( 'key' => 'pkg_highlights', 'label' => 'Highlights', 'name' => 'highlights', 'type' => 'repeater', 'button_label' => 'Add Highlight', 'sub_fields' => array(
				array( 'key' => 'pkg_hl', 'label' => 'Highlight', 'name' => 'text', 'type' => 'text' ),
			) ),
			array( 'key' => 'pkg_addons', 'label' => 'Add-ons (client can add)', 'name' => 'addons', 'type' => 'repeater', 'button_label' => 'Add Add-on', 'sub_fields' => array(
				array( 'key' => 'pkg_addon_name', 'label' => 'Add-on', 'name' => 'name', 'type' => 'text' ),
				array( 'key' => 'pkg_addon_price', 'label' => 'Price (KES)', 'name' => 'price', 'type' => 'number' ),
			) ),

			array( 'key' => 'pkg_tab_avail', 'label' => 'Availability & Terms', 'type' => 'tab' ),
			array( 'key' => 'pkg_days', 'label' => 'Available Days', 'name' => 'available_days', 'type' => 'checkbox', 'choices' => array( 'Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday','Sun'=>'Sunday' ) ),
			array( 'key' => 'pkg_max', 'label' => 'Maximum Bookings Per Day', 'name' => 'max_per_day', 'type' => 'number' ),
			array( 'key' => 'pkg_cancel', 'label' => 'Cancellation Policy', 'name' => 'cancellation', 'type' => 'textarea' ),
			array( 'key' => 'pkg_damage', 'label' => 'Damage Policy', 'name' => 'damage_policy', 'type' => 'textarea' ),
			array( 'key' => 'pkg_security', 'label' => 'Security Deposit', 'name' => 'security_deposit', 'type' => 'text' ),
			array( 'key' => 'pkg_brochure', 'label' => 'PDF Brochure', 'name' => 'brochure', 'type' => 'file', 'return_format' => 'url' ),
			array( 'key' => 'pkg_gallery', 'label' => 'Gallery', 'name' => 'gallery', 'type' => 'gallery' ),
		),
	) );

} );
