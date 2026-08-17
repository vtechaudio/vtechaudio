<?php
/**
 * Book a Consultation — streamlined 3-step form.
 * Step 1: Who you are + how to reach you.
 * Step 2: What you need (project basics).
 * Step 3: Site visit + submit.
 * Submits via AJAX to vtech_consultation (see hire-module.php).
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
// Form field helpers come from inc/form-helpers.php.
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Book a Free AV Consultation', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Tell us a little about your project. Our engineers review every detail, then prepare a tailored quotation and technical recommendation — usually within 24 hours. It takes about 2 minutes and there is no obligation.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section"><div class="container narrow">
	<?php if ( function_exists( 'vtech_form_success_banner' ) ) { vtech_form_success_banner(); } ?>
	<form class="cform" id="vtech-consult-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="vtech_form">
		<input type="hidden" name="form_kind" value="consultation">
		<input type="hidden" name="_vtech_return" value="<?php echo esc_url( get_permalink() ); ?>">
		<?php wp_nonce_field( 'vtech_form', 'vtech_form_nonce' ); ?>

		<!-- Progress -->
		<div class="cform__progress" aria-hidden="true"><div class="cform__bar" data-cform-bar></div></div>
		<p class="cform__stepnote" data-cform-note>Step 1 of 3</p>

		<!-- STEP 1: Your details -->
		<fieldset class="cform__step is-active" data-step="1">
			<legend><?php esc_html_e( '1. Your details', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php
				vtc_field( 'Name', 'contact_person', 'text', true );
				vtc_field( 'Company / Organization', 'company' );
				vtc_field( 'Phone / WhatsApp', 'phone', 'tel', true );
				vtc_field( 'Email Address', 'email', 'email', true );
				vtc_field( 'Location / Town', 'project_location' );
				vtc_select( 'Type of organisation', 'industry', array( 'Church', 'School / University', 'Hotel / Hospitality', 'Corporate', 'Government', 'Event Company', 'Hospital', 'Retail / Mall', 'Residential', 'Other' ) );
				?>
			</div>
		</fieldset>

		<!-- STEP 2: What you need -->
		<fieldset class="cform__step" data-step="2">
			<legend><?php esc_html_e( '2. What you need', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php vtc_checks( 'Which systems are you interested in?', 'systems', array( 'Sound / PA', 'LED Screens & Video', 'Conference / Boardroom', 'Stage Lighting', 'Acoustic Treatment', 'Digital Signage', 'Live Streaming', 'Equipment Hire' ) ); ?>
				<?php vtc_radios( 'Is this a new installation or an upgrade?', 'install_type', array( 'New installation', 'Upgrade / expansion', 'Repair / maintenance', 'Not sure yet' ) ); ?>
				<?php vtc_radios( 'Approximate budget (KES)', 'budget', array( 'Under 500,000', '500,000 – 2M', '2M – 5M', 'Above 5M', 'Not sure yet' ) ); ?>
				<?php vtc_field( 'Tell us briefly about your project', 'project_desc', 'textarea', false, 'e.g. Sound and LED for a 500-seat church, or 4 boardrooms for our office.' ); ?>
			</div>
		</fieldset>

		<!-- STEP 3: Site visit + submit -->
		<fieldset class="cform__step" data-step="3">
			<legend><?php esc_html_e( '3. Free site survey', 'vtech-av' ); ?></legend>
			<div class="cf-grid">
				<?php vtc_radios( 'Can we visit your site for a free survey?', 'site_visit', array( 'Yes, please', 'Maybe later', 'Prefer a call first' ) ); ?>
				<?php vtc_field( 'When would you like this done?', 'completion_date', 'text', false, 'e.g. within 2 weeks, before December, no rush' ); ?>
				<?php vtc_field( 'Anything else we should know? (optional)', 'final_notes', 'textarea' ); ?>
			</div>
			<label class="cf-check cf-consent"><input type="checkbox" name="consent" value="Yes" required> <span><?php esc_html_e( 'I understand this is a request for a quotation, not a confirmed booking.', 'vtech-av' ); ?></span></label>
		</fieldset>

		<div class="cform__nav">
			<button type="button" class="btn btn--ghost" data-cform-prev hidden><?php esc_html_e( 'Back', 'vtech-av' ); ?></button>
			<button type="button" class="btn btn--accent" data-cform-next><?php esc_html_e( 'Next', 'vtech-av' ); ?></button>
			<button type="submit" class="btn btn--accent btn--lg" data-cform-submit hidden><?php esc_html_e( 'Submit &amp; Request Quotation', 'vtech-av' ); ?></button>
		</div>
		<p class="cf-status" role="status" aria-live="polite"></p>

		<p class="cform__alt" style="text-align:center;margin-top:1.5rem;color:var(--c-muted)">
			<?php esc_html_e( 'Prefer to talk? Call or WhatsApp us on', 'vtech-av' ); ?>
			<a href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_phone', '+254 728 135 246' ) ); ?></a>.
		</p>
	</form>
</div></section>

<?php get_footer();
