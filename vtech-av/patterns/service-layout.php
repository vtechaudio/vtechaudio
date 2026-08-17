<?php
/**
 * Title: VTECH — Service Page Layout
 * Slug: vtech-av/service-layout
 * Categories: vtech-service
 * Description: Reusable block layout for a service page (used when authoring a
 * Service in Gutenberg without ACF). Sections: intro, benefits, process, CTA.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<!-- wp:group {"className":"section narrow","layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group section narrow">
<!-- wp:paragraph {"fontSize":"medium"} --><p class="has-medium-font-size">Introduce the service, the problem it solves for Kenyan organisations, and why VTECH is the right integrator. Aim for 120–180 words with the primary keyword in the first sentence.</p><!-- /wp:paragraph -->
</div><!-- /wp:group -->

<!-- wp:group {"align":"full","className":"section section--surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section section--surface">
<!-- wp:heading {"fontSize":"large"} --><h2 class="wp-block-heading has-large-font-size">Benefits</h2><!-- /wp:heading -->
<!-- wp:columns {"className":"card-grid card-grid--3"} --><div class="wp-block-columns card-grid card-grid--3">
<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"feature"} --><div class="wp-block-group feature"><!-- wp:heading {"level":3,"fontSize":"medium"} --><h3 class="wp-block-heading has-medium-font-size">Benefit one</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"muted"} --><p class="has-muted-color has-text-color">Describe the outcome.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"feature"} --><div class="wp-block-group feature"><!-- wp:heading {"level":3,"fontSize":"medium"} --><h3 class="wp-block-heading has-medium-font-size">Benefit two</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"muted"} --><p class="has-muted-color has-text-color">Describe the outcome.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"feature"} --><div class="wp-block-group feature"><!-- wp:heading {"level":3,"fontSize":"medium"} --><h3 class="wp-block-heading has-medium-font-size">Benefit three</h3><!-- /wp:heading --><!-- wp:paragraph {"textColor":"muted"} --><p class="has-muted-color has-text-color">Describe the outcome.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:column -->
</div><!-- /wp:columns -->
</div><!-- /wp:group -->

<!-- wp:group {"align":"full","className":"section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section">
<!-- wp:group {"className":"cta-band","layout":{"type":"constrained"}} --><div class="wp-block-group cta-band section">
<!-- wp:heading {"textAlign":"center","textColor":"white"} --><h2 class="wp-block-heading has-text-align-center has-white-color has-text-color">Get a quote in 24 hours</h2><!-- /wp:heading -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent","textColor":"secondary"} --><div class="wp-block-button"><a class="wp-block-button__link has-secondary-color has-accent-background-color has-text-color has-background wp-element-button" href="/contact/">Request a Site Survey</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div><!-- /wp:group -->
</div><!-- /wp:group -->
