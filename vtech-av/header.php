<?php
/**
 * Header: skip link, utility bar (social + NAP), sticky primary nav.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Social links helper (pulls from theme options, falls back to placeholders). */
if ( ! function_exists( 'vtech_social_links' ) ) {
	function vtech_social_links() {
		return array(
			'facebook'  => vtech_opt( 'vtech_facebook', 'https://web.facebook.com/vtechaudioke' ),
			'instagram' => vtech_opt( 'vtech_instagram', '' ),
			'linkedin'  => vtech_opt( 'vtech_linkedin', 'https://www.linkedin.com/company/vtech-audio/' ),
			'x'         => vtech_opt( 'vtech_x', '' ),
			'youtube'   => vtech_opt( 'vtech_youtube', '' ),
			'tiktok'    => vtech_opt( 'vtech_tiktok', 'https://www.tiktok.com/@vtech.audio' ),
		);
	}
}
if ( ! function_exists( 'vtech_social_icon' ) ) {
	function vtech_social_icon( $n ) {
		$p = array(
			'facebook'  => '<path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.5 2.9h-2.3v7A10 10 0 0022 12z"/>',
			'instagram' => '<path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.1.4.3 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.1-1 .3-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.1-.4-.3-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.1 1-.3 2.2-.4C8.4 2.2 8.8 2.2 12 2.2zm0 3.4A6.4 6.4 0 1018.4 12 6.4 6.4 0 0012 5.6zm0 10.5A4.1 4.1 0 1116.1 12 4.1 4.1 0 0112 16.1zm6.6-10.9a1.5 1.5 0 11-1.5-1.5 1.5 1.5 0 011.5 1.5z"/>',
			'linkedin'  => '<path d="M6.9 8.8H3.7V21h3.2V8.8zM5.3 3.5A1.9 1.9 0 105.3 7.3a1.9 1.9 0 000-3.8zM21 21h-3.2v-6c0-1.4-.5-2.4-1.8-2.4-1 0-1.5.7-1.8 1.3-.1.2-.1.5-.1.9V21H10s.1-11 0-12.2h3.2v1.7a3.2 3.2 0 012.9-1.6c2.1 0 3.7 1.4 3.7 4.3V21z"/>',
			'x'         => '<path d="M17.5 3h3l-6.6 7.5L21.8 21h-6l-4.7-6.1L5.7 21H2.7l7-8L2.5 3h6.1l4.3 5.6L17.5 3zm-1 16h1.6L7.6 4.7H5.9L16.5 19z"/>',
			'youtube'   => '<path d="M23 12s0-3.2-.4-4.7a2.5 2.5 0 00-1.7-1.7C19.4 5.2 12 5.2 12 5.2s-7.4 0-8.9.4A2.5 2.5 0 001.4 7.3C1 8.8 1 12 1 12s0 3.2.4 4.7a2.5 2.5 0 001.7 1.7c1.5.4 8.9.4 8.9.4s7.4 0 8.9-.4a2.5 2.5 0 001.7-1.7C23 15.2 23 12 23 12zM9.8 15.3V8.7l5.7 3.3-5.7 3.3z"/>',
			'tiktok'    => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>',
		);
		return isset( $p[ $n ] ) ? $p[ $n ] : '';
	}
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'vtech-av' ); ?></a>

<div class="utility-bar" role="region" aria-label="<?php esc_attr_e( 'Contact information and social links', 'vtech-av' ); ?>">
	<div class="container utility-bar__inner">
		<div class="utility-social" aria-label="<?php esc_attr_e( 'Social media', 'vtech-av' ); ?>">
			<?php foreach ( vtech_social_links() as $name => $url ) : if ( ! $url ) { continue; } ?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo vtech_social_icon( $name ); // phpcs:ignore ?></svg></a>
			<?php endforeach; ?>
		</div>
		<div class="utility-bar__contact">
			<span class="utility-bar__item"><a href="tel:<?php echo esc_attr( str_replace( ' ', '', vtech_opt( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_phone', '+254 728 135 246' ) ); ?></a></span>
			<span class="utility-bar__item"><a href="mailto:<?php echo esc_attr( vtech_opt( 'vtech_email', 'info@vtechaudio.co.ke' ) ); ?>"><?php echo esc_html( vtech_opt( 'vtech_email', 'info@vtechaudio.co.ke' ) ); ?></a></span>
			<span class="utility-bar__item utility-bar__hours"><?php echo esc_html( vtech_opt( 'vtech_hours', 'Mon–Fri, 9:00 AM – 6:00 PM' ) ); ?></span>
		</div>
	</div>
</div>

<header class="site-header" data-sticky>
	<div class="container site-header__inner">
		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?>
				<a class="brand-text" href="<?php echo esc_url( home_url( '/' ) ); ?>">VTECH <span>Audio Visual</span></a>
			<?php } ?>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'vtech-av' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'site-nav__menu', 'fallback_cb' => false, 'depth' => 2 ) ); ?>
		</nav>

		<div class="site-header__actions">
			<a class="btn btn--accent" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
			<button class="nav-toggle" aria-expanded="false" aria-controls="mobile-nav" aria-label="<?php esc_attr_e( 'Open menu', 'vtech-av' ); ?>"><span></span><span></span><span></span></button>
		</div>
	</div>
</header>

<div id="mobile-nav" class="mobile-nav" hidden>
	<div class="mobile-nav__head">
		<a class="mobile-nav__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">VTECH <span>Audio Visual</span></a>
		<button type="button" class="mobile-nav__close" aria-label="<?php esc_attr_e( 'Close menu', 'vtech-av' ); ?>">&times;</button>
	</div>
	<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'mobile-nav__menu', 'fallback_cb' => false, 'depth' => 2 ) ); ?>
	<div class="mobile-nav__cta">
		<a class="btn btn--accent btn--block" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
		<a class="btn btn--ghost btn--block" href="tel:<?php echo esc_attr( str_replace( ' ', '', get_theme_mod( 'vtech_phone', '+254 728 135 246' ) ) ); ?>"><?php esc_html_e( 'Call Us', 'vtech-av' ); ?></a>
	</div>
</div>

<main id="main" class="site-main">
