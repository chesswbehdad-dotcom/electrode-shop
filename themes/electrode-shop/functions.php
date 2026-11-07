<?php
/**
 * Electrode Shop theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELS_VERSION', '1.0.0' );

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 200, 'flex-width' => true, 'flex-height' => true ) );
		add_theme_support( 'responsive-embeds' );
		add_image_size( 'els-card', 600, 420, true );
		register_nav_menus(
			array(
				'primary' => 'منوی اصلی',
				'footer'  => 'منوی فوتر',
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'els-style', get_stylesheet_uri(), array(), ELS_VERSION );
		wp_enqueue_script( 'els-main', get_template_directory_uri() . '/assets/js/main.js', array(), ELS_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
);

/**
 * Preload the font for faster first paint.
 */
add_action(
	'wp_head',
	function () {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_template_directory_uri() . '/assets/fonts/Vazirmatn.woff2' ) );
	},
	1
);

/**
 * Whether the catalog plugin is active.
 *
 * @return bool
 */
function els_has_catalog() {
	return function_exists( 'elc_specs' );
}

/**
 * Site-wide contact setting with fallback when plugin is inactive.
 *
 * @param string $key Key.
 * @return string
 */
function els_contact( $key ) {
	return els_has_catalog() ? elc_setting( $key ) : '';
}

/**
 * Fallback primary menu when none is assigned.
 */
function els_fallback_menu() {
	$items = array(
		home_url( '/' )         => 'خانه',
		home_url( '/products/' ) => 'محصولات',
		home_url( '/about/' )   => 'درباره ما',
		home_url( '/contact/' ) => 'تماس و استعلام',
	);
	echo '<ul class="nav__list">';
	foreach ( $items as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Colour per electrode family, used by the illustration on cards.
 *
 * @param int $post_id Post ID.
 * @return string Hex colour.
 */
function els_type_color( $post_id ) {
	$map   = array(
		'rutile'     => '#c9a46b',
		'basic'      => '#8a9aa8',
		'cellulosic' => '#b98a5a',
		'stainless'  => '#d6dde3',
		'low-alloy'  => '#7c8f6f',
		'cast-iron'  => '#5d6670',
		'hardfacing' => '#a25b3b',
		'aluminum'   => '#e8ecef',
	);
	$terms = get_the_terms( $post_id, 'electrode_type' );
	if ( $terms && ! is_wp_error( $terms ) && isset( $map[ $terms[0]->slug ] ) ) {
		return $map[ $terms[0]->slug ];
	}
	return '#b0b8bf';
}

/**
 * Product image, or a generated electrode illustration when no image is set.
 *
 * @param int $post_id Post ID.
 */
function els_product_media( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, 'els-card', array( 'loading' => 'lazy' ) );
		return;
	}
	$c   = els_type_color( $post_id );
	$aws = els_has_catalog() ? get_post_meta( $post_id, '_elc_aws', true ) : '';
	$label = trim( preg_replace( '/^(AWS|DIN)\s+A?[0-9.]*:?\s*/', '', (string) $aws ) );
	?>
	<svg class="els-art" viewBox="0 0 600 420" role="img" aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
		<defs>
			<linearGradient id="g<?php echo (int) $post_id; ?>" x1="0" y1="0" x2="0" y2="1">
				<stop offset="0" stop-color="#ffffff" stop-opacity=".55"/>
				<stop offset=".45" stop-color="<?php echo esc_attr( $c ); ?>"/>
				<stop offset="1" stop-color="#000" stop-opacity=".35"/>
			</linearGradient>
		</defs>
		<rect width="600" height="420" fill="#1d232b"/>
		<g transform="rotate(-18 300 210)">
			<?php for ( $i = 0; $i < 3; $i++ ) : $y = 150 + $i * 48; ?>
				<rect x="70" y="<?php echo (int) $y; ?>" width="70" height="20" rx="3" fill="#9aa3ab"/>
				<rect x="140" y="<?php echo (int) ( $y - 3 ); ?>" width="380" height="26" rx="5" fill="url(#g<?php echo (int) $post_id; ?>)"/>
				<circle cx="522" cy="<?php echo (int) ( $y + 10 ); ?>" r="6" fill="#2b3036"/>
			<?php endfor; ?>
		</g>
		<circle cx="520" cy="80" r="40" fill="#f26b1d" opacity=".25"/>
		<circle cx="520" cy="80" r="16" fill="#ffb27a"/>
		<?php if ( $label ) : ?>
			<text x="40" y="385" fill="#fff" font-family="Vazirmatn, sans-serif" font-size="40" font-weight="800" direction="ltr"><?php echo esc_html( $label ); ?></text>
		<?php endif; ?>
	</svg>
	<?php
}

/**
 * Render one product card.
 *
 * @param int $post_id Post ID.
 */
function els_product_card( $post_id ) {
	$terms = get_the_terms( $post_id, 'electrode_type' );
	$stock = els_has_catalog() ? elc_stock( $post_id ) : array( 'key' => 'in', 'label' => '' );
	$diam  = get_post_meta( $post_id, '_elc_diameters', true );
	?>
	<article class="card">
		<a class="card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php els_product_media( $post_id ); ?>
		</a>
		<div class="card__body">
			<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
				<span class="card__cat"><?php echo esc_html( $terms[0]->name ); ?></span>
			<?php endif; ?>
			<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 18 ) ); ?></p>
			<?php if ( $diam ) : ?>
				<p class="card__meta">قطر: <span dir="ltr"><?php echo esc_html( $diam ); ?></span> mm</p>
			<?php endif; ?>
			<div class="card__foot">
				<span class="price"><?php echo esc_html( els_has_catalog() ? elc_price_label( $post_id ) : '' ); ?></span>
				<?php if ( $stock['label'] ) : ?>
					<span class="badge badge--<?php echo esc_attr( $stock['key'] ); ?>"><?php echo esc_html( $stock['label'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
}

/**
 * WhatsApp link from mobile setting.
 *
 * @param string $text Prefilled message.
 * @return string URL or empty.
 */
function els_whatsapp_url( $text = '' ) {
	$m = preg_replace( '/[^0-9]/', '', els_contact( 'mobile' ) );
	if ( ! $m ) {
		return '';
	}
	if ( 0 === strpos( $m, '0' ) ) {
		$m = '98' . substr( $m, 1 );
	}
	return 'https://wa.me/' . $m . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * Archive title cleanup.
 */
add_filter(
	'get_the_archive_title',
	function ( $title ) {
		if ( is_post_type_archive( 'electrode' ) ) {
			return 'محصولات';
		}
		if ( is_tax() ) {
			return single_term_title( '', false );
		}
		return $title;
	}
);

/**
 * Minimal inline icon set.
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 * @return string SVG markup (static, safe).
 */
function els_icon( $name, $size = 22 ) {
	$paths = array(
		'phone'  => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
		'mail'   => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'pin'    => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'clock'  => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'truck'  => '<path d="M1 3h15v13H1zM16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
		'tag'    => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.5"/>',
		'chat'   => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
		'bolt'   => '<path d="M13 2 3 14h9l-1 8 10-12h-9z"/>',
	);
	if ( 'whatsapp' === $name ) {
		return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1s-.5-.1-.7.1-.8 1-.9 1.2-.3.2-.6.1a8 8 0 0 1-2.4-1.5 9 9 0 0 1-1.6-2c-.2-.3 0-.5.1-.6l.5-.5.3-.5v-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6a1.1 1.1 0 0 0-.8.4 3.4 3.4 0 0 0-1 2.5 5.9 5.9 0 0 0 1.2 3.1 13.4 13.4 0 0 0 5.2 4.6c1.9.8 2.6.9 3.5.7a3 3 0 0 0 2-1.4 2.4 2.4 0 0 0 .2-1.4c-.1-.1-.3-.2-.6-.4zM12 21.8a9.8 9.8 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8zM20.5 3.5A11.8 11.8 0 0 0 1.9 17.7L.2 24l6.4-1.7A11.8 11.8 0 0 0 24 12a11.7 11.7 0 0 0-3.5-8.5z"/></svg>';
	}
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $name ] . '</svg>';
}

/**
 * Simple breadcrumbs.
 */
function els_breadcrumbs() {
	echo '<nav class="crumbs" aria-label="مسیر"><a href="' . esc_url( home_url( '/' ) ) . '">خانه</a>';
	if ( is_singular( 'electrode' ) || is_tax( array( 'electrode_type', 'electrode_brand' ) ) ) {
		echo ' / <a href="' . esc_url( get_post_type_archive_link( 'electrode' ) ) . '">محصولات</a>';
	}
	if ( is_singular( 'electrode' ) ) {
		$terms = get_the_terms( get_the_ID(), 'electrode_type' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			echo ' / <a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
		}
	}
	echo '</nav>';
}

if ( ! function_exists( 'elc_fa_digits' ) ) {
	/**
	 * Fallback when the catalog plugin is inactive.
	 *
	 * @param string $v Value.
	 * @return string
	 */
	function elc_fa_digits( $v ) {
		return str_replace( range( 0, 9 ), array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), (string) $v );
	}
}

/**
 * Product searches (?s=...&post_type=electrode) use the products archive layout.
 */
add_filter(
	'template_include',
	function ( $template ) {
		if ( is_search() && 'electrode' === get_query_var( 'post_type' ) ) {
			$t = locate_template( 'archive-electrode.php' );
			return $t ? $t : $template;
		}
		return $template;
	}
);

add_filter(
	'get_the_archive_title',
	function ( $title ) {
		return is_search() ? 'نتایج جستجو: ' . get_search_query() : $title;
	},
	20
);
