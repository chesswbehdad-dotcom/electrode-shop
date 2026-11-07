<?php
/**
 * Not found.
 */

get_header();
?>
<section class="section">
	<div class="wrap">
		<div class="empty">
			<h1 style="font-size:64px;color:var(--spark)">۴۰۴</h1>
			<h2>صفحه مورد نظر پیدا نشد</h2>
			<p>شاید بین محصولات ما پیدایش کنید.</p>
			<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'electrode' ) ? get_post_type_archive_link( 'electrode' ) : home_url( '/' ) ); ?>">مشاهده محصولات</a>
		</div>
	</div>
</section>
<?php
get_footer();
