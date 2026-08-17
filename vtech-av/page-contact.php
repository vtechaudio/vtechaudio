<?php
/**
 * Template Name: Contact / Get a Quote
 * A professional contact + quote-request page. Uses a Contact Form 7 form when
 * the [vtech_quote_form] shortcode / a CF7 form is available, otherwise falls
 * back to a native HTML form that posts to admin-ajax and emails the site owner.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$to = vtech_opt( 'vtech_email', 'info@vtechaudio.co.ke' );
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Get a Quote in 24 Hours', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'Tell us about your project and we\'ll respond with a fixed, transparent quote within 24 hours — after a free site survey where needed.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container quote-grid">
		<div>
			<h2 class="section__title"><?php esc_html_e( 'Request a Quote', 'vtech-av' ); ?></h2>
			<?php
			// Prefer a Contact Form 7 form if the site owner set one in Theme Options.
			$cf7_id = vtech_opt( 'vtech_cf7_id', '' );
			if ( $cf7_id && function_exists( 'do_shortcode' ) && shortcode_exists( 'contact-form-7' ) ) {
				echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7_id ) . '"]' );
			} elseif ( trim( get_the_content() ) ) {
				// If the page content contains a CF7 shortcode, render it.
				the_post();
				the_content();
				rewind_posts();
			} else {
				// Native fallback form -> emails the owner via admin-ajax (see inc/features/contact-form.php).
				?>
				<?php if ( function_exists( 'vtech_form_success_banner' ) ) { vtech_form_success_banner(); } ?>
				<form class="quote-form" id="vtech-quote-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="vtech_form">
					<input type="hidden" name="form_kind" value="quote">
					<input type="hidden" name="_vtech_return" value="<?php echo esc_url( get_permalink() ); ?>">
					<?php wp_nonce_field( 'vtech_form', 'vtech_form_nonce' ); ?>
					<div><label for="qf-name"><?php esc_html_e( 'Full name *', 'vtech-av' ); ?></label><input id="qf-name" name="name" type="text" required></div>
					<div><label for="qf-email"><?php esc_html_e( 'Email *', 'vtech-av' ); ?></label><input id="qf-email" name="email" type="email" required></div>
					<div><label for="qf-phone"><?php esc_html_e( 'Phone', 'vtech-av' ); ?></label><input id="qf-phone" name="phone" type="tel"></div>
					<div><label for="qf-org"><?php esc_html_e( 'Organisation', 'vtech-av' ); ?></label><input id="qf-org" name="organisation" type="text"></div>
					<div><label for="qf-service"><?php esc_html_e( 'Service needed', 'vtech-av' ); ?></label>
						<select id="qf-service" name="service">
							<option><?php esc_html_e( 'Sound Systems', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'LED Screens & Video Walls', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Conference & Boardroom Systems', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Stage & Architectural Lighting', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Acoustic Design & Soundproofing', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Digital Signage & Displays', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Equipment Hire', 'vtech-av' ); ?></option>
							<option><?php esc_html_e( 'Other / Not sure', 'vtech-av' ); ?></option>
						</select>
					</div>
					<div><label for="qf-location"><?php esc_html_e( 'Location (town / county)', 'vtech-av' ); ?></label><input id="qf-location" name="location" type="text"></div>
					<div><label for="qf-message"><?php esc_html_e( 'Project details *', 'vtech-av' ); ?></label><textarea id="qf-message" name="message" rows="5" required></textarea></div>
					<div><button type="submit" class="btn btn--accent btn--lg"><?php esc_html_e( 'Send My Request', 'vtech-av' ); ?></button></div>
					<p class="qf-status" role="status" aria-live="polite"></p>
				</form>
				<?php
			}
			?>
		</div>

		<aside class="quote-info">
			<h3><?php esc_html_e( 'Talk to VTECH', 'vtech-av' ); ?></h3>
			<p><strong>VTECH Audio Visual Solutions</strong></p>
			<ul>
				<li><?php echo esc_html( vtech_opt( 'vtech_address', 'Mpaka Plaza, Mpaka Road, Nairobi' ) ); ?></li>
				<li><a href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_phone', '+254 728 135 246' ) ); ?></a></li>
				<li><a href="mailto:<?php echo esc_attr( $to ); ?>"><?php echo esc_html( $to ); ?></a></li>
				<li><?php echo esc_html( vtech_opt( 'vtech_hours', 'Mon–Fri, 9:00 AM – 6:00 PM' ) ); ?></li>
				<li><?php esc_html_e( 'Serving all 47 counties in Kenya & East Africa', 'vtech-av' ); ?></li>
			</ul>
			<a class="btn btn--accent btn--block" style="margin-top:1.5rem" href="https://wa.me/<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_whatsapp', '254728135246' ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chat on WhatsApp', 'vtech-av' ); ?></a>
			<div class="footer-map" style="margin-top:1.5rem;border-radius:12px;overflow:hidden">
				<iframe title="<?php esc_attr_e( 'VTECH office location', 'vtech-av' ); ?>" src="<?php echo esc_url( vtech_opt( 'vtech_map_embed', 'https://www.google.com/maps?q=Mpaka+Road+Nairobi&output=embed' ) ); ?>" loading="lazy" style="width:100%;height:220px;border:0"></iframe>
			</div>
		</aside>
	</div>
</section>

<?php get_footer();
