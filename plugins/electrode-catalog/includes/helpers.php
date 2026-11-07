<?php
/**
 * Shared helpers for Electrode Catalog.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Technical spec fields stored as post meta (key => [label, type, placeholder]).
 *
 * @return array
 */
function elc_spec_fields() {
	return array(
		'_elc_aws'        => array( 'label' => 'استاندارد / کلاس AWS', 'type' => 'text', 'ph' => 'AWS A5.1 E7018' ),
		'_elc_iso'        => array( 'label' => 'معادل ISO / EN', 'type' => 'text', 'ph' => 'ISO 2560-A: E 42 4 B 42 H5' ),
		'_elc_coating'    => array( 'label' => 'نوع پوشش', 'type' => 'text', 'ph' => 'قلیایی (بازی) با پودر آهن' ),
		'_elc_diameters'  => array( 'label' => 'قطرهای موجود (mm)', 'type' => 'text', 'ph' => '2.5, 3.25, 4, 5' ),
		'_elc_length'     => array( 'label' => 'طول (mm)', 'type' => 'text', 'ph' => '350 / 450' ),
		'_elc_current'    => array( 'label' => 'نوع جریان', 'type' => 'text', 'ph' => 'DC+ / AC' ),
		'_elc_amperage'   => array( 'label' => 'محدوده آمپر', 'type' => 'text', 'ph' => '70 – 220 A' ),
		'_elc_positions'  => array( 'label' => 'وضعیت‌های جوشکاری', 'type' => 'text', 'ph' => 'همه وضعیت‌ها به‌جز سرازیر' ),
		'_elc_tensile'    => array( 'label' => 'استحکام کششی', 'type' => 'text', 'ph' => '≥ 490 MPa' ),
		'_elc_base_metal' => array( 'label' => 'فلز پایه مناسب', 'type' => 'text', 'ph' => 'فولادهای ساختمانی، مخازن تحت فشار' ),
		'_elc_packaging'  => array( 'label' => 'بسته‌بندی', 'type' => 'text', 'ph' => 'کارتن ۵ کیلوگرمی / ۲۰ کیلوگرمی' ),
		'_elc_drying'     => array( 'label' => 'شرایط خشک کردن', 'type' => 'text', 'ph' => '۳۵۰ درجه به مدت ۲ ساعت' ),
	);
}

/**
 * Commercial fields (not part of the spec table).
 *
 * @return array
 */
function elc_commerce_fields() {
	return array(
		'_elc_price'    => array( 'label' => 'قیمت تقریبی (تومان / کیلوگرم) – خالی = «استعلام بگیرید»', 'type' => 'number', 'ph' => '' ),
		'_elc_stock'    => array( 'label' => 'وضعیت موجودی', 'type' => 'select', 'options' => elc_stock_options() ),
		'_elc_min_order' => array( 'label' => 'حداقل سفارش', 'type' => 'text', 'ph' => '۱ کارتن' ),
		'_elc_featured' => array( 'label' => 'نمایش در صفحه اصلی (محصول ویژه)', 'type' => 'checkbox' ),
	);
}

/**
 * @return array
 */
function elc_stock_options() {
	return array(
		'in'    => 'موجود',
		'order' => 'سفارشی / با زمان تحویل',
		'out'   => 'ناموجود',
	);
}

/**
 * Convert latin digits to Persian digits.
 *
 * @param string|int|float $value Input.
 * @return string
 */
function elc_fa_digits( $value ) {
	return str_replace(
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
		(string) $value
	);
}

/**
 * Formatted price label for a product.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function elc_price_label( $post_id ) {
	$price = (int) get_post_meta( $post_id, '_elc_price', true );
	if ( $price <= 0 ) {
		return 'استعلام بگیرید';
	}
	return elc_fa_digits( number_format( $price ) ) . ' تومان / کیلوگرم';
}

/**
 * Stock label + status key.
 *
 * @param int $post_id Post ID.
 * @return array{key:string,label:string}
 */
function elc_stock( $post_id ) {
	$key     = get_post_meta( $post_id, '_elc_stock', true );
	$options = elc_stock_options();
	if ( ! isset( $options[ $key ] ) ) {
		$key = 'in';
	}
	return array( 'key' => $key, 'label' => $options[ $key ] );
}

/**
 * Non-empty spec rows for a product.
 *
 * @param int $post_id Post ID.
 * @return array<int,array{label:string,value:string}>
 */
function elc_specs( $post_id ) {
	$rows = array();
	foreach ( elc_spec_fields() as $key => $field ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( '' !== (string) $value ) {
			$rows[] = array( 'label' => $field['label'], 'value' => $value );
		}
	}
	return $rows;
}

/**
 * Get a plugin setting.
 *
 * @param string $key     Setting key.
 * @param string $default Default.
 * @return string
 */
function elc_setting( $key, $default = '' ) {
	$opts = get_option( 'elc_settings', array() );
	return isset( $opts[ $key ] ) && '' !== $opts[ $key ] ? $opts[ $key ] : $default;
}

/**
 * Convert Persian/Arabic digits to latin digits.
 *
 * @param string $value Input.
 * @return string
 */
function elc_latin_digits( $value ) {
	return str_replace(
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		(string) $value
	);
}

/**
 * Google Maps embed URL from settings (no API key needed).
 *
 * @return string
 */
function elc_map_embed_url() {
	$lat  = elc_setting( 'map_lat' );
	$lng  = elc_setting( 'map_lng' );
	$zoom = elc_setting( 'map_zoom', '15' );
	if ( '' === $lat || '' === $lng ) {
		return '';
	}
	return 'https://maps.google.com/maps?q=' . rawurlencode( $lat . ',' . $lng ) . '&z=' . (int) $zoom . '&hl=fa&output=embed';
}

/**
 * Google Maps directions link.
 *
 * @return string
 */
function elc_map_link() {
	$lat = elc_setting( 'map_lat' );
	$lng = elc_setting( 'map_lng' );
	return ( '' === $lat || '' === $lng ) ? '' : 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $lat . ',' . $lng );
}

/**
 * Hero slides (settings with sensible defaults).
 *
 * @return array<int,array{title:string,text:string,button:string,link:string,image:string}>
 */
function elc_slides() {
	$defaults = array(
		1 => array( 'تأمین انواع الکترود جوشکاری', 'روتیلی، قلیایی، سلولزی، استیل و چدن — مستقیم از انبار، با تحویل سریع', 'مشاهده محصولات', home_url( '/products/' ) ),
		2 => array( 'فروش عمده برای پروژه‌های صنعتی', 'قیمت ویژه برای پیمانکاران، کارخانه‌ها و پروژه‌های نفت و گاز', 'استعلام قیمت', home_url( '/contact/' ) ),
		3 => array( 'مشاوره فنی رایگان انتخاب الکترود', 'بر اساس فلز پایه، وضعیت جوشکاری و استاندارد پروژه، بهترین الکترود را پیشنهاد می‌دهیم', 'تماس با ما', home_url( '/contact/' ) ),
	);
	$slides = array();
	foreach ( $defaults as $i => $d ) {
		$slides[] = array(
			'title'  => elc_setting( "slide{$i}_title", $d[0] ),
			'text'   => elc_setting( "slide{$i}_text", $d[1] ),
			'button' => elc_setting( "slide{$i}_button", $d[2] ),
			'link'   => elc_setting( "slide{$i}_link", $d[3] ),
			'image'  => elc_setting( "slide{$i}_image" ),
		);
	}
	return $slides;
}

/**
 * Teaser video embed info.
 *
 * @return array{type:string,src:string}  type: mp4|iframe|none
 */
function elc_teaser_video() {
	$url = elc_setting( 'teaser_video' );
	if ( ! $url ) {
		return array( 'type' => 'none', 'src' => '' );
	}
	if ( preg_match( '#aparat\.com/v/([A-Za-z0-9]+)#', $url, $m ) ) {
		return array( 'type' => 'iframe', 'src' => 'https://www.aparat.com/video/video/embed/videohash/' . $m[1] . '/vt/frame' );
	}
	if ( preg_match( '#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
		return array( 'type' => 'iframe', 'src' => 'https://www.youtube-nocookie.com/embed/' . $m[1] );
	}
	return array( 'type' => 'mp4', 'src' => $url );
}
