<?php
/**
 * Price-inquiry form: render, handle, store, list in admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inquiry fields shown in admin (meta key => label).
 *
 * @return array
 */
function elc_inquiry_meta_labels() {
	return array(
		'_elc_q_name'     => 'نام و نام خانوادگی',
		'_elc_q_phone'    => 'شماره تماس',
		'_elc_q_company'  => 'شرکت / کارگاه',
		'_elc_q_city'     => 'شهر',
		'_elc_q_product'  => 'محصول',
		'_elc_q_diameter' => 'قطر (mm)',
		'_elc_q_qty'      => 'مقدار (کیلوگرم)',
		'_elc_q_message'  => 'توضیحات',
	);
}

/**
 * Render the inquiry form.
 *
 * @param int $product_id Preselected product (0 = none).
 * @return string HTML.
 */
function elc_inquiry_form( $product_id = 0 ) {
	$status   = isset( $_GET['inquiry'] ) ? sanitize_key( wp_unslash( $_GET['inquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$products = get_posts(
		array(
			'post_type'      => 'electrode',
			'posts_per_page' => 100,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	$diameters = array( '1.6', '2', '2.5', '3.25', '4', '5', '6' );

	ob_start();
	?>
	<div class="elc-form-wrap" id="inquiry">
		<?php if ( 'sent' === $status ) : ?>
			<div class="elc-alert elc-alert--ok" role="status">درخواست شما ثبت شد. همکاران ما به‌زودی برای اعلام قیمت با شما تماس می‌گیرند.</div>
		<?php elseif ( 'invalid' === $status ) : ?>
			<div class="elc-alert elc-alert--err" role="alert">لطفاً نام و شماره تماس معتبر (مثل ۰۹۱۲۱۲۳۴۵۶۷) وارد کنید.</div>
		<?php elseif ( 'wait' === $status ) : ?>
			<div class="elc-alert elc-alert--err" role="alert">درخواست قبلی شما همین الان ثبت شد؛ لطفاً یک دقیقه بعد دوباره تلاش کنید.</div>
		<?php endif; ?>

		<form class="elc-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="elc_inquiry">
			<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">
			<?php wp_nonce_field( 'elc_inquiry', 'elc_inquiry_nonce' ); ?>
			<div class="elc-hp" aria-hidden="true"><label>وب‌سایت <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

			<div class="elc-form__grid">
				<label>نام و نام خانوادگی <span class="req">*</span>
					<input type="text" name="q_name" required maxlength="80" autocomplete="name">
				</label>
				<label>شماره تماس <span class="req">*</span>
					<input type="tel" name="q_phone" required maxlength="20" inputmode="tel" dir="ltr" placeholder="09xx xxx xxxx" autocomplete="tel">
				</label>
				<label>شرکت / کارگاه
					<input type="text" name="q_company" maxlength="100" autocomplete="organization">
				</label>
				<label>شهر
					<input type="text" name="q_city" maxlength="50">
				</label>
				<label>الکترود مورد نظر
					<select name="q_product">
						<option value="0">— انتخاب کنید —</option>
						<?php foreach ( $products as $p ) : ?>
							<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $product_id, $p->ID ); ?>><?php echo esc_html( get_the_title( $p ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>قطر (میلی‌متر)
					<select name="q_diameter">
						<option value="">— فرقی نمی‌کند —</option>
						<?php foreach ( $diameters as $d ) : ?>
							<option value="<?php echo esc_attr( $d ); ?>"><?php echo esc_html( elc_fa_digits( $d ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>مقدار تقریبی (کیلوگرم)
					<input type="number" name="q_qty" min="1" step="1" inputmode="numeric">
				</label>
				<label class="elc-form__full">توضیحات
					<textarea name="q_message" rows="4" maxlength="1000" placeholder="مثلاً: برای جوشکاری مخزن تحت فشار، نیاز به تحویل در تهران"></textarea>
				</label>
			</div>
			<button type="submit" class="btn btn--primary">ارسال درخواست استعلام قیمت</button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode(
	'electrode_inquiry_form',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'product' => 0 ), $atts, 'electrode_inquiry_form' );
		return elc_inquiry_form( absint( $atts['product'] ) );
	}
);

/**
 * Normalise an Iranian phone number (Persian/Arabic digits to latin, strip spaces).
 *
 * @param string $phone Raw.
 * @return string
 */
function elc_normalize_phone( $phone ) {
	$phone = elc_latin_digits( $phone );
	return preg_replace( '/[^0-9+]/', '', $phone );
}

add_action( 'admin_post_nopriv_elc_inquiry', 'elc_handle_inquiry' );
add_action( 'admin_post_elc_inquiry', 'elc_handle_inquiry' );

/**
 * Handle form submission.
 */
function elc_handle_inquiry() {
	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	$back     = function ( $status ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'inquiry', $status, $redirect ) . '#inquiry' );
		exit;
	};

	if ( ! isset( $_POST['elc_inquiry_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elc_inquiry_nonce'] ) ), 'elc_inquiry' ) ) {
		$back( 'invalid' );
	}

	// Honeypot: bots fill it, humans don't. Pretend success.
	if ( ! empty( $_POST['website'] ) ) {
		$back( 'sent' );
	}

	$ip_key = 'elc_q_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x' );
	if ( get_transient( $ip_key ) ) {
		$back( 'wait' );
	}

	$name    = isset( $_POST['q_name'] ) ? sanitize_text_field( wp_unslash( $_POST['q_name'] ) ) : '';
	$phone   = isset( $_POST['q_phone'] ) ? elc_normalize_phone( sanitize_text_field( wp_unslash( $_POST['q_phone'] ) ) ) : '';
	$company = isset( $_POST['q_company'] ) ? sanitize_text_field( wp_unslash( $_POST['q_company'] ) ) : '';
	$city    = isset( $_POST['q_city'] ) ? sanitize_text_field( wp_unslash( $_POST['q_city'] ) ) : '';
	$product = isset( $_POST['q_product'] ) ? absint( $_POST['q_product'] ) : 0;
	$diam    = isset( $_POST['q_diameter'] ) ? sanitize_text_field( wp_unslash( $_POST['q_diameter'] ) ) : '';
	$qty     = isset( $_POST['q_qty'] ) ? absint( $_POST['q_qty'] ) : 0;
	$message = isset( $_POST['q_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['q_message'] ) ) : '';

	if ( '' === $name || mb_strlen( $name ) > 80 || ! preg_match( '/^\+?[0-9]{8,14}$/', $phone ) ) {
		$back( 'invalid' );
	}
	if ( $product && 'electrode' !== get_post_type( $product ) ) {
		$product = 0;
	}
	if ( '' !== $diam && ! preg_match( '/^[0-9.]{1,5}$/', $diam ) ) {
		$diam = '';
	}

	$product_title = $product ? get_the_title( $product ) : 'نامشخص';
	$inquiry_id    = wp_insert_post(
		array(
			'post_type'   => 'elc_inquiry',
			'post_status' => 'publish',
			'post_title'  => sprintf( '%s – %s', $name, $product_title ),
		),
		true
	);
	if ( is_wp_error( $inquiry_id ) ) {
		$back( 'invalid' );
	}

	$meta = array(
		'_elc_q_name'     => $name,
		'_elc_q_phone'    => $phone,
		'_elc_q_company'  => $company,
		'_elc_q_city'     => $city,
		'_elc_q_product'  => $product,
		'_elc_q_diameter' => $diam,
		'_elc_q_qty'      => $qty ? $qty : '',
		'_elc_q_message'  => $message,
		'_elc_q_status'   => 'new',
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $inquiry_id, $k, $v );
	}

	set_transient( $ip_key, 1, MINUTE_IN_SECONDS );

	$to = elc_setting( 'notify_email', get_option( 'admin_email' ) );
	if ( is_email( $to ) ) {
		$body = '';
		foreach ( elc_inquiry_meta_labels() as $k => $label ) {
			$v     = '_elc_q_product' === $k ? $product_title : $meta[ $k ];
			$body .= $label . ': ' . ( '' === (string) $v ? '-' : $v ) . "\n";
		}
		$body .= "\n" . admin_url( 'post.php?post=' . $inquiry_id . '&action=edit' );
		wp_mail( $to, 'استعلام قیمت جدید: ' . $product_title, $body );
	}

	$back( 'sent' );
}

/* ---------- Admin UI for inquiries ---------- */

add_filter(
	'manage_elc_inquiry_posts_columns',
	function () {
		return array(
			'cb'           => '<input type="checkbox">',
			'title'        => 'درخواست',
			'elc_phone'    => 'تماس',
			'elc_qty'      => 'مقدار',
			'elc_status'   => 'وضعیت',
			'date'         => 'تاریخ',
		);
	}
);

add_action(
	'manage_elc_inquiry_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'elc_phone' === $col ) {
			$phone = get_post_meta( $post_id, '_elc_q_phone', true );
			printf( '<a href="tel:%1$s" dir="ltr">%2$s</a>', esc_attr( $phone ), esc_html( $phone ) );
		} elseif ( 'elc_qty' === $col ) {
			$q = get_post_meta( $post_id, '_elc_q_qty', true );
			echo esc_html( $q ? $q . ' kg' : '—' );
		} elseif ( 'elc_status' === $col ) {
			$s = get_post_meta( $post_id, '_elc_q_status', true );
			echo 'new' === $s ? '<strong style="color:#d63638">جدید</strong>' : 'بررسی شده';
		}
	},
	10,
	2
);

add_action(
	'add_meta_boxes_elc_inquiry',
	function () {
		add_meta_box(
			'elc_inquiry_details',
			'جزئیات درخواست',
			function ( $post ) {
				// Opening the inquiry marks it as reviewed.
				if ( 'new' === get_post_meta( $post->ID, '_elc_q_status', true ) ) {
					update_post_meta( $post->ID, '_elc_q_status', 'seen' );
				}
				echo '<table class="widefat striped"><tbody>';
				foreach ( elc_inquiry_meta_labels() as $k => $label ) {
					$v = get_post_meta( $post->ID, $k, true );
					if ( '_elc_q_product' === $k ) {
						$v = $v ? '<a href="' . esc_url( get_edit_post_link( (int) $v ) ) . '">' . esc_html( get_the_title( (int) $v ) ) . '</a>' : '—';
					} elseif ( '_elc_q_phone' === $k ) {
						$v = '<a dir="ltr" href="tel:' . esc_attr( $v ) . '">' . esc_html( $v ) . '</a>';
					} else {
						$v = '' === (string) $v ? '—' : nl2br( esc_html( $v ) );
					}
					printf( '<tr><th style="width:180px">%s</th><td>%s</td></tr>', esc_html( $label ), $v ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts above.
				}
				echo '</tbody></table>';
			},
			'elc_inquiry',
			'normal',
			'high'
		);
	}
);

/**
 * Count bubble on the inquiries menu.
 */
add_action(
	'admin_menu',
	function () {
		global $menu;
		$count = count(
			get_posts(
				array(
					'post_type'      => 'elc_inquiry',
					'posts_per_page' => 99,
					'fields'         => 'ids',
					'meta_key'       => '_elc_q_status', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => 'new', // phpcs:ignore WordPress.DB.SlowDBQuery
					'no_found_rows'  => true,
				)
			)
		);
		if ( ! $count || ! is_array( $menu ) ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=elc_inquiry' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	},
	99
);
