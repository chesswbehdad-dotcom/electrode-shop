<?php
/**
 * Single electrode product.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$els_id    = get_the_ID();
	$els_stock = els_has_catalog() ? elc_stock( $els_id ) : array( 'key' => 'in', 'label' => '' );
	$els_specs = els_has_catalog() ? elc_specs( $els_id ) : array();
	$els_wa    = els_whatsapp_url( 'سلام، قیمت «' . get_the_title() . '» را می‌خواستم.' );
	$els_min   = get_post_meta( $els_id, '_elc_min_order', true );
	?>
	<section class="page-hero">
		<div class="wrap"><?php els_breadcrumbs(); ?></div>
	</section>

	<section class="section">
		<div class="wrap">
			<div class="product">
				<div class="product__media"><?php els_product_media( $els_id ); ?></div>

				<div class="product__summary">
					<?php
					$els_terms = get_the_terms( $els_id, 'electrode_type' );
					if ( $els_terms && ! is_wp_error( $els_terms ) ) :
						?>
						<a class="eyebrow" href="<?php echo esc_url( get_term_link( $els_terms[0] ) ); ?>"><?php echo esc_html( $els_terms[0]->name ); ?></a>
					<?php endif; ?>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?><p class="product__lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>

					<div class="product__price">
						<span><?php echo esc_html( els_has_catalog() ? elc_price_label( $els_id ) : '' ); ?></span>
						<?php if ( $els_stock['label'] ) : ?><span class="badge badge--<?php echo esc_attr( $els_stock['key'] ); ?>"><?php echo esc_html( $els_stock['label'] ); ?></span><?php endif; ?>
					</div>

					<div class="quick">
						<div>کلاس<strong><?php echo esc_html( get_post_meta( $els_id, '_elc_aws', true ) ? preg_replace( '/^AWS\s+A[0-9.]+\s*/', '', get_post_meta( $els_id, '_elc_aws', true ) ) : '—' ); ?></strong></div>
						<div>جریان<strong><?php echo esc_html( get_post_meta( $els_id, '_elc_current', true ) ? get_post_meta( $els_id, '_elc_current', true ) : '—' ); ?></strong></div>
						<div>حداقل سفارش<strong style="direction:rtl"><?php echo esc_html( $els_min ? $els_min : '—' ); ?></strong></div>
					</div>

					<div class="product__actions">
						<a class="btn btn--primary" href="#inquiry">استعلام قیمت این محصول</a>
						<?php if ( $els_wa ) : ?><a class="btn btn--wa" href="<?php echo esc_url( $els_wa ); ?>" target="_blank" rel="noopener"><?php echo els_icon( 'whatsapp', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> واتساپ</a><?php endif; ?>
						<?php if ( els_contact( 'phone' ) ) : ?><a class="btn btn--dark" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', els_contact( 'phone' ) ) ); ?>"><?php echo els_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> تماس</a><?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section section--white">
		<div class="wrap two-col">
			<div>
				<h2>مشخصات فنی</h2>
				<?php if ( $els_specs ) : ?>
					<table class="specs">
						<tbody>
							<?php foreach ( $els_specs as $row ) : ?>
								<tr><th scope="row"><?php echo esc_html( $row['label'] ); ?></th><td><?php echo esc_html( $row['value'] ); ?></td></tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p>مشخصات فنی به‌زودی اضافه می‌شود.</p>
				<?php endif; ?>

				<?php if ( get_the_content() ) : ?>
					<h2 style="margin-top:36px">توضیحات و کاربرد</h2>
					<div class="content"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
			<div>
				<h2>استعلام قیمت</h2>
				<?php echo els_has_catalog() ? elc_inquiry_form( $els_id ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
		</div>
	</section>

	<?php
	$els_related = new WP_Query(
		array(
			'post_type'      => 'electrode',
			'posts_per_page' => 4,
			'post__not_in'   => array( $els_id ),
			'no_found_rows'  => true,
			'tax_query'      => $els_terms && ! is_wp_error( $els_terms ) ? array( array( 'taxonomy' => 'electrode_type', 'terms' => wp_list_pluck( $els_terms, 'term_id' ) ) ) : array(), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	if ( $els_related->post_count < 2 ) {
		$els_related = new WP_Query( array( 'post_type' => 'electrode', 'posts_per_page' => 4, 'post__not_in' => array( $els_id ), 'no_found_rows' => true ) );
	}
	if ( $els_related->have_posts() ) :
		?>
		<section class="section">
			<div class="wrap">
				<div class="section__head"><h2>محصولات مرتبط</h2></div>
				<div class="grid grid--4">
					<?php
					while ( $els_related->have_posts() ) :
						$els_related->the_post();
						els_product_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
