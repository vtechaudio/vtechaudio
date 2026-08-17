<?php
/**
 * Title: VTECH — Home Hero
 * Slug: vtech-av/home-hero
 * Categories: vtech-home
 * Block Types: core/cover
 * Description: Full-bleed hero with eyebrow, display heading, dual CTA, trust row.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$hero = get_template_directory_uri() . '/assets/img/hero.webp';
?>
<!-- wp:cover {"url":"<?php echo esc_url( $hero ); ?>","dimRatio":72,"overlayColor":"secondary","minHeight":86,"minHeightUnit":"vh","align":"full","className":"hero"} -->
<div class="wp-block-cover alignfull hero" style="min-height:86vh"><span aria-hidden="true" class="wp-block-cover__background has-secondary-background-color has-background-dim-70 has-background-dim"></span>
<img class="wp-block-cover__image-background" src="<?php echo esc_url( $hero ); ?>" alt="Premium audio visual auditorium in Kenya" data-object-fit="cover" fetchpriority="high"/>
<div class="wp-block-cover__inner-container">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"820px"}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"className":"hero__eyebrow","style":{"color":{"text":"#FFB400"}}} --><p class="hero__eyebrow" style="color:#FFB400">AUDIO VISUAL COMPANY · KENYA & EAST AFRICA</p><!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"fontSize":"xx-large","textColor":"white"} --><h1 class="has-white-color has-text-color has-xx-large-font-size">Kenya's Premium Audio Visual Company</h1><!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"white","fontSize":"medium"} --><p class="has-white-color has-text-color has-medium-font-size">Sound systems, LED screens, stage lighting, conference & PA systems, acoustics and CCTV — designed, installed and supported for organisations across Kenya.</p><!-- /wp:paragraph -->
<!-- wp:buttons --><div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"accent","textColor":"secondary","className":"is-style-fill"} --><div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-secondary-color has-accent-background-color has-text-color has-background wp-element-button" href="/contact/">Get a Quote in 24 Hours</a></div><!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline","textColor":"white"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-white-color has-text-color wp-element-button" href="/projects/">See Our Projects</a></div><!-- /wp:button -->
</div><!-- /wp:buttons -->
</div><!-- /wp:group -->
</div></div>
<!-- /wp:cover -->
