<?php
/**
 * Default page template.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
while ( have_posts() ) : the_post(); ?>
	<article class="container section page">
		<?php vtech_breadcrumbs(); ?>
		<h1 class="page__title"><?php the_title(); ?></h1>
		<div class="page__content narrow"><?php the_content(); ?></div>
	</article>
<?php endwhile; get_footer();
