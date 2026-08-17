<?php
/**
 * Single Industry landing page — hero, intro, tailored services, related
 * projects in this sector, CTA. Renders fully even with minimal content.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post();
	$id = get_the_ID();
	$slug = get_post_field( 'post_name', $id );
	?>
	<section class="svc-hero">
		<div class="container">
			<?php if ( function_exists( 'vtech_breadcrumbs' ) ) { vtech_breadcrumbs(); } ?>
			<h1 class="svc-hero__title"><?php the_title(); ?></h1>
			<p class="svc-hero__tagline"><?php echo esc_html( get_the_excerpt() ?: 'Professional audio-visual solutions tailored for your sector across Kenya and East Africa.' ); ?></p>
			<div class="svc-hero__cta"><a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Request a Site Survey', 'vtech-av' ); ?></a></div>
		</div>
	</section>

	<?php if ( has_post_thumbnail() ) : ?>
	<section class="section svc-featured"><div class="container">
		<figure class="svc-featured__media"><?php the_post_thumbnail( 'vtech-hero', array( 'loading' => 'eager', 'class' => 'svc-featured__img' ) ); ?></figure>
	</div></section>
	<?php endif; ?>

	<?php if ( trim( get_the_content() ) ) : ?>
	<section class="section svc-intro"><div class="container narrow"><?php the_content(); ?></div></section>
	<?php endif; ?>

	<?php
	// Related projects in this industry.
	$proj = new WP_Query( array( 'post_type' => 'project', 'posts_per_page' => 6, 'tax_query' => array( array( 'taxonomy' => 'industry', 'field' => 'slug', 'terms' => $slug ) ) ) );
	if ( $proj->have_posts() ) : ?>
	<section class="section section--surface"><div class="container">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Projects in this sector', 'vtech-av' ); ?></h2>
		<div class="card-grid card-grid--3">
		<?php while ( $proj->have_posts() ) : $proj->the_post(); ?>
			<a class="card card--project" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) : ?><figure class="card__media"><?php the_post_thumbnail( 'vtech-card', array( 'loading' => 'lazy' ) ); ?></figure><?php endif; ?>
				<div class="card__body"><h3 class="card__title"><?php the_title(); ?></h3><p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p></div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div></section>
	<?php endif; ?>

	<section class="section"><div class="container"><div class="cta-band section"><div class="cta-band__inner">
		<h2><?php esc_html_e( 'Planning an AV project for your organisation?', 'vtech-av' ); ?></h2>
		<p><?php esc_html_e( 'Book a free site survey and get a fixed quote within 24 hours.', 'vtech-av' ); ?></p>
		<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
	</div></div></div></section>
<?php endwhile; get_footer();
