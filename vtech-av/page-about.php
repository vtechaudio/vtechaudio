<?php
/**
 * Template Name: About VTECH
 * Professional, international-standard About page. Kenya & East Africa market.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$img = VTECH_URI . '/assets/img/';
?>
<section class="about-hero">
	<div class="container">
		<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
		<h1><?php esc_html_e( 'Kenya\'s Trusted Audio-Visual Integrator', 'vtech-av' ); ?></h1>
		<p><?php esc_html_e( 'VTECH Audio Visual Solutions designs, installs and supports professional AV systems for organisations across Kenya and East Africa — engineered to international standards, delivered with local expertise.', 'vtech-av' ); ?></p>
	</div>
</section>

<section class="section"><div class="container narrow">
	<h2 class="section__title"><?php esc_html_e( 'Who we are', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Founded and headquartered in Nairobi, VTECH Audio Visual Solutions is a full-service audio-visual integrator serving churches, hotels, schools and universities, corporates, government, healthcare, media houses and event organisers. We bring together system design, supply, installation, commissioning and long-term support under one accountable partner.', 'vtech-av' ); ?></p>
	<p><?php esc_html_e( 'Our engineers specify equipment from leading global manufacturers and install to recognised international standards for acoustics, electrical safety and signal integrity — while remaining grounded in the realities of the Kenyan and East African market: power conditions, room types, budgets and timelines. The result is dependable AV that performs on day one and keeps performing for years.', 'vtech-av' ); ?></p>
</div></section>

<section class="section section--surface"><div class="container">
	<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'By the numbers', 'vtech-av' ); ?></h2>
	<div class="about-stats">
		<div><div class="about-stat__num">42+</div><p><?php esc_html_e( 'Installations delivered', 'vtech-av' ); ?></p></div>
		<div><div class="about-stat__num">47</div><p><?php esc_html_e( 'Counties served', 'vtech-av' ); ?></p></div>
		<div><div class="about-stat__num">24h</div><p><?php esc_html_e( 'Quote turnaround', 'vtech-av' ); ?></p></div>
		<div><div class="about-stat__num">12mo</div><p><?php esc_html_e( 'Support & warranty', 'vtech-av' ); ?></p></div>
	</div>
</div></section>

<section class="section"><div class="container">
	<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'What we stand for', 'vtech-av' ); ?></h2>
	<div class="about-values">
		<div class="feature"><h3 class="feature__title"><?php esc_html_e( 'Engineering integrity', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Systems designed to international standards, not shortcuts. We measure, specify and document every install.', 'vtech-av' ); ?></p></div>
		<div class="feature"><h3 class="feature__title"><?php esc_html_e( 'Transparent pricing', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Fixed written quotes within 24 hours after a free site survey — no hidden costs, no surprises.', 'vtech-av' ); ?></p></div>
		<div class="feature"><h3 class="feature__title"><?php esc_html_e( 'Local accountability', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'A Kenyan team you can reach, with nationwide coverage and rapid support across East Africa.', 'vtech-av' ); ?></p></div>
		<div class="feature"><h3 class="feature__title"><?php esc_html_e( 'Long-term partnership', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Every project includes a 12-month support window and optional annual maintenance contracts.', 'vtech-av' ); ?></p></div>
	</div>
</div></section>

<section class="section section--surface"><div class="container narrow">
	<h2 class="section__title"><?php esc_html_e( 'Our approach', 'vtech-av' ); ?></h2>
	<ol class="process">
		<li class="process__step"><span class="process__num">1</span><div><h3><?php esc_html_e( 'Listen & survey', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'We start with your goals and a free on-site survey of your space and acoustics.', 'vtech-av' ); ?></p></div></li>
		<li class="process__step"><span class="process__num">2</span><div><h3><?php esc_html_e( 'Design & quote', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'A tailored system design and a fixed, transparent quote within 24 hours.', 'vtech-av' ); ?></p></div></li>
		<li class="process__step"><span class="process__num">3</span><div><h3><?php esc_html_e( 'Install & commission', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Clean, standards-compliant installation, full testing and staff training.', 'vtech-av' ); ?></p></div></li>
		<li class="process__step"><span class="process__num">4</span><div><h3><?php esc_html_e( 'Support & maintain', 'vtech-av' ); ?></h3><p><?php esc_html_e( 'Ongoing support and maintenance so your system keeps performing.', 'vtech-av' ); ?></p></div></li>
	</ol>
</div></section>


<section class="section section--surface"><div class="container">
	<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Learn more', 'vtech-av' ); ?></h2>
	<div class="card-grid card-grid--3 about-resources">
		<a class="card" href="<?php echo esc_url( home_url( '/equipment-hire/' ) ); ?>"><div class="card__body"><h3 class="card__title"><?php esc_html_e( 'Equipment Hire Guide', 'vtech-av' ); ?></h3><p class="card__excerpt"><?php esc_html_e( 'In-depth articles on hiring sound, LED, lighting and stage for your event.', 'vtech-av' ); ?></p><span class="card__link"><?php esc_html_e( 'Read', 'vtech-av' ); ?></span></div></a>
		<a class="card" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><div class="card__body"><h3 class="card__title"><?php esc_html_e( 'FAQ', 'vtech-av' ); ?></h3><p class="card__excerpt"><?php esc_html_e( 'Answers to common audio-visual questions for the Kenyan market.', 'vtech-av' ); ?></p><span class="card__link"><?php esc_html_e( 'Read', 'vtech-av' ); ?></span></div></a>
		<a class="card" href="<?php echo esc_url( get_post_type_archive_link( 'hire_package' ) ?: home_url( '/hire-packages/' ) ); ?>"><div class="card__body"><h3 class="card__title"><?php esc_html_e( 'Hire Packages', 'vtech-av' ); ?></h3><p class="card__excerpt"><?php esc_html_e( 'Ready-made AV packages for events of every size.', 'vtech-av' ); ?></p><span class="card__link"><?php esc_html_e( 'Browse', 'vtech-av' ); ?></span></div></a>
	</div>
</div></section>

<section class="section"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
	<h2><?php esc_html_e( 'Let\'s build your AV system the right way', 'vtech-av' ); ?></h2>
	<p><?php esc_html_e( 'Book a free site survey and get a fixed quote within 24 hours.', 'vtech-av' ); ?></p>
	<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
</div></div></div></section>

<?php $vtc_brands = function_exists( 'vtech_get_brands' ) ? vtech_get_brands( 12 ) : array(); if ( ! empty( $vtc_brands ) ) : ?>
<section class="section section--surface"><div class="container">
	<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Brands We Work With', 'vtech-av' ); ?></h2>
	<p style="text-align:center;color:var(--c-muted);max-width:640px;margin:0 auto 2.5rem"><?php esc_html_e( 'We specify equipment from leading global manufacturers, matched to your budget and application.', 'vtech-av' ); ?></p>
	<div class="brand-carousel" data-brand-carousel>
		<button type="button" class="brand-carousel__nav brand-carousel__nav--prev" data-brand-prev aria-label="Previous brands">&#8249;</button>
		<div class="brand-carousel__track" data-brand-track>
			<?php foreach ( $vtc_brands as $vtc_b ) : ?>
				<div class="brand-carousel__item">
					<?php if ( $vtc_b['logo'] ) : ?>
						<img src="<?php echo esc_url( $vtc_b['logo'] ); ?>" alt="<?php echo esc_attr( $vtc_b['name'] ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="brand-carousel__name"><?php echo esc_html( $vtc_b['name'] ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<button type="button" class="brand-carousel__nav brand-carousel__nav--next" data-brand-next aria-label="Next brands">&#8250;</button>
	</div>
</div></section>
<?php endif; ?>

<?php get_footer();
