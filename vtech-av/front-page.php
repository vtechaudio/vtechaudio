<?php
/**
 * Front page — renders the FULL homepage directly in PHP using bundled assets.
 *
 * This does NOT depend on page content, block patterns, or the setup wizard.
 * The homepage always displays correctly the moment the theme is active and a
 * static front page is set — nothing can strip or fail to expand. Content is
 * pulled from the CPTs when present, with built-in fallbacks so it looks
 * complete even before setup runs.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

// --- DEFENSIVE FALLBACKS (guarantee the homepage never blanks) ---
if ( ! function_exists( 'vtech_opt' ) ) {
	function vtech_opt( $key, $default = '' ) { return get_theme_mod( $key, $default ); }
}
if ( ! defined( 'VTECH_URI' ) ) { define( 'VTECH_URI', get_template_directory_uri() ); }

if ( ! function_exists( 'vtech_service_fallback_img' ) ) {
	function vtech_service_fallback_img( $slug ) {
		$slug = strtolower( (string) $slug );
		$map = array(
			'sound' => 'sound-systems.webp', 'led' => 'led-screens.webp', 'screen' => 'led-screens.webp',
			'video-wall' => 'led-screens.webp', 'conference' => 'conference-systems.webp', 'boardroom' => 'conference-systems.webp',
			'light' => 'stage-lighting.webp', 'acoustic' => 'acoustic-solutions.webp',
			'signage' => 'video-systems.webp', 'display' => 'video-systems.webp', 'video-system' => 'video-systems.webp',
			'consult' => 'consultation.webp', 'design' => 'consultation.webp',
		);
		foreach ( $map as $needle => $file ) {
			if ( false !== strpos( $slug, $needle ) ) { return $file; }
		}
		return 'og-default.webp';
	}
}

$img = VTECH_URI . '/assets/img/';
?>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title">
	<img class="hero__bg skip-lazy no-lazy" src="<?php echo esc_url( get_theme_mod( 'vtech_hero_img', $img . 'hero.webp' ) ); ?>" alt="VTECH Audio Visual — premium AV solutions in Kenya" fetchpriority="high" loading="eager" decoding="async" data-no-lazy="1" data-skip-lazy width="1600" height="900">
	<div class="container hero__inner">
		<p class="hero__eyebrow"><?php esc_html_e( 'Audio Visual Company · Kenya & East Africa', 'vtech-av' ); ?></p>
		<h1 id="hero-title" class="hero__title"><?php echo esc_html( vtech_opt( 'vtech_hero_title', "Kenya's Premium Audio Visual Company" ) ); ?></h1>
		<p class="hero__sub"><?php echo esc_html( vtech_opt( 'vtech_hero_sub', 'Sound systems, LED screens, stage lighting, conference & PA systems, acoustics and digital signage — designed, installed and supported for organisations across Kenya.' ) ); ?></p>
		<div class="hero__cta">
			<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Book a Consultation', 'vtech-av' ); ?></a>
			<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ?: home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'See Our Projects', 'vtech-av' ); ?></a>
		</div>
		<?php
		$vtc_stats = array(
			array( vtech_opt( 'vtech_stat1_num', '200+' ),  vtech_opt( 'vtech_stat1_lbl', 'Installations delivered' ) ),
			array( vtech_opt( 'vtech_stat2_num', '47' ),    vtech_opt( 'vtech_stat2_lbl', 'Counties served' ) ),
			array( vtech_opt( 'vtech_stat3_num', '24h' ),   vtech_opt( 'vtech_stat3_lbl', 'Quote turnaround' ) ),
			array( vtech_opt( 'vtech_stat4_num', '12mo' ),  vtech_opt( 'vtech_stat4_lbl', 'Support & warranty' ) ),
		);
		?>
		<div class="hero__stats">
			<?php foreach ( $vtc_stats as $vtc_st ) : if ( '' === trim( (string) $vtc_st[0] ) && '' === trim( (string) $vtc_st[1] ) ) { continue; } ?>
				<div class="hero__stat">
					<span class="hero__stat-num"><?php echo esc_html( $vtc_st[0] ); ?></span>
					<span class="hero__stat-lbl"><?php echo esc_html( $vtc_st[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ TRUSTED CLIENTS (logo slider) ============ -->
<?php
$vtc_clients = function_exists( 'vtech_get_clients' ) ? vtech_get_clients() : array();
// Only render this section when there are real clients (added in the dashboard).
// No placeholder names are ever shown.
if ( ! empty( $vtc_clients ) ) : ?>
<section class="clients" aria-label="Organisations that trust VTECH">
	<div class="container">
		<p class="clients__eyebrow"><?php esc_html_e( 'Trusted by leading organisations across Kenya', 'vtech-av' ); ?></p>
	</div>
	<div class="logo-marquee" role="group" aria-label="Client logos">
		<div class="logo-marquee__track">
			<?php
			$loop = array_merge( $vtc_clients, $vtc_clients );
			foreach ( $loop as $vtc_c ) :
				$vtc_inner = $vtc_c['logo']
					? '<img src="' . esc_url( $vtc_c['logo'] ) . '" alt="' . esc_attr( $vtc_c['name'] ) . '" loading="lazy" decoding="async" class="logo-marquee__img">'
					: '<span class="logo-marquee__name">' . esc_html( $vtc_c['name'] ) . '</span>';
				if ( ! empty( $vtc_c['url'] ) ) {
					echo '<a class="logo-marquee__item" href="' . esc_url( $vtc_c['url'] ) . '" target="_blank" rel="noopener">' . $vtc_inner . '</a>';
				} else {
					echo '<span class="logo-marquee__item">' . $vtc_inner . '</span>';
				}
			endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============ SERVICES ============ -->
<section class="section section--surface" aria-labelledby="svc-title">
	<div class="container">
		<h2 id="svc-title" class="section__title" style="text-align:center"><?php esc_html_e( 'Complete Audio Visual Solutions', 'vtech-av' ); ?></h2>
		<p style="text-align:center;color:var(--c-muted);max-width:640px;margin:0 auto 2.5rem"><?php esc_html_e( 'From boardrooms in Nairobi to auditoriums nationwide — one integrator for design, supply, installation and support.', 'vtech-av' ); ?></p>
		<div class="card-grid card-grid--3">
		<?php
		$services = new WP_Query( array( 'post_type' => 'service', 'posts_per_page' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
		if ( $services->have_posts() ) :
			while ( $services->have_posts() ) : $services->the_post(); ?>
				<a class="card card--service" href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="card__media"><?php the_post_thumbnail( 'vtech-card', array( 'loading' => 'lazy' ) ); ?></figure>
					<?php else : ?>
						<figure class="card__media"><img src="<?php echo esc_url( $img . vtech_service_fallback_img( get_post_field( 'post_name', get_the_ID() ) ) ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?> in Kenya" loading="lazy" decoding="async"></figure>
					<?php endif; ?>
					<h3 class="card__title"><?php the_title(); ?></h3>
					<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
					<span class="card__link"><?php esc_html_e( 'Explore', 'vtech-av' ); ?></span>
				</a>
			<?php endwhile; wp_reset_postdata();
		else :
			// Fallback before setup: static cards with bundled images.
			$fallback = array(
				array( 'Professional Sound Systems', 'Line arrays, mixing and PA for churches, halls and stadiums.', 'sound-systems.webp', '/services/professional-sound-systems/' ),
				array( 'LED Screens & Video Walls', 'Indoor and outdoor LED display supply and installation.', 'led-screens.webp', '/services/led-screens-video-walls/' ),
				array( 'Conference & Boardroom AV', 'Video conferencing and one-touch meeting rooms.', 'conference-systems.webp', '/services/conference-boardroom-av/' ),
				array( 'Stage & Architectural Lighting', 'Event and permanent lighting design and control.', 'stage-lighting.webp', '/services/stage-architectural-lighting/' ),
				array( 'Acoustic Design & Soundproofing', 'Room acoustics and noise control for clean audio.', 'acoustic-solutions.webp', '/services/acoustic-design-soundproofing/' ),
				array( 'Digital Signage & Displays', 'Indoor and outdoor digital signage and professional display networks.', 'video-systems.webp', '/services/digital-signage/' ),
			);
			foreach ( $fallback as $f ) : ?>
				<a class="card card--service" href="<?php echo esc_url( home_url( $f[3] ) ); ?>">
					<figure class="card__media"><img src="<?php echo esc_url( $img . $f[2] ); ?>" alt="<?php echo esc_attr( $f[0] ); ?> in Kenya" loading="lazy" decoding="async"></figure>
					<h3 class="card__title"><?php echo esc_html( $f[0] ); ?></h3>
					<p class="card__excerpt"><?php echo esc_html( $f[1] ); ?></p>
					<span class="card__link"><?php esc_html_e( 'Explore', 'vtech-av' ); ?></span>
				</a>
			<?php endforeach;
		endif; ?>
		</div>
	</div>
</section>

<!-- ============ INDUSTRIES ============ -->
<section class="section" aria-labelledby="ind-title">
	<div class="container">
		<h2 id="ind-title" class="section__title" style="text-align:center"><?php esc_html_e( 'AV Solutions for Every Sector in Kenya', 'vtech-av' ); ?></h2>
		<div class="card-grid card-grid--3">
		<?php
		$industries = array(
			array( 'Churches', 'Church sound systems Kenya', 'industry-churches.webp', '/industries/churches/' ),
			array( 'Hotels & Hospitality', 'Hotel audio visual solutions Kenya', 'industry-hotels.webp', '/industries/hotels/' ),
			array( 'Schools & Universities', 'School PA systems Kenya', 'industry-education.webp', '/industries/education/' ),
			array( 'Corporates', 'Boardroom AV solutions Nairobi', 'industry-corporate.webp', '/industries/corporate/' ),
			array( 'Government', 'Conference systems Kenya', 'industry-government.webp', '/industries/government/' ),
			array( 'Media Houses', 'Broadcast & live streaming', 'industry-media.webp', '/industries/media/' ),
			array( 'Hospitals & Healthcare', 'Nurse call & PA systems Kenya', 'industry-healthcare.webp', '/industries/healthcare/' ),
			array( 'Conference Centres', 'Delegate & conference systems', 'industry-conference-centres.webp', '/industries/conference-centres/' ),
			array( 'Event Organisers', 'Sound & lighting hire Kenya', 'industry-events.webp', '/industries/events/' ),
		);
		// Homepage shows a curated, fixed set (default 9). Filterable if you ever want to change it.
		$ind_limit = (int) apply_filters( 'vtech_home_industries_limit', 9 );
		$industries = array_slice( $industries, 0, $ind_limit );
		foreach ( $industries as $i ) : ?>
			<a class="card" href="<?php echo esc_url( home_url( $i[3] ) ); ?>">
				<figure class="card__media"><img src="<?php echo esc_url( $img . $i[2] ); ?>" alt="AV solutions for <?php echo esc_attr( $i[0] ); ?> in Kenya" loading="lazy" decoding="async"></figure>
				<div class="card__body">
					<h3 class="card__title"><?php echo esc_html( $i[0] ); ?></h3>
					<p class="card__excerpt"><?php echo esc_html( $i[1] ); ?></p>
				</div>
			</a>
		<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<section class="section" style="background:linear-gradient(135deg,#81007F,#5C005A)">
	<div class="container">
		<div class="stats">
			<?php
			$vtc_blue_stats = array(
				array( vtech_opt( 'vtech_stat1_num', '200+' ),  vtech_opt( 'vtech_stat1_lbl', 'Installations delivered' ) ),
				array( vtech_opt( 'vtech_stat2_num', '47' ),    vtech_opt( 'vtech_stat2_lbl', 'Counties served' ) ),
				array( vtech_opt( 'vtech_stat3_num', '24h' ),   vtech_opt( 'vtech_stat3_lbl', 'Quote turnaround' ) ),
				array( vtech_opt( 'vtech_stat4_num', '12mo' ),  vtech_opt( 'vtech_stat4_lbl', 'Support & warranty' ) ),
			);
			foreach ( $vtc_blue_stats as $vtc_bs ) :
				$vtc_raw = trim( (string) $vtc_bs[0] );
				$vtc_lbl = trim( (string) $vtc_bs[1] );
				if ( '' === $vtc_raw && '' === $vtc_lbl ) { continue; }
				// Split leading digits (for the count-up animation) from any suffix like +, h, mo.
				preg_match( '/^(\\d+)(.*)$/', $vtc_raw, $vtc_m );
				$vtc_count  = isset( $vtc_m[1] ) ? $vtc_m[1] : '';
				$vtc_suffix = isset( $vtc_m[2] ) ? $vtc_m[2] : '';
				if ( '' === $vtc_count ) : ?>
					<div class="stat"><div class="stat__num"><?php echo esc_html( $vtc_raw ); ?></div><div class="stat__label"><?php echo esc_html( $vtc_lbl ); ?></div></div>
				<?php else : ?>
					<div class="stat"><div class="stat__num" data-countup="<?php echo esc_attr( $vtc_count ); ?>" data-suffix="<?php echo esc_attr( $vtc_suffix ); ?>"><?php echo esc_html( $vtc_count . $vtc_suffix ); ?></div><div class="stat__label"><?php echo esc_html( $vtc_lbl ); ?></div></div>
				<?php endif;
			endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ FEATURED PROJECTS ============ -->
<?php
$vtech_proj_limit = (int) apply_filters( 'vtech_home_projects_limit', (int) get_theme_mod( 'vtech_home_projects', 6 ) );
if ( $vtech_proj_limit < 1 ) { $vtech_proj_limit = 6; }
$projects = new WP_Query( array( 'post_type' => 'project', 'posts_per_page' => $vtech_proj_limit, 'orderby' => 'date', 'order' => 'DESC' ) );
if ( $projects->have_posts() ) : ?>
<section class="section section--surface">
	<div class="container">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Recent Projects in Kenya', 'vtech-av' ); ?></h2>
		<div class="card-grid card-grid--3">
		<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
			<a class="card card--project" href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) : ?><figure class="card__media"><?php the_post_thumbnail( 'vtech-card', array( 'loading' => 'lazy' ) ); ?></figure><?php endif; ?>
				<div class="card__body">
					<h3 class="card__title"><?php the_title(); ?></h3>
					<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
				</div>
			</a>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<section class="section">
	<div class="container narrow">
		<h2 class="section__title" style="text-align:center"><?php esc_html_e( 'Frequently Asked Questions', 'vtech-av' ); ?></h2>
		<div class="faq">
			<details class="faq__item"><summary><?php esc_html_e( 'How fast can VTECH quote my AV project?', 'vtech-av' ); ?></summary><div class="faq__answer"><?php esc_html_e( 'We provide a fixed written quote within 24 hours of a free site survey in Nairobi, and within 48 hours upcountry.', 'vtech-av' ); ?></div></details>
			<details class="faq__item"><summary><?php esc_html_e( 'Do you cover locations outside Nairobi?', 'vtech-av' ); ?></summary><div class="faq__answer"><?php esc_html_e( 'Yes. We install and support AV systems across all 47 counties in Kenya and select projects across East Africa.', 'vtech-av' ); ?></div></details>
			<details class="faq__item"><summary><?php esc_html_e( 'Do you offer equipment hire as well as installation?', 'vtech-av' ); ?></summary><div class="faq__answer"><?php esc_html_e( 'Yes — sound, lighting, LED and conferencing equipment is available for event hire with delivery, setup and an on-site technician.', 'vtech-av' ); ?></div></details>
			<details class="faq__item"><summary><?php esc_html_e( 'Do you provide maintenance after installation?', 'vtech-av' ); ?></summary><div class="faq__answer"><?php esc_html_e( 'Every installation includes a 12-month support window, with annual maintenance contracts available.', 'vtech-av' ); ?></div></details>
		</div>
	</div>
</section>

<!-- ============ CTA ============ -->
<section class="section">
	<div class="container">
		<div class="cta-band section">
			<div class="cta-band__inner">
				<h2><?php esc_html_e( 'Planning an AV project in Kenya?', 'vtech-av' ); ?></h2>
				<p><?php esc_html_e( 'Book a free site survey and get a fixed quote within 24 hours.', 'vtech-av' ); ?></p>
				<a class="btn btn--accent btn--lg" href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Request a Site Survey', 'vtech-av' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer();
