<?php
/**
 * Title: VTECH — Our Services Grid
 * Slug: vtech-av/home-services
 * Categories: vtech-home
 * Description: Three-column services grid with bundled images and links.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$img = get_template_directory_uri() . '/assets/img/';
?>
<!-- wp:group {"align":"full","className":"section section--surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section section--surface">
<!-- wp:heading {"textAlign":"center","fontSize":"x-large"} --><h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Complete Audio Visual Solutions</h2><!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"muted"} --><p class="has-text-align-center has-muted-color has-text-color">From boardrooms in Nairobi to auditoriums nationwide — one integrator for design, supply, installation and support.</p><!-- /wp:paragraph -->
<!-- wp:columns {"className":"card-grid card-grid--3"} -->
<div class="wp-block-columns card-grid card-grid--3">
<?php
$services = array(
	array( 'Professional Sound Systems', 'Line arrays, mixing and PA tuned for churches, halls and stadiums across Kenya.', '/services/sound-systems/', 'sound-systems.webp' ),
	array( 'LED Screens & Video Walls', 'Indoor and outdoor LED display supply, installation and content.', '/services/led-screens/', 'led-screens.webp' ),
	array( 'Conference & Boardroom AV', 'Video conferencing, room automation and one-touch meeting rooms.', '/services/conference-systems/', 'conference-systems.webp' ),
	array( 'Stage & Architectural Lighting', 'Event and permanent lighting design, rigging and control.', '/services/lighting/', 'stage-lighting.webp' ),
	array( 'Acoustic Design & Soundproofing', 'Room acoustics, treatment and noise control for clean, clear audio.', '/services/acoustic-solutions/', 'acoustic-solutions.webp' ),
	array( 'CCTV & Digital Signage', 'Security integration and digital signage networks.', '/services/video-systems/', 'video-systems.webp' ),
);
foreach ( $services as $s ) : ?>
<!-- wp:column --><div class="wp-block-column">
<!-- wp:group {"className":"card card--service"} --><div class="wp-block-group card card--service">
<!-- wp:image {"className":"card__media"} --><figure class="wp-block-image card__media"><img src="<?php echo esc_url( $img . $s[3] ); ?>" alt="<?php echo esc_attr( $s[0] ); ?> in Kenya" loading="lazy" decoding="async"/></figure><!-- /wp:image -->
<!-- wp:heading {"level":3,"fontSize":"large"} --><h3 class="wp-block-heading has-large-font-size"><?php echo esc_html( $s[0] ); ?></h3><!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"muted"} --><p class="has-muted-color has-text-color"><?php echo esc_html( $s[1] ); ?></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p><a class="card__link" href="<?php echo esc_url( $s[2] ); ?>">Explore</a></p><!-- /wp:paragraph -->
</div><!-- /wp:group -->
</div><!-- /wp:column -->
<?php endforeach; ?>
</div><!-- /wp:columns -->
</div><!-- /wp:group -->
