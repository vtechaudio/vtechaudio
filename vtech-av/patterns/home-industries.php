<?php
/**
 * Title: VTECH — Industries Served
 * Slug: vtech-av/home-industries
 * Categories: vtech-home
 * Description: Industry cards with bundled images linking to local landing pages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$img = get_template_directory_uri() . '/assets/img/';
?>
<!-- wp:group {"align":"full","className":"section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section">
<!-- wp:heading {"textAlign":"center","fontSize":"x-large"} --><h2 class="wp-block-heading has-text-align-center has-x-large-font-size">AV Solutions for Every Sector in Kenya</h2><!-- /wp:heading -->
<!-- wp:columns {"className":"card-grid card-grid--3"} -->
<div class="wp-block-columns card-grid card-grid--3">
<?php
$ind = array(
	array( 'Churches', 'Church sound systems Kenya', '/industry/churches/', 'industry-churches.webp' ),
	array( 'Hotels', 'Hotel audio visual solutions Kenya', '/industry/hotels/', 'industry-hotels.webp' ),
	array( 'Schools & Universities', 'School PA systems Kenya', '/industry/education/', 'industry-education.webp' ),
	array( 'Corporates', 'Boardroom AV solutions Nairobi', '/industry/corporate/', 'industry-corporate.webp' ),
	array( 'Government', 'Conference systems Kenya', '/industry/government/', 'industry-government.webp' ),
	array( 'Media Houses', 'Broadcast & live streaming', '/industry/media/', 'industry-media.webp' ),
	array( 'Hospitals & Healthcare', 'Nurse call & PA systems Kenya', '/industry/healthcare/', 'industry-corporate.webp' ),
	array( 'Conference Centres', 'Delegate & conference systems', '/industry/conference-centres/', 'industry-government.webp' ),
	array( 'Event Organisers', 'Sound & lighting hire Kenya', '/industry/events/', 'industry-media.webp' ),
);
foreach ( $ind as $i ) : ?>
<!-- wp:column --><div class="wp-block-column">
<!-- wp:group {"className":"card"} --><div class="wp-block-group card">
<!-- wp:image {"className":"card__media"} --><figure class="wp-block-image card__media"><img src="<?php echo esc_url( $img . $i[3] ); ?>" alt="AV solutions for <?php echo esc_attr( $i[0] ); ?> in Kenya" loading="lazy" decoding="async"/></figure><!-- /wp:image -->
<!-- wp:group {"style":{"spacing":{"padding":{"all":"1.25rem"}}}} --><div class="wp-block-group" style="padding:1.25rem">
<!-- wp:heading {"level":3,"fontSize":"medium"} --><h3 class="wp-block-heading has-medium-font-size"><a href="<?php echo esc_url( $i[2] ); ?>"><?php echo esc_html( $i[0] ); ?></a></h3><!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} --><p class="has-muted-color has-text-color has-small-font-size"><?php echo esc_html( $i[1] ); ?></p><!-- /wp:paragraph -->
</div><!-- /wp:group -->
</div><!-- /wp:group -->
</div><!-- /wp:column -->
<?php endforeach; ?>
</div><!-- /wp:columns -->
</div><!-- /wp:group -->
