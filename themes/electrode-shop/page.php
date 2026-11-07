<?php
/**
 * Generic page.
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero"><div class="wrap"><?php els_breadcrumbs(); ?><h1><?php the_title(); ?></h1></div></section>
	<section class="section"><div class="wrap"><div class="content"><?php the_content(); ?></div></div></section>
	<?php
endwhile;
get_footer();
