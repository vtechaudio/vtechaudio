<?php
/**
 * Footer: NAP, quick links, services, visit/map, social, floating CTAs.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
</main><!-- #main -->

<footer class="site-footer">
	<div class="container site-footer__grid">

		<!-- Col 1: Brand + NAP -->
		<div class="site-footer__col site-footer__brand">
			<a class="brand-text brand-text--light" href="<?php echo esc_url( home_url( '/' ) ); ?>">VTECH <span>Audio Visual</span></a>
			<p><?php echo esc_html( vtech_opt( 'vtech_footer_blurb', "Kenya's premium audio-visual integrator. Sound, LED, lighting, conference & PA systems, acoustics and CCTV designed, installed and supported across Kenya and East Africa." ) ); ?></p>
			<address>
				<strong>VTECH Audio Visual Solutions</strong><br>
				<?php echo esc_html( vtech_opt( 'vtech_address', 'Mpaka Plaza, Mpaka Road, Nairobi' ) ); ?><br>
				<a href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_phone', '+254 728 135 246' ) ); ?></a><br>
				<a href="mailto:<?php echo esc_attr( vtech_opt( 'vtech_email', 'info@vtechaudio.co.ke' ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_email', 'info@vtechaudio.co.ke' ) ); ?></a><br>
				<span><?php echo esc_html( vtech_opt( 'vtech_hours', 'Mon–Fri, 9:00 AM – 6:00 PM' ) ); ?></span>
			</address>
			<div class="footer-social" aria-label="<?php esc_attr_e( 'Social media', 'vtech-av' ); ?>">
				<?php if ( function_exists( 'vtech_social_links' ) ) : foreach ( vtech_social_links() as $name => $url ) : if ( ! $url ) { continue; } ?>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo vtech_social_icon( $name ); // phpcs:ignore ?></svg></a>
				<?php endforeach; endif; ?>
			</div>
		</div>

		<!-- Col 2: Quick links -->
		<div class="site-footer__col">
			<h3 class="footer-widget__title"><?php esc_html_e( 'Company', 'vtech-av' ); ?></h3>
			<?php if ( has_nav_menu( 'footer_company' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer_company', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false, 'depth' => 1 ) );
			} else { ?>
			<ul class="footer-menu">
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ?: home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/equipment-hire/' ) ); ?>"><?php esc_html_e( 'Equipment Hire', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Contact', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'vtech-av' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'vtech-av' ); ?></a></li>
			</ul>
			<?php } ?>
		</div>

		<!-- Col 3: Services -->
		<div class="site-footer__col">
			<h3 class="footer-widget__title"><?php esc_html_e( 'Services', 'vtech-av' ); ?></h3>
			<?php
			$has_footer_menu = has_nav_menu( 'footer' );
			if ( $has_footer_menu ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false, 'depth' => 1 ) );
			} else {
				$svc = new WP_Query( array( 'post_type' => 'service', 'posts_per_page' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
				echo '<ul class="footer-menu">';
				if ( $svc->have_posts() ) {
					while ( $svc->have_posts() ) { $svc->the_post(); echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>'; }
					wp_reset_postdata();
				} else {
					foreach ( array( 'Sound Systems' => '/services/sound-systems/', 'LED Screens' => '/services/led-screens/', 'Conference Systems' => '/services/conference-systems/', 'Stage Lighting' => '/services/lighting/', 'Acoustic Solutions' => '/services/acoustic-solutions/', 'CCTV & Signage' => '/services/video-systems/' ) as $t => $u ) {
						echo '<li><a href="' . esc_url( home_url( $u ) ) . '">' . esc_html( $t ) . '</a></li>';
					}
				}
				echo '</ul>';
			}
			?>
		</div>

		<!-- Col 4: Visit us + map -->
		<div class="site-footer__col">
			<h3 class="footer-widget__title"><?php esc_html_e( 'Visit Us', 'vtech-av' ); ?></h3>
			<?php
			$map = vtech_opt( 'vtech_map_embed', 'https://www.google.com/maps?q=Mpaka+Road+Nairobi&output=embed' );
			if ( $map ) : ?>
				<div class="footer-map"><iframe title="<?php esc_attr_e( 'VTECH office location map', 'vtech-av' ); ?>" src="<?php echo esc_url( $map ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
			<?php endif; ?>
			<a class="btn btn--accent btn--block" style="margin-top:1rem" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
		</div>

	</div>
	<div class="site-footer__bottom container">
		<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( vtech_opt( 'vtech_footer_copyright', 'VTECH Audio Visual Solutions. All rights reserved.' ) ); ?></p>
		<p class="site-footer__legal"><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'vtech-av' ); ?></a> &middot; <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'vtech-av' ); ?></a></p>
	</div>
</footer>

<?php // Floating conversion elements.
$wa = str_replace( ' ', '', vtech_opt( 'vtech_whatsapp', '254728135246' ) );
if ( vtech_opt( 'vtech_show_whatsapp', true ) ) : ?>
	<a class="float-btn float-btn--wa" href="https://wa.me/<?php echo esc_attr( $wa ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'vtech-av' ); ?>">WhatsApp</a>
<?php endif;
if ( vtech_opt( 'vtech_show_call', true ) ) : ?>
	<a class="float-btn float-btn--call" href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>" aria-label="<?php esc_attr_e( 'Call VTECH', 'vtech-av' ); ?>">Call</a>
<?php endif;

if ( vtech_opt( 'vtech_show_sticky_cta', true ) ) : ?>
	<div class="sticky-cta" data-sticky-cta hidden>
		<span><?php esc_html_e( 'Ready to upgrade your AV setup?', 'vtech-av' ); ?></span>
		<a class="btn btn--accent" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
