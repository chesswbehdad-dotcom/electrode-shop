<?php
/**
 * Contact page (slug: contact): contact cards, Google Map, inquiry form.
 */

get_header();

$els_map  = els_has_catalog() ? elc_map_embed_url() : '';
$els_dir  = els_has_catalog() ? elc_map_link() : '';
$els_wa   = els_whatsapp_url( 'سلام، برای استعلام قیمت الکترود پیام می‌دهم.' );
$els_tel  = els_contact( 'phone' );
$els_mob  = els_contact( 'mobile' );
$els_mail = els_contact( 'email' );
?>

<section class="page-hero">
	<div class="wrap">
		<?php els_breadcrumbs(); ?>
		<h1><?php the_title(); ?></h1>
		<p>برای قیمت روز، فروش عمده و مشاوره انتخاب الکترود با ما در تماس باشید.</p>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="contact-cards">
			<div class="contact-card">
				<div class="feature__icon"><?php echo els_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div><h3>تلفن</h3>
					<?php if ( $els_tel ) : ?><a dir="ltr" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $els_tel ) ); ?>"><?php echo esc_html( $els_tel ); ?></a><br><?php endif; ?>
					<?php if ( $els_mob ) : ?><a dir="ltr" href="tel:<?php echo esc_attr( $els_mob ); ?>"><?php echo esc_html( $els_mob ); ?></a><?php endif; ?>
				</div>
			</div>
			<div class="contact-card">
				<div class="feature__icon" style="background:#e7f9ee;color:#1eb457"><?php echo els_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div><h3>واتساپ</h3>
					<?php if ( $els_wa ) : ?><a href="<?php echo esc_url( $els_wa ); ?>" target="_blank" rel="noopener">شروع گفتگو</a><?php else : ?><p>—</p><?php endif; ?>
				</div>
			</div>
			<div class="contact-card">
				<div class="feature__icon"><?php echo els_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div><h3>ایمیل</h3><?php if ( $els_mail ) : ?><a href="mailto:<?php echo esc_attr( $els_mail ); ?>"><?php echo esc_html( $els_mail ); ?></a><?php else : ?><p>—</p><?php endif; ?></div>
			</div>
			<div class="contact-card">
				<div class="feature__icon"><?php echo els_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div><h3>ساعات کاری</h3><p><?php echo esc_html( els_contact( 'hours' ) ? els_contact( 'hours' ) : '—' ); ?></p></div>
			</div>
		</div>

		<div class="two-col">
			<div>
				<h2>آدرس و نقشه</h2>
				<?php if ( els_contact( 'address' ) ) : ?>
					<p style="display:flex;gap:8px;align-items:center"><?php echo els_icon( 'pin', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( els_contact( 'address' ) ); ?></p>
				<?php endif; ?>
				<?php if ( $els_map ) : ?>
					<div class="map">
						<iframe src="<?php echo esc_url( $els_map ); ?>" title="موقعیت ما روی نقشه گوگل" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
						<?php if ( $els_dir ) : ?><a class="btn btn--primary map__link" href="<?php echo esc_url( $els_dir ); ?>" target="_blank" rel="noopener">مسیریابی</a><?php endif; ?>
					</div>
				<?php else : ?>
					<div class="empty">مختصات نقشه را در «الکترودها ← تنظیمات سایت» وارد کنید.</div>
				<?php endif; ?>

				<?php
				while ( have_posts() ) :
					the_post();
					$els_content = trim( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ) );
					if ( $els_content ) :
						?>
						<div class="content" style="margin-top:24px"><?php echo wp_kses_post( wpautop( $els_content ) ); ?></div>
						<?php
					endif;
				endwhile;
				?>
			</div>
			<div>
				<h2>فرم استعلام قیمت</h2>
				<?php echo els_has_catalog() ? elc_inquiry_form() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
