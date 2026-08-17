<?php
/**
 * Template Name: Landing Page (industry)
 * High-intent local landing pages, e.g. "AV Solutions for Churches in Kenya".
 * Injects a lead form + FAQ schema pulled from the page's ACF faqs field.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$faqs = function_exists( 'get_field' ) ? get_field( 'faqs' ) : array(); ?>

	<section class="svc-hero svc-hero--landing">
		<div class="container">
			<?php vtech_breadcrumbs(); ?>
			<h1 class="svc-hero__title"><?php the_title(); ?></h1>
			<div class="svc-hero__cta">
				<a class="btn btn--accent btn--lg" href="#lead"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
			</div>
		</div>
	</section>

	<section class="section narrow"><div class="container narrow"><?php the_content(); ?></div></section>

	<?php if ( $faqs ) : ?>
	<section class="section svc-faq"><div class="container narrow">
		<h2 class="section__title"><?php esc_html_e( 'FAQs', 'vtech-av' ); ?></h2>
		<div class="faq">
			<?php foreach ( $faqs as $f ) : ?>
				<details class="faq__item"><summary><?php echo esc_html( $f['question'] ); ?></summary><div class="faq__answer"><?php echo wp_kses_post( wpautop( $f['answer'] ) ); ?></div></details>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php
		// FAQ schema.
		$items = array();
		foreach ( $faqs as $f ) { $items[] = array( '@type' => 'Question', 'name' => $f['question'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $f['answer'] ) ) ); }
		vtech_json_ld( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ) );
	endif; ?>

	<section id="lead" class="section cta-band"><div class="container cta-band__inner">
		<h2><?php esc_html_e( 'Request your free site survey', 'vtech-av' ); ?></h2>
		<p><?php esc_html_e( 'Tell us about your venue and we\'ll send a fixed quote within 24 hours.', 'vtech-av' ); ?></p>
		<?php echo do_shortcode( '[contact-form-7 title="Lead Form"]' ); // CF7-compatible; swap for your form. ?>
		<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Contact VTECH', 'vtech-av' ); ?></a>
	</div></section>

<?php endwhile; get_footer();
