<?php
/**
 * Single Service template. Sections: Hero, Benefits, Process, Gallery, FAQ,
 * Related Services, CTA. Powered by ACF when present, with professional
 * fallbacks so the page ALWAYS renders fully — even before any ACF data is
 * entered and even if ACF is not installed. No fatal errors.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

/* Safe ACF getter: returns $default if ACF missing or field empty. */
if ( ! function_exists( 'vtech_field' ) ) {
	function vtech_field( $name, $id = null, $default = '' ) {
		if ( ! function_exists( 'get_field' ) ) { return $default; }
		$v = get_field( $name, $id );
		return ( null === $v || false === $v || '' === $v ) ? $default : $v;
	}
}

while ( have_posts() ) : the_post();
	$id      = get_the_ID();
	$title   = get_the_title();

	$tagline = vtech_field( 'tagline', $id, get_the_excerpt() ?: sprintf( 'Professional %s designed, installed and supported across Kenya and East Africa.', $title ) );
	$price   = vtech_field( 'price_from', $id, '' );

	// Ensure repeaters are always arrays.
	$benefits = vtech_field( 'benefits', $id, array() );
	$process  = vtech_field( 'process', $id, array() );
	$faqs     = vtech_field( 'faqs', $id, array() );
	$related  = vtech_field( 'related', $id, array() );
	$gallery  = vtech_field( 'gallery', $id, array() );
	if ( ! is_array( $benefits ) ) { $benefits = array(); }
	if ( ! is_array( $process ) )  { $process  = array(); }
	if ( ! is_array( $faqs ) )     { $faqs     = array(); }
	if ( ! is_array( $related ) )  { $related  = array(); }
	if ( ! is_array( $gallery ) )  { $gallery  = array(); }

	/* ---- Professional fallbacks (used when ACF fields are empty) ---- */
	if ( empty( $benefits ) ) {
		$benefits = array(
			array( 'title' => 'Free Site Survey', 'text' => 'We assess your space, acoustics and requirements on-site before quoting — no guesswork, no surprises.' ),
			array( 'title' => 'Fixed 24-Hour Quote', 'text' => 'A clear, itemised written quote within 24 hours in Nairobi and 48 hours upcountry.' ),
			array( 'title' => 'Certified Installation', 'text' => 'Clean, standards-compliant installation by trained technicians, with full documentation.' ),
			array( 'title' => '12-Month Support', 'text' => 'Every project includes a 12-month support window, with annual maintenance contracts available.' ),
			array( 'title' => 'Trusted Brands', 'text' => 'We specify and supply reliable, warrantied equipment from leading global manufacturers.' ),
			array( 'title' => 'Nationwide Coverage', 'text' => 'Delivery, installation and support across all 47 counties in Kenya and select East Africa projects.' ),
		);
	}
	if ( empty( $process ) ) {
		$process = array(
			array( 'title' => 'Consultation & Site Survey', 'text' => 'We visit your site to understand your goals, space and budget.' ),
			array( 'title' => 'Design & Proposal', 'text' => 'A tailored system design with a fixed, transparent quote.' ),
			array( 'title' => 'Supply & Installation', 'text' => 'Professional installation with minimal disruption to your operations.' ),
			array( 'title' => 'Testing & Handover', 'text' => 'Full commissioning, staff training and documentation.' ),
			array( 'title' => 'Support & Maintenance', 'text' => 'Ongoing 12-month support and optional annual servicing.' ),
		);
	}
	if ( empty( $faqs ) ) {
		$faqs = array(
			array( 'question' => sprintf( 'How much does %s cost in Kenya?', $title ), 'answer' => 'Pricing depends on your space, coverage and equipment tier. After a free site survey we provide a fixed written quote within 24 hours.' ),
			array( 'question' => 'Do you cover locations outside Nairobi?', 'answer' => 'Yes. We install and support across all 47 counties in Kenya and select projects across East Africa.' ),
			array( 'question' => 'Do you provide maintenance and support?', 'answer' => 'Every installation includes a 12-month support window, with annual maintenance contracts available.' ),
			array( 'question' => 'Can you work with our existing equipment?', 'answer' => 'Yes — we assess and integrate compatible existing systems wherever possible to protect your investment.' ),
		);
	}
	?>

	<section class="svc-hero">
		<div class="container">
			<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
			<h1 class="svc-hero__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $tagline ) : ?><p class="svc-hero__tagline"><?php echo esc_html( $tagline ); ?></p><?php endif; ?>
			<div class="svc-hero__cta">
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Request a Site Survey', 'vtech-av' ); ?></a>
				<?php if ( $price ) : ?><span class="svc-hero__price"><?php printf( esc_html__( 'From KES %s', 'vtech-av' ), esc_html( number_format( (float) $price ) ) ); ?></span><?php endif; ?>
			</div>
		</div>
	</section>

	<?php $content = get_the_content(); if ( trim( $content ) ) : ?>
	<section class="section svc-intro"><div class="container narrow"><?php the_content(); ?></div></section>
	<?php endif; ?>

	<?php if ( $benefits ) : ?>
	<section class="section section--surface svc-benefits"><div class="container">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Why choose VTECH', 'vtech-av' ); ?></h2>
		<div class="card-grid card-grid--3">
			<?php foreach ( $benefits as $b ) : $bt = is_array( $b ) ? ( $b['title'] ?? '' ) : ''; $bx = is_array( $b ) ? ( $b['text'] ?? '' ) : ''; ?>
				<div class="feature"><h3 class="feature__title"><?php echo esc_html( $bt ); ?></h3><p><?php echo esc_html( $bx ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<?php if ( $process ) : ?>
	<section class="section svc-process"><div class="container narrow">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Our process', 'vtech-av' ); ?></h2>
		<ol class="process">
			<?php foreach ( $process as $i => $p ) : $pt = is_array( $p ) ? ( $p['title'] ?? '' ) : ''; $px = is_array( $p ) ? ( $p['text'] ?? '' ) : ''; ?>
				<li class="process__step"><span class="process__num"><?php echo esc_html( $i + 1 ); ?></span><div><h3><?php echo esc_html( $pt ); ?></h3><p><?php echo esc_html( $px ); ?></p></div></li>
			<?php endforeach; ?>
		</ol>
	</div></section>
	<?php endif; ?>

	<?php if ( $gallery ) : ?>
	<section class="section section--surface svc-gallery"><div class="container">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Recent work', 'vtech-av' ); ?></h2>
		<div class="gallery-grid">
			<?php foreach ( $gallery as $img ) :
				if ( ! is_array( $img ) ) { continue; }
				$src = $img['sizes']['vtech-card'] ?? ( $img['url'] ?? '' );
				$alt = $img['alt'] ?? $title;
				if ( ! $src ) { continue; } ?>
				<figure><img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async"></figure>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<?php if ( $faqs ) : ?>
	<section class="section svc-faq"><div class="container narrow">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Frequently asked questions', 'vtech-av' ); ?></h2>
		<div class="faq">
			<?php foreach ( $faqs as $f ) : $q = is_array( $f ) ? ( $f['question'] ?? '' ) : ''; $a = is_array( $f ) ? ( $f['answer'] ?? '' ) : ''; if ( ! $q ) { continue; } ?>
				<details class="faq__item"><summary><?php echo esc_html( $q ); ?></summary><div class="faq__answer"><?php echo wp_kses_post( wpautop( $a ) ); ?></div></details>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<?php
	// Related services: use ACF if set, else fall back to other services.
	if ( empty( $related ) ) {
		$rq = new WP_Query( array( 'post_type' => 'service', 'posts_per_page' => 3, 'post__not_in' => array( $id ), 'orderby' => 'rand' ) );
		$related = wp_list_pluck( $rq->posts, 'ID' );
		wp_reset_postdata();
	}
	if ( $related ) : ?>
	<section class="section section--surface svc-related"><div class="container">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Related services', 'vtech-av' ); ?></h2>
		<div class="card-grid card-grid--3">
			<?php foreach ( $related as $r ) : $rid = is_object( $r ) ? $r->ID : $r; if ( ! $rid ) { continue; } ?>
				<a class="card card--service" href="<?php echo esc_url( get_permalink( $rid ) ); ?>"><h3 class="card__title"><?php echo esc_html( get_the_title( $rid ) ); ?></h3><span class="card__link"><?php esc_html_e( 'Explore', 'vtech-av' ); ?></span></a>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<section class="section"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
		<h2><?php printf( esc_html__( 'Get a %s quote in 24 hours', 'vtech-av' ), esc_html( $title ) ); ?></h2>
		<p><?php esc_html_e( 'Book a free site survey and get a fixed, transparent quote — no obligation.', 'vtech-av' ); ?></p>
		<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Free Consultation', 'vtech-av' ); ?></a>
	</div></div></div></section>

<?php endwhile; get_footer();
