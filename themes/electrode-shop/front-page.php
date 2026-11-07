<?php
/**
 * Home page: slider, stats, categories, featured products, teaser, features, CTA.
 */

get_header();

$els_slides = els_has_catalog() ? elc_slides() : array();
$els_kicks  = array( 'فروش مستقیم از انبار', 'ویژه پیمانکاران و صنایع', 'مشاوره رایگان' );
?>

<?php if ( $els_slides ) : ?>
<section class="slider" data-slider data-interval="6000" aria-roledescription="carousel" aria-label="اسلایدشو">
	<div class="slider__track">
		<?php foreach ( $els_slides as $i => $s ) : ?>
			<div class="slide <?php echo $s['image'] ? '' : 'slide--art-' . ( $i + 1 ); ?><?php echo 0 === $i ? ' is-active' : ''; ?>"
				<?php echo $s['image'] ? 'style="background-image:url(' . esc_url( $s['image'] ) . ')"' : ''; ?>
				role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $i + 1 ) . ' از ' . count( $els_slides ) ); ?>">
				<?php if ( ! $s['image'] ) : ?>
					<svg class="slide__art" viewBox="0 0 400 300" aria-hidden="true">
						<g transform="rotate(-25 200 150)">
							<?php for ( $r = 0; $r < 5; $r++ ) : $y = 90 + $r * 26; ?>
								<rect x="40" y="<?php echo (int) $y; ?>" width="50" height="12" rx="2" fill="#8d97a1"/>
								<rect x="90" y="<?php echo (int) ( $y - 3 ); ?>" width="250" height="18" rx="4" fill="<?php echo esc_attr( array( '#c9a46b', '#8a9aa8', '#d6dde3' )[ $i % 3 ] ); ?>" opacity="<?php echo esc_attr( 1 - $r * 0.08 ); ?>"/>
							<?php endfor; ?>
						</g>
						<circle cx="345" cy="70" r="46" fill="#f26b1d" opacity=".25"/>
						<circle cx="345" cy="70" r="18" fill="#ffd2ad"/>
					</svg>
				<?php endif; ?>
				<div class="wrap">
					<div class="slide__content">
						<span class="slide__kicker"><?php echo esc_html( $els_kicks[ $i % 3 ] ); ?></span>
						<h2><?php echo esc_html( $s['title'] ); ?></h2>
						<p><?php echo esc_html( $s['text'] ); ?></p>
						<div>
							<?php if ( $s['button'] ) : ?>
								<a class="btn btn--primary" href="<?php echo esc_url( $s['link'] ); ?>"><?php echo esc_html( $s['button'] ); ?></a>
							<?php endif; ?>
							<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">تماس با ما</a>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<button class="slider__btn slider__btn--prev" type="button" aria-label="اسلاید قبلی">›</button>
	<button class="slider__btn slider__btn--next" type="button" aria-label="اسلاید بعدی">‹</button>
	<div class="slider__dots" role="tablist"></div>
	<div class="slider__progress"></div>
</section>
<?php endif; ?>

<div class="wrap">
	<div class="stats">
		<?php
		$els_count = wp_count_posts( 'electrode' );
		$els_n     = isset( $els_count->publish ) ? (int) $els_count->publish : 0;
		$els_stats = array(
			array( elc_fa_digits( $els_n ) . '+', 'نوع الکترود' ),
			array( '۲۴ ساعته', 'پاسخ به استعلام' ),
			array( 'AWS / ISO', 'محصولات استاندارد' ),
			array( 'سراسر ایران', 'ارسال به همه شهرها' ),
		);
		foreach ( $els_stats as $st ) :
			?>
			<div class="stat"><strong><?php echo esc_html( $st[0] ); ?></strong><span><?php echo esc_html( $st[1] ); ?></span></div>
		<?php endforeach; ?>
	</div>
</div>

<?php
$els_cats = taxonomy_exists( 'electrode_type' ) ? get_terms( array( 'taxonomy' => 'electrode_type', 'hide_empty' => false ) ) : array();
if ( $els_cats && ! is_wp_error( $els_cats ) ) :
	$els_colors = array( 'rutile' => '#c9a46b', 'basic' => '#8a9aa8', 'cellulosic' => '#b98a5a', 'stainless' => '#c8d1d9', 'low-alloy' => '#7c8f6f', 'cast-iron' => '#5d6670', 'hardfacing' => '#a25b3b', 'aluminum' => '#dfe5ea' );
	?>
<section class="section">
	<div class="wrap">
		<div class="section__head">
			<div><span class="eyebrow">دسته‌بندی محصولات</span><h2>الکترود مناسب کار خود را پیدا کنید</h2></div>
			<a class="btn btn--dark" href="<?php echo esc_url( get_post_type_archive_link( 'electrode' ) ); ?>">همه محصولات</a>
		</div>
		<div class="cats">
			<?php foreach ( $els_cats as $c ) : ?>
				<a class="cat" href="<?php echo esc_url( get_term_link( $c ) ); ?>">
					<span class="cat__count"><?php echo esc_html( elc_fa_digits( $c->count ) ); ?> محصول</span>
					<span class="cat__swatch" style="background:<?php echo esc_attr( isset( $els_colors[ $c->slug ] ) ? $els_colors[ $c->slug ] : '#b0b8bf' ); ?>"></span>
					<h3><?php echo esc_html( $c->name ); ?></h3>
					<p><?php echo esc_html( wp_trim_words( $c->description, 12 ) ); ?></p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$els_featured = new WP_Query(
	array(
		'post_type'      => 'electrode',
		'posts_per_page' => 4,
		'meta_key'       => '_elc_featured', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
		'no_found_rows'  => true,
	)
);
if ( ! $els_featured->have_posts() ) {
	$els_featured = new WP_Query( array( 'post_type' => 'electrode', 'posts_per_page' => 4, 'no_found_rows' => true ) );
}
if ( $els_featured->have_posts() ) :
	?>
<section class="section section--white">
	<div class="wrap">
		<div class="section__head">
			<div><span class="eyebrow">پرفروش‌ها</span><h2>محصولات ویژه</h2><p>الکترودهایی که بیشترین سفارش را دارند</p></div>
		</div>
		<div class="grid grid--4">
			<?php
			while ( $els_featured->have_posts() ) :
				$els_featured->the_post();
				els_product_card( get_the_ID() );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$els_video  = els_has_catalog() ? elc_teaser_video() : array( 'type' => 'none', 'src' => '' );
$els_ttitle = els_contact( 'teaser_title' ) ? els_contact( 'teaser_title' ) : 'قدرت در هر اتصال';
$els_ttext  = els_contact( 'teaser_text' );
?>
<section class="teaser" id="teaser">
	<div class="wrap">
		<div>
			<span class="eyebrow">تیزر معرفی</span>
			<h2><?php echo esc_html( $els_ttitle ); ?></h2>
			<?php if ( $els_ttext ) : ?><p><?php echo esc_html( $els_ttext ); ?></p><?php endif; ?>
			<ul class="teaser__list">
				<li>کنترل کیفیت هر بچ تولید</li>
				<li>بسته‌بندی ضد رطوبت و وکیوم</li>
				<li>ارسال سریع به کارگاه و سایت پروژه</li>
			</ul>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">همین حالا استعلام بگیرید</a>
		</div>
		<div class="teaser__screen">
			<?php if ( 'mp4' === $els_video['type'] ) : ?>
				<video src="<?php echo esc_url( $els_video['src'] ); ?>" <?php echo els_contact( 'teaser_poster' ) ? 'poster="' . esc_url( els_contact( 'teaser_poster' ) ) . '"' : ''; ?> controls playsinline preload="metadata"></video>
			<?php elseif ( 'iframe' === $els_video['type'] ) : ?>
				<iframe src="<?php echo esc_url( $els_video['src'] ); ?>" title="تیزر تبلیغاتی" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>
			<?php else : ?>
				<div class="film" aria-label="تیزر متحرک">
					<span class="film__badge">● تیزر</span>
					<div class="film__scene"><div><b><?php bloginfo( 'name' ); ?></b><small>تأمین‌کننده تخصصی الکترود جوشکاری</small></div></div>
					<div class="film__scene"><div><b>E6013 · E7018 · E308L</b><small>روتیلی، قلیایی، استیل، چدن و سخت‌کاری</small></div></div>
					<div class="film__scene"><div><b>استعلام قیمت در کمتر از ۲۴ ساعت</b><small><?php echo esc_html( els_contact( 'phone' ) ? els_contact( 'phone' ) : 'همین حالا تماس بگیرید' ); ?></small></div></div>
					<div class="film__bead"></div>
					<div class="film__rod"></div>
					<div class="film__arc"></div>
					<?php
					$els_sparks = array( array( 70, -90 ), array( -60, -110 ), array( 110, -40 ), array( -120, -50 ), array( 30, -140 ), array( -20, -70 ), array( 140, -100 ), array( -90, -130 ) );
					foreach ( $els_sparks as $k => $sp ) :
						?>
						<span class="spark" style="--x:<?php echo (int) $sp[0]; ?>px;--y:<?php echo (int) $sp[1]; ?>px;animation-delay:<?php echo esc_attr( $k * 0.13 ); ?>s"></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<div class="section__head"><div><span class="eyebrow">چرا ما؟</span><h2>خرید مطمئن الکترود</h2></div></div>
		<div class="features">
			<?php
			$els_features = array(
				array( 'shield', 'اصالت و کیفیت', 'محصولات دارای برگه مشخصات فنی و مطابق استانداردهای AWS و ISO.' ),
				array( 'tag', 'قیمت رقابتی', 'قیمت ویژه برای خرید عمده و همکاران.' ),
				array( 'truck', 'ارسال سریع', 'ارسال به سراسر کشور با بسته‌بندی ضد رطوبت.' ),
				array( 'chat', 'مشاوره فنی', 'کمک به انتخاب الکترود مناسب فلز پایه و پروژه شما.' ),
			);
			foreach ( $els_features as $f ) :
				?>
				<div class="feature">
					<div class="feature__icon"><?php echo els_icon( $f[0], 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<h3><?php echo esc_html( $f[1] ); ?></h3>
					<p><?php echo esc_html( $f[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section section--white">
	<div class="wrap">
		<div class="cta">
			<div>
				<h2>برای پروژه‌تان الکترود لازم دارید؟</h2>
				<p>نوع، قطر و مقدار را بفرستید؛ قیمت روز و زمان تحویل را اعلام می‌کنیم.</p>
			</div>
			<div style="display:flex;gap:10px;flex-wrap:wrap">
				<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">ثبت استعلام قیمت</a>
				<?php $els_wa = els_whatsapp_url( 'سلام، استعلام قیمت الکترود دارم.' ); ?>
				<?php if ( $els_wa ) : ?><a class="btn btn--wa" href="<?php echo esc_url( $els_wa ); ?>" target="_blank" rel="noopener">واتساپ</a><?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
