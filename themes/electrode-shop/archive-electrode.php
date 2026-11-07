<?php
/**
 * Products archive (also used for electrode_type / electrode_brand taxonomies).
 */

get_header();

$els_current_term = is_tax() ? get_queried_object() : null;
$els_base_url     = $els_current_term ? get_term_link( $els_current_term ) : get_post_type_archive_link( 'electrode' );
$els_d            = isset( $_GET['d'] ) ? sanitize_text_field( wp_unslash( $_GET['d'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$els_stock        = isset( $_GET['stock'] ) ? sanitize_key( wp_unslash( $_GET['stock'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
global $wp_query;
?>

<section class="page-hero">
	<div class="wrap">
		<?php els_breadcrumbs(); ?>
		<h1><?php the_archive_title(); ?></h1>
		<?php if ( $els_current_term && $els_current_term->description ) : ?>
			<p><?php echo esc_html( $els_current_term->description ); ?></p>
		<?php else : ?>
			<p>کاتالوگ کامل الکترودهای جوشکاری با مشخصات فنی — برای قیمت روز استعلام بگیرید.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="wrap layout">
		<aside class="filters" aria-label="فیلتر محصولات">
			<h3>جستجو</h3>
			<form class="search-box" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="مثلاً 7018" value="<?php echo esc_attr( get_search_query() ); ?>">
				<input type="hidden" name="post_type" value="electrode">
				<button class="btn btn--dark" style="padding:8px 14px" type="submit">برو</button>
			</form>

			<h3>دسته‌بندی</h3>
			<ul>
				<li><a class="<?php echo $els_current_term ? '' : 'is-active'; ?>" href="<?php echo esc_url( get_post_type_archive_link( 'electrode' ) ); ?>">همه محصولات</a></li>
				<?php
				$els_types = get_terms( array( 'taxonomy' => 'electrode_type', 'hide_empty' => true ) );
				if ( ! is_wp_error( $els_types ) ) :
					foreach ( $els_types as $t ) :
						$active = $els_current_term && $els_current_term->term_id === $t->term_id;
						?>
						<li><a class="<?php echo $active ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $t ) ); ?>"><span><?php echo esc_html( $t->name ); ?></span><span><?php echo esc_html( elc_fa_digits( $t->count ) ); ?></span></a></li>
						<?php
					endforeach;
				endif;
				?>
			</ul>

			<h3>قطر الکترود (mm)</h3>
			<div class="chips">
				<a class="chip <?php echo '' === $els_d ? 'is-active' : ''; ?>" href="<?php echo esc_url( remove_query_arg( array( 'd', 'paged' ), add_query_arg( array() ) ) ); ?>">همه</a>
				<?php foreach ( array( '2', '2.5', '3.25', '4', '5' ) as $d ) : ?>
					<a class="chip <?php echo $els_d === $d ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'd', $d, $els_base_url ) . ( $els_stock ? '&stock=' . rawurlencode( $els_stock ) : '' ) ); ?>"><?php echo esc_html( elc_fa_digits( $d ) ); ?></a>
				<?php endforeach; ?>
			</div>

			<h3>موجودی</h3>
			<div class="chips">
				<a class="chip <?php echo 'in' !== $els_stock ? 'is-active' : ''; ?>" href="<?php echo esc_url( remove_query_arg( array( 'stock', 'paged' ) ) ); ?>">همه</a>
				<a class="chip <?php echo 'in' === $els_stock ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'stock', 'in' ) ); ?>">فقط موجود</a>
			</div>

			<h3>برند</h3>
			<ul>
				<?php
				$els_brands = get_terms( array( 'taxonomy' => 'electrode_brand', 'hide_empty' => true ) );
				if ( ! is_wp_error( $els_brands ) ) :
					foreach ( $els_brands as $b ) :
						?>
						<li><a href="<?php echo esc_url( get_term_link( $b ) ); ?>"><span><?php echo esc_html( $b->name ); ?></span><span><?php echo esc_html( elc_fa_digits( $b->count ) ); ?></span></a></li>
						<?php
					endforeach;
				endif;
				?>
			</ul>
		</aside>

		<div>
			<div class="results-bar">
				<span><?php echo esc_html( elc_fa_digits( (int) $wp_query->found_posts ) ); ?> محصول</span>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">الکترود مورد نظرتان را پیدا نکردید؟ بپرسید</a>
			</div>
			<?php if ( have_posts() ) : ?>
				<div class="grid">
					<?php
					while ( have_posts() ) :
						the_post();
						els_product_card( get_the_ID() );
					endwhile;
					?>
				</div>
				<div class="pagination"><?php echo wp_kses_post( paginate_links( array( 'prev_text' => 'قبلی', 'next_text' => 'بعدی' ) ) ); ?></div>
			<?php else : ?>
				<div class="empty">
					<h2>محصولی با این فیلتر پیدا نشد</h2>
					<p>فیلترها را تغییر دهید یا برای تأمین سفارشی با ما تماس بگیرید.</p>
					<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'electrode' ) ); ?>">نمایش همه</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();
