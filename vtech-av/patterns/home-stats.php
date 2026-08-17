<?php
/**
 * Title: VTECH — Statistics Band
 * Slug: vtech-av/home-stats
 * Categories: vtech-home
 * Description: Glass stat cards over a blue band with animated count-up.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<!-- wp:group {"align":"full","backgroundColor":"primary","className":"section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull section has-primary-background-color has-background">
<!-- wp:html -->
<div class="stats">
  <div class="stat"><div class="stat__num" data-countup="200" data-suffix="+">0</div><div class="stat__label">Installations delivered</div></div>
  <div class="stat"><div class="stat__num" data-countup="47" data-suffix="">0</div><div class="stat__label">Counties served</div></div>
  <div class="stat"><div class="stat__num" data-countup="24" data-suffix="h">0</div><div class="stat__label">Quote turnaround</div></div>
  <div class="stat"><div class="stat__num" data-countup="12" data-suffix="mo">0</div><div class="stat__label">Support & warranty</div></div>
</div>
<!-- /wp:html -->
</div><!-- /wp:group -->
