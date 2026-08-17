<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search', 'vtech-av' ); ?></label>
	<input type="search" id="s" name="s" placeholder="<?php esc_attr_e( 'Search VTECH…', 'vtech-av' ); ?>" value="<?php echo get_search_query(); ?>">
	<button type="submit" class="btn"><?php esc_html_e( 'Search', 'vtech-av' ); ?></button>
</form>
