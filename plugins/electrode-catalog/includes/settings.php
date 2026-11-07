<?php
/**
 * Settings page: contact details, map, slider and teaser.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setting sections → fields (key => [label, placeholder, type]).
 *
 * @return array
 */
function elc_setting_sections() {
	$sections = array(
		'تماس' => array(
			'phone'        => array( 'تلفن ثابت', '021-12345678', 'text' ),
			'mobile'       => array( 'موبایل / واتساپ', '09121234567', 'text' ),
			'email'        => array( 'ایمیل عمومی', 'info@example.com', 'email' ),
			'address'      => array( 'آدرس', 'تهران، بازار آهن شادآباد، ...', 'text' ),
			'hours'        => array( 'ساعات کاری', 'شنبه تا پنجشنبه ۸ تا ۱۷', 'text' ),
			'notify_email' => array( 'ایمیل دریافت استعلام‌ها', get_option( 'admin_email' ), 'email' ),
		),
		'نقشه گوگل' => array(
			'map_lat' => array( 'عرض جغرافیایی (Latitude)', '35.6892', 'coord' ),
			'map_lng' => array( 'طول جغرافیایی (Longitude)', '51.3890', 'coord' ),
			'map_zoom' => array( 'بزرگنمایی (۱۰ تا ۱۹)', '15', 'int' ),
		),
		'تیزر تبلیغاتی' => array(
			'teaser_title' => array( 'عنوان بخش تیزر', 'قدرت در هر اتصال', 'text' ),
			'teaser_text'  => array( 'متن کوتاه تیزر', 'الکترودهای استاندارد برای پروژه‌های صنعتی', 'text' ),
			'teaser_video' => array( 'آدرس ویدئو (فایل MP4، یا لینک آپارات / یوتیوب) – خالی = تیزر متحرک پیش‌فرض', 'https://www.aparat.com/v/xxxx', 'url' ),
			'teaser_poster' => array( 'تصویر کاور ویدئو (اختیاری)', 'https://...', 'url' ),
		),
	);
	for ( $i = 1; $i <= 3; $i++ ) {
		$sections[ 'اسلاید ' . $i ] = array(
			"slide{$i}_title"  => array( 'عنوان', '', 'text' ),
			"slide{$i}_text"   => array( 'متن', '', 'text' ),
			"slide{$i}_button" => array( 'متن دکمه', 'مشاهده محصولات', 'text' ),
			"slide{$i}_link"   => array( 'لینک دکمه', home_url( '/products/' ), 'url' ),
			"slide{$i}_image"  => array( 'آدرس تصویر پس‌زمینه (از کتابخانه رسانه کپی کنید – خالی = طرح پیش‌فرض)', 'https://...', 'url' ),
		);
	}
	return $sections;
}

/**
 * Flat list of all fields.
 *
 * @return array
 */
function elc_setting_fields() {
	$all = array();
	foreach ( elc_setting_sections() as $fields ) {
		$all += $fields;
	}
	return $all;
}

add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=electrode',
			'تنظیمات سایت',
			'تنظیمات سایت',
			'manage_options',
			'elc-settings',
			'elc_render_settings_page'
		);
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'elc_settings',
			'elc_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'elc_sanitize_settings',
			)
		);
	}
);

/**
 * Sanitize settings by field type.
 *
 * @param mixed $input Raw.
 * @return array
 */
function elc_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array();
	foreach ( elc_setting_fields() as $key => $f ) {
		$v = isset( $input[ $key ] ) ? trim( (string) $input[ $key ] ) : '';
		switch ( $f[2] ) {
			case 'email':
				$clean[ $key ] = sanitize_email( $v );
				break;
			case 'url':
				$clean[ $key ] = esc_url_raw( $v, array( 'http', 'https' ) );
				break;
			case 'coord':
				// Accept Persian digits and the Persian decimal separator.
				$v             = preg_replace( '/[^0-9.\-]/', '', elc_latin_digits( str_replace( array( '٫', '/' ), '.', $v ) ) );
				$clean[ $key ] = preg_match( '/^-?[0-9]{1,3}(\.[0-9]+)?$/', $v ) ? $v : '';
				break;
			case 'int':
				$clean[ $key ] = '' === $v ? '' : (string) min( 21, max( 1, absint( $v ) ) );
				break;
			default:
				$clean[ $key ] = sanitize_text_field( $v );
		}
	}
	if ( isset( $clean['mobile'] ) ) {
		$clean['mobile'] = preg_replace( '/[^0-9]/', '', elc_normalize_phone( $clean['mobile'] ) );
	}
	return $clean;
}

/**
 * Render the settings page.
 */
function elc_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$opts = get_option( 'elc_settings', array() );
	?>
	<div class="wrap">
		<h1>تنظیمات سایت الکترود</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'elc_settings' ); ?>
			<?php foreach ( elc_setting_sections() as $title => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $title ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $f ) : ?>
						<tr>
							<th scope="row"><label for="elc-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f[0] ); ?></label></th>
							<td><input class="large-text" <?php echo in_array( $f[2], array( 'url', 'email', 'coord', 'int' ), true ) ? 'dir="ltr"' : ''; ?> id="elc-<?php echo esc_attr( $key ); ?>" name="elc_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( isset( $opts[ $key ] ) ? $opts[ $key ] : '' ); ?>" placeholder="<?php echo esc_attr( $f[1] ); ?>"></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button( 'ذخیره تنظیمات' ); ?>
		</form>
	</div>
	<?php
}
