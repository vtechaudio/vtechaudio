<?php
/**
 * Equipment Hire Request — streamlined 3-step version.
 * Step 1: Event details. Step 2: What you need (simple checklists).
 * Step 3: Logistics + submit. Submits via AJAX to vtech_hire_request.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
// Form helpers from inc/form-helpers.php.
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Equipment Hire Request', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Tell us about your event and what you need. We prepare a quotation with delivery, setup and technician within 24 hours. It takes about 2 minutes — this is a request, not a confirmed booking.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section"><div class="container narrow">
	<?php if ( function_exists( 'vtech_form_success_banner' ) ) { vtech_form_success_banner(); } ?>
	<form class="cform" id="vtech-hire-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="vtech_form">
		<input type="hidden" name="form_kind" value="hire">
		<input type="hidden" name="_vtech_return" value="<?php echo esc_url( get_permalink() ); ?>">
		<?php wp_nonce_field( 'vtech_form', 'vtech_form_nonce' ); ?>
		<div class="cform__progress" aria-hidden="true"><div class="cform__bar" data-cform-bar></div></div>
		<p class="cform__stepnote" data-cform-note>Step 1 of 3</p>

		<!-- 1. Event details -->
		<fieldset class="cform__step is-active" data-step="1">
			<legend><?php esc_html_e( '1. Your event', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php
				vtc_field( 'Name', 'contact_person', 'text', true );
				vtc_field( 'Phone / WhatsApp', 'phone', 'tel', true );
				vtc_field( 'Email Address', 'email', 'email', true );
				vtc_field( 'Company / Organization', 'company' );
				vtc_select( 'Event Type', 'event_type', array( 'Wedding', 'Conference', 'Church Crusade', 'Church Service', 'Graduation', 'Corporate Meeting', 'Product Launch', 'Concert', 'Festival', 'Exhibition', 'Funeral', 'Political Rally', 'Sports Event', 'School Event', 'Government Function', 'NGO Event', 'Other' ), true );
				vtc_field( 'Event Date', 'event_date', 'date', true );
				vtc_field( 'Venue / Location', 'venue_name' );
				?>
				<?php vtc_radios( 'Indoor or Outdoor?', 'indoor_outdoor', array( 'Indoor', 'Outdoor', 'Both' ) ); ?>
				<?php vtc_radios( 'Expected guests', 'audience', array( 'Under 50', '50–150', '150–500', '500–1,000', '1,000–5,000', 'Above 5,000' ) ); ?>
			</div>
		</fieldset>

		<!-- 2. What you need -->
		<fieldset class="cform__step" data-step="2">
			<legend><?php esc_html_e( '2. What you need', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php vtc_checks( 'Equipment needed (tick all that apply)', 'equipment', array( 'Sound / PA system', 'Wireless microphones', 'Stage monitors', 'LED screen / video wall', 'Projector & screen', 'Stage lighting', 'Staging / truss', 'Live streaming', 'Backup generator', 'Cameras / video' ) ); ?>
				<?php vtc_radios( 'Do you need technicians on site?', 'technicians', array( 'Yes, please', 'No', 'Not sure — advise me' ) ); ?>
				<?php vtc_radios( 'Is a ready-made package a good fit?', 'package_interest', array( 'Bronze (up to 50)', 'Silver (up to 150)', 'Gold (up to 500)', 'Platinum (up to 1,000)', 'Not sure — recommend one' ) ); ?>
				<?php vtc_field( 'Describe what your event needs', 'event_desc', 'textarea', false, 'e.g. Sound for 300 guests, 4 wireless mics, an LED screen and a technician for the day.' ); ?>
			</div>
		</fieldset>

		<!-- 3. Logistics + submit -->
		<fieldset class="cform__step" data-step="3">
			<legend><?php esc_html_e( '3. Logistics', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php vtc_radios( 'Is power available at the venue?', 'power_available', array( 'Yes', 'No', 'Not sure' ) ); ?>
				<?php vtc_field( 'Setup date (if different from event date)', 'setup_date', 'date' ); ?>
				<?php vtc_radios( 'Approximate budget (KES)', 'budget', array( 'Under 50,000', '50,000 – 150,000', '150,000 – 500,000', 'Above 500,000', 'Not sure yet' ) ); ?>
				<?php vtc_field( 'Anything else we should know? (optional)', 'notes', 'textarea' ); ?>
			</div>
			<label class="cf-check cf-consent"><input type="checkbox" name="consent" value="Yes" required> <span><?php esc_html_e( 'I understand this is a hire request for a quotation, not a confirmed booking.', 'vtech-av' ); ?></span></label>
		</fieldset>

		<div class="cform__nav">
			<button type="button" class="btn btn--ghost" data-cform-prev hidden><?php esc_html_e( 'Back', 'vtech-av' ); ?></button>
			<button type="button" class="btn btn--accent" data-cform-next><?php esc_html_e( 'Next', 'vtech-av' ); ?></button>
			<button type="submit" class="btn btn--accent btn--lg" data-cform-submit hidden><?php esc_html_e( 'Submit Hire Request', 'vtech-av' ); ?></button>
		</div>
		<p class="cf-status" role="status" aria-live="polite"></p>

		<p class="cform__alt" style="text-align:center;margin-top:1.5rem;color:var(--c-muted)">
			<?php esc_html_e( 'Prefer to talk? Call or WhatsApp us on', 'vtech-av' ); ?>
			<a href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_phone', '+254 728 135 246' ) ); ?></a>.
		</p>
	</form>
</div></section>

<?php get_footer();
