<?php
/**
 * Fallback template (blog, search, archives).
 */

get_header();
?>
<section class="page-hero"><div class="wrap"><h1><?php echo is_search() ? esc_html( 'نتایج جستجو: ' . get_search_query() ) : ( is_home() ? 'اخبار و مقالات' : wp_kses_post( get_the_archive_title() ) ); ?></h1></div></section>
<section class="section">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="grid">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( 'electrode' === get_post_type() ) {
						els_product_card( get_the_ID() );
						continue;
					}
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?><a class="card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'els-card' ); ?></a><?php endif; ?>
						<div class="card__body">
							<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination"><?php echo wp_kses_post( paginate_links() ); ?></div>
		<?php else : ?>
			<div class="empty"><h2>چیزی پیدا نشد</h2></div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
